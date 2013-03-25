<?php

namespace Raindrop\PageBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Raindrop\PageBundle\Form\EventListener\AddRouteFieldSubscriber;
use Raindrop\PageBundle\Form\EventListener\AddMetaFieldSubscriber;
use Raindrop\RoutingBundle\Entity\Route;

class PageAdmin extends Admin
{
//    public $supportsPreviewMode = true;

    protected $container, $layoutProvider, $blockProvider;

    public function setContainer($container) {
        $this->container = $container;
    }

    public function setLayoutProvider($layoutProvider) {
        $this->layoutProvider = $layoutProvider;

        return $this;
    }

    public function setBlockProvider($blockProvider) {
        $this->blockProvider = $blockProvider;

        return $this;
    }

    public function getBlockProvider() {
        return $this->blockProvider;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
                ->add('name', null, array('required' => true))
                ->add('layout', 'choice', array(
                    'required' => true,
                    'choices' => $this->layoutProvider->provide(),
                    'data' => $this->getSubject()->getLayout() ?: ''
                ))
                ->add('title', null, array('required' => true))
        ;

        $builder = $formMapper->getFormBuilder();

        $builder->addEventSubscriber(new AddRouteFieldSubscriber($builder->getFormFactory()));

        $http_metas = $this->container->getParameter('raindrop_page.admin.http_metas');
        $builder->addEventSubscriber(new AddMetaFieldSubscriber($builder->getFormFactory(), $http_metas));
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
        /**
         * Check for related route
         * @param type $page
         */
        $this->setRelatedRoute($page);
        $this->updateRelatedLayout($page);
    }

    public function postUpdate($page)
    {
        $route = $page->getRoute();
        $route->setContent($page);
        $this->modelManager->getEntityManager($route)->flush();
    }

    protected function setRelatedRoute($page)
    {
        $orm = $this->container->get('doctrine.orm.default_entity_manager');

        $query = $this->container->get('request')->query->all();
        $uniqid = $query['uniqid'];
        $requestParams = $this->container->get('request')->request->all();
        $formParams = $requestParams[$uniqid];

        $url = $formParams['url'];

        if (!empty($url) && ($page->getRoute() && $page->getRoute()->getPath() !== $url)) {
            $routeRepo = $orm->getRepository($this->container->getParameter('raindrop_routing_bundle.route_object_class'));
            $route = $routeRepo->findOneByPath($url);
            if (!$route) {
                $route = new Route;
                $route->setPath($url);
            }

            // make sure the controller is properly bound
            $route->setController('raindrop_page.page_controller');
            $page->setRoute($route);

            $orm->persist($route);
            $orm->flush();
        }
    }

    protected function updateRelatedLayout($page) {
        $this->container
                ->get('raindrop_page.page.manager')
                ->updatePageLayoutTimestamp($page);
    }
}