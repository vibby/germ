<?php

namespace GermBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use GermBundle\DependencyInjection\Compiler\PersonSearcherPass;

class GermBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new PersonSearcherPass());
    }
}
