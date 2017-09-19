<?php

// file: ../symfony-standard/src/Acme/DemoBundle/Subscriber/PaginateDirectorySubscriber.php
// requires // Symfony\Component\Finder\Finder

namespace GermBundle\Subscriber;

use Symfony\Component\Finder\Finder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Knp\Component\Pager\Event\ItemsEvent;
use PommProject\Foundation\Where;
use PommProject\ModelManager\Model\Model;

class PommSubscriber implements EventSubscriberInterface
{
    public function items(ItemsEvent $event)
    {
        if (isset($event->target[0])
            && isset($event->target[1])
            && is_a($event->target[0], Model::class)
            && is_a($event->target[1], Where::class)
        ) {
            $model = $event->target[0];
            $where = $event->target[1];
            $event->count = $model->countWhere($where);
            $event->items = $event->target[0]->paginateFindWhere(
                $event->target[1],
                $event->getLimit(),
                $event->getOffset()/$event->getLimit() + 1
            )->getIterator();
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