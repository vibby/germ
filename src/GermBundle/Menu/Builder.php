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

        $menu->addChild('Home', array('route' => 'germ_homepage'));
        $menu->addChild('Persons', array('route' => 'germ_person_list'));
        $menu->addChild('Logout', array('route' => 'fos_user_security_logout'));

        // // create another menu item
        // $menu->addChild('Persons', array(
        //     'route' => 'persons_list',
        //     'routeParameters' => array('id' => $blog->getId())
        // ));
        // // you can also add sub level's to your menu's as follows
        // $menu['Persons']->addChild('List persons', array(
        //     'route' => 'persons_list',
        // ));
        // $menu['Persons']->addChild('Last person', array(
        //     'route' => 'persons_list',
        //     'routeParameters' => array('id' => $person->getId())
        // ));

        return $menu;
    }
}