<?php

namespace Raindrop\PageBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\Container;
use Sonata\AdminBundle\Route\RouteCollection;

class MenuAdmin extends Admin
{
    protected $container;

    public function setContainer($container) {
        $this->container = $container;
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'edit':
                return 'RaindropPageBundle:Menu:menu_editor.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('append');
        $collection->add('reorder');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', null, array('required' => true))
            ->add('country', null, array('required' => true))
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
            ->addIdentifier('country')
        ;
    }

    public function preUpdate($variable)
    {
    }

    public function postUpdate($variable)
    {
    }
}