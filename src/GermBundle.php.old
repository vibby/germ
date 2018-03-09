<?php

namespace Germ;

use Germ\DependencyInjection\Compiler\FilterSearcherPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Germ extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FilterSearcherPass());
    }
}
