<?php

namespace Raindrop\PageBundle\Directory;

use Raindrop\PageBundle\HttpKernel\WebsiteTreeWarmer;

/**
 * Description of CacheReader
 *
 * @author teito
 */
class CacheReader {

    protected $cacheDir;

    public function __construct($cacheDir) {
        $this->cacheDir = $cacheDir;
    }

    //put your code here
    public function getCache() {
        return unserialize(file_get_contents($this->cacheDir . WebsiteTreeWarmer::getCacheFile()));
    }
}

?>
