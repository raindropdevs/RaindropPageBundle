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
            ->leftJoin('p.menus', 'm')
            ->leftJoin('m.menu', 'n')
            ->where('n.name = :name AND n.country = :country')
            ->setParameter('name', $name)
            ->setParameter('country', $country)
            ->orderBy('m.position', 'ASC')
        ;

        return $q->getQuery()->getResult();
    }
}
