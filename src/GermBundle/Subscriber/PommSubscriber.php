<?php

// file: ../symfony-standard/src/Acme/DemoBundle/Subscriber/PaginateDirectorySubscriber.php
// requires // Symfony\Component\Finder\Finder

namespace GermBundle\Subscriber;

use Symfony\Component\Finder\Finder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Knp\Component\Pager\Event\ItemsEvent;
use PommProject\Foundation\Where;
use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\CollectionIterator;

class PommSubscriber implements EventSubscriberInterface
{
    public function items(ItemsEvent $event)
    {
        if (isset($event->target[0])
            && isset($event->target[1])
            && is_a($event->target[0], Model::class)
        ) {
            $model = $event->target[0];
            if (is_string($event->target[1]) && method_exists($model, $event->target[1])) {
                $methodName = $event->target[1];
                $parameters = array_merge(
                    $event->target[2],
                    [
                        $event->getLimit(),
                        $event->getOffset()/$event->getLimit() + 1,
                    ]
                );
                list($event->count, $query) = call_user_func_array([$model, $methodName], $parameters);
            } elseif ($event->target[1] instanceOf Where) {
                $where = $event->target[1];
                $event->count = $model->countWhere($where);
                $query = $model->paginateFindWhere(
                    $where,
                    $event->getLimit(),
                    $event->getOffset()/$event->getLimit() + 1
                );
            } else {
                throw new \Exception('Cannot understand how to deal pagination');
            }
            $event->items = $query->getIterator();
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