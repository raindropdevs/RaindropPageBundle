<?php

namespace Raindrop\PageBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Repository to load pages using Doctrine ORM
 *
 */
class PageRepository extends EntityRepository
{
    public function getPagesForMenu($menu)
    {
        $q =
        $this->createQueryBuilder('p')
            ->select('p', 'm')
            ->leftJoin('p.menus', 'm')
            ->where('m.menu = :menu')
            ->setParameter('menu', $menu)
            ->orderBy('m.position', 'ASC')
        ;

        return $q->getQuery()->getResult();
    }

    public function getCurrentMenu($name, $country)
    {
        $q =
        $this->createQueryBuilder('p')
            ->select('p', 'm', 'n')
            ->leftJoin('p.menus', 'm')
            ->leftJoin('m.menu', 'n')
            ->where('n.name = :name AND n.country = :country')
            ->setParameter('name', $name)
            ->setParameter('country', $country)
            ->orderBy('m.position', 'ASC')
        ;

        return $q->getQuery()->getResult();
    }

    public function findByCountryWithMenu($country)
    {
        $q =
        $this->createQueryBuilder('p')
            ->select('p', 'm', 'r')
            ->leftJoin('p.menus', 'm')
            ->leftJoin('p.route', 'r')
            ->where('p.country = :country')
            ->setParameter('country', $country)
        ;

        return $q->getQuery()
            ->getResult();
    }

    public function findEager($id)
    {
        $q =
        $this->createQueryBuilder('p')
            ->select('p', 'b', 'v')
            ->leftJoin('p.blocks', 'b')
            ->leftJoin('b.variables', 'v')
            ->where('p.id = :id')
            ->setParameter('id', $id)
        ;

        return $q->getQuery()
            ->getOneOrNullResult();
    }

    public function getPagesForSection($section, $country)
    {
        return $this->findBy(array(
            'type' => $section,
            'country' => $country
        ));
    }

    public function getSiblingsPagesForSection($section, $country, $page, $excludeSelf = false)
    {
        $q =
        $this->createQueryBuilder('p')
            ->select('p')
            ->leftJoin('p.route', 'r')
            ->where('p.type = :type AND p.country = :country AND r.locale = :locale')
            ->setParameter('type', $section)
            ->setParameter('country', $country)
            ->setParameter('locale', $page->getRoute()->getLocale())
        ;

        $pages = $q->getQuery()->getResult();

        $return = array_filter($pages, function ($el) use ($page, $excludeSelf) {
            if ($excludeSelf) {
                return ($el->getPageDepth() == $page->getPageDepth() && $el->getId() != $page->getId());
            } else {
                return $el->getPageDepth() == $page->getPageDepth();
            }
        });

        return array_values($return);
    }

    public function getChildrenPagesForSection($section, $country, $page)
    {
        $q =
        $this->createQueryBuilder('p')
            ->select('p')
            ->leftJoin('p.route', 'r')
            ->where('p.type = :type AND p.country = :country AND p.id != :id AND r.locale = :locale')
            ->setParameter('type', $section)
            ->setParameter('country', $country)
            ->setParameter('locale', $page->getRoute()->getLocale())
            ->setParameter('id', $page->getId())
        ;

        $pages = $q->getQuery()->getResult();

        $return = array_filter($pages, function ($el) use ($page) {
            return ($el->getPageDepth() == $page->getPageDepth() + 1);
        });

        return array_values($return);
    }

    public function getChildrenPagesForSectionWithMenu($section, $country, $page)
    {
        $q =
        $this->createQueryBuilder('p')
            ->select('p')
            ->leftJoin('p.route', 'r')
            ->leftJoin('p.menus', 'm')
            ->leftJoin('m.menu', 'n')
            ->where('p.type = :type AND n.country = :country AND p.id != :id AND r.locale = :locale')
            ->setParameter('type', $section)
            ->setParameter('country', $country)
            ->setParameter('locale', $page->getRoute()->getLocale())
            ->setParameter('id', $page->getId())
        ;

        $pages = $q->getQuery()->getResult();

        $return = array_filter($pages, function ($el) use ($page) {
            return ($el->getPageDepth() == $page->getPageDepth() + 1);
        });

        foreach ($pages as $child) {
            if ($child->getPageDepth() == $page->getPageDepth() + 1) {
                $menus = $child->getMenus();
                $return [$menus[0]->getPosition()] = $child;
            }
        }

        return array_values($return);
    }
}
