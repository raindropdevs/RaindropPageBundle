<?php

namespace Raindrop\PageBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Raindrop\PageBundle\Entity\BlockVariable;
use Raindrop\PageBundle\Entity\Block;

/**
 * UpdateListener
 */
class UpdateListener
{
    //post update for block variables, blocks
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
       
        if ($entity instanceof Block) {
            if($entity->getPage()) {
                $entity->getPage()->setUpdated(new \DateTime);   
                $entityManager->persist($entity->getPage());
                $entityManager->flush();                
            }
        }        
    }
    
    //post persist for block variables, blocks
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();
        
        if ($entity instanceof BlockVariable) {
            $entity->getBlock()->setUpdated(new \DateTime);   
            
            $entity->getBlock()->getPage()->setUpdated(new \DateTime);   
            $entityManager->persist($entity->getBlock()->getPage());
            $entityManager->flush();
        }        
        
        if ($entity instanceof Block) {
            if($entity->getPage()) {
                $entity->getPage()->setUpdated(new \DateTime);   
                $entityManager->persist($entity->getPage());
                $entityManager->flush();                
            }
        }
    }
}