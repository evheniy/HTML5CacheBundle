<?php
namespace Evheniy\HTML5CacheBundle\Tests\Command;

use Evheniy\HTML5CacheBundle\Command\DumpCommand;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\Templating\TemplateNameParser;

/**
 * Class DumpCommandTest
 *
 * @package Evheniy\HTML5CacheBundle\Tests\Command
 */
class DumpCommandUrlsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DumpCommand
     */
    protected $command;
    /**
     * @var \ReflectionClass
     */
    protected $reflectionClass;
    /**
     * @var Container
     */
    protected $container;

    /**
     *
     */
    protected function setUp()
    {
        $this->command = new DumpCommand();
        $this->reflectionClass = new \ReflectionClass('\Evheniy\HTML5CacheBundle\Command\DumpCommand');
        $this->container = new Container();
        $this->container->set('twig', new TwigEngine(new \Twig_Environment(new \Twig_Loader_Array(array('HTML5CacheBundle::cache.html.twig' => file_get_contents(dirname(__FILE__) . '/../../Resources/views/cache.html.twig')))), new TemplateNameParser()));
    }

    /**
     *
     */
    public function testGetJqueryUrls()
    {
        $this->container->setParameter('jquery', array('version' => '1.11.2'));
        $this->command->setContainer($this->container);
        $method = $this->reflectionClass->getMethod('getJqueryUrls');
        $method->setAccessible(true);
        $urls = $method->invoke($this->command);
        $this->assertTrue(is_array($urls));
        $this->assertNotEmpty($urls);
        $this->assertCount(1, $urls);
        $this->assertEquals($urls[0], 'https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js');
    }

    /**
     *
     */
    public function testGetTwitterBootstrapUrls()
    {
        $this->container->setParameter('twitter_bootstrap', array('version' => '3.3.4'));
        $this->command->setContainer($this->container);
        $method = $this->reflectionClass->getMethod('getTwitterBootstrapUrls');
        $method->setAccessible(true);
        $urls = $method->invoke($this->command);
        $this->assertTrue(is_array($urls));
        $this->assertNotEmpty($urls);
        $this->assertCount(3, $urls);
        $this->assertEquals($urls[0], 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css');
        $this->assertEquals($urls[1], 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css');
        $this->assertEquals($urls[2], 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js');
    }

    /**
     *
     */
    public function testGetMaterializeUrls()
    {
        $this->container->setParameter('materialize', array('version' => '0.97.0'));
        $this->command->setContainer($this->container);
        $method = $this->reflectionClass->getMethod('getMaterializeUrls');
        $method->setAccessible(true);
        $urls = $method->invoke($this->command);
        $this->assertTrue(is_array($urls));
        $this->assertNotEmpty($urls);
        $this->assertCount(2, $urls);
        $this->assertEquals($urls[0], 'https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.0/css/materialize.min.css');
        $this->assertEquals($urls[1], 'https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.0/js/materialize.min.js');
    }
}