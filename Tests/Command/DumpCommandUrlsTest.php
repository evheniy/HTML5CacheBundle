<?php
namespace Evheniy\HTML5CacheBundle\Tests\Command;

use Evheniy\HTML5CacheBundle\Command\DumpCommand;
use Symfony\Component\DependencyInjection\Container;
use PHPUnit\Framework\TestCase;

/**
 * Class DumpCommandTest
 *
 * @package Evheniy\HTML5CacheBundle\Tests\Command
 */
class DumpCommandUrlsTest extends TestCase
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
        $version = '1.11.3';
        $this->container->setParameter('jquery', array('version' => $version));
        $jqueryUrls = $this->getUrls('getJqueryUrls');
        $this->assertCount(1, $jqueryUrls);
        $this->assertEquals($jqueryUrls[0], 'https://ajax.googleapis.com/ajax/libs/jquery/' . $version . '/jquery.min.js');
    }

    /**
     *
     */
    public function testGetTwitterBootstrapUrls()
    {
        $version = '3.3.4';
        $this->container->setParameter('twitter_bootstrap', array('version' => $version));
        $twitterBootstrapUrls = $this->getUrls('getTwitterBootstrapUrls');
        $this->assertCount(3, $twitterBootstrapUrls);
        $this->assertEquals($twitterBootstrapUrls[0], 'https://maxcdn.bootstrapcdn.com/bootstrap/' . $version . '/css/bootstrap.min.css');
        $this->assertEquals($twitterBootstrapUrls[1], 'https://maxcdn.bootstrapcdn.com/bootstrap/' . $version . '/css/bootstrap-theme.min.css');
        $this->assertEquals($twitterBootstrapUrls[2], 'https://maxcdn.bootstrapcdn.com/bootstrap/' . $version . '/js/bootstrap.min.js');
    }

    /**
     *
     */
    public function testGetMaterializeUrls()
    {
        $version = '0.97.0';
        $this->container->setParameter('materialize', array('version' => $version));
        $materializeUrls = $this->getUrls('getMaterializeUrls');
        $this->assertCount(2, $materializeUrls);
        $this->assertEquals($materializeUrls[0], 'https://cdnjs.cloudflare.com/ajax/libs/materialize/' . $version . '/css/materialize.min.css');
        $this->assertEquals($materializeUrls[1], 'https://cdnjs.cloudflare.com/ajax/libs/materialize/' . $version . '/js/materialize.min.js');
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