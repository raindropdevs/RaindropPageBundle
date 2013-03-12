<?php

namespace Raindrop\PageBundle\Renderer;

use Raindrop\PageBundle\Renderer\RendererInterface;

class PageRenderer implements RendererInterface {

    protected $page;

    public function getLayout() {
        return $this->page->getLayout();
    }
}