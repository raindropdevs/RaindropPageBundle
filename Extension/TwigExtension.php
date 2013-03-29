<?php

namespace Raindrop\PageBundle\Extension;

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
        $blocks = $this->guessPage()->getChildren();
        $requirements = array();

        foreach ($blocks as $block) {
            foreach ($block->getJavascripts() as $js) {
                $requirements [$js]= true;
            }
        }

        if (empty($requirements)) {
            return '';
        }

        $path = $this->container->get('router')->generate('raindrop_combined_assets', array(
            'type' => 'js',
            'assets' => implode(",", array_keys($requirements))
        ));

        return '<script type="text/javascript" src="'. $path .'"></script>';
    }

    public function renderStylesheet() {
        $blocks = $this->guessPage()->getChildren();
        $requirements = array();

        foreach ($blocks as $block) {
            foreach ($block->getStylesheets() as $css) {
                $requirements [$css]= true;
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

    protected function guessPage() {
        return $this->container->get('router')
                ->getRouteCollection()
                ->get($this->container->get('request')->get('_route'))
                ->getContent();
    }
}
