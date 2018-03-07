<?php

namespace GermBundle\Twig;

class CensusExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('group_censuses', array($this, 'groupCensuses')),
        );
    }

    public function groupCensuses(\Traversable $censuses)
    {
        $grouped = [];
        foreach ($censuses as $census) {
            if (!isset($grouped[$census['church_id']])) {
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
