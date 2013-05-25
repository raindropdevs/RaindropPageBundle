<?php

namespace Raindrop\PageBundle\Manager;

use Raindrop\PageBundle\Entity\Block;
use Raindrop\PageBundle\Entity\BlockConfig;
use Raindrop\PageBundle\Entity\BlockVariable;
use Doctrine\Common\Collections\ArrayCollection;

class BlockManager
{
    protected $orm;

    public function __construct($orm, $pageManager)
    {
        $this->orm = $orm;
        $this->pageManager = $pageManager;
    }

    public function createBlock($page, $block_config_name, $block_layout_position)
    {
        $block = new Block;
        $this->orm->persist($block);

        if (preg_match('/block-([0-9]+)/', $block_layout_position, $m)) {
            $parent_block_id = $m[1];
            $parent_block = $this->orm->getRepository('RaindropPageBundle:Block')
                    ->find($parent_block_id);
            $block->setParent($parent_block);
        } else {
            $block->setPage($page);
        }



        $blockConfig = $this->getBlockConfig($block_config_name);

        if (!$blockConfig) {
            throw new \Exception("Block configuration named {$block_config_name} not found!");
        }

        $block->setName($block_config_name);
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

    public function findBlockByPageAndName($page, $name)
    {
        return $this->orm->getRepository('RaindropPageBundle:Block')
            ->findOneByPageAndName($page, $name);
    }

    protected function getBlockConfig($block_config_name)
    {
        $repo = $this->orm->getRepository('Raindrop\PageBundle\Entity\BlockConfig');

        return $repo->findOneByName($block_config_name);
    }

    protected function createBlockVariables($blockConfig, $block)
    {
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

    public function reorderBlocks($page, $ids)
    {
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

    public function moveBlock($id, $layout)
    {
        $orm = $this->orm;
        $block = $orm
                ->getRepository('Raindrop\PageBundle\Entity\Block')
                ->find($id);
        $block->setLayout($layout);
        $orm->flush();
    }

    public function removeBlock($id)
    {
        $block = $this->orm
                ->getRepository('Raindrop\PageBundle\Entity\Block')
                ->find($id);
        $this->orm->remove($block);
        $this->orm->flush();

        return true;
    }
}
