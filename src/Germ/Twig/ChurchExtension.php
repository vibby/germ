<?php

namespace Germ\Twig;

use Germ\Filter\Church\CriteriaTerms;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ChurchExtension extends AbstractExtension
{
    private $searchTerms;

    public function __construct(CriteriaTerms $searchTerms)
    {
        $this->searchTerms = $searchTerms;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('highlightChurch', [$this->searchTerms, 'highlight'], ['is_safe' => ['html']]),
        ];
    }
}
