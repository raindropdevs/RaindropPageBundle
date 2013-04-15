<?php

namespace Raindrop\PageBundle\Renderer;

use Raindrop\PageBundle\Renderer\RendererInterface;

class PageRenderer implements RendererInterface {

    protected $page, $container;

    public function __construct($container) {
        $this->container = $container;
    }

    public function setPage($page) {
        $this->page = $page;

        return $this;
    }

    public function getLayout() {
        return $this->page->getLayout();
    }

    public function renderJavascript() {
        $blocks = $this->getPageBlocks();

        if (!count($blocks)) {
            return;
        }

        $requirements = array();

        foreach ($blocks as $block) {
            if ($block->hasJavascripts()) {
                foreach ($block->getJavascripts() as $js) {
                    $requirements [$js]= true;
                }
            }
        }

        if (empty($requirements)) {
            return '';
        }

        $path = $this->container->get('router')->generate('raindrop_combined_assets', array(
            'type' => 'js',
            'assets' => implode(",", array_keys($requirements))
        ));

        return '<script src="'. $path .'" type="text/javascript"></script>';
    }

    public function renderStylesheet() {
        $blocks = $this->getPageBlocks();

        if (empty($blocks)) {
            return '';
        }

        $requirements = array();

        foreach ($blocks as $block) {
            if ($block->hasStylesheets()) {
                foreach ($block->getStylesheets() as $css) {
                    $requirements [$css]= true;
                }
            }
        }

        if (empty($requirements)) {
            return '';
        }

        $path = $this->container->get('router')->generate('raindrop_combined_assets', array(
            'type' => 'css',
            'assets' => implode(",", array_keys($requirements))
        ));

        return '<link rel="stylesheet" type="text/css" href="'. $path .'" />';
    }

    protected function getPageBlocks() {
        $page = $this->guessPage();
        if (!$page instanceof Page) {
            return array();
        }
        return $page->getChildren();
    }

    /**
     * Try to find out if current route points to a page entity.
     * @return null
     */
    protected function guessPage() {
        $route = $this->container->get('router')
                ->getRouteCollection()
                ->get($this->container->get('request')->get('_route'));

        if ($route instanceof Route) {
            return $route->getContent();
        }

        $id = $this->container->get('request')->get('page_id');
        if ($id) {
            $orm = $this
                ->container
                ->get('doctrine.orm.default_entity_manager');
            $page = $orm
                ->getRepository($this->container->getParameter('raindrop_page_bundle.page_class'))
                ->find($id);
            if ($page) {
                return $page;
            }
        }



        return null;
    }
}