<?php

namespace Raindrop\PageBundle\HttpKernel;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\ContainerInterface;

class WebsiteTreeWarmer implements CacheWarmerInterface
{
    const BUNDLE_NAMESPACE = 'raindrop';
    const CACHE_DIR = 'page';
    const FILENAME = 'tree.meta';

    protected $kernel;
    protected $treeBuilder;
    protected $container;

    public function __construct(KernelInterface $kernel, ContainerInterface $container, $treeBuilder)
    {
        $this->container = $container;
        $this->kernel = $kernel;
        $this->treeBuilder = $treeBuilder;
    }

    public function warmUp($cacheDir)
    {
        // This avoids class-being-declared twice errors when the cache:clear
        // command is called.
        if (basename($cacheDir) === $this->kernel->getEnvironment().'_new') {
            return;
        }

        $cacheFilePath = $cacheDir . self::getCacheFile();
        $cache = new ConfigCache($cacheFilePath, $this->container->getParameter('kernel.debug'));
        $cache->write(serialize($this->treeBuilder->buildTree()->toArray()));
    }

    public static function getCacheFile()
    {
        return
            DIRECTORY_SEPARATOR . self::BUNDLE_NAMESPACE .
            DIRECTORY_SEPARATOR . self::CACHE_DIR .
            DIRECTORY_SEPARATOR . self::FILENAME
            ;
    }

    public function isOptional()
    {
        return false;
    }
}
