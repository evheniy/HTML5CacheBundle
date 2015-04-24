<?php

namespace Evheniy\HTML5CacheBundle\Twig;

/**
 * Class HTML5CacheExtension
 *
 * @package Evheniy\HTML5CacheBundle\Twig
 */
class HTML5CacheExtension extends \Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * @return \Twig_SimpleFunction[]
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('cache_manifest', array($this, 'getCacheManifest'))
        );
    }

    /**
     * @param \Twig_Environment $environment
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return string
     */
    public function getCacheManifest()
    {
        return $this->environment->isDebug() ? '' : ' manifest="/cache.manifest"';
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'html5_cache_extension';
    }
}