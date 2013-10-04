<?php

namespace Raindrop\PageBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Raindrop\PageBundle\Form\EventListener\AddRouteFieldSubscriber;
use Raindrop\PageBundle\Form\EventListener\AddMetaFieldSubscriber;
use Raindrop\PageBundle\Form\EventListener\AddTagsSubscriber;
use Raindrop\RoutingBundle\Entity\Route;
use Sonata\AdminBundle\Route\RouteCollection;
use Doctrine\Common\Collections\ArrayCollection;

class PageAdmin extends Admin
{
//    public $supportsPreviewMode = true;

    protected $container, $layoutProvider, $blockProvider;

    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function setLayoutProvider($layoutProvider)
    {
        $this->layoutProvider = $layoutProvider;

        return $this;
    }

    public function setBlockProvider($blockProvider)
    {
        $this->blockProvider = $blockProvider;

        return $this;
    }

    public function getBlockProvider()
    {
        return $this->blockProvider;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('preview', $this->getRouterIdParameter().'/view');
        $collection->add('country_switch', 'country/{country}');
        $collection->add('clone_page_to_url', 'clone/page/{id}');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {

        $builder = $formMapper->getFormBuilder();

        switch ($this->getSubject()->getType()) {
            /**
             * Only let user edit target route name. Just make sure proper
             * controller is bound.
             */
            case 'redirect':
                  $formMapper
                    ->add('title')
                    ->add('layout', 'choice', array(
                        'label' => 'Target route',
                        'required' => true,
                        'choices' => $this->container
                            ->get('raindrop_page.route.provider')->provide(),
                        'data' => $this->getSubject()->getLayout() ?: ''
                    ))
                    ;
                break;
            /**
             * Let user chose url and target redirection route.
             */
            case 'external_redirect':
                $id = null;
                $route = $this->getSubject()->getRoute();
                if ($route) {
                    $data = $route->getContent();
                    if ($data) {
                        $id = $data->getId();
                    }
                }

                $formMapper
                    ->add('layout', 'hidden', array(
                        'data' => 'external_redirect'
                    ))
                    ->add('title')
                    ->add('target_route', 'choice', array(
                        'label' => 'Target route',
                        'required' => true,
                        'choices' => $this->container
                            ->get('raindrop_page.external_route.provider')
                            ->provide(),
                        'data' => $id,
                        'mapped' => false
                    ))
                    ;
                break;
            default:
                $formMapper
                    ->add('title', null, array(
                        'required' => true,
                        'attr' => array(
                            'class' => 'span7'
                        ),
                    ))
                    ->add('layout', 'choice', array(
                        'required' => false,
                        'empty_value' => 'Choose a layout',
                        'empty_data'  => '',
                        'choices' => $this->layoutProvider->provide(),
                        'data' => $this->getSubject()->getLayout() ?: ''
                    ))
                    ;

                $http_metas = $this->container->getParameter('raindrop_page.admin.http_metas');
                $builder->addEventSubscriber(new AddMetaFieldSubscriber($builder->getFormFactory(), $http_metas));
                break;
        }

        $urlValue = '';
        $parent = $this->container->get('request')->get('parent');
        if ($parent && $parent != '/') {
            $urlValue = $parent . '/';
        }
        $builder->addEventSubscriber(new AddRouteFieldSubscriber($builder->getFormFactory(), array('url' => $urlValue)));
        $builder->addEventSubscriber(new AddTagsSubscriber($builder->getFormFactory()));
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
                switch ($this->getSubject()->getType()) {
                    case 'redirect':
                        return 'RaindropPageBundle:Page:redirect_page_editor.html.twig';
                        break;
                    case 'external_redirect':
                        return 'RaindropPageBundle:Page:external_redirect_page_editor.html.twig';
                        break;
                    default:
                        return 'RaindropPageBundle:Page:page_editor.html.twig';
                        break;
                }
                break;
            case 'list':
                return 'RaindropPageBundle:Page:page_tree_view.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }

    public function prePersist($page)
    {
        if (!$page->getId()) {
            $page->setCountry($this->container->get('session')->get('raindrop:admin:country'));
        }

        $page->setName($page->getTitle());
    }

    public function preUpdate($page)
    {
        $this->prePersist($page);
    }

    public function postPersist($page)
    {
        $this->setRelatedRoute($page);
        $this->updateTagging($page);
    }

    /**
     * @param type $page
     */
    public function postUpdate($page)
    {
        $this->setRelatedRoute($page);
        $this->updateRelatedLayout($page);
        $this->updateTagging($page);
    }

//    public function preRemove($object) {
//        parent::preRemove($object);
//
//        if ($object->getParent()) {
//            $object->getParent()->removeChildren($object);
//        }
//
//        foreach ($object->getChildren() as $child) {
//            $this->getOrm()->remove($child);
//        }
//
//        $this->getOrm()->flush();
//    }

    protected function updateTagging($page)
    {
        $page->setTags(new ArrayCollection);

        $tags = $this->getRequestProperty('tags');

        foreach ($tags as $tag) {
            if (!empty($tag)) {
                $this->container->get('raindrop_page.page.manager')
                        ->addTagToPage($tag, $page);
            }
        }

        // this flush is required when all tags get removed
        $this->container->get('doctrine.orm.default_entity_manager')->flush();
    }

    protected function setRelatedRoute($page)
    {
        switch ($page->getType()) {
            case 'redirect':
                $this->bindRedirectToPage($page);
                break;
            case 'external_redirect':
                $this->bindExternalRedirect($page);
                break;
            default:
                $this->bindRouteToPage($page);
                break;
        }
    }

    /**
     * Routes points to an external_route entity
     * and page exists only to attach route to a menu.
     */
    protected function bindExternalRedirect($page)
    {
        $targetRouteId = $this->getRequestProperty('target_route');

        $route = $page->getRoute();

        $url = $this->getRequestProperty('url');

        // try to find a previous route
        if (!$route) {
            $route = $this->retrieveRoute($url);
        }

        if (!$route) {
            $route = new Route;
            $resolver = $this->container
                ->get('raindrop_routing.content_resolver');
            $resolver->setEntityManager($this->getOrm());
            $route->setController('RaindropRoutingBundle:Generic:redirectRoute');
            $route->setResolver($resolver);
            $route->setPath($url);
            $route->setNameFromPath();
            $this->getOrm()->persist($route);

            $page->setRoute($route);
        }

        if ($route) {
            $externalRoute = $this->getOrm()
                    ->getRepository('RaindropRoutingBundle:ExternalRoute')
                    ->find($targetRouteId);
            if ($externalRoute) {
                $route->setContent($externalRoute);
                $this->getOrm()->flush();
            }
        }
    }

    /**
     * Make sure inner redirect points to the
     * @param type $page
     */
    protected function bindRedirectToPage($page)
    {
        $url = $this->getRequestProperty('url');

        // try to find a previous route
        $route = $this->retrieveRoute($url);

        if (!$route) {
            $route = new Route;
            $resolver = $this->container
                ->get('raindrop_routing.content_resolver');
            $resolver->setEntityManager($this->getOrm());
            $route->setResolver($resolver);
            $route->setPath($url);
            $route->setNameFromPath();
            $this->getOrm()->persist($route);

            $page->setRoute($route);
        }

        $route->setController('RaindropPageBundle:Page:childRedirection');
        $this->getOrm()->flush();
    }

    protected function retrieveRoute($url)
    {
        return $this->getOrm()
            ->getRepository($this->container->getParameter('raindrop_routing_bundle.route_object_class'))
            ->findOneByPath($url);
    }

    protected function bindRouteToPage($page)
    {
        $url = $this->getRequestProperty('url');

        if (!empty($url)) {
            $route = $page->getRoute();

            if (!$route) {
                $route = $this->retrieveRoute($url);
            }

            if (!$route) {
                $route = new Route;
                $resolver = $this->container
                    ->get('raindrop_routing.content_resolver');
                $resolver->setEntityManager($this->getOrm());
                $route->setResolver($resolver);
                $route->setPath($url);
                $route->setNameFromPath();
                $this->getOrm()->persist($route);
            }

            // make sure url and controller are properly bound
            $page->setRoute($route);
            $route->setPath($url);

            // set controller only the first time
            if (!$route->getController()) {
                $route->setController($this->container->getParameter('raindrop_page.page_controller'));
            }

            $this->getOrm()->flush();
        }
    }

    /**
     * Retrieves a form field from sonata post request
     * @param  type $name
     * @return null
     */
    protected function getRequestProperty($name)
    {
        $query = $this->container->get('request')->query->all();
        $uniqid = $query['uniqid'];
        $requestParams = $this->container->get('request')->request->all();
        $formParams = $requestParams[$uniqid];
        if (isset($formParams[$name])) {
            return $formParams[$name];
        }

        return null;
    }

    protected function updateRelatedLayout($page)
    {
        $this->container
                ->get('raindrop_page.page.manager')
                ->updatePageLayoutTimestamp($page);
    }

    protected function getOrm()
    {
        return $this->container->get('doctrine.orm.default_entity_manager');
    }
}
