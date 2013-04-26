<?php

namespace Raindrop\PageBundle\Resolver;

use Raindrop\PageBundle\Resolver\ResolverInterface;

/**
 * Description of ServiceResolver
 *
 * @author teito
 */
class ServiceResolver implements ResolverInterface {

    public function __construct($container) {
        $this->container = $container;
    }

    public function resolve($variable) {
        $service_id = $variable->getContent();
        if ($service_id) {
            return $this->container->get($service_id);
        }
    }
}

?>
