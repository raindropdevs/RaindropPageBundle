<?php

namespace Raindrop\PageBundle\Resolver;

use Raindrop\PageBundle\Resolver\ResolverInterface;

/**
 * Description of SimpleResolver
 *
 * @author teito
 */
class SimpleResolver implements ResolverInterface
{
    //put your code here
    public function resolve($variable)
    {
        return $variable->getContent();
    }
}
