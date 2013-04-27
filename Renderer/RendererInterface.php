<?php

namespace Raindrop\PageBundle\Renderer;

use Raindrop\PageBundle\Renderer\RenderableObjectInterface;

interface RendererInterface {
    public function render(RenderableObjectInterface $object);
    public function renderJavascript();
    public function renderStylesheet();
}
