<?php

namespace Germ\Controller;

use Germ\Filter\Church\Searcher;
use Germ\Model\Germ\Church\ChurchFinder;
use Germ\Model\Germ\Church\ChurchSaver;
use Germ\Model\Germ\ChurchSchema\ChurchModel;
use PommProject\Foundation\Pomm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Germ\Type\ChurchType;

class ChurchController extends Controller
{
    private $finder;
    private $searcher;
    private $model;
    private $saver;

    public function __construct(ChurchFinder $churchFinder, Searcher $searcher, Pomm $pomm, ChurchSaver $saver)
    {
        $this->finder = $churchFinder;
        $this->searcher = $searcher;
        $this->saver = $saver;
        $this->model = $pomm['germ']->getModel(ChurchModel::class);
    }

    public function listAction(Request $request, $page, $search = null)
    {
        if ($request->get('_format') != 'html') {
            $output['churches'] = $this->finder->findAll();
        } else {
            if ($redirect = $this->searcher->handleRequest($request)) {
                return $redirect;
            }
            $output['searchForm'] = $this->searcher->getForm()->createView();
            $paginator = $this->get('knp_paginator');
            $output['paginatedChurches'] = $paginator->paginate(
                [
                    $this->finder,
                    'paginateFilterQuery',
                    [$this->searcher],
                ],
                $page,
                min((int) $request->get('perPage', 30), 250)
            );
        }

        $response = $this->render('Church/list.'.$request->get('_format').'.twig', $output);

        if ($request->get('_format') != 'html') {
            $response->headers->set(
                'Content-Disposition',
                sprintf('attachment; filename="churches.%s";"', $request->get('_format'))
            );
            $response->headers->set(
                'Content-Type',
                sprintf('Content-Type="text/%s";', $request->get('_format'))
            );
        }

        return $response;
    }

    public function editAction(Request $request, $churchSlug)
    {
        $church = $this->getChurchOr404($churchSlug);
        $churchForm = $this->get('form.factory')->create(ChurchType::class,$church);
        $churchForm->handleRequest($request);
        if ($churchForm->isSubmitted() && $churchForm->isValid()) {
            $this->model->updateOne($church, array_keys($churchForm->getData()->extract()));
            $this->get('session')->getFlashBag()->add('success', 'Church updated');

            return $this->redirectToRoute('germ_church_edit', ['churchSlug' => $church->getSlug()]);
        }

        return $this->render(
            'Church/edit.html.twig',
            array(
                'mode' => 'edit',
                'form' => $churchForm->createView(),
                'church' => $church,
            )
        );
    }

    public function createAction(Request $request)
    {
        $churchForm = $this->get('form.factory')->create(ChurchType::class);
        $churchForm->handleRequest($request);

        if ($churchForm->isSubmitted() && $churchForm->isValid()) {
            $church = $this->saver->create($churchForm->getData());
            $translator = $this->get('translator');
            $this->get('session')->getFlashBag()->add('success', $translator->trans('Church created'));

            return $this->redirectToRoute('germ_church_edit', ['churchSlug' => $church->getSlug()]);
        }

        return $this->render(
            'Church/edit.html.twig',
            array(
                'mode' => 'create',
                'form' => $churchForm->createView(),
            )
        );
    }

    public function removeAction($churcheslug)
    {
        $church = $this->getChurchOr404($churcheslug);
        $this->model->deleteOne($church);
        $this->get('session')->getFlashBag()->add('success', 'Church deleted');

        return $this->redirectToRoute('germ_church_list');
    }

    private function getChurchOr404($churchSlug)
    {
        $church = $this->finder->findOneBySlug($churchSlug);
        if (!$church) {
            throw $this->createNotFoundException('The church does not exist');
        }

        return $church;
    }
}
