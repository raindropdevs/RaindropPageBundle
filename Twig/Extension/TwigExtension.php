<?php

namespace Raindrop\PageBundle\Twig\Extension;

use Raindrop\PageBundle\Entity\Page;

class TwigExtension extends \Twig_Extension
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            "raindrop_blocks_stylesheets" => new \Twig_Function_Method($this, "renderStylesheet"),
            "raindrop_blocks_javascripts" => new \Twig_Function_Method($this, "renderJavascript")
        );
    }

    public function renderJavascript()
    {
        return $this->container
                ->get('raindrop.page.renderer')
                ->renderJavascript();
    }

    public function renderStylesheet()
    {
        return $this->container
                ->get('raindrop.page.renderer')
                ->renderStylesheet();
    }

    public function getGlobals()
    {
        return array(
            'raindrop_intl_provider' => $this->container->get('raindrop.page.intl.provider'),
            'raindrop_admin_current_country' => $this->container->get('session')->get('raindrop:admin:country')
        );
    }

    public function getName()
    {
        return 'raindrop_page_extension';
    }
}
