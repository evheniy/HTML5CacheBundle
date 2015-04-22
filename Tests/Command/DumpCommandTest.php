<?php
namespace Evheniy\HTML5CacheBundle\Tests\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Output\StreamOutput;
use Evheniy\HTML5CacheBundle\Command\DumpCommand;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Templating\TemplateNameParser;
use Symfony\Bundle\FrameworkBundle\Console\Application;

/**
 * Class DumpCommandTest
 *
 * @package Evheniy\HTML5CacheBundle\Tests\Command
 */
class DumpCommandTest extends KernelTestCase
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
        $this->reflectionClass = new \ReflectionClass(
            '\Evheniy\HTML5CacheBundle\Command\DumpCommand'
        );

        $this->container = new Container();
    }

    /**
     *
     */
    public function testGetPath()
    {
        $method = $this->reflectionClass->getMethod('getPath');
        $method->setAccessible(true);
        $this->assertEquals($method->invoke($this->command, 'path/web/test'), 'test');
        $this->setExpectedException('\Evheniy\HTML5CacheBundle\Exception\PathException');
        $method->invoke($this->command, 'path/test');
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
        $this->container->setParameter(
            'twitter_bootstrap', array('version' => '3.3.4')
        );
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
     * @throws \Twig_Error_Loader
     */
    public function testRender()
    {
        $loader = new \Twig_Loader_Filesystem();
        $loader->addPath(dirname(__FILE__) . '/../../Resources/views', 'HTML5CacheBundle');
        $this->container->set('twig', new TwigEngine(new \Twig_Environment($loader), new TemplateNameParser()));
        $this->command->setContainer($this->container);
        $html5Cache = $this->reflectionClass->getProperty('html5Cache');
        $html5Cache->setAccessible(true);
        $method = $this->reflectionClass->getMethod('render');
        $method->setAccessible(true);

        $initData = array(
            'paths'        => array(
                'test.png',
                'test.gif'
            ),
            'cdn'          => 'cdn.site.com',
            'http'         => true,
            'https'        => true,
            'custom_paths' => array(
                'https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js',
            )
        );
        $html5Cache->setValue($this->command, $initData);
        $data = $method->invoke($this->command);
        $this->assertRegExp('/CACHE MANIFEST/', $data);
        $this->assertRegExp('/CACHE:/', $data);
        $this->assertRegExp('/NETWORK:/', $data);
        $this->assertRegExp('/http:\/\/cdn.site.com\/test.png/', $data);
        $this->assertRegExp('/https:\/\/cdn.site.com\/test.png/', $data);
        $this->assertRegExp('/\/test.png/', $data);
        $this->assertRegExp('/http:\/\/cdn.site.com\/test.gif/', $data);
        $this->assertRegExp('/https:\/\/cdn.site.com\/test.gif/', $data);
        $this->assertRegExp('/\/test.gif/', $data);
        $this->assertRegExp('/https:\/\/ajax.googleapis.com\/ajax\/libs\/jquery\/1.11.2\/jquery.min.js/', $data);

        $initData = array(
            'paths' => array(
                'test.png',
                'test.gif'
            ),
            'cdn'   => 'cdn.site.com',
            'http'  => false,
            'https' => true
        );
        $html5Cache->setValue($this->command, $initData);
        $data = $method->invoke($this->command);
        $this->assertRegExp('/https:\/\/cdn.site.com\/test.png/', $data);
        $this->assertRegExp('/\/test.png/', $data);
        $this->assertRegExp('/https:\/\/cdn.site.com\/test.gif/', $data);
        $this->assertRegExp('/\/test.gif/', $data);

        $initData = array(
            'paths' => array(
                'test.png',
                'test.gif'
            ),
            'cdn'   => 'cdn.site.com',
            'http'  => true,
            'https' => false
        );
        $html5Cache->setValue($this->command, $initData);
        $data = $method->invoke($this->command);
        $this->assertRegExp('/http:\/\/cdn.site.com\/test.png/', $data);
        $this->assertRegExp('/\/test.png/', $data);
        $this->assertRegExp('/http:\/\/cdn.site.com\/test.gif/', $data);
        $this->assertRegExp('/\/test.gif/', $data);

        $initData = array(
            'paths' => array(
                'test.png',
                'test.gif'
            )
        );
        $html5Cache->setValue($this->command, $initData);
        $data = $method->invoke($this->command);
        $this->assertRegExp('/\/test.png/', $data);
        $this->assertRegExp('/\/test.gif/', $data);
    }

    /**
     *
     */
    public function testSetHtml5Cache()
    {
        $initData = array(
            'cdn'          => 'cdn.site.com',
            'http'         => true,
            'https'        => true,
            'custom_paths' => array(
                'https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js',
                'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css',
                'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css',
                'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js'
            )
        );
        $this->container->setParameter('html5_cache', $initData);
        $this->command->setContainer($this->container);
        $method = $this->reflectionClass->getMethod('setHtml5Cache');
        $method->setAccessible(true);
        $method->invoke($this->command, array('test.png', 'test.gif'));
        $html5Cache = $this->reflectionClass->getProperty('html5Cache');
        $html5Cache->setAccessible(true);
        $data = $html5Cache->getValue($this->command);
        $initData['paths'] = array('test.png', 'test.gif');
        $this->assertEquals($data, $initData);
    }

    /**
     *
     */
    public function testGetPaths()
    {
        $webPath = dirname(__FILE__) . '/web';
        $filesystem = new Filesystem();
        $filesystem->mkdir($webPath);
        $filesystem->touch($webPath . '/test.png');
        $filesystem->touch($webPath . '/test.gif');

        $webDirectory = $this->reflectionClass->getProperty('webDirectory');
        $webDirectory->setAccessible(true);
        $webDirectory->setValue($this->command, $webPath);

        $finder = $this->reflectionClass->getProperty('finder');
        $finder->setAccessible(true);
        $finder->setValue($this->command, new Finder());

        $method = $this->reflectionClass->getMethod('getPaths');
        $method->setAccessible(true);
        $paths = $method->invoke($this->command);

        $this->assertNotEmpty($paths);
        $this->assertTrue(is_array($paths));
        $this->assertCount(2, $paths);
        $this->assertTrue(in_array('test.png', $paths));
        $this->assertTrue(in_array('test.gif', $paths));

        $filesystem->remove($webPath);
    }

    /**
     * @throws \Twig_Error_Loader
     */
    public function testDump()
    {
        $webPath = dirname(__FILE__) . '/web';
        $file = new Filesystem();
        $file->mkdir($webPath);

        $loader = new \Twig_Loader_Filesystem();
        $loader->addPath(dirname(__FILE__) . '/../../Resources/views', 'HTML5CacheBundle');
        $this->container->set('twig', new TwigEngine(new \Twig_Environment($loader), new TemplateNameParser()));
        $this->command->setContainer($this->container);

        $webDirectory = $this->reflectionClass->getProperty('webDirectory');
        $webDirectory->setAccessible(true);
        $webDirectory->setValue($this->command, $webPath);

        $filesystem = $this->reflectionClass->getProperty('filesystem');
        $filesystem->setAccessible(true);
        $filesystem->setValue($this->command, new Filesystem());

        $html5Cache = $this->reflectionClass->getProperty('html5Cache');
        $html5Cache->setAccessible(true);
        $initData = array(
            'paths'        => array(
                'test.png',
                'test.gif'
            ),
            'cdn'          => 'cdn.site.com',
            'http'         => true,
            'https'        => true,
            'custom_paths' => array(
                'https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js',
                'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css',
                'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css',
                'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js'
            )
        );
        $html5Cache->setValue($this->command, $initData);

        $method = $this->reflectionClass->getMethod('dump');
        $method->setAccessible(true);
        $method->invoke($this->command);

        $this->parseFile($file, $webPath);

        $file->remove($webPath);
    }

    /**
     * @throws \Twig_Error_Loader
     */
    public function testExecute()
    {
        $webPath = dirname(__FILE__) . '/web';
        $filesystem = new Filesystem();
        $filesystem->mkdir($webPath);
        $filesystem->touch($webPath . '/test.png');
        $filesystem->touch($webPath . '/test.gif');

        $loader = new \Twig_Loader_Filesystem();
        $loader->addPath(dirname(__FILE__) . '/../../Resources/views', 'HTML5CacheBundle');
        $this->container->set('twig', new TwigEngine(new \Twig_Environment($loader), new TemplateNameParser()));
        $initData = array(
            'cdn'          => 'cdn.site.com',
            'http'         => true,
            'https'        => true,
            'custom_paths' => array(
                'https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js',
                'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css',
                'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css',
                'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js'
            )
        );
        $this->container->setParameter('html5_cache', $initData);
        $kernel = $this->getMock('Symfony\Component\HttpKernel\KernelInterface');
        $kernel->method('getRootdir')->willReturn($webPath);
        $this->container->set('kernel', $kernel);
        $this->command->setContainer($this->container);

        $method = $this->reflectionClass->getMethod('execute');
        $method->setAccessible(true);
        $output = new StreamOutput(fopen('php://memory', 'w', false));
        $method->invoke($this->command, new ArrayInput(array()), $output);
        rewind($output->getStream());
        $this->assertRegExp('/Done/', stream_get_contents($output->getStream()));

        $this->parseFile($filesystem, $webPath);

        $filesystem->remove($webPath);
    }

    /**
     * @param Filesystem $file
     * @param string     $webPath
     */
    private function parseFile(Filesystem $file, $webPath)
    {
        $this->assertTrue($file->exists(array($webPath . '/cache.manifest')));
        $data = file_get_contents($webPath . '/cache.manifest');

        $this->assertRegExp('/CACHE MANIFEST/', $data);
        $this->assertRegExp('/CACHE:/', $data);
        $this->assertRegExp('/NETWORK:/', $data);
        $this->assertRegExp('/http:\/\/cdn.site.com\/test.png/', $data);
        $this->assertRegExp('/https:\/\/cdn.site.com\/test.png/', $data);
        $this->assertRegExp('/\/test.png/', $data);

        $this->assertRegExp('/http:\/\/cdn.site.com\/test.gif/', $data);
        $this->assertRegExp('/https:\/\/cdn.site.com\/test.gif/', $data);
        $this->assertRegExp('/\/test.gif/', $data);

        $this->assertRegExp('/https:\/\/ajax.googleapis.com\/ajax\/libs\/jquery\/1.11.2\/jquery.min.js/', $data);
        $this->assertRegExp('/https:\/\/maxcdn.bootstrapcdn.com\/bootstrap\/3.3.4\/css\/bootstrap.min.css/', $data);
        $this->assertRegExp('/https:\/\/maxcdn.bootstrapcdn.com\/bootstrap\/3.3.4\/css\/bootstrap-theme.min.css/', $data);
        $this->assertRegExp('/https:\/\/maxcdn.bootstrapcdn.com\/bootstrap\/3.3.4\/js\/bootstrap.min.js/', $data);
    }
}