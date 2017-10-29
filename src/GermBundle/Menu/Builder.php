<?php

namespace GermBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $menu->addChild('Home', ['route' => 'germ_homepage']);
        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_CHURCH_LIST')) {
            $menu->addChild('Churches', ['route' => 'germ_church_list']);
        }
        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_PERSON_LIST')) {
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
