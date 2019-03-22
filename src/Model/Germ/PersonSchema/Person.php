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
    public function __construct()
    {
        $this->roles = [];
    }

    public function __toString()
    {
        return $this->getFirstname().' '.$this->getLastname();
    }

    public function getId()
    {
        return $this->get('id_person_person');
    }
}
