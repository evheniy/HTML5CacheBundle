<?php

namespace Evheniy\HTML5CacheBundle\Test\Exception;

use Evheniy\HTML5CacheBundle\Exception\PathException;
use PHPUnit\Framework\TestCase;

/**
 * Class PathExceptionTest
 *
 * @package Evheniy\HTML5CacheBundle\Test\Exception
 */
class PathExceptionTest extends TestCase
{
    /**
     * @throws PathException
     */
    public function test()
    {
        $this->assertInstanceOf('\Exception', new PathException());
        $this->expectException(\Evheniy\HTML5CacheBundle\Exception\PathException::class);
        throw new PathException('test');
    }
}