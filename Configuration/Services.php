<?php

namespace System25\T3sports;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use System25\T3sports\Sports\ISports;

return function (ContainerConfigurator $container, ContainerBuilder $containerBuilder) {
    $containerBuilder->registerForAutoconfiguration(ISports::class)->addTag('t3sports.sports');
    $containerBuilder->addCompilerPass(new DependencyInjection\SportsCompilerPass());
};
