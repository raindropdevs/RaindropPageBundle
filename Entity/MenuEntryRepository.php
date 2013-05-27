<?php

namespace Raindrop\PageBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Repository to load menu
 */
class MenuEntryRepository extends EntityRepository
{
    public function getByPageAndMenu($page, $menu)
    {
        $query = $this->createQueryBuilder('m')
            ->select('m')
            ->where('m.page = :page AND m.menu = :menu')
            ->setParameters(array(
                'page' => $page,
                'menu' => $menu
            ))
        ;

        return $query->getQuery()->getOneOrNullResult();
    }
}
