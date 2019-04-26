<?php

namespace Germ\Domain\Model\Person;

class Person
{
    const FULL_NAME_SEPARATOR = ' ';

    private $firstName;
    private $lastName;

    public function __construct($firstName, $lastName)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function getFullName()
    {
        return $this->firstName.self::FULL_NAME_SEPARATOR.$this->lastName;
    }
}
