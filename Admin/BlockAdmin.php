<?php

namespace Raindrop\PageBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

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
//                ->add('page', null, array('required' => false))
//                ->add('template', null, array('required' => true))
//                ->add('parent', null, array('required' => false))
        ;

        $formMapper
            ->add('variables', 'sonata_type_collection',
                array(
                    'by_reference' => false
                ), array(
                    'edit' => 'inline',
                    'inline' => 'table'
                ));

//        foreach ($this->getSubject()->getVariables() as $variable) {
//            $formMapper
//                ->add('content', $variable->getType(), $variable->getOptions());
//        }
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

            // add custom action links
//            ->add('_action', 'actions', array(
//                'actions' => array(
//                    'view' => array(),
//                    'edit' => array()
//                )
//            ))
        ;
    }

//    public function preUpdate($page)
//    {
//        $page->setController('RaindropPageBundle:Page:index');
//    }
//
//    public function postUpdate($page)
//    {
//        $route = $page->getRoute();
//        $route->setContent($page);
//        $this->modelManager->getEntityManager($route)->flush();
//    }
}