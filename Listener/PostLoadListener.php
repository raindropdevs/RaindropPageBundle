<?php

namespace Raindrop\PageBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Raindrop\PageBundle\Entity\Block;

class PostLoadListener {

    protected $variablesResolver;

    public function __construct($variablesResolver) {
        $this->variablesResolver = $variablesResolver;
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Block) {
            /**
             * Use entity manager to retrieve related entity
             */
            $entity->setResolver($this->variablesResolver);
        }
    }
}