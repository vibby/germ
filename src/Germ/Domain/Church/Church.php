<?php

namespace Germ\Domain\Church;


class Church
{
    private $name;

    public function __construct(string $name) {
        $this->name = $name;
    }
}
