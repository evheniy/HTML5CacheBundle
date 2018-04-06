<?php

namespace Evheniy\HTML5CacheBundle\Test\Twig;

use Evheniy\HTML5CacheBundle\Twig\HTML5CacheExtension;
use PHPUnit\Framework\TestCase;

/**
 * Class HTML5CacheExtensionTest
 *
 * @package Evheniy\HTML5CacheBundle\Test\Twig
 */
class HTML5CacheExtensionTest extends TestCase
{
    /**
     * @var HTML5CacheExtension
     */
    protected $extension;

    /**
     *
     */
    protected function setUp()
    {
        $this->extension = new HTML5CacheExtension();
    }

    /**
     *
     */
    public function testGetFunctions()
    {
        $this->assertTrue(is_array($this->extension->getFunctions()));
        $this->assertNotEmpty($this->extension->getFunctions());
        $functions = $this->extension->getFunctions();
        $this->assertInstanceOf('\Twig_SimpleFunction', $functions[0]);
        $function = $functions[0];
        $this->assertEquals('cache_manifest', $function->getName());
    }

    /**
     *
     */
    public function testGetName()
    {
        $this->assertNotEmpty($this->extension->getName());
        $this->assertEquals('html5_cache_extension', $this->extension->getName());
    }

    /**
     *
     */
    public function testInitRuntime()
    {
        $reflectionClass = new \ReflectionClass('\Evheniy\HTML5CacheBundle\Twig\HTML5CacheExtension');

        $this->extension->initRuntime(new \Twig_Environment(new \Twig_Loader_Array(), array('debug' => false)));
        $environment = $reflectionClass->getProperty('environment');
        $environment->setAccessible(true);
        /** @var \Twig_Environment $environmentData */
        $environmentData = $environment->getValue($this->extension);
        $this->assertInstanceOf('\Twig_Environment', $environmentData);
        $this->assertFalse($environmentData->isDebug());

        $this->extension->initRuntime(new \Twig_Environment(new \Twig_Loader_Array(), array('debug' => true)));
        $environment = $reflectionClass->getProperty('environment');
        $environment->setAccessible(true);
        /** @var \Twig_Environment $environmentData */
        $environmentData = $environment->getValue($this->extension);
        $this->assertInstanceOf('\Twig_Environment', $environmentData);
        $this->assertTrue($environmentData->isDebug());
    }

    /**
     *
     */
    public function testGetCacheManifest()
    {
        $environment = new \Twig_Environment(new \Twig_Loader_Array(), array('debug' => false));
        $this->extension->initRuntime($environment);
        $this->assertEquals($this->extension->getCacheManifest(), ' manifest="/cache.manifest"');

        $environment = new \Twig_Environment(new \Twig_Loader_Array(), array('debug' => true));
        $this->extension->initRuntime($environment);
        $this->assertEquals($this->extension->getCacheManifest(), '');
    }

    /**
     *
     */
    public function testTwigRender()
    {
        $twig = new \Twig_Environment(new \Twig_Loader_Array(array('test' => '{{ cache_manifest()|raw }}')), array('debug' => false));
        $this->extension->initRuntime($twig);
        $twig->addExtension($this->extension);
        $this->assertEquals($twig->render('test'), ' manifest="/cache.manifest"');

        $twig = new \Twig_Environment(new \Twig_Loader_Array(array('test' => '{{ cache_manifest()|raw }}')), array('debug' => true));
        $this->extension->initRuntime($twig);
        $twig->addExtension($this->extension);
        $this->assertEquals($twig->render('test'), '');
    }
}