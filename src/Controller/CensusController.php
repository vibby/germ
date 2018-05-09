<?php

namespace Germ\Controller;

use Germ\Filter\Census\Searcher;
use Germ\Model\Germ\Census\CensusFinder;
use Germ\Model\Germ\Census\CensusSaver;
use Germ\Model\Germ\ChurchSchema\CensusModel;
use PommProject\Foundation\Pomm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Germ\Type\CensusType;

class CensusController extends Controller
{
    private $finder;
    private $searcher;
    private $saver;
    private $model;

    public function __construct(CensusFinder $finder, Searcher $searcher, CensusSaver $saver, Pomm $pomm)
    {
        $this->finder = $finder;
        $this->searcher = $searcher;
        $this->saver = $saver;
        $this->model = $pomm['germ']->getModel(CensusModel::class);
    }

    public function listAction(Request $request, $page)
    {
        if ($request->get('_format') != 'html') {
            $output['censuses'] = $this->finder->findAll();
        } else {
            if ($redirect = $this->searcher->handleRequest($request)) {
                return $redirect;
            }
            $output['searchForm'] = $this->searcher->getForm()->createView();
            $paginator = $this->get('knp_paginator');
            $output['paginatedCensuses'] = $paginator->paginate(
                [
                    $this->finder,
                    'paginateFilterQuery',
                    [$this->searcher],
                ],
                $page,
                min((int) $request->get('perPage', 30), 250)
            );
        }

        $response = $this->render('Census/list.'.$request->get('_format').'.twig', $output);

        if ($request->get('_format') != 'html') {
            $response->headers->set(
                'Content-Disposition',
                sprintf('attachment; filename="censuses.%s";"', $request->get('_format'))
            );
            $response->headers->set(
                'Content-Type',
                sprintf('Content-Type="text/%s";', $request->get('_format'))
            );
        }

        return $response;
    }

    public function editAction(Request $request, $censusId)
    {
        $census = $this->getCensusOr404($censusId);
        $censusForm = $this->get('form.factory')->create(CensusType::class, $census);
        $censusForm->handleRequest($request);
        if ($censusForm->isSubmitted() && $censusForm->isValid()) {
            $this->model->updateOne($census, array_keys($censusForm->getData()->extract()));
            $this->get('session')->getFlashBag()->add('success', 'Census updated');

            return $this->redirectToRoute('germ_census_edit', ['censusId' => $census->getId()]);
        }

        return $this->render(
            'Census/edit.html.twig',
            array(
                'mode' => 'edit',
                'form' => $censusForm->createView(),
                'census' => $census,
            )
        );
    }

    public function createAction(Request $request)
    {
        $censusForm = $this->get('form.factory')->create(CensusType::class);
        $censusForm->handleRequest($request);

        if ($censusForm->isSubmitted() && $censusForm->isValid()) {
            $census = $this->saver->create($censusForm->getData());
            $translator = $this->get('translator');
            $this->get('session')->getFlashBag()->add('success', $translator->trans('Census created'));

            return $this->redirectToRoute('germ_census_edit', ['censusId' => $census->getId()]);
        }

        return $this->render(
            'Census/edit.html.twig',
            array(
                'mode' => 'create',
                'form' => $censusForm->createView(),
            )
        );
    }

    public function removeAction($censusId)
    {
        $census = $this->getCensusOr404($censusId);
        $this->model->deleteOne($census);
        $this->get('session')->getFlashBag()->add('success', 'Census deleted');

        return $this->redirectToRoute('germ_census_list');
    }

    private function getCensusOr404($censusId)
    {
        $census = $this->finder->findOneById($censusId);
        if (!$census) {
            throw $this->createNotFoundException('The census does not exist');
        }
        return $census;
    }
}
