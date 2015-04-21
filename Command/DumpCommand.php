<?php

namespace Evheniy\HTML5CacheBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class DumpCommand
 *
 * @package Evheniy\HTML5CacheBundle\Command
 */
class DumpCommand extends ContainerAwareCommand
{
    /**
     * @var array
     */
    protected $_types
        = array(
            'jpg',
            'jpeg',
            'gif',
            'png',
            'bmp',
            'ico',
            'pdf',
            'flv',
            'swf',
            'html',
            'htm',
            'txt',
            'css',
            'js',
            'eot',
            'woff',
            'ttf',
            'svg',
            'ogg'
        );
    /**
     * @var string
     */
    protected $webDirectory;
    /**
     * @var array
     */
    protected $html5Cache;

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('manifest:dump')
            ->setDescription('Dumps Cache Manifest file for using HTML5 Application Cache')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command dumps Cache Manifest file for using HTML5 Application Cache.

  <info>php %command.full_name%</info>
EOF
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->webDirectory = $this->getContainer()->get('kernel')->getRootdir().'/../web';
        $this->setHtml5Cache($this->getPaths());
        $this->dump();
    }

    /**
     * @param array $paths
     */
    protected function setHtml5Cache(array $paths = array())
    {
        $this->html5Cache = $this->getContainer()->getParameter('html5_cache');
        $this->html5Cache['paths'] = $paths;
        $this->html5Cache['custom_paths'] = array_merge($this->html5Cache['custom_paths'], $this->getJqueryUrls(), $this->getTwitterBootstrapUrls());
    }

    /**
     * @return array
     */
    protected function getPaths()
    {
        $paths = array();
        $dir = new \RecursiveDirectoryIterator($this->webDirectory);
        foreach (new \RecursiveIteratorIterator($dir) as $file) {
            if ($file->isFile() && in_array($file->getExtension(), $this->_types)) {
                $paths[] = $this->getPath($file);
            }
        }

        return $paths;
    }

    /**
     *
     */
    protected function dump()
    {
        $fs = new Filesystem();
        $fs->dumpFile(
            $this->webDirectory . '/cache.manifest',
            $this->render($this->html5Cache)
        );
    }

    /**
     * @param array $html5Cache
     *
     * @return string
     */
    protected function render(array $html5Cache)
    {
        return $this->getContainer()->get('twig')->render('@HTML5CacheBundle::cache.html.twig', $html5Cache);
    }

    /**
     * @param \SplFileInfo $file
     *
     * @return string
     */
    protected function getPath(\SplFileInfo $file)
    {
        $path = explode('/web/', $file->getRealPath());

        return $path[1];
    }

    /**
     * @return array
     */
    protected function getJqueryUrls()
    {
        $url = array();
        if ($this->getContainer()->hasParameter('jquery') && $this->getContainer()->hasParameter('jquery.local')) {
            $jquery = $this->getContainer()->getParameter('jquery');
            if (!empty($jquery)) {
                $url[] = "https://ajax.googleapis.com/ajax/libs/jquery/{$jquery['version']}/jquery.min.js";
            }
        }

        return $url;
    }

    /**
     * @return array
     */
    protected function getTwitterBootstrapUrls()
    {
        $url = array();
        if ($this->getContainer()->hasParameter('twitter_bootstrap') && $this->getContainer()->hasParameter('twitter_bootstrap.local_js')) {
            $twitterBootstrap = $this->getContainer()->getParameter('twitter_bootstrap');
            if (!empty($twitterBootstrap)) {
                $url[] = "https://maxcdn.bootstrapcdn.com/bootstrap/{$twitterBootstrap['version']}/css/bootstrap.min.css";
                $url[] = "https://maxcdn.bootstrapcdn.com/bootstrap/{$twitterBootstrap['version']}/css/bootstrap-theme.min.css";
                $url[] = "https://maxcdn.bootstrapcdn.com/bootstrap/{$twitterBootstrap['version']}/js/bootstrap.min.js";
            }
        }

        return $url;
    }
}