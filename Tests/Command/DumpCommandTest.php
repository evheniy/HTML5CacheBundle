<?php
namespace Evheniy\HTML5CacheBundle\Tests\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Evheniy\HTML5CacheBundle\Command\DumpCommand;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Templating\TemplateNameParser;
use PHPUnit\Framework\TestCase;

/**
 * Class DumpCommandTest
 *
 * @package Evheniy\HTML5CacheBundle\Tests\Command
 */
class DumpCommandTest extends TestCase
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
     * @var \ReflectionProperty
     */
    protected $html5Cache;
    /**
     * @var \ReflectionProperty
     */
    protected $webDirectory;
    /**
     * @var string
     */
    protected $webPath;
    /**
     * @var Filesystem
     */
    protected $filesystem;
    /**
     * @var \ReflectionProperty
     */
    protected $filesystemField;
    /**
     * @var \ReflectionProperty
     */
    protected $finder;

    /**
     *
     */
    protected function setUp()
    {
        $this->command = new DumpCommand();
        $this->reflectionClass = new \ReflectionClass('\Evheniy\HTML5CacheBundle\Command\DumpCommand');
        $this->container = new Container();
        $this->container->set('twig', new TwigEngine(new \Twig_Environment(new \Twig_Loader_Array(array('HTML5CacheBundle::cache.html.twig' => file_get_contents(dirname(__FILE__) . '/../../Resources/views/cache.html.twig')))), new TemplateNameParser()));

        $this->webPath = dirname(__FILE__) . '/web';
        $this->filesystem = new Filesystem();
        $this->filesystem->mkdir($this->webPath);
        $this->filesystem->touch($this->webPath . '/test.png');
        $this->filesystem->touch($this->webPath . '/test.gif');

        $this->setMockFields();
    }

    /**
     *
     */
    protected function setMockFields()
    {
        $this->html5Cache = $this->reflectionClass->getProperty('html5Cache');
        $this->html5Cache->setAccessible(true);

        $this->webDirectory = $this->reflectionClass->getProperty('webDirectory');
        $this->webDirectory->setAccessible(true);
        $this->webDirectory->setValue($this->command, $this->webPath);

        $this->finder = $this->reflectionClass->getProperty('finder');
        $this->finder->setAccessible(true);

        $this->filesystemField = $this->reflectionClass->getProperty('filesystem');
        $this->filesystemField->setAccessible(true);
    }

    /**
     *
     */
    protected function tearDown()
    {
        $this->filesystem->remove($this->webPath);
    }

    /**
     *
     */
    public function testGetPath()
    {
        $method = $this->reflectionClass->getMethod('getPath');
        $method->setAccessible(true);
        $this->assertEquals($method->invoke($this->command, 'path/web/test'), 'test');
        $this->expectException('\Evheniy\HTML5CacheBundle\Exception\PathException');
        $method->invoke($this->command, 'path/test');
    }

    /**
     * @throws \Twig_Error_Loader
     */
    public function testRender()
    {
        $this->command->setContainer($this->container);
        $method = $this->reflectionClass->getMethod('render');
        $method->setAccessible(true);

        $initData = array(
            'paths'        => array(
                'test.png',
                'test.gif'
            ),
            'cdn'          => '//cdn.site.com',
            'http'         => true,
            'https'        => true,
            'custom_paths' => array(
                'https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js',
            )
        );
        $this->html5Cache->setValue($this->command, $initData);
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
            'cdn'   => '//cdn.site.com',
            'http'  => false,
            'https' => true
        );
        $this->html5Cache->setValue($this->command, $initData);
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
            'cdn'   => '//cdn.site.com',
            'http'  => true,
            'https' => false
        );
        $this->html5Cache->setValue($this->command, $initData);
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
        $this->html5Cache->setValue($this->command, $initData);
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
            'cdn'          => '//cdn.site.com',
            'http'         => true,
            'https'        => true,
            'custom_paths' => array(
                'https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js',
            )
        );
        $this->container->setParameter('html5_cache', $initData);
        $this->command->setContainer($this->container);
        $method = $this->reflectionClass->getMethod('setHtml5Cache');
        $method->setAccessible(true);
        $method->invoke($this->command, array('test.png', 'test.gif'));
        $data = $this->html5Cache->getValue($this->command);
        $initData['paths'] = array('test.png', 'test.gif');
        $this->assertEquals($data, $initData);
    }

    /**
     *
     */
    public function testGetPaths()
    {
        $this->finder->setValue($this->command, new Finder());
        $method = $this->reflectionClass->getMethod('getPaths');
        $method->setAccessible(true);
        $paths = $method->invoke($this->command);
        $this->assertNotEmpty($paths);
        $this->assertTrue(is_array($paths));
        $this->assertCount(2, $paths);
        $this->assertTrue(in_array('test.png', $paths));
        $this->assertTrue(in_array('test.gif', $paths));
    }

    /**
     * @throws \Twig_Error_Loader
     */
    public function testDump()
    {
        $this->filesystemField->setValue($this->command, new Filesystem());
        $this->command->setContainer($this->container);
        $initData = array(
            'paths'        => array(
                'test.png',
                'test.gif'
            ),
            'cdn'          => '//cdn.site.com',
            'http'         => true,
            'https'        => true,
            'custom_paths' => array(
                'https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js',
            )
        );
        $this->html5Cache->setValue($this->command, $initData);
        $method = $this->reflectionClass->getMethod('dump');
        $method->setAccessible(true);
        $method->invoke($this->command);
        $this->parseFile();
    }

    /**
     * @throws \Twig_Error_Loader
     */
    public function testExecute()
    {
        $initData = array(
            'cdn'          => '//cdn.site.com',
            'http'         => true,
            'https'        => true,
            'custom_paths' => array(
                'https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js',
            )
        );
        $this->container->setParameter('html5_cache', $initData);
        $kernel = $this->createMock(\Symfony\Component\HttpKernel\KernelInterface::class);
        $kernel->method('getRootdir')->willReturn($this->webPath);
        $this->container->set('kernel', $kernel);
        $this->command->setContainer($this->container);
        $method = $this->reflectionClass->getMethod('execute');
        $method->setAccessible(true);
        $output = new StreamOutput(fopen('php://memory', 'w', false));
        $method->invoke($this->command, new ArrayInput(array()), $output);
        rewind($output->getStream());
        $this->assertRegExp('/Done/', stream_get_contents($output->getStream()));
        $this->parseFile();
    }

    /**
     *
     */
    private function parseFile()
    {
        $this->assertTrue($this->filesystem->exists(array($this->webPath . '/cache.manifest')));
        $data = file_get_contents($this->webPath . '/cache.manifest');
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
    }
}