<?php

namespace Evheniy\HTML5CacheBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;

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
        $config['cdn'] = $this->filterCdn($config['cdn']);
        $container->setParameter('html5_cache', $config);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * @param $cdn
     *
     * @return mixed
     */
    protected function filterCdn($cdn)
    {
        if (!empty($cdn)) {
            $url = parse_url($cdn);
            if (!empty($url['host'])) {
                $cdn = $url['host'];
            } else {
                $cdn = current(
                    array_filter(preg_split('/[^a-z0-9\.]+/', $url['path']))
                );
            }
        }

        return $cdn;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return 'html5_cache';
    }
}
