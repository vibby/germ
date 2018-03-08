<?php

namespace Germ\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Germ\Type\CensusType;
use Germ\Model\Germ\ChurchSchema\Census;
use PommProject\Foundation\Where;

class CensusController extends Controller
{

    public function listAction(Request $request, $page, $search = null)
    {
        $finder = $this->get('Germ\Model\Germ\Census\CensusFinder');
        if ($request->get('_format') != 'html') {
            $output['censuses'] = $finder->findAll();
        } else {
            $searcher = $this->get('Germ\Filter\Census\Searcher');
            if ($redirect = $searcher->handleRequest($request)) {
                return $redirect;
            }
            $output['searchForm'] = $searcher->getForm()->createView();
            $paginator = $this->get('knp_paginator');
            $output['paginatedCensuses'] = $paginator->paginate(
                [
                    $finder,
                    'paginateFilterQuery',
                    [$searcher],
                ],
                $page,
                min((int) $request->get('perPage', 30), 250)
            );
        }

        $response = $this->render('Germ:Census:list.'.$request->get('_format').'.twig', $output);

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
        $censusForm = $this->get('form.factory')->create(CensusType::class,$census);
        $censusForm->handleRequest($request);
        if ($censusForm->isSubmitted() && $censusForm->isValid()) {
            $censusModel = $this->get('pomm')['germ']->getModel('Germ\Model\Germ\ChurchSchema\CensusModel');
            $censusModel->updateOne($census, array_keys($censusForm->getData()->extract()));
            $this->get('session')->getFlashBag()->add('success', 'Census updated');

            return $this->redirectToRoute('germ_census_edit', ['censusId' => $census->getId()]);
        }

        return $this->render(
            'Germ:Census:edit.html.twig',
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
            $censusModel = $this->get('pomm')['germ']->getModel('Germ\Model\Germ\ChurchSchema\CensusModel');
            $census = $this->get('Germ\Model\Germ\Census\CensusSaver')->create($censusForm->getData());
            $translator = $this->get('translator');
            $this->get('session')->getFlashBag()->add('success', $translator->trans('Census created'));

            return $this->redirectToRoute('germ_census_edit', ['censusId' => $census->getId()]);
        }

        return $this->render(
            'Germ:Census:edit.html.twig',
            array(
                'mode' => 'create',
                'form' => $censusForm->createView(),
            )
        );
    }

    public function removeAction($censusId)
    {
        $census = $this->getCensusOr404($censusId);
        $censusModel = $this->get('pomm')['germ']->getModel('Germ\Model\Germ\ChurchSchema\CensusModel');
        $censusModel->deleteOne($census);
        $this->get('session')->getFlashBag()->add('success', 'Census deleted');

        return $this->redirectToRoute('germ_census_list');
    }

    private function getCensusOr404($censusId)
    {
        $censusModel = $this->get('pomm')['germ']->getModel('Germ\Model\Germ\ChurchSchema\CensusModel');
        $census = $censusModel->findWhere(new Where('id_church_census = $1', [':id' => $censusId]))->current();
        if (!$census) {
            throw $this->createNotFoundException('The census does not exist');
        }
        return $census;
    }
}
