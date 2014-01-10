<?php

namespace Raindrop\PageBundle\Admin\Providers;

/**
 * This class provides layout for the admin edit page.
 */
class RouteProvider
{
    protected $repository;

    public function __construct($repository, $container)
    {
        $this->repository = $repository;
        $this->container = $container;
    }

    protected function createQueryForAllChildren($path)
    {
        return $this->repository
                ->createQueryBuilder('r')
                ->select('r.name', 'r.path')
                ->where('r.path like :path')
                ->setParameter('path', $path . '%')
            ;
    }

    public function provide()
    {
        $result = $this->repository
            ->createQueryBuilder('r')
            ->where('r.locale = :locale')
            ->setParameter('locale', $this->container->get('session')->get('raindrop_locale'))
            ->orderBy('r.path', 'ASC')
            ->getQuery()
            ->getResult();

        $return = array();

        array_walk($result, function (&$el) use (&$return) {
            $return[$el->getName()] = $el->getPath();
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
