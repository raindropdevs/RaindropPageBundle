<?php

namespace Raindrop\PageBundle\Resolver;

use Raindrop\PageBundle\Resolver\ResolverInterface;

/**
 * Description of ArrayResolver
 */
class ArrayResolver implements ResolverInterface
{
    public function resolve($variable)
    {
        return unserialize($variable->getContent());
    }
}
