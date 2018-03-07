<?php

namespace GermBundle;

use GermBundle\DependencyInjection\Compiler\FilterSearcherPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class GermBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FilterSearcherPass());
    }
}
