<?php

namespace Raindrop\PageBundle\Admin\Providers;

class LayoutProvider {

    protected $repository;

    public function __construct($repository) {
        $this->repository = $repository;
    }

    protected function getAll() {
        $result = $this->createQueryForAll()
                ->getQuery()
                ->getScalarResult();

        array_walk($result, function (&$el) {
            $el = $el['name'];
        });

        return $result;
    }

    protected function createQueryForAll() {
        return $this->repository
                ->createQueryBuilder('t')
                ->select('t.name')
                ->where('t.type = :type')
                ->setParameter('type', 'layout')
            ;
    }

    public function provide() {
        return $this->getAll();
    }
}
