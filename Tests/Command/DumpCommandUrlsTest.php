<?php
namespace Evheniy\HTML5CacheBundle\Tests\Command;

use Evheniy\HTML5CacheBundle\Command\DumpCommand;
use Symfony\Component\DependencyInjection\Container;

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
    }

    /**
     *
     */
    public function testGetJqueryUrls()
    {
        $this->container->setParameter('jquery', array('version' => '1.11.2'));
        $urls = $this->getUrls('getJqueryUrls');
        $this->assertCount(1, $urls);
        $this->assertEquals($urls[0], 'https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js');
    }

    /**
     *
     */
    public function testGetTwitterBootstrapUrls()
    {
        $this->container->setParameter('twitter_bootstrap', array('version' => '3.3.4'));
        $urls = $this->getUrls('getTwitterBootstrapUrls');
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
        $urls = $this->getUrls('getMaterializeUrls');
        $this->assertCount(2, $urls);
        $this->assertEquals($urls[0], 'https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.0/css/materialize.min.css');
        $this->assertEquals($urls[1], 'https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.0/js/materialize.min.js');
    }

    /**
     * @param string $method
     *
     * @return array
     */
    protected function getUrls($method)
    {
        $this->command->setContainer($this->container);
        $method = $this->reflectionClass->getMethod($method);
        $method->setAccessible(true);
        $urls = $method->invoke($this->command);
        $this->assertTrue(is_array($urls));
        $this->assertNotEmpty($urls);

        return $urls;
    }
}