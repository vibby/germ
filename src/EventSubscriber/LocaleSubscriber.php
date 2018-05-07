<?php
namespace Germ\EventSubscriber;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LocaleSubscriber implements EventSubscriberInterface
{
    const SESSION_KEY = '_locale';

    private $defaultLocale;

    public function __construct($defaultLocale = 'en')
    {
        $this->defaultLocale = $defaultLocale;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        // try to see if the locale has been set as a _locale routing parameter
        if ($locale = $request->attributes->get('_locale')) {
            $request->getSession()->set(self::SESSION_KEY, $locale);
            dump('set locale in session', $locale);
        } else {
            // if no explicit locale has been set on this request, use one from the session
            $lang = $request->getSession()->get(self::SESSION_KEY, $this->defaultLocale);
            $request->setLocale($lang);
            $request->attributes->set('_locale', $lang);
            dump('set locale in request', $lang);
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            // must be registered after the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 15)),
        );
    }
}
