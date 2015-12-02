HTML5CacheBundle
=================

This bundle provides HTML5 Application Cache for Symfony2

[![knpbundles.com](http://knpbundles.com/evheniy/HTML5CacheBundle/badge)](http://knpbundles.com/evheniy/HTML5CacheBundle)

[![Latest Stable Version](https://poser.pugx.org/evheniy/html5-cache-bundle/v/stable)](https://packagist.org/packages/evheniy/html5-cache-bundle) [![Total Downloads](https://poser.pugx.org/evheniy/html5-cache-bundle/downloads)](https://packagist.org/packages/evheniy/html5-cache-bundle) [![Latest Unstable Version](https://poser.pugx.org/evheniy/html5-cache-bundle/v/unstable)](https://packagist.org/packages/evheniy/html5-cache-bundle) [![License](https://poser.pugx.org/evheniy/html5-cache-bundle/license)](https://packagist.org/packages/evheniy/html5-cache-bundle)

[![Build Status](https://travis-ci.org/evheniy/HTML5CacheBundle.svg)](https://travis-ci.org/evheniy/HTML5CacheBundle)
[![Coverage Status](https://coveralls.io/repos/evheniy/HTML5CacheBundle/badge.svg?branch=master&service=github)](https://coveralls.io/github/evheniy/HTML5CacheBundle?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/evheniy/HTML5CacheBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/evheniy/HTML5CacheBundle/?branch=master) [![Build Status](https://scrutinizer-ci.com/g/evheniy/HTML5CacheBundle/badges/build.png?b=master)](https://scrutinizer-ci.com/g/evheniy/HTML5CacheBundle/build-status/master)

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
        custom_urls:
            - https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js
            - https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css
            - https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css
            - https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js
            - ...

layout.html.twig:

    <html{{- cache_manifest()|raw -}}>
    ...

The last step

    app/console manifest:dump

Documentation
-------------

You can use local CDN (domain):

    html5_cache:
        cdn: cdn.site.com

Default value is empty

You can set protocols for local CDN:

    html5_cache:
        cdn: cdn.site.com
        http:  true
        https: false

Default value: true (for both)

You can set custom urls:

    html5_cache:
        custom_urls:
            - https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js
            - https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css
            - https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css
            - https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js
            - ...

Default value is empty

Partial using
-------------

layout.html.twig:
   
    <html{%- block cache_manifest -%}{%- endblock -%}>
    ...
        
page_with_cache.html.twig:
    
    {%- extends "layout.html.twig" -%}
    {%- block cache_manifest -%}{{- cache_manifest()|raw -}}{%- endblock -%}
    ...
        
page_without_cache.html.twig:
    
    {%- extends "layout.html.twig" -%}
    {%- block cache_manifest -%}{%- endblock -%}
    ...


JqueryBundle
------------

If You are using [JqueryBundle][5], this url:

    https://ajax.googleapis.com/ajax/libs/jquery/{JqueryBundle.config.version}/jquery.min.js 

will be inserted automatically


TwitterBootstrapBundle
----------------------

If You are using [TwitterBootstrapBundle][6], those urls:

    - https://maxcdn.bootstrapcdn.com/bootstrap/{TwitterBootstrapBundle.config.version}/css/bootstrap.min.css
    - https://maxcdn.bootstrapcdn.com/bootstrap/{TwitterBootstrapBundle.config.version}/css/bootstrap-theme.min.css
    - https://maxcdn.bootstrapcdn.com/bootstrap/{TwitterBootstrapBundle.config.version}/js/bootstrap.min.js

will be inserted automatically

MaterializeBundle
----------------------

If You are using [MaterializeBundle][7], those urls:

    - https://cdnjs.cloudflare.com/ajax/libs/materialize/{MaterializeBundle.config.version}/css/materialize.min.css
    - https://cdnjs.cloudflare.com/ajax/libs/materialize/{MaterializeBundle.config.version}/js/materialize.min.js

will be inserted automatically

License
-------

This bundle is under the [MIT][3] license.

[Документация на русском языке][1]

[Demo][2] - Open page, then turn off network and update page

[HTML5 Application Cache][4]

[1]:  http://makedev.org/articles/symfony/bundles/html5_cache_bundle.html
[2]:  http://makedev.org/
[3]:  https://github.com/evheniy/HTML5CacheBundle/blob/master/Resources/meta/LICENSE
[4]:  http://www.w3schools.com/html/html5_app_cache.asp
[5]:  https://github.com/evheniy/JqueryBundle
[6]:  https://github.com/evheniy/TwitterBootstrapBundle
[7]:  https://github.com/evheniy/MaterializeBundle