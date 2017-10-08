<?php

namespace GermBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class PersonSearcherPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // always first check if the primary service is defined
        if (!$container->has('germ.person_searcher')) {
            return;
        }

        $definition = $container->findDefinition('germ.person_searcher');

        // find all service IDs with the app.mail_transport tag
        $taggedServices = $container->findTaggedServiceIds('person.searcher_item');

        foreach ($taggedServices as $id => $tags) {
            // add the transport service to the ChainTransport service
            $definition->addMethodCall('addItem', array(new Reference($id)));
        }
    }
}