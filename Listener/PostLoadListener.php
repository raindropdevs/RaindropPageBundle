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
             * Use entity manager to retrieve related entity
             */
            $entity->setEntityManager($entityManager);
        }
    }
}