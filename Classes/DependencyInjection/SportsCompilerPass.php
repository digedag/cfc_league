<?php

namespace System25\T3sports\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use System25\T3sports\Sports\ServiceLocator;

class SportsCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        // always first check if the primary service is defined
        if (!$container->has(ServiceLocator::class)) {
            return;
        }

        $definition = $container->findDefinition(ServiceLocator::class);

        // find all service IDs with the t3sports.sports tag
        $taggedServices = $container->findTaggedServiceIds('t3sports.sports');

        foreach ($taggedServices as $id => $tags) {
            // add the indexer to the IndexerProvider service
            $definition->addMethodCall('addSports', [new Reference($id)]);
        }
    }
}
