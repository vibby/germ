<?php

namespace Germ\Legacy\Controller;

use Germ\Legacy\Filter\Census\Searcher;
use Germ\Legacy\Model\Germ\Census\CensusFinder;
use Germ\Legacy\Model\Germ\Census\CensusSaver;
use Germ\Legacy\Model\Germ\ChurchSchema\CensusModel;
use Germ\Legacy\Type\CensusType;
use Knp\Component\Pager\PaginatorInterface;
use PommProject\Foundation\Pomm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CensusController extends AbstractController
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

    public function listAction(Request $request, $page, PaginatorInterface $paginator)
    {
        if ('html' != $request->get('_format')) {
            $output['censuses'] = $this->finder->findAll();
        } else {
            if ($redirect = $this->searcher->handleRequest($request)) {
                return $redirect;
            }
            $output['searchForm'] = $this->searcher->getForm()->createView();
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

        if ('html' != $request->get('_format')) {
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
        $censusForm = $this->createForm(CensusType::class, $census);
        $censusForm->handleRequest($request);
        if ($censusForm->isSubmitted() && $censusForm->isValid()) {
            $this->model->updateOne($census, array_keys($censusForm->getData()->extract()));
            $request->getSession()->getFlashBag()->add('success', 'Census updated');

            return $this->redirectToRoute('germ_census_edit', ['censusId' => $census->getId()]);
        }

        return $this->render(
            'Census/edit.html.twig',
            [
                'mode' => 'edit',
                'form' => $censusForm->createView(),
                'census' => $census,
            ]
        );
    }

    public function createAction(Request $request)
    {
        $censusForm = $this->createForm(CensusType::class);
        $censusForm->handleRequest($request);

        if ($censusForm->isSubmitted() && $censusForm->isValid()) {
            $census = $this->saver->create($censusForm->getData());
            $translator = $this->get('translator');
            $request->getSession()->getFlashBag()->add('success', $translator->trans('Census created'));

            return $this->redirectToRoute('germ_census_edit', ['censusId' => $census->getId()]);
        }

        return $this->render(
            'Census/edit.html.twig',
            [
                'mode' => 'create',
                'form' => $censusForm->createView(),
            ]
        );
    }

    public function removeAction($censusId, Request $request)
    {
        $census = $this->getCensusOr404($censusId);
        $this->model->deleteOne($census);
        $request->getSession()->getFlashBag()->add('success', 'Census deleted');

        return $this->redirectToRoute('germ_census_list');
    }

    private function getCensusOr404($censusId)
    {
        $census = $this->finder->findOneById($censusId);
        if (! $census) {
            throw $this->createNotFoundException('The census does not exist');
        }

        return $census;
    }
}
