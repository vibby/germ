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

        $menu->addChild('Accueil', array('route' => 'germ_homepage'));
        $menu->addChild('Membres', array('route' => 'germ_member_list'));
        $menu->addChild('Déconnexion', array('route' => 'germ_logout'));

        // // create another menu item
        // $menu->addChild('Members', array(
        //     'route' => 'members_list',
        //     'routeParameters' => array('id' => $blog->getId())
        // ));
        // // you can also add sub level's to your menu's as follows
        // $menu['Members']->addChild('List members', array(
        //     'route' => 'members_list',
        // ));
        // $menu['Members']->addChild('Last member', array(
        //     'route' => 'members_list',
        //     'routeParameters' => array('id' => $member->getId())
        // ));

        return $menu;
    }
}