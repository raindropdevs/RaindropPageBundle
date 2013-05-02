<?php

namespace Raindrop\PageBundle\Renderer;

/**
 * RenderableObjectInterface has convenient methods required by the page
 * renderer in order to accomplish rendering and http caching mechanism.
 *
 * @author teito
 */
interface RenderableObjectInterface
{
    /**
     * Must return valid twig template.
     */
    public function getLayout();

    /**
     * Should return an array of variables to populate the template with.
     */
    public function getParameters();

    /**
     * Must return a valid last modified date using either \DateTime object
     * or timestamp integer.
     */
    public function getLastModified();

    /**
     * Must return a valid time interval in seconds that is added to current
     * servertime for far future expiration.
     */
    public function getExpiresAfter();
}
