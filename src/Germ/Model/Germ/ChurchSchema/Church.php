<?php

namespace Germ\Model\Germ\ChurchSchema;

use PommProject\ModelManager\Model\FlexibleEntity;

/**
 * Church
 *
 * Flexible entity for relation
 * church.church
 *
 * @see FlexibleEntity
 */
class Church extends FlexibleEntity
{
    public function __toString(): string
    {
        return (string) $this->get('name');
    }
}
