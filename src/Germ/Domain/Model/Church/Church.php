<?php

namespace Germ\Domain\Model\Church;

class Church
{
    private $name;

    public function __construct($name)
    {
        $this->setName($name);
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }
}
