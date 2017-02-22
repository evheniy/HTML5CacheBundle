<?php

namespace Evheniy\HTML5CacheBundle\Twig;

/**
 * Class HTML5CacheExtension
 *
 * @package Evheniy\HTML5CacheBundle\Twig
 */
class HTML5CacheExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{

    /**
     * @return \Twig_SimpleFunction[]
     */
    public function getFunctions()
    {
      return [
                 new \Twig_SimpleFunction('cache_manifest', [$this, 'getCacheManifest'], [
                     'needs_environment' => true
                 ])
             ];
    }

    /**
     * @return string
     */
    public function getCacheManifest(\Twig_Environment $environment)
    {
        return $environment->isDebug() ? '' : ' manifest="/cache.manifest"';
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
