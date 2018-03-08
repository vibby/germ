<?php

namespace Germ\Twig;

use Germ\Filter\Church\CriteriaTerms;

class ChurchExtension extends \Twig_Extension
{
    private $searchTerms;

    public function __construct(CriteriaTerms $searchTerms)
    {
        $this->searchTerms = $searchTerms;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('highlightChurch', array($this->searchTerms, 'highlight'), ['is_safe' => ['html']]),
        );
    }
}
