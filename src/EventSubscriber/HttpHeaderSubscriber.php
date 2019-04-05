<?php
namespace Germ\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class HttpHeaderSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            // must be registered after the default Locale listener
            KernelEvents::RESPONSE => [['onKernelRequest', 20]],
        );
    }

    public function onKernelRequest(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $response = $event->getResponse();
        $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:4200');

        $event->setResponse($response);
    }
}
