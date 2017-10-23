<?php

namespace GermBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use GermBundle\Model\Germ\ChurchSchema\Church;
use PommProject\Foundation\Where;

class ChurchController extends Controller
{

    public function listAction(Request $request, $page, $search = null)
    {
        $finder = $this->get('GermBundle\Model\Germ\Church\ChurchFinder');
        if ($request->get('_format') != 'html') {
            $output['churches'] = $finder->findAll();
        } else {
            $searcher = $this->get('GermBundle\Filter\Church\Searcher');
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

        $response = $this->render('GermBundle:Church:list.'.$request->get('_format').'.twig', $output);

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
        $churchForm = $this->buildchurchForm($church);
        $churchForm->handleRequest($request);

        if ($churchForm->isSubmitted() && $churchForm->isValid()) {
            $churchModel = $this->get('pomm')['germ']->getModel('GermBundle\Model\Germ\ChurchSchema\ChurchModel');
            $churchModel->updateOne($church, array_keys($churchForm->getData()->extract()));
            $this->get('session')->getFlashBag()->add('success', 'church updated');
            return $this->redirectToRoute('germ_church_edit', ['churcheslug' => $church->getSlugCanonical()]);
        }
        $churchForm = $churchForm->createView();

        return $this->render(
            'GermBundle:Church:edit.html.twig',
            array(
                'mode' => 'edit',
                'form' => $churchForm,
            )
        );
    }

    public function createAction(Request $request)
    {
        $form = $this->createFormBuilder(churchType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $churchModel = $this->get('pomm')['germ']->getModel('GermBundle\Model\Germ\ChurchSchema\ChurchModel');
            $church = new church();
            foreach ($form->getData()->extract() as $key => $value) {
                $method = 'set'.ucfirst($key);
                $church->$method($value);
            }
            $churchModel->insertOne($church);
            $translator = $this->get('translator');
            $this->get('session')->getFlashBag()->add('success', $translator->trans('church created'));

            return $this->redirectToRoute('germ_church_edit', ['churcheslug' => $church->getSlugCanonical()]);
        }

        return $this->render(
            'GermBundle:Church:edit.html.twig',
            array(
                'mode' => 'create',
                'form' => $form->createView(),
            )
        );
    }

    public function removeAction($churcheslug)
    {
        $church = $this->getChurchOr404($churcheslug);
        $churchModel = $this->get('pomm')['germ']->getModel('GermBundle\Model\Germ\ChurchSchema\ChurchModel');
        $churchModel->deleteOne($church);
        $this->get('session')->getFlashBag()->add('success', 'Church deleted');

        return $this->redirectToRoute('germ_church_list');
    }

    private function getChurchOr404($churcheslug)
    {
        $churchModel = $this->get('pomm')['germ']->getModel('GermBundle\Model\Germ\ChurchSchema\ChurchModel');
        $church = $churchModel->findWhere(new Where('slug_canonical = $1', [':slug' => $churcheslug]))->current();
        if (!$church) {
            throw $this->createNotFoundException('The church does not exist');
        }
        return $church;
    }
}
