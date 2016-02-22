<?php

// file: ../symfony-standard/src/Acme/DemoBundle/Subscriber/PaginateDirectorySubscriber.php
// requires // Symfony\Component\Finder\Finder

namespace GermBundle\Subscriber;

use Symfony\Component\Finder\Finder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Knp\Component\Pager\Event\ItemsEvent;

class PommSubscriber implements EventSubscriberInterface
{
    public function items(ItemsEvent $event)
    {
        if (isset($event->target[0])
        	&& is_a($event->target[0], 'PommProject\ModelManager\Model\Model')
        	&& is_a($event->target[1], 'PommProject\Foundation\Where')
        ) {
            $event->count = count($event->target);
            $event->items = $event->target[0]->paginateFindWhere(
            	$event->target[1],
            	$event->getLimit(),
            	$event->getOffset()/$event->getLimit()
            )->getIterator();
            // dump($event->items);
            // die;
            $event->stopPropagation();
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            'knp_pager.items' => array('items', 1/*increased priority to override any internal*/)
        );
    }
}