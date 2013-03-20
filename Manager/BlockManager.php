<?php

namespace Raindrop\PageBundle\Manager;

use Raindrop\PageBundle\Entity\Block;
use Raindrop\PageBundle\Entity\BlockConfig;
use Raindrop\PageBundle\Entity\BlockVariable;
use Doctrine\Common\Collections\ArrayCollection;

class BlockManager {

    protected $orm;

    public function __construct($orm) {
        $this->orm = $orm;
    }

    public function createBlock($page, $type) {
        $block = new Block;
        $block->setName($type);
        $block->setPage($page);

        $blockConfig = $this->getBlockConfig($type);
        $block->setTemplate($blockConfig->getTemplate());

        $variables = $this->createBlockVariables($blockConfig);
        $block->setVariables($variables);

        $variables->forAll(function ($index, $variable) use ($page, $block) {
            $variable->setBlock($block);
        });

        $this->orm->persist($block);
        $this->orm->flush();

        return $block;
    }

    protected function getBlockConfig($type) {
        $repo = $this->orm->getRepository('Raindrop\PageBundle\Entity\BlockConfig');
        return $repo->findOneByType($type);
    }

    protected function createBlockVariables($blockConfig) {
        $return = new ArrayCollection;

        foreach ($blockConfig->getOptions() as $name => $options) {
            $config = new BlockVariable;
            $config->setName($name);
            $config->setType($options['type']);
            $config->setOptions($options['options']);

            $return []= $config;
        }

        return $return;
    }
}