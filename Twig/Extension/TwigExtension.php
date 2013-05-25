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

    public function getCountry()
    {
        $page = $this->container
                ->get('raindrop.page.renderer')
                ->guessPage();

        return $page ? $page->getCountry() : null;
    }

    public function getLocale()
    {
        $page = $this->container
                ->get('raindrop.page.renderer')
                ->guessPage();

        return $page ? $page->getRoute()->getLocale() : null;
    }

    public function getGlobals()
    {
        $useTheme = $this->container->getParameter('use_liip_theme');
        $theme = $this->container->get('request')->get('theme');
        if (is_null($theme)) {
            $theme_suffix = '';
        } else {
            $theme_suffix = '|' . $theme;
        }

        return array(
            'use_liip_theme' => $useTheme,
            'liip_theme' => $theme,
            'liip_theme_suffix' => $theme_suffix,
            'raindrop_intl_provider' => $this->container->get('raindrop.page.intl.provider'),
            'raindrop_admin_current_country' => $this->container->get('session')->get('raindrop:admin:country'),
            'raindrop_country' => $this->getCountry(),
            'raindrop_locale' => $this->getLocale(),
        );
    }

    public function getName()
    {
        return 'raindrop_page_extension';
    }
}
