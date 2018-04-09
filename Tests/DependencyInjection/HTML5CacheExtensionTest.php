<?php

namespace Evheniy\HTML5CacheBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Evheniy\HTML5CacheBundle\DependencyInjection\HTML5CacheExtension;
use PHPUnit\Framework\TestCase;

/**
 * Class HTML5CacheExtensionTest
 *
 * @package Evheniy\HTML5CacheBundle\Tests\DependencyInjection
 */
class HTML5CacheExtensionTest extends TestCase
{
    /**
     * @var HTML5CacheExtension
     */
    private $extension;
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     *
     */
    protected function setUp()
    {
        $this->extension = new HTML5CacheExtension();

        $this->container = new ContainerBuilder();
        $this->container->registerExtension($this->extension);
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $resource
     */
    protected function loadConfiguration(ContainerBuilder $container, $resource)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/Fixtures/'));
        $loader->load($resource . '.yml');
    }

    /**
     * Test empty config
     */
    public function testWithoutConfiguration()
    {
        $this->container->loadFromExtension($this->extension->getAlias());
        $this->container->compile();
        $this->assertTrue($this->container->hasParameter('html5_cache'));
        $html5Cache = $this->container->getParameter('html5_cache');
        $this->assertEmpty($html5Cache['cdn']);
        $this->assertTrue($html5Cache['http']);
        $this->assertTrue($html5Cache['https']);
    }

    /**
     * Test normal config
     */
    public function testTest()
    {
        $this->loadConfiguration($this->container, 'test');
        $this->container->compile();
        $this->assertTrue($this->container->hasParameter('html5_cache'));
        $html5Cache = $this->container->getParameter('html5_cache');
        $this->assertNotEmpty($html5Cache['cdn']);
        $this->assertEquals($html5Cache['cdn'], '//cdn.site.com');
        $this->assertEmpty($html5Cache['https']);
        $this->assertFalse($html5Cache['https']);
        $this->assertEmpty($html5Cache['http']);
        $this->assertFalse($html5Cache['http']);
    }

    /**
     *
     */
    public function testGetAlias()
    {
        $this->assertEquals($this->extension->getAlias(), 'html5_cache');
    }
}
