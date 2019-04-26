<?php

namespace Germ\Legacy\DependencyInjection\Compiler;

use Germ\Legacy\Filter\AbstractSearcher;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class FilterSearcherPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $searcherServices = $container->findTaggedServiceIds(AbstractSearcher::class);

        foreach (array_keys($searcherServices) as $idSearcher) {
            $this->addCriterias($container, $idSearcher);
        }
    }

    private function addCriterias(ContainerBuilder $container, $searcherService)
    {
        // always first check if the primary service is defined
        if (!$container->has($searcherService)) {
            return;
        }

        $definition = $container->findDefinition($searcherService);

        $taggedServices = $container->findTaggedServiceIds($searcherService);

        foreach ($taggedServices as $id => $tags) {
            // add the transport service to the ChainTransport service
            $definition->addMethodCall('addItem', array(new Reference($id)));
        }
    }
}
