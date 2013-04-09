<?php

namespace Raindrop\PageBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Raindrop\PageBundle\Form\EventListener\AddRouteFieldSubscriber;
use Raindrop\PageBundle\Form\EventListener\AddMetaFieldSubscriber;
use Raindrop\RoutingBundle\Entity\Route;
use Sonata\AdminBundle\Route\RouteCollection;

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
                ->add('title', null, array('required' => true))
                ->add('layout', 'choice', array(
                    'required' => true,
                    'choices' => $this->layoutProvider->provide(),
                    'data' => $this->getSubject()->getLayout() ?: ''
                ))
        ;

        $urlValue = '';
        $parent = $this->container->get('request')->get('parent');
        if ($parent) {
            $urlValue = $parent . '/<name>';
        }

        $builder = $formMapper->getFormBuilder();
        $builder->addEventSubscriber(new AddRouteFieldSubscriber($builder->getFormFactory(), array('url' => $urlValue)));

        $http_metas = $this->container->getParameter('raindrop_page.admin.http_metas');
        $builder->addEventSubscriber(new AddMetaFieldSubscriber($builder->getFormFactory(), $http_metas));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
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
            case 'list':
                return 'RaindropPageBundle:Page:page_tree_view.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }

    public function prePersist($page) {
        $page->setName($page->getTitle());
    }

    public function preUpdate($page) {
        $this->prePersist($page);
    }

    public function postPersist($page) {
        $this->setRelatedRoute($page);
    }

    /**
     * @param type $page
     */
    public function postUpdate($page)
    {
        $this->setRelatedRoute($page);
        $this->updateRelatedLayout($page);
    }

    protected function setRelatedRoute($page)
    {
        $orm = $this->container->get('doctrine.orm.default_entity_manager');

        /**
         * This is insane to access a form property...
         */
        $query = $this->container->get('request')->query->all();
        $uniqid = $query['uniqid'];
        $requestParams = $this->container->get('request')->request->all();
        $formParams = $requestParams[$uniqid];
        $url = $formParams['url'];

        if (!empty($url)) {
            $route = $page->getRoute();

            if (!$route) {
                $route = new Route;
                $resolver = $this->container
                    ->get('raindrop_routing.content_resolver');
                $resolver->setEntityManager($orm);
                $route->setResolver($resolver);
                $route->setPath($url);
                $route->setNameFromPath();
                $orm->persist($route);
            }

            // make sure url and controller are properly bound
            $page->setRoute($route);
            $route->setPath($url);
            $route->setController($this->container->getParameter('raindrop_page.page_controller'));

            $orm->flush();
        }
    }

    protected function updateRelatedLayout($page) {
        $this->container
                ->get('raindrop_page.page.manager')
                ->updatePageLayoutTimestamp($page);
    }
}