<?php

namespace Raindrop\PageBundle\Extension;

use Raindrop\RoutingBundle\Entity\Route;


class TwigExtension extends \Twig_Extension {

    protected $container;

    public function __construct($container) {
        $this->container = $container;
    }

    public function getFunctions() {
        return array(
            "raindrop_blocks_stylesheets" => new \Twig_Function_Method($this, "renderStylesheet"),
            "raindrop_blocks_javascripts" => new \Twig_Function_Method($this, "renderJavascript")
        );
    }

    public function renderJavascript() {
        $page = $this->guessPage();
        if (!$page) {
            return '';
        }
        $blocks = $page->getChildren();
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
        $page = $this->guessPage();
        if (!$page) {
            return '';
        }
        $blocks = $page->getChildren();
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

    public function getName() {
        return 'raindrop_page_extension';
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

        return null;
    }
}
