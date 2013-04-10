<?php

namespace Raindrop\PageBundle\Manager;

use Raindrop\PageBundle\Entity\Block;
use Raindrop\PageBundle\Entity\BlockConfig;
use Raindrop\PageBundle\Entity\BlockVariable;
use Doctrine\Common\Collections\ArrayCollection;

class PageManager {

    protected $orm, $twigEntityClass;

    public function __construct($orm, $twigEntityClass) {
        $this->orm = $orm;
        $this->twigEntityClass = $twigEntityClass;
    }

    public function updatePageLayoutTimestamp($page) {
        // update template last modified as the content has changed
        $layout = $page->getLayout();

        // @TODO: fix this shit :)
        $params = explode(':', $layout);
        if ($params[0] == 'database') {
            $templateRepo = $this->orm
                ->getRepository($this->twigEntityClass);
            $tpl = $templateRepo->findOneByName($params[1]);
            $tpl->setUpdatedValue();
            $this->orm->flush();
        }
    }
}