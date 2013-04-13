<?php

namespace Raindrop\PageBundle\Manager;

use Raindrop\PageBundle\Entity\Block;
use Raindrop\PageBundle\Entity\BlockConfig;
use Raindrop\PageBundle\Entity\BlockVariable;
use Doctrine\Common\Collections\ArrayCollection;

class BlockManager {

    protected $orm;

    public function __construct($orm, $pageManager) {
        $this->orm = $orm;
        $this->pageManager = $pageManager;
    }

    public function createBlock($page, $type, $block_layout_position) {
        $block = new Block;
        $this->orm->persist($block);

        $block->setName($type);
        $block->setPage($page);

        $blockConfig = $this->getBlockConfig($type);
        $block->setTemplate($blockConfig->getTemplate());

        $variables = $this->createBlockVariables($blockConfig, $block);
        $block->setVariables($variables);

        $block->setJavascripts($blockConfig->getJavascripts());
        $block->setStylesheets($blockConfig->getStylesheets());

        $block->setPosition(1000);
        $block->setLayout($block_layout_position);

//        $variables->forAll(function ($index, $variable) use ($page, $block) {
//            $variable->setBlock($block);
//        });


        // update template last modified as the content has changed
        // NO MORE NEEDED (maybe...)
//        $this->pageManager->updatePageLayoutTimestamp($page);
        $this->orm->flush();

        return $block;
    }

    protected function getBlockConfig($type) {
        $repo = $this->orm->getRepository('Raindrop\PageBundle\Entity\BlockConfig');
        return $repo->findOneByType($type);
    }

    protected function createBlockVariables($blockConfig, $block) {
        $return = new ArrayCollection;

        foreach ($blockConfig->getOptions() as $name => $options) {
            $config = new BlockVariable;
            $config->setName($name);
            $config->setType($options['type']);
            $config->setBlock($block);

            if (isset($options['options'])) {
                $config->setOptions($options['options']);
            }

            $return []= $config;
        }

        return $return;
    }


    public function reorderBlocks($page, $ids) {
        if (!is_array($ids)) {
            return;
        }
        $position = 0;
        $orm = $this->orm;
        array_walk($ids, function ($id) use ($orm, &$position) {
            $block = $orm
                ->getRepository('Raindrop\PageBundle\Entity\Block')
                ->find($id);
            $block->setPosition($position);
            $position++;
        });
    }

    public function moveBlock($id, $layout) {
        $orm = $this->orm;
        $block = $orm
                ->getRepository('Raindrop\PageBundle\Entity\Block')
                ->find($id);
        $block->setLayout($layout);
        $orm->flush();
    }

    public function removeBlock($id) {
        $block = $this->orm
                ->getRepository('Raindrop\PageBundle\Entity\Block')
                ->find($id);
        $this->orm->remove($block);
        $this->orm->flush();

        return true;
    }
}