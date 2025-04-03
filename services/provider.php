<?php

\defined('_JEXEC') or die;

use Joomla\CMS\Extension\Service\Provider\Module as ModuleServiceProvider;
use Joomla\CMS\Extension\Service\Provider\ModuleDispatcherFactory as ModuleDispatcherFactoryServiceProvider;
use Joomla\CMS\Extension\Service\Provider\HelperFactory as HelperFactoryServiceProvider;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;



return new class () implements ServiceProviderInterface {

    public function register(Container $container): void
    {
        $container->registerServiceProvider(new ModuleDispatcherFactoryServiceProvider('\\Ericc70\\Module\\Googlereviews'));
        $container->registerServiceProvider(new HelperFactoryServiceProvider('\\Ericc70\\Module\\Googlereviews\\Site\\Helper'));
        $container->registerServiceProvider(new ModuleServiceProvider());
    }
};