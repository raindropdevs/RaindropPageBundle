<?php

namespace Raindrop\PageBundle\Manager;

use Raindrop\TwigLoaderBundle\Loader\DatabaseTwigLoader;
use Raindrop\PageBundle\Entity\PageTag;

class PageManager
{
    protected $orm, $twigEntityClass;

    public function __construct($orm, $twigEntityClass)
    {
        $this->orm = $orm;
        $this->twigEntityClass = $twigEntityClass;
    }

    public function updatePageLayoutTimestamp($page)
    {
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

    public function getTemplateRepository()
    {
        return $this->orm->getRepository($this->twigEntityClass);
    }

    public function getRepository()
    {
        return $this->orm->getRepository('RaindropPageBundle:Page');
    }

    public function getPagesForMenu($menu)
    {
        return $this->getRepository()->getPagesForMenu($menu);
    }

    public function getCurrentMenu($name, $country)
    {
        return $this->getRepository()->getCurrentMenu($name, $country);
    }

    public function getCountryPages($country)
    {
        $q = $this->getRepository()->createQueryBuilder('m')
            ->select('m')
            ->where('m.country = :country AND m.route is not null')
            ->setParameter('country', $country)
        ;

        return $q->getQuery()->getResult();
    }

    public function searchPagesByCountryAndPath($country, $path)
    {
        $q = $this->getRepository()->createQueryBuilder('m')
            ->select('m')
            ->leftJoin('m.route', 'r')
            ->where('r.path LIKE :path AND m.country = :country')
            ->setParameter('path', '%' . $path . '%')
            ->setParameter('country', $country)
        ;

        return $q->getQuery()->getResult();
    }

    /**
     * Add a tag string to a page
     * @param string $name
     * @param Raindrop\PageBundle\Entity\Page $page
     * @return boolean (tag gets added if not already present)
     */
    public function addTagToPage($name, $page)
    {
        $previous = $page->getTags();

        // check for previous tagging
        foreach ($previous as $prev) {
            if ($prev->getName() == $name) {
                return false;
            }
        }

        $tagRepo = $this->orm->getRepository('RaindropPageBundle:PageTag');
        $tag = $tagRepo->findOneBy(array(
            'name' => $name
        ));

        if (!$tag) {
            $tag = new PageTag;
            $tag->setName($name);
        }

        $page->addTag($tag);

        $this->orm->flush();

        return true;
    }
}
