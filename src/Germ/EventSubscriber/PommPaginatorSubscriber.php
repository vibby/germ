<?php

namespace Germ\EventSubscriber;

use Germ\Model\Germ\AbstractFinder;
use Knp\Component\Pager\Event\ItemsEvent;
use PommProject\Foundation\Where;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PommPaginatorSubscriber implements EventSubscriberInterface
{
    public function items(ItemsEvent $event)
    {
        if (isset($event->target[0])
            && isset($event->target[1])
            && is_a($event->target[0], AbstractFinder::class)
        ) {
            $finder = $event->target[0];
            if (\is_string($event->target[1]) && method_exists($finder, $event->target[1])) {
                $methodName = $event->target[1];
                $parameters = array_merge(
                    $event->target[2],
                    [
                        $event->getLimit(),
                        $event->getOffset() / $event->getLimit() + 1,
                    ]
                );
                list($event->count, $query) = \call_user_func_array([$finder, $methodName], $parameters);
            } elseif ($event->target[1] instanceof Where) {
                $where = $event->target[1];
                $event->count = $finder->countWhere($where);
                $query = $finder->paginateFindWhere(
                    $where,
                    $event->getLimit(),
                    $event->getOffset() / $event->getLimit() + 1
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
        return [
            'knp_pager.items' => ['items', 1/*increased priority to override any internal*/],
        ];
    }
}
