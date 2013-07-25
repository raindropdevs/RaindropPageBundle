<?php

namespace Raindrop\PageBundle\Manager;

use Raindrop\TwigLoaderBundle\Loader\DatabaseTwigLoader;
use Raindrop\PageBundle\Entity\PageTag;
use Raindrop\RoutingBundle\Entity\Route;

class PageManager
{
    protected $orm, $twigEntityClass;

    public function __construct($orm, $twigEntityClass)
    {
        $this->orm = $orm;
        $this->twigEntityClass = $twigEntityClass;
    }

    public function setRouteContentResolver($resolver)
    {
        $this->resolver = $resolver;
    }

    public function getResolver()
    {
        return $this->resolver;
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
        if (count($previous)) {
            foreach ($previous as $prev) {
                if ($prev->getName() == $name) {
                    return false;
                }
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

    public function getPagesTaggedBy($tag, $country)
    {
        return $this->getRepository()->getPagesByTag($tag, $country);
    }

    public function getOnePageTaggedBy($tag, $country)
    {
        return $this->getRepository()->getSinglePageByTag($tag, $country);
    }

    public function clonePageToUrl($page, $url)
    {
        $newPage = $this->getRepository()->clonePage($page);

        $routeRepo = $this->orm
                ->getRepository('RaindropRoutingBundle:Route');

        $route = $routeRepo
                ->findOneBy(array(
                    'path' => $url
                ));

        if (!$route) {
            $route = new Route;
            $this->orm->persist($route);
            $route->setPath($url);
            $route->setNameFromPath();
            $route->setController($page->getRoute()->getController());
        }

        $this->orm->flush();

        $route->setResolver($this->getResolver());
        $newPage->setRoute($route);

        $this->verifyParentPageRelation($newPage);

        $this->orm->flush();

        return $newPage;
    }

    public function verifyParentPageRelation($page)
    {
        if (
                $page->hasParent() &&
                dirname($page->getRoute()->getPath()) == $page->getParent()->getRoute()->getPath()
                ) {
            return;
        }

        $path = dirname($page->getRoute()->getPath());

        $parentRoute = $this
            ->orm
            ->getRepository('RaindropRoutingBundle:Route')
            ->findOneBy(array(
                'path' => $path
            ));

        if ($parentRoute) {
            $parentPage = $parentRoute->getContent();
            if ($parentPage instanceof Page) {
                $page->setParent($parentPage);
            }
        }
    }
}
