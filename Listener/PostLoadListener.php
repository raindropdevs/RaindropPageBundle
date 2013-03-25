<?php

namespace Raindrop\PageBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Raindrop\PageBundle\Entity\Block;

class PostLoadListener {

    protected $emBound = false;

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        if ($entity instanceof Block) {

            /**
             * This binding is here to avoid circular dependency.
             * Check if resolver is already bound to an entityManager
             * and, in case, assign it.
             */
            $entity->setEntityManager($entityManager);
        }
    }
}