<?php declare(strict_types=1);

namespace Ctrl\SQSInsightBundle\DependencyInjection\Compiler;

use Ctrl\SQSInsightBundle\SQS\SQSClient;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ClientConfigurationPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $config = $container->getExtensionConfig('sqs_insight')[0];

        if ($container->has(SQSClient::class)) {
            $container->getDefinition(SQSClient::class)
                ->setArgument(0, $config['sqs_client'])
                ->setArgument(1, $config['stripFromName'])
                ->setArgument(2, $config['ignoreQueues'])
            ;
        }
    }
}
