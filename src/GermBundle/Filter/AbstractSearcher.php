<?php

namespace GermBundle\Filter;

use GermBundle\Filter\Criteria\AbstractCriteria;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class AbstractSearcher
{
    const URL_SEPARATOR = '/';
    const URL_NAMER = ':';
    const ROUTE_PARAMETER = 'filters';

    private $formFactory;
    private $form;
    private $criterias = [];

    public function __construct(FormFactoryInterface $formFactory, RouterInterface $router)
    {
        $this->formFactory = $formFactory;
        $this->router = $router;
    }

    public function addItem(AbstractCriteria $criteria)
    {
        $this->criterias[] = $criteria;
    }

    public function getcriterias()
    {
        return $this->criterias;
    }

    public function getForm(Request $request = null)
    {
        if (!$this->form) {
            if ($request->get(self::ROUTE_PARAMETER)) {
                foreach (explode(self::URL_SEPARATOR, $request->get(self::ROUTE_PARAMETER)) as $filter) {
                    $namingPos = strpos($filter, self::URL_NAMER);
                    $namePrefix = substr($filter, 0, $namingPos);
                    $value = substr($filter, $namingPos + strlen(self::URL_NAMER));
                    foreach ($this->criterias as $criteria) {
                        if ($criteria::getUrlPrefix() == $namePrefix) {
                            $criteria->setData($value, true);
                        }
                    }
                }
            }
            $form = $this->formFactory
                ->create(FormType::class, null, [
                    'show_legend' => false,
                ]);
            foreach ($this->criterias as $criteria) {
                $criteria->alterForm($form);
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
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $params = [];
            foreach ($this->criterias as $criteria) {
                if (in_array($criteria::getUrlPrefix(), array_keys($params))) {
                    throw new \Exception(sprintf("There cannot be two filter with same prefix : «%s»", $criteria::getUrlPrefix()), 1);
                }
                if (strpos($criteria::getUrlPrefix(), self::URL_NAMER) !== false) {
                    throw new \Exception(sprintf("Prefix cannot include reserved char for naming : «%s»", self::URL_NAMER), 1);
                }
                if (strpos($criteria::getUrlPrefix(), self::URL_SEPARATOR) !== false) {
                    throw new \Exception(sprintf("Prefix cannot include reserved char for separator : «%s»", self::URL_NAMER), 1);
                }
                $valueUrlized = $criteria->urlize($searchForm->getData()[$criteria::getFormName()]);
                if (strpos($valueUrlized, self::URL_SEPARATOR) !== false) {
                    throw new \Exception(sprintf("Value cannot include reserved char for separator : «%s». «%s» given", self::URL_SEPARATOR, $valueUrlized), 1);
                }
                if (strpos($valueUrlized, self::URL_NAMER) !== false) {
                    throw new \Exception(sprintf("Value cannot include reserved char for separator : «%s». «%s» given", self::URL_NAMER, $valueUrlized), 1);
                }
                if ($valueUrlized) {
                    $params[$criteria::getUrlPrefix()] = $criteria::getUrlPrefix().self::URL_NAMER.$valueUrlized;
                }
            }

            return new RedirectResponse($this->router->generate(static::getRouteName(),[self::ROUTE_PARAMETER => implode(self::URL_SEPARATOR, $params)]));
        }
    }

    abstract public static function getRouteName();
}
