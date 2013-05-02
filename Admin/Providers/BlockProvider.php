<?php

namespace Raindrop\PageBundle\Admin\Providers;

/**
 * This class is used to provide source blocks when configuring page layout.
 * Blocks will be available for drag/drop into page.
 */
class BlockProvider
{
    protected $repository;

    public function __construct($em, $class)
    {
        $this->repository = $em->getRepository($class);
    }

    public function provide()
    {
        return $this->repository->findAll();
    }
}
