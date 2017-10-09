<?php

namespace GermBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class PersonSearcherPass implements CompilerPassInterface
{
    const SERVICE_ALIAS = 'GermBundle\Person\Searcher';

    public function process(ContainerBuilder $container)
    {
        // always first check if the primary service is defined
        if (!$container->has(SELF::SERVICE_ALIAS)) {
            return;
        }

        $definition = $container->findDefinition(SELF::SERVICE_ALIAS);

        // find all service IDs with the app.mail_transport tag
        $taggedServices = $container->findTaggedServiceIds('GermBundle\Person\AbstractSearchItem');

        foreach ($taggedServices as $id => $tags) {
            // add the transport service to the ChainTransport service
            $definition->addMethodCall('addItem', array(new Reference($id)));
        }
    }
}