<?php

namespace GermBundle\Person;

use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Translation\TranslatorInterface;

class RoleManager
{
    private $config;
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function string($role)
    {
        return $this->config['strings'][$role];
    }

    public function getStrings(array $roles = null)
    {
        if (!$roles) {
            return $this->config['strings'];
        }

        return array_filter(
            $this->config['strings'],
            function($key) use ($roles) {
                return in_array($key, $roles);
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    public function getTranslations(array $roles = null)
    {
        $translator = $this->translator;
        return array_map(
            function ($roleString) use ($translator) {
                return $translator->trans($roleString);
            },
            $this->getStrings($roles)
        );
    }

    public function getRoles(array $strings = null)
    {
        if (!$strings) {
            return array_flip($this->config['strings']);
        }

        return array_flip(array_filter(
            $this->config['strings'],
            function($value) use ($strings) {
                return in_array($value, $strings);
            }
        ));
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
