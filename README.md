HTML5CacheBundle
=================

This bundle provides HTML5 Application Cache for Symfony2


[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/evheniy/HTML5CacheBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/evheniy/HTML5CacheBundle/?branch=master) [![Build Status](https://scrutinizer-ci.com/g/evheniy/HTML5CacheBundle/badges/build.png?b=master)](https://scrutinizer-ci.com/g/evheniy/HTML5CacheBundle/build-status/master)

[![Build Status](https://travis-ci.org/evheniy/HTML5CacheBundle.svg)](https://travis-ci.org/evheniy/HTML5CacheBundle)

Documentation
-------------

You can use local CDN (domain):

    html5_cache:
        cdn: cdn.site.com

Default value is empty

You can set protocols for local CDN:

    html5_cache:
        http:  false
        https: false

Default value: true (for both). 

Installation
------------

    $ composer require evheniy/html5-cache-bundle "1.*"

Or add to composer.json

    "evheniy/html5-cache-bundle": "1.*"

AppKernel:

    public function registerBundles()
        {
            $bundles = array(
                ...
                new Evheniy\HTML5CacheBundle\HTML5CacheBundle(),
            );
            ...

config.yml:

    #HTML5CacheBundle
    html5_cache: ~

    or

    #HTML5CacheBundle
    html5_cache:
        cdn: cdn.site.com
        http: true
        https: false

The last step

    app/console cache_manifest:dump

License
-------

This bundle is under the [MIT][3] license.

[Документация на русском языке][1]

[Demo][3] - Open page, then turn off network and update page

[HTML5 Application Cache][4]

[1]:  http://makedev.org/articles/symfony/bundles/jquery_bundle.html
[2]:  http://makedev.org/
[3]:  https://github.com/evheniy/JqueryBundle/blob/master/Resources/meta/LICENSE
[4]:  http://www.w3schools.com/html/html5_app_cache.asp
