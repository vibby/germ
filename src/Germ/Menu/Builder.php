<?php

namespace Germ\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    private $factory;
    private $authorizationChecker;
    private $request;

    public function __construct(
        FactoryInterface $factory,
        AuthorizationCheckerInterface $authorizationChecker,
        RequestStack $requestStack
    ) {
        $this->factory = $factory;
        $this->authorizationChecker = $authorizationChecker;
        $this->request = $requestStack->getCurrentRequest();
    }

    public function mainMenu()
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('Home', ['route' => 'germ_homepage']);
        $routeParameters = [
            '_locale' => $this->request->attributes->get('_locale'),
        ];
        if ($this->authorizationChecker->isGranted('ROLE_CHURCH_LIST')) {
            $menu->addChild('Churches', ['route' => 'germ_church_list', 'routeParameters' => $routeParameters]);
        }
        if ($this->authorizationChecker->isGranted('ROLE_LOCAL_CENSUS_LIST')
            || $this->authorizationChecker->isGranted('ROLE_CENSUS_LIST')
        ) {
            $menu->addChild('Census', ['route' => 'germ_census_list', 'routeParameters' => $routeParameters]);
        }
        if ($this->authorizationChecker->isGranted('ROLE_PERSON_LIST')) {
            $menu->addChild('Persons', ['route' => 'germ_person_list', 'routeParameters' => $routeParameters]);
        }
        if ($this->authorizationChecker->isGranted('ROLE_EVENT_LIST')) {
            $menu->addChild('Events', [
                'route' => 'germ_event_list',
            ]);
        }
        if ($this->authorizationChecker->isGranted('ROLE_SMS_VIEW_DASHBOARD')) {
            $menu->addChild('Sms', [
                'route' => 'germ_sms_list',
            ]);
        }
        $menu->addChild('My account', [
            'route' => 'germ_person_edit_myself',
        ]);
        $menu->addChild('Logout', [
            'route' => 'fos_user_security_logout',
        ]);

        return $menu;
    }
}
