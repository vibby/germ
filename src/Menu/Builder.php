<?php

namespace Germ\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function __construct(
        FactoryInterface $factory,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->factory = $factory;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function mainMenu()
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('Home', ['route' => 'germ_homepage']);
        if ($this->authorizationChecker->isGranted('ROLE_CHURCH_LIST')) {
            $menu->addChild('Churches', ['route' => 'germ_church_list']);
        }
        if ($this->authorizationChecker->isGranted('ROLE_LOCAL_CENSUS_LIST')
            || $this->authorizationChecker->isGranted('ROLE_CENSUS_LIST')
        ) {
            $menu->addChild('Census', ['route' => 'germ_census_list']);
        }
        if ($this->authorizationChecker->isGranted('ROLE_PERSON_LIST')) {
            $menu->addChild('Persons', ['route' => 'germ_person_list']);
        }
        // if ($this->container->get('security.authorization_checker')->isGranted('ROLE_EVENT_LIST')) {
        //     $menu->addChild('Events', [
        //         'route' => 'germ_event_list',
        //     ]);
        // }
        $menu->addChild('My account', [
            'route' => 'germ_person_edit_myself',
        ]);
        $menu->addChild('Logout', [
            'route' => 'fos_user_security_logout',
        ]);

        return $menu;
    }
}
