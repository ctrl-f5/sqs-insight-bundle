<?php

namespace Ctrl\SQSInsightBundle;

use Ctrl\SQSInsightBundle\DependencyInjection\Compiler\ClientConfigurationPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SQSInsightBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ClientConfigurationPass());
    }
}
