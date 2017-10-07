<?php

namespace GermBundle\Person;

use Symfony\Component\HttpFoundation\Request;
use PommProject\Foundation\Where;
use PommProject\Foundation\Projection;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Searcher
{

    private $terms = [];
    private $router;
    private $formFactory;
    private $form;

    public function __construct(RouterInterface $router, FormFactoryInterface $formFactory)
    {
        $this->router = $router;
        $this->formFactory = $formFactory;
    }

    public function getForm($request = null)
    {
        if (!$this->form) {
            $this->form = $this->formFactory
                ->create(FormType::class, null, ['label' => false])
                ->add('search', SearchType::class, ['label' => 'Filter', 'data' => $request->attributes->get('search')])
                ->add('ok', SubmitType::class, ['label' => 'Search', 'attr' => ['class' => 'sr-only']]);
        }

        return $this->form;
    }

    public function handleRequest(Request $request)
    {
        $searchForm = $this->getForm($request);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted()) {
            return new RedirectResponse($this->router->generate(
                'germ_person_search',
                [
                    'search' => $searchForm->get('search')->getData(),
                ]
            ));
        }
        $this->setTerms($request);

        return;
    }

    private function setTerms($request)
    {
        $this->terms = explode(' ', (string) $request->attributes->get('search'));
        if ($this->form) {
            $this->form->get('search')->setData(implode(' ', $this->terms));
        }
    }

    public function getPaginationData($model)
    {
        return [
            $model,
            'searchQuery',
            [$this->terms],
        ];
    }

    public function highlight($string)
    {
        preg_match('~\w+~', implode(' ', $this->terms), $matches);
        if (!$matches) {
            return $string;
        }
        $re = '# '.implode('| ',$this->terms).'|^'.implode('|^',$this->terms).'#i';

        return preg_replace($re, '<strong>$0</strong>', $string);
    }
}
