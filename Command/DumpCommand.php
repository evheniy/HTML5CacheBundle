<?php

namespace Evheniy\HTML5CacheBundle\Command;

use Evheniy\HTML5CacheBundle\Exception\PathException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

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
    protected $_types  = array('jpg', 'jpeg', 'gif', 'png', 'bmp', 'ico', 'flv', 'swf', 'txt', 'css', 'js', 'eot', 'woff', 'woff2','ttf', 'svg');
    /**
     * @var string
     */
    protected $webDirectory;
    /**
     * @var array
     */
    protected $html5Cache;
    /**
     * @var Filesystem
     */
    protected $filesystem;
    /**
     * @var Finder
     */
    protected $finder;

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('manifest:dump')
            ->setDescription('Dumps Cache Manifest file for using HTML5 Application Cache')
            ->setHelp("\nThe <info>%command.name%</info> command dumpsCache Manifest file for using HTML5 Application Cache.\n\n<info>%command.full_name%</info>\n");
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $input;
        $output->writeln('<comment>Start dumping cache.manifest...</comment>');
        $this->webDirectory = $this->getContainer()->get('kernel')->getRootdir() . '/../web';
        $this->filesystem = new Filesystem();
        $this->finder = new Finder();
        $this->setHtml5Cache($this->getPaths());
        $this->dump();
        $output->writeln('<info>Done</info>');
    }

    /**
     * @param array $paths
     */
    protected function setHtml5Cache(array $paths = array())
    {
        $this->html5Cache = array_merge(
            $this->getContainer()->getParameter('html5_cache'),
            array('paths' => $paths)
        );
        $this->html5Cache['custom_paths'] = array_merge(
            $this->html5Cache['custom_paths'],
            $this->getJqueryUrls(),
            $this->getTwitterBootstrapUrls(),
            $this->getMaterializeUrls()
        );
    }

    /**
     * @return array
     */
    protected function getPaths()
    {
        $paths = array();
        foreach ($this->finder->files()->in($this->webDirectory) as $file) {
            if ($file->isFile() && in_array($file->getExtension(), $this->_types)) {
                $paths[] = $this->getPath($file->getRealPath());
            }
        }

        return $paths;
    }

    /**
     *
     */
    protected function dump()
    {
        $this->filesystem->dumpFile(
            $this->webDirectory . '/cache.manifest',
            $this->render()
        );
    }

    /**
     * @return string
     */
    protected function render()
    {
        return $this->getContainer()->get('twig')->render('HTML5CacheBundle::cache.html.twig', $this->html5Cache);
    }

    /**
     * @param string $filePath
     *
     * @return string
     * @throws PathException
     */
    protected function getPath($filePath)
    {
        $path = explode('/web/', $filePath);
        if (empty($path) || empty($path[1])) {
            throw new PathException('You must search files in web directory');
        }

        return $path[1];
    }

    /**
     * @return array
     */
    protected function getJqueryUrls()
    {
        $url = array();
        if ($this->getContainer()->hasParameter('jquery')) {
            $jquery = $this->getContainer()->getParameter('jquery');
            if (!empty($jquery) && !empty($jquery['version'])) {
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
        if ($this->getContainer()->hasParameter('twitter_bootstrap')) {
            $twitterBootstrap = $this->getContainer()->getParameter('twitter_bootstrap');
            if (!empty($twitterBootstrap) && !empty($twitterBootstrap['version'])) {
                $url[] = "https://maxcdn.bootstrapcdn.com/bootstrap/{$twitterBootstrap['version']}/css/bootstrap.min.css";
                $url[] = "https://maxcdn.bootstrapcdn.com/bootstrap/{$twitterBootstrap['version']}/css/bootstrap-theme.min.css";
                $url[] = "https://maxcdn.bootstrapcdn.com/bootstrap/{$twitterBootstrap['version']}/js/bootstrap.min.js";
            }
        }

        return $url;
    }

    /**
     * @return array
     */
    protected function getMaterializeUrls()
    {
        $url = array();
        if ($this->getContainer()->hasParameter('materialize')) {
            $materialize = $this->getContainer()->getParameter('materialize');
            if (!empty($materialize) && !empty($materialize['version'])) {
                $url[] = "https://cdnjs.cloudflare.com/ajax/libs/materialize/{$materialize['version']}/css/materialize.min.css";
                $url[] = "https://cdnjs.cloudflare.com/ajax/libs/materialize/{$materialize['version']}/js/materialize.min.js";
            }
        }

        return $url;
    }
}