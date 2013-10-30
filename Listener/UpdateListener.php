<?php

namespace Raindrop\PageBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Raindrop\PageBundle\Entity\BlockVariable;
/**
 * UpdateListener
 */
class UpdateListener
{
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        if ($entity instanceof BlockVariable) {
            $entity->getBlock()->setUpdated(new \DateTime);   
            
            $entity->getBlock()->getPage()->setUpdated(new \DateTime);   
            $entityManager->persist($entity->getBlock()->getPage());
            $entityManager->flush();
        }
    }
}