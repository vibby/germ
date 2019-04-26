<?php

namespace Germ\Legacy\Controller;

use Germ\Legacy\Filter\Church\Searcher;
use Germ\Legacy\Model\Germ\Church\ChurchFinder;
use Germ\Legacy\Model\Germ\Church\ChurchSaver;
use Germ\Legacy\Model\Germ\ChurchSchema\ChurchModel;
use Germ\Legacy\Type\ChurchType;
use Knp\Component\Pager\PaginatorInterface;
use PommProject\Foundation\Pomm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChurchController extends AbstractController
{
    private $finder;
    private $model;
    private $translator;

    public function __construct(ChurchFinder $churchFinder, Pomm $pomm, TranslatorInterface $translator)
    {
        $this->finder = $churchFinder;
        $this->model = $pomm['germ']->getModel(ChurchModel::class);
        $this->translator = $translator;
    }

    public function listAction(Request $request, $page, Searcher $searcher, PaginatorInterface $paginator)
    {
        if ('html' != $request->get('_format')) {
            $output['churches'] = $this->finder->findAll();
        } else {
            if ($redirect = $searcher->handleRequest($request)) {
                return $redirect;
            }
            $output['searchForm'] = $searcher->getForm()->createView();
            $output['paginatedChurches'] = $paginator->paginate(
                [
                    $this->finder,
                    'paginateFilterQuery',
                    [$searcher],
                ],
                $page,
                min((int) $request->get('perPage', 30), 250)
            );
        }

        $response = $this->render('Church/list.'.$request->get('_format').'.twig', $output);

        if (!in_array($request->get('_format'), ['html', 'json'])) {
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
        $churchForm = $this->createForm(ChurchType::class, $church);
        $churchForm->handleRequest($request);
        if ($churchForm->isSubmitted() && $churchForm->isValid()) {
            $this->model->updateOne($church, array_keys($churchForm->getData()->extract()));
            $request->getSession()->getFlashBag()->add('success', $this->translator->trans('Church updated'));

            return $this->redirectToRoute('germ_church_edit', ['churchSlug' => $church->getSlug()]);
        }

        return $this->render(
            sprintf(
                'Church/edit.%s.twig',
                $request->get('_format')
            ),
            [
                'mode' => 'edit',
                'form' => $churchForm->createView(),
                'church' => $church,
            ]
        );
    }

    public function createAction(Request $request, ChurchSaver $saver)
    {
        $churchForm = $this->createForm(ChurchType::class);
        $churchForm->handleRequest($request);

        if ($churchForm->isSubmitted() && $churchForm->isValid()) {
            $church = $saver->create($churchForm->getData());
            $request->getSession()->getFlashBag()->add('success', $this->translator->trans('Church created'));

            return $this->redirectToRoute('germ_church_edit', ['churchSlug' => $church->getSlug()]);
        }

        return $this->render(
            'Church/edit.html.twig',
            [
                'mode' => 'create',
                'form' => $churchForm->createView(),
            ]
        );
    }

    public function removeAction($churcheslug, Request $request)
    {
        $church = $this->getChurchOr404($churcheslug);
        $this->model->deleteOne($church);
        $request->getSession()->getFlashBag()->add('success', 'Church deleted');

        return $this->redirectToRoute('germ_church_list');
    }

    private function getChurchOr404($churchSlug)
    {
        $church = $this->finder->findOneBySlug($churchSlug);
        if (! $church) {
            throw $this->createNotFoundException('The church does not exist');
        }

        return $church;
    }
}
