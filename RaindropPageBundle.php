<?php

namespace Raindrop\PageBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Raindrop\PageBundle\DependencyInjection\Compiler\AddFormFieldsCompiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;

class RaindropPageBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->loadFromExtension('twig', array(
            'form' => array(
                'resources' => array(
                    'RaindropPageBundle:Form:fields.html.twig',
                ),
            ),
        ));
    }
}
