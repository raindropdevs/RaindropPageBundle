<?php

namespace Raindrop\PageBundle\Admin\Providers;

/**
 * This class provides layout for the admin edit page.
 */
class LayoutProvider
{
    protected $repository;

    public function __construct($repository)
    {
        $this->repository = $repository;
    }

    protected function getAll()
    {
        $result = $this->createQueryForAll()
                ->getQuery()
                ->getScalarResult();

        array_walk($result, function (&$el) {
            $el = $el['name'];
        });

        return $result;
    }

    protected function createQueryForAll()
    {
        return $this->repository
                ->createQueryBuilder('t')
                ->select('t.name')
                ->where('t.type = :type')
                ->setParameter('type', 'layout')
            ;
    }

    public function provide()
    {
        $rawList = $this->getAll();

        $return = array();
        array_walk($rawList, function ($value) use (&$return) {
            $return['database:' . $value] = $value;
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
