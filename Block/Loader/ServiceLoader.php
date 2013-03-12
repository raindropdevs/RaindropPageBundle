<?php

namespace Raindrop\PageBundle\Block\Loader;

use Sonata\BlockBundle\Block\BlockLoaderInterface;

class ServiceLoader implements BlockLoaderInterface
{
    protected $blockRepository;

    public function __construct($blockRepository) {
        $this->blockRepository = $blockRepository;
    }

    public function support($configuration) {
        if (isset($configuration['type']) && $configuration['type'] == 'raindrop_page.block.service.template') {
            return true;
        }

        return false;
    }

    public function load($name) {
        return $this->blockRepository->findOneByName($name);
    }
}
