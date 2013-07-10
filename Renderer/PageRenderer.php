<?php

namespace Raindrop\PageBundle\Renderer;

use Raindrop\PageBundle\Renderer\RendererInterface;
use Raindrop\RoutingBundle\Entity\Route;
use Raindrop\PageBundle\Entity\Page;
use Symfony\Component\HttpFoundation\Response;
use Raindrop\PageBundle\Renderer\RenderableObjectInterface;

class PageRenderer implements RendererInterface
{
    protected $page, $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Renders a page using the same logic as the base symfony 2 controller.
     *
     * @param  \Raindrop\PageBundle\Renderer\RenderableObjectInterface $page
     * @return type
     */
    public function render(RenderableObjectInterface $object)
    {
        return $this->container
            ->get('templating')
            ->renderResponse(
                $this->getLayout($object),
                $this->getParameters($object),
                $this->getBaseResponse($object)
            )
            ;
    }

    protected function getLayout($object)
    {
        if ($object instanceof Page) {
            return $object->getLayout();
        }

        return null;
    }

    protected function getParameters($object)
    {
        return $object->getParameters();
    }

    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    protected function getBaseResponse($object)
    {
        $response = new Response;

        if ($this->container->getParameter('kernel.environment') !== 'prod') {
            return $response;
        }

        $response->setPublic();

        $lastModified = $object->getLastModified();
        if (!$lastModified instanceof \DateTime) {
            $date = new \DateTime();
            $date->setTimestamp($lastModified);
            $lastModified = $date;
        }

        $response->setLastModified();
        $response->headers->set('Expires', gmdate("D, d M Y H:i:s", time() + $object->getExpiresAfter()) . " GMT");

        return $response;
    }

    protected function getAssetsUrlSuffix()
    {
        $release_version = '';

        if ($this->container->hasParameter('release_version')) {
            $release_version = '?' . $this->container->getParameter('release_version');
        }

        return $release_version;
    }

    public function renderJavascript()
    {
        $blocks = $this->getPageBlocks();

        if (!count($blocks)) {
            return;
        }

        $requirements = array();
        $inclusion = array();

        foreach ($blocks as $block) {
            if ($this->blockInUse($block)) {
                if ($block->hasJavascripts()) {
                    foreach ($block->getJavascripts() as $js) {
                        if (!isset($requirements[$js])) {
                            $requirements [$js]= true;
                            $inclusion []= $js;
                        }
                    }
                }
            }
        }

        if (empty($requirements)) {
            return '';
        }

        $path = $this->container->get('router')->generate('raindrop_combined_assets', array(
            'type' => 'js',
            'assets' => implode(",", $inclusion)
        ));

        return '<script src="'. $path . $this->getAssetsUrlSuffix() .'" type="text/javascript"></script>';
    }

    public function renderStylesheet()
    {
        $blocks = $this->getPageBlocks();

        if (empty($blocks)) {
            return '';
        }

        $requirements = array();
        $inclusion = array();

        foreach ($blocks as $block) {
            if ($this->blockInUse($block)) {
                if ($block->hasStylesheets()) {
                    foreach ($block->getStylesheets() as $css) {
                        if (!isset($requirements[$css])) {
                            $requirements [$css]= true;
                            $inclusion []= $css;
                        }
                    }
                }
            }
        }

        if (empty($requirements)) {
            return '';
        }

        $path = $this->container->get('router')->generate('raindrop_combined_assets', array(
            'type' => 'css',
            'assets' => implode(",", $inclusion)
        ));

        return '<link rel="stylesheet" type="text/css" href="'. $path . $this->getAssetsUrlSuffix() .'" />';
    }

    protected function blockInUse($block)
    {
        $useLiipTheme = $this->container->getParameter('use_liip_theme');
        if ($useLiipTheme) {
            $defaultTheme = 'desktop';
            $theme = $this->container->get('liip_theme.active_theme')->getName();

            /**
             * If theme is null and block has no reference to theme, it's ok.
             * If theme is not null and block refers to that theme, it's ok.
             */
            if (is_null($theme) or $theme == $defaultTheme) {
                if (!preg_match('/[\|]+/', $block->getLayout())) {
                    return true;
                }
            }
            $pattern = "/.*\|{$theme}$/";

            return preg_match($pattern, $block->getLayout());
        }

        return true;
    }

    protected function getPageBlocks()
    {
        $page = $this->guessPage();
        if (!$page instanceof Page) {
            return array();
        }

        return $page->getBlocks();
    }

    /**
     * Try to find out if current route points to a page entity.
     * @return null
     */
    public function guessPage()
    {
        if (!empty($this->page)) {
            return $this->page;
        }

        $route = $this->container->get('raindrop_routing.route_repository')
                ->findOneByName($this->container->get('request')->get('_route'));

        if ($route instanceof Route) {
            $page = $route->getContent();
            if ($page instanceof Page) {
                $this->page = $page;

                return $page;
            }
        }

        $id = $this->container->get('request')->get('id');

        if ($id) {
            $orm = $this
                ->container
                ->get('doctrine.orm.default_entity_manager');

            $page = $orm
                ->getRepository($this->container->getParameter('raindrop_page_bundle.page_class'))
                ->find($id);

            if ($page instanceof Page) {
                $this->page = $page;

                return $page;
            }
        }

        return null;
    }
}
