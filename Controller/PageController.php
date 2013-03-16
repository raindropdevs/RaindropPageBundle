<?php

namespace Raindrop\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
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

    /**
     * @Route("/admin/page/url/check")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxUrlCheck() {

        $name = $this->get('request')->get('url');
        $route = $this
            ->get('raindrop_routing.route_repository')
            ->findOneByPath($name);

        $response = new Response(
                json_encode(array(
                    'available' => !$route
                )),
                200,
                array(
                    'Content-Type' => 'application/json'
                )
        );

        return $response;
    }
}
