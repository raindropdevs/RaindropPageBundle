<?php

namespace Raindrop\PageBundle\Manager;

use Raindrop\PageBundle\Entity\MenuEntry;

class MenuManager
{
    protected $orm, $classname, $repository;

    public function __construct($orm, $classname)
    {
        $this->orm = $orm;
        $this->classname = $classname;

        $this->repository = $orm->getRepository($classname);
    }

    public function appendPageToMenu($page_id, $menu_id)
    {
        $menu = $this
            ->orm->getRepository('RaindropPageBundle:Menu')
            ->find($menu_id)
            ;

        $page = $this
            ->orm->getRepository('RaindropPageBundle:Page')
            ->find($page_id)
            ;

        if (!$menu or !$page) {
            throw new Exception('Menu or page not found.');
        }

        $menuEntry = $this
                ->findMenuItemByPageAndMenu($menu, $page);

        if (!$menuEntry) {
            $menuEntry = new MenuEntry;
            $this->orm->persist($menuEntry);
        }

        $menuEntry->setPage($page);
        $menuEntry->setMenu($menu);
        $this->orm->flush();

        return true;
    }

    public function reorderItems($ids)
    {
        $position = 0;
        $repository = $this->repository;
        array_walk($ids, function ($id) use ($repository, &$position) {
            $menu = $repository
                ->find($id);
            $menu->setPosition($position);
            $position++;
        });
        $this->orm->flush();
    }

    public function findMenuItemByPageAndMenu($menu, $page)
    {
        $query = $this->repository->createQueryBuilder('m')
                ->select('m')
                ->where('m.page = :page AND m.menu = :menu')
                ->setParameters(array(
                    'page' => $page,
                    'menu' => $menu
                ))
            ;

        return $query->getQuery()->getResult();
    }
}
