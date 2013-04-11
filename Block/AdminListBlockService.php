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
class AdminListBlockService extends BaseBlockService
{
    /**
     * {@inheritdoc}
     */
    public function execute(BlockInterface $block, Response $response = null)
    {
        $settings = array_merge($this->getDefaultSettings(), $block->getSettings());

        return $this->renderResponse($settings['template'], array(
            'block'     => $block,
            'settings'  => $settings
        ), $response);
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
        return 'Raindrop Admin Block';
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
