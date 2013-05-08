<?php

namespace Raindrop\PageBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\Container;
use Sonata\AdminBundle\Route\RouteCollection;

class MenuEntryAdmin extends Admin
{
    protected $container;

    public function setContainer($container)
    {
        $this->container = $container;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $label = $this->getSubject()->getLabelString();

        $formMapper
            ->add('label', null, array(
                'required' => false,
                'data' => $label
            ))
            ->add('position', null, array('required' => false))
            ->add('file', 'file', array('required' => false))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('label')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('label', null, array(
                'template' => 'RaindropPageBundle:Menu:menu_admin_list_label.html.twig'
            ))
            ->addIdentifier('image', null, array(
                'template' => 'RaindropPageBundle:Menu:menu_admin_list_image.html.twig'
            ))
        ;
    }

    public function preUpdate($menuEntry)
    {
        $this->saveFile($menuEntry);
    }

    public function prePersist($menuEntry)
    {
        $this->saveFile($menuEntry);
    }

    public function saveFile($menuEntry) {
        $appBasePath = dirname($this->container->getParameter('kernel.root_dir'));
        $menuEntry->upload($appBasePath);
    }
}
