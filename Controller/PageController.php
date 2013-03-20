<?php

namespace Raindrop\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;

class PageController extends Controller
{
    /**
     * @Template()
     */
    public function indexAction($content)
    {
        return $this->render($content->getLayout(), array(
            'blocks' => $content->getChildren()
        ));
    }
}
