<?php

namespace Germ\Model\Germ\PersonSchema;

use PommProject\ModelManager\Model\FlexibleEntity;

/**
 * Person.
 *
 * Flexible entity for relation
 * person.person
 *
 * @see FlexibleEntity
 */
class Person extends FlexibleEntity
{
    const FULL_NAME_SEPARATOR = ' ';

    public function __toString()
    {
        return $this->getFirstname().self::FULL_NAME_SEPARATOR.$this->getLastname();
    }

    public function getId()
    {
        return $this->get('id_person_person');
    }
}
