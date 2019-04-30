<?php

namespace Germ\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CensusExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('group_censuses', [$this, 'groupCensuses']),
        ];
    }

    public function groupCensuses(\Traversable $censuses)
    {
        $grouped = [];
        foreach ($censuses as $census) {
            if (! isset($grouped[$census['church_id']])) {
                $grouped[$census['church_id']] = [
                    'name' => $census['church_name'],
                    'censuses' => [$census],
                ];
            } else {
                $grouped[$census['church_id']]['censuses'][] = $census;
            }
            $grouped[$census['church_id']];
        }

        return $grouped;
    }

    public function getName()
    {
        return 'census_extension';
    }
}
