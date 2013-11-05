<?php
 
namespace Raindrop\PageBundle\Listener;
 
use Doctrine\ORM\Event\OnFlushEventArgs;
use Raindrop\PageBundle\Entity\BlockVariable;
use Raindrop\PageBundle\Entity\Block;
 
/**
 * UpdateListener
 */
class UpdateListener
{
    /**
     * Handle Doctrine onFlush event
     *
     * @param OnFlushEventArgs $args "On Flush" event arguments
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $entityManager = $args->getEntityManager();
        $uow = $entityManager->getUnitOfWork();
 
        $entities = array_merge(
            $uow->getScheduledEntityInsertions(),
            $uow->getScheduledEntityUpdates(),
            $uow->getScheduledEntityDeletions()
        );
 
        foreach ($entities as $entity) {
            if ($entity instanceof BlockVariable) {
                $block = $entity->getBlock();
                $page = $block->getPage();
 
                $block->setUpdated(new \DateTime);
                $page->setUpdated(new \DateTime);
                $entityManager->persist($page);
 
                $classMetadata = $entityManager->getClassMetadata(get_class($page));
                $uow->computeChangeSet($classMetadata, $page);
            }
 
            if ($entity instanceof Block) {
                if ($entity->getPage()) {
                    $page = $entity->getPage();
                    $page->setUpdated(new \DateTime);
                    $entityManager->persist($page);
                    $classMetadata = $entityManager->getClassMetadata(get_class($page));
                    $uow->computeChangeSet($classMetadata, $page);
                }
            }
        }
    }
}