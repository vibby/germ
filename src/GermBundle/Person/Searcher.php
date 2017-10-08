<?php

namespace GermBundle\Person;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class Searcher
{
    private $formFactory;
    private $form;
    private $items = [];

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
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
            $form = $this->formFactory
                ->create(FormType::class, null, [
                    'show_legend' => false,
                    'csrf_protection' => false,
                    'method' => 'GET',
                ]);
            foreach ($this->items as $item) {
                $item->setData($request->query->get($item::NAME));
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
                $item->setData($searchForm->getData()[$item::NAME]);
            }
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
