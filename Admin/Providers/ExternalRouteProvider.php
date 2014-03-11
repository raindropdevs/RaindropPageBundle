<?php

namespace Raindrop\PageBundle\Admin\Providers;

/**
 * Description of ExternalRouteProvider
 *
 * @author teito
 */
class ExternalRouteProvider
{
    protected $repository;

    public function __construct($repository)
    {
        $this->repository = $repository;
    }

    public function provide()
    {
        $routes = $this->repository->findBy(
            array(),
            array('uri' => 'ASC')
        );

        $return = array();

        array_walk($routes, function ($el) use (&$return) {
            $return[$el->getId()] = $el->getUri();
        });

        return $return;
    }
}
