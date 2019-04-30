<?php

namespace Germ\Model\Germ\ChurchSchema;

use PommProject\ModelManager\Model\FlexibleEntity;

/**
 * Census
 *
 * Flexible entity for relation
 * church.census
 *
 * @see FlexibleEntity
 */
class Census extends FlexibleEntity
{
    public $keyForId = 'id_church_census';

    public function __toString()
    {
        return (string) $this->get('date')->format('Y-m-d');
    }

    public function getId()
    {
        return $this->get($this->keyForId);
    }
}
