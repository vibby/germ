<?php

namespace GermBundle\Person;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Searcher
{
    private $formFactory;
    private $form;
    private $items = [];

    public function __construct(FormFactoryInterface $formFactory, RouterInterface $router)
    {
        $this->formFactory = $formFactory;
        $this->router = $router;
    }

    public function addItem(AbstractSearchItem $item)
    {
        $this->items[] = $item;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function getForm($request = null)
    {
        if (!$this->form) {
            foreach ($this->items as $item) {
                if ($value = $request->get($item::NAME)) {
                    $item->setData($value, true);
                }
            }
            $form = $this->formFactory
                ->create(FormType::class, null, [
                    'show_legend' => false,
                    'csrf_protection' => false,
                    'method' => 'GET',
                ]);
            foreach ($this->items as $item) {
                $item->alterForm($form);
            }
            $form->add('ok', SubmitType::class, [
                'label' => 'Filter'
            ]);

            $this->form = $form;
        }

        return $this->form;
    }

    public function handleRequest(Request $request)
    {
        $searchForm = $this->getForm($request);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted()) {
            foreach ($this->items as $item) {
                $params[$item::NAME] = $item->serialize($searchForm->getData()[$item::NAME]);
            }

            return new RedirectResponse($this->router->generate('germ_person_filter',$params));
        }

        return;
    }

    public function getPaginationData($model)
    {
        return [
            $model,
            'searchQuery',
            [$this],
        ];
    }
}
