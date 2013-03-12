<?php

namespace Raindrop\PageBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class PageAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
                ->add('name', null, array('required' => true))
                ->add('layout', null, array('required' => true))
                ->add('children', 'sonata_type_collection', array(), array(
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable'  => 'position'
                ))
                ->add('route', null, array('required' => true))
        ;
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
            ->addIdentifier('route.path', null, array('label' => 'route name'))
            ->addIdentifier('layout')

            // add custom action links
            ->add('_action', 'actions', array(
                'actions' => array(
                    'view' => array(),
                    'edit' => array()
                )
            ))
        ;
    }

    public function preUpdate($page)
    {
        $page->setController('RaindropPageBundle:Page:index');
    }

    public function postUpdate($page)
    {
        $route = $page->getRoute();
        $route->setContent($page);
        $this->modelManager->getEntityManager($route)->flush();
    }
}