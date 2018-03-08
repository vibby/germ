<?php

namespace Germ\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Germ\Type\ChurchType;
use Germ\Model\Germ\ChurchSchema\Church;
use PommProject\Foundation\Where;

class ChurchController extends Controller
{

    public function listAction(Request $request, $page, $search = null)
    {
        $finder = $this->get('Germ\Model\Germ\Church\ChurchFinder');
        if ($request->get('_format') != 'html') {
            $output['churches'] = $finder->findAll();
        } else {
            $searcher = $this->get('Germ\Filter\Church\Searcher');
            if ($redirect = $searcher->handleRequest($request)) {
                return $redirect;
            }
            $output['searchForm'] = $searcher->getForm()->createView();
            $paginator = $this->get('knp_paginator');
            $output['paginatedChurches'] = $paginator->paginate(
                [
                    $finder,
                    'paginateFilterQuery',
                    [$searcher],
                ],
                $page,
                min((int) $request->get('perPage', 30), 250)
            );
        }

        $response = $this->render('Germ:Church:list.'.$request->get('_format').'.twig', $output);

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
            $churchModel = $this->get('pomm')['germ']->getModel('Germ\Model\Germ\ChurchSchema\ChurchModel');
            $churchModel->updateOne($church, array_keys($churchForm->getData()->extract()));
            $this->get('session')->getFlashBag()->add('success', 'Church updated');

            return $this->redirectToRoute('germ_church_edit', ['churchSlug' => $church->getSlug()]);
        }

        return $this->render(
            'Germ:Church:edit.html.twig',
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
            $churchModel = $this->get('pomm')['germ']->getModel('Germ\Model\Germ\ChurchSchema\ChurchModel');
            $church = $this->get('Germ\Model\Germ\Church\ChurchSaver')->create($churchForm->getData());
            $translator = $this->get('translator');
            $this->get('session')->getFlashBag()->add('success', $translator->trans('Church created'));

            return $this->redirectToRoute('germ_church_edit', ['churchSlug' => $church->getSlug()]);
        }

        return $this->render(
            'Germ:Church:edit.html.twig',
            array(
                'mode' => 'create',
                'form' => $churchForm->createView(),
            )
        );
    }

    public function removeAction($churcheslug)
    {
        $church = $this->getChurchOr404($churcheslug);
        $churchModel = $this->get('pomm')['germ']->getModel('Germ\Model\Germ\ChurchSchema\ChurchModel');
        $churchModel->deleteOne($church);
        $this->get('session')->getFlashBag()->add('success', 'Church deleted');

        return $this->redirectToRoute('germ_church_list');
    }

    private function getChurchOr404($churcheslug)
    {
        $churchModel = $this->get('pomm')['germ']->getModel('Germ\Model\Germ\ChurchSchema\ChurchModel');
        $church = $churchModel->findWhere(new Where('slug = $1', [':slug' => $churcheslug]))->current();
        if (!$church) {
            throw $this->createNotFoundException('The church does not exist');
        }
        return $church;
    }
}
