<?php

namespace Raindrop\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;

class PageController extends Controller
{

    protected function getBaseResponse($object, $expiresAfter = 86400) {
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
        $response->headers->set('Expires', gmdate("D, d M Y H:i:s", time() + $expiresAfter) . " GMT");
        return $response;
    }

    /**
     * @Template()
     */
    public function indexAction($content)
    {
        $response = $this->getBaseResponse($content);

        if ($response->isNotModified($this->getRequest())) {
            // return the 304 Response immediately
            return $response;
        }

        return $this->render($content->getLayout(), array(
            'blocks' => $content->getChildren()
        ), $response);
    }

    /**
     * @Route("/assets/combined/{type}/{assets}", name="raindrop_combined_assets", requirements={ "assets" = ".+" })
     */
    public function assetsAction() {

        $files = explode(",", $this->get('request')->get('assets'));
        $type = $this->get('request')->get('type');

        array_walk($files, function (&$file) {
            $file = new FileAsset($file);
        });

        $filesContent = new AssetCollection($files);

        $response = $this->getBaseResponse($filesContent, 86400 * 7);

        if ($response->isNotModified($this->getRequest())) {
            // return the 304 Response immediately
            return $response;
        }

        switch ($type) {
            case 'js':
                $response->headers->set('Content-type', 'application/javascript');
                break;
            case 'css':
                $response->headers->set('Content-type', 'text/css');
                break;
        }

        $response->setContent($filesContent->dump());

        return $response;
    }
}
