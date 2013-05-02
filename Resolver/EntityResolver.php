<?php

namespace Raindrop\PageBundle\Resolver;

use Raindrop\PageBundle\Resolver\ResolverInterface;
/**
 * Description of EntityResolver
 *
 * @author teito
 */
class EntityResolver implements ResolverInterface
{
    public function __construct($container)
    {
        $this->container = $container;
    }

    //put your code here
    public function resolve($variable)
    {
        $orm = $this->container
                ->get('doctrine.orm.default_entity_manager');

        $options = $variable->getOptions();
        if ($variable->getContent()) {
            return $orm
                    ->getRepository($options['model'])
                    ->find($variable->getContent());
        }

        return null;
    }
}
