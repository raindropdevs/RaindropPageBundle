<?php

namespace Raindrop\PageBundle\Block\Loader;

use Sonata\BlockBundle\Block\BlockLoaderInterface;
use Sonata\BlockBundle\Model\Block;

class AdminDashboardLoader implements BlockLoaderInterface
{
    protected $blockRepository;

    public function __construct($blockRepository) {
        $this->blockRepository = $blockRepository;
    }

    public function support($configuration) {
        if (isset($configuration['type']) && $configuration['type'] == 'raindrop.admin.block.service') {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function load($configuration)
    {
        $block = new Block;
        $block->setId(uniqid());
        $block->setType($configuration['type']);
        $block->setSettings($this->getSettings($configuration));
        $block->setEnabled(true);
        $block->setCreatedAt(new \DateTime);
        $block->setUpdatedAt(new \DateTime);

        return $block;
    }

    public function getSettings() {
        return array();
    }
}
