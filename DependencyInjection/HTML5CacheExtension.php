<?php

namespace Evheniy\HTML5CacheBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Evheniy\JqueryBundle\Helper\CdnHelper;

/**
 * Class HTML5CacheBundle
 *
 * @package Evheniy\HTML5CacheBundle\DependencyInjection
 */
class HTML5CacheExtension extends Extension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);
        $config['cdn'] = CdnHelper::createInstance()->filterCdn($config['cdn']);
        $container->setParameter('html5_cache', $config);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return 'html5_cache';
    }
}
