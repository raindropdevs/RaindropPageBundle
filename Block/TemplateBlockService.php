<?php

namespace Raindrop\PageBundle\Block;

use Symfony\Component\HttpFoundation\Response;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\BaseBlockService;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 *
 * @author Matteo Caberlotto <mcaber@gmail.com>
 */
class TemplateBlockService extends BaseBlockService
{
    protected $resolver;

    public function setResolver($resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        return $this->renderResponse(
            $blockContext->getBlock()->getTemplate(),
            $blockContext->getBlock()->getVariablesArray(),
            $response
        );
    }

    public function getSettings($block)
    {
        return array_merge($this->getDefaultSettings(), $this->resolver->resolve($block->getVariables()));
    }

    /**
     * {@inheritdoc}
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
        // TODO: Implement validateBlock() method.
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        foreach ($block->getVariables() as $type) {
            $formMapper->add($type->getName(), $type->getType(), $type->getOptions());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Raindrop Block';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultSettings()
    {
        return array(
            'template' => 'RaindropPageBundle:Default:default_template.html.twig',
            'content' => null
        );
    }
}
