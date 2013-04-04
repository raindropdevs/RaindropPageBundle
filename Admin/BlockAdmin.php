<?php

namespace Raindrop\PageBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Raindrop\PageBundle\Form\EventListener\AddAssetsFieldSubscriber;

class BlockAdmin extends Admin
{
    protected $blockService;

    public function setBlockService($blockService) {
        $this->blockService = $blockService;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
                ->add('name', null, array('required' => true))
        ;

        $formMapper
            ->add('variables', 'sonata_type_collection',
                array(
                    'by_reference' => false
                ), array(
                    'edit' => 'inline',
                    'inline' => 'table'
                ))
            ;

        $builder = $formMapper->getFormBuilder();
        $builder->addEventSubscriber(new AddAssetsFieldSubscriber($builder->getFormFactory()));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->addIdentifier('page')
            ->addIdentifier('template')
        ;
    }

    /**
     * Filter empty input
     *
     * @param type $block
     * @return type
     */
    public function preUpdate($block)
    {
        $jss = array_filter($block->getJavascripts(), function ($js) {
            return !empty($js);
        });

        $css = array_filter($block->getStylesheets(), function ($cs) {
            return !empty($cs);
        });

        $block->setJavascripts($jss);
        $block->setStylesheets($css);
    }
}