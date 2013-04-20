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
                ->getTemplateRepository();
            $tpl = $templateRepo->findOneByName($templateName);
            if ($tpl) {
                $tpl->setUpdatedValue();
                $this->orm->flush();
            }
        }
    }

    public function getTemplateRepository() {
        return $this->orm->getRepository($this->twigEntityClass);
    }

    public function getRepository() {
        return $this->orm->getRepository('RaindropPageBundle:Page');
    }

    public function getPagesForMenu($menu) {
        return $this->getRepository()->getPagesForMenu($menu);
    }

    public function getCurrentMenu($name, $country) {
        return $this->getRepository()->getCurrentMenu($name, $country);
    }

    public function getCountryPages($country) {
        $q = $this->getRepository()->createQueryBuilder('m')
            ->select('m')
            ->where('m.country = :country AND m.route is not null')
            ->setParameter('country', $country)
        ;
        return $q->getQuery()->getResult();
    }
}
