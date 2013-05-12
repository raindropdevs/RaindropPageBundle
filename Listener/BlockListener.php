<?php

namespace Raindrop\PageBundle\Listener;

use Raindrop\PageBundle\Entity\Block;
use Doctrine\ORM\Event\LifecycleEventArgs;

class BlockListener {
    public function __construct($resolver)
    {
        $this->resolver = $resolver;
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        if ($entity instanceof Block) {
            $entity->setResolver($this->resolver);
        }
    }
}