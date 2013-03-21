<?php

namespace Raindrop\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Raindrop\PageBundle\Entity\Block;

class AdminController extends Controller
{

    /**
     * @Route("/admin/page/url/check", name="raindrop_admin_url_check")
     * @Secure(roles="ROLE_ADMIN")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function ajaxUrlCheck() {

        $name = $this->get('request')->get('url');
        $route = $this
            ->get('raindrop_routing.route_repository')
            ->findOneByPath($name);

        return new JsonResponse(array('available' => !$route));
    }


    /**
     * @Route("/admin/page/add/block/{page_id}/{type}", name="raindrop_admin_add_block")
     * @Secure(roles="ROLE_ADMIN")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addBlockAction() {
        $result = false;
        $page_id = $this->get('request')->get('page_id');
        $block_type = $this->get('request')->get('type');

        $orm = $this
                ->get('doctrine.orm.default_entity_manager');

        $page = $orm
                ->getRepository($this->container->getParameter('raindrop_page_bundle.page_class'))
                ->find($page_id);

        if ($page) {
            $this->get('raindrop_page.block.manager')
                    ->createBlock($page, $block_type);

            $result = true;
        }

        return new JsonResponse(array('result' => $result));
    }


    /**
     * @Route("/admin/page/order/blocks/{page_id}", name="raindrop_admin_order_blocks")
     * @Secure(roles="ROLE_ADMIN")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function orderBlocksAction() {

        $result = false;
        $page_id = $this->get('request')->get('page_id');

        $page = $this->getPage($page_id);

        if ($page) {
            $this->get('raindrop_page.block.manager')
                    ->reorderBlocks($page, $this->get('request')->get('ids'));

            $this->get('raindrop_page.page.manager')
                ->updatePageLayoutTimestamp($page);

            $result = true;
        }
        return new JsonResponse(array('result' => $result));
    }

    protected function getPage($id) {
        $orm = $this
                ->get('doctrine.orm.default_entity_manager');

        return $orm
                ->getRepository($this->container->getParameter('raindrop_page_bundle.page_class'))
                ->find($id);
    }

    /**
     * @Route("/admin/page/remove/block/{block_id}", name="raindrop_admin_remove_block")
     * @Secure(roles="ROLE_ADMIN")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeBlockAction() {
        $block_id = $this->get('request')->get('block_id');

        $result = $this->get('raindrop_page.block.manager')
                ->removeBlock($block_id);

        return new JsonResponse(array('result' => $result));
    }
}
