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
        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_PERSON_LIST')) {
            $menu->addChild('Persons', ['route' => 'germ_person_list']);
        }
        $menu->addChild('Events', [
            'route' => 'germ_event_list',
        ]);
        $menu->addChild('Logout', [
            'route' => 'fos_user_security_logout',
        ]);

        // // create another menu item
        // $menu->addChild('Persons', [
        //     'route' => 'persons_list',
        //     'routeParameters' => ['id' => $blog->getId())
        // ));
        // // you can also add sub level's to your menu's as follows
        // $menu['Persons']->addChild('List persons', [
        //     'route' => 'persons_list',
        // ));
        // $menu['Persons']->addChild('Last person', [
        //     'route' => 'persons_list',
        //     'routeParameters' => ['id' => $person->getId())
        // ));

        return $menu;
    }
}