<?php

namespace Evheniy\HTML5CacheBundle\Test\Exception;

use Evheniy\HTML5CacheBundle\Exception\PathException;

/**
 * Class PathExceptionTest
 *
 * @package Evheniy\HTML5CacheBundle\Test\Exception
 */
class PathExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @throws PathException
     */
    public function test()
    {
        $this->assertInstanceOf('\Exception', new PathException());
        $this->setExpectedException('\Evheniy\HTML5CacheBundle\Exception\PathException');
        throw new PathException('test');
    }
}