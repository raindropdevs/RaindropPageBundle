<?php

namespace Raindrop\PageBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class PageAdmin extends Admin
{
    protected $container, $layoutProvider;

    public function setContainer($container) {
        $this->container = $container;
    }

    public function setLayoutProvider($layoutProvider) {
        $this->layoutProvider = $layoutProvider;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
                ->add('name', null, array('required' => true))
                ->add('layout', 'choice', array(
                    'required' => true,
                    'choices' => $this->layoutProvider->provide()
                ))
                ->add('url', 'text', array('required' => true, 'property_path' => false))
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

    public function getTemplate($name)
    {
        switch ($name) {
            case 'edit':
                return 'RaindropPageBundle:Page:page_editor.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }

    public function preUpdate($page)
    {
        // make sure route points to proper controller
        // @TODO: make this parameter configurable
        $page->setController('RaindropPageBundle:Page:index');

        // update template last modified as the content has changed
        $layout = $page->getLayout();
        $orm = $this->container->get('doctrine.orm.default_entity_manager');
        $templateClass = $this->container->getParameter('raindrop_twig_loader_bundle.entity_class');
        $templateRepo = $orm
            ->getRepository($templateClass);

        // @TODO: fix this shit :)
        $params = explode(':', $layout);
        if ($params[0] == 'database') {
            $tpl = $templateRepo->findOneByName($params[1]);
            $tpl->setUpdated(new \DateTime);
            $orm->persist($tpl);
            $orm->flush();
        }
    }

    public function postUpdate($page)
    {
        $route = $page->getRoute();
        $route->setContent($page);
        $this->modelManager->getEntityManager($route)->flush();
    }
}