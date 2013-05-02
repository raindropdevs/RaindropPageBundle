<?php

namespace Raindrop\PageBundle\Admin\Providers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Description of ServicesProvider
 *
 * @author teito
 */
class ServicesProvider
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function provide()
    {
        $collector = $this->container->get('raindrop_page.container.data.collector');
        $collector->collect(new Request, new Response);
        $services = $collector->getServices();

        array_walk($services, function ($service, $key) use (&$services) {
            $services[$key] = $key;
        });

        return $services;
    }
}
