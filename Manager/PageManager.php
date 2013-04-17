<?php

namespace Raindrop\PageBundle\Manager;

use Raindrop\PageBundle\Entity\Block;
use Raindrop\PageBundle\Entity\BlockConfig;
use Raindrop\PageBundle\Entity\BlockVariable;
use Doctrine\Common\Collections\ArrayCollection;
use Raindrop\TwigLoaderBundle\Loader\DatabaseTwigLoader;

class PageManager {

    protected $orm, $twigEntityClass;

    public function __construct($orm, $twigEntityClass) {
        $this->orm = $orm;
        $this->twigEntityClass = $twigEntityClass;
    }

    public function updatePageLayoutTimestamp($page) {
        // update template last modified as the content has changed
        $layout = $page->getLayout();

        if (substr($layout, 0, strlen(DatabaseTwigLoader::DATABASE_ID)) == DatabaseTwigLoader::DATABASE_ID) {
            $templateName = substr($layout, strlen(DatabaseTwigLoader::DATABASE_ID));
            $templateRepo = $this
                ->orm
                ->getRepository($this->twigEntityClass);
            $tpl = $templateRepo->findOneByName($templateName);
            if ($tpl) {
                $tpl->setUpdatedValue();
                $this->orm->flush();
            }
        }
    }
}