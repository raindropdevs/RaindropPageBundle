<?php

namespace Raindrop\PageBundle\Block;

use Symfony\Component\HttpFoundation\Response;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BaseBlockService;

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
    public function execute(BlockInterface $block, Response $response = null)
    {
        $settings = $this->getSettings($block);
        $settings['block'] = $block;

        return $this->renderResponse($block->getTemplate(), $settings, $response);
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
