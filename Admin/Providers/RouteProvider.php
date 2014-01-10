<?php

namespace Raindrop\PageBundle\Admin\Providers;

/**
 * This class provides layout for the admin edit page.
 */
class RouteProvider
{
    protected $repository;

    public function __construct($route_repository, $page_repository, $container)
    {
        $this->route_epository = $route_repository;
        $this->page_repository = $page_repository;
        $this->container = $container;
    }

    protected function createQueryForAllChildren($path)
    {
        return $this->route_repository
                ->createQueryBuilder('r')
                ->select('r.name', 'r.path')
                ->where('r.path like :path')
                ->setParameter('path', $path . '%')
            ;
    }

    public function provide()
    {
        $result = $this->page_repository
            ->createQueryBuilder('p')
            //->select('r, p')
            ->leftJoin('p.route', 'r')
            ->where('p.country = :country')
            ->setParameter('country', $this->container->get('session')->get('raindrop:admin:country'))
            ->orderBy('r.path', 'ASC')
            ->getQuery()
            ->getResult();

        $return = array();

        array_walk($result, function (&$el) use (&$return) {
            $return[$el->getRoute()->getName()] = $el->getRoute()->getPath();
        });

        return $return;
    }

    public function databaseTemplateChoiceList()
    {
        $templates = $this
            ->repository->findByType('block')
        ;

        $return = array();

        array_walk($templates, function ($template) use (&$return) {
            $return['database:' . $template->getName()] = $template->getName();
        });

        return $return;
    }
}
