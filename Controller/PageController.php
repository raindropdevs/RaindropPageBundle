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
     * @Route("/assets/combined/{type}/{assets}", name="raindrop_combined_assets", requirements={ "assets" = ".+" })
     */
    public function assetsAction() {
        $files = explode(",", $this->get('request')->get('assets'));
        $type = $this->get('request')->get('type');

        array_walk($files, function (&$file) {
            $file = new FileAsset($file);
        });

        $filesContent = new AssetCollection($files);

        $response = new Response();

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
