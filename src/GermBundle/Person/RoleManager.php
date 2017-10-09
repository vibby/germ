<?php

namespace GermBundle\Person;

use Symfony\Component\Security\Core\Role\Role;

class RoleManager
{
    private $config;

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function string($role)
    {
        return $this->config['strings'][$role];
    }

    public function getColored()
    {
        return array_map(function ($roleName) { return new Role($roleName); }, $this->config['colored']);
    }

    public function getFilterChoices()
    {
        return $this->formatChoice($this->config['filter']);
    }

    public function getAssignable()
    {
        return $this->formatChoice($this->config['assignable']);
    }

    private function formatChoice($roles)
    {
        return array_flip(array_intersect_key($this->config['strings'], array_flip($roles)));
    }
}
