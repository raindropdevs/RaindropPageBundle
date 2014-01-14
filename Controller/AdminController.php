<?php

namespace Raindrop\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Raindrop\PageBundle\Entity\Block;

class AdminController extends Controller
{

    /**
     * @Route("/admin/page/url/check", name="raindrop_admin_url_check")
     * @Secure(roles="ROLE_ADMIN")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function ajaxUrlCheck()
    {
        $name = $this->get('request')->get('url');
        $route = $this
            ->get('raindrop_routing.route_repository')
            ->findOneByPath($name);

        $available = true;
        $page_title = null;

        if ($route) {
            $page = $this->get('raindrop_page.page_repository')->findOneByRoute($route->getId());
            if ($page) {
                $available = false;
                $page_title = $page->getTitle();
            }
        }

        return new JsonResponse(array('available' => $available, 'page' => $page_title));
    }

    /**
     * @Route("/admin/page/add/block/{page_id}/{name}/{layout}", name="raindrop_admin_add_block", defaults={"layout" = "center"})
     * @Secure(roles="ROLE_ADMIN")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addBlockAction()
    {
        try {
            $result = false;
            $page_id = $this->get('request')->get('page_id');
            $block_name = $this->get('request')->get('name');
            $block_layout_position = $this->get('request')->get('layout');

            $orm = $this
                    ->get('doctrine.orm.default_entity_manager');

            // child of a block else child of parent page
            if (preg_match('/block-([0-9]+)/', $block_layout_position, $m)) {
                $block_id = $m[1];
                $block = $orm->getRepository('RaindropPageBundle:Block')
                        ->find($block_id);

                if ($block) {
                    $this->get('raindrop_page.block.manager')
                        ->createBlock($block, $block_name, $block_layout_position);

                    $result = true;
                }
            } else {
                $page = $orm
                        ->getRepository($this->container->getParameter('raindrop_page_bundle.page_class'))
                        ->find($page_id);

                if ($page) {
                    $this->get('raindrop_page.block.manager')
                            ->createBlock($page, $block_name, $block_layout_position);

                    $result = true;
                }
            }

        } catch (\Exception $e) {
            return new JsonResponse(array('error' => $e->getMessage()), 500);
        }

        return new JsonResponse(array('result' => $result));
    }

    /**
     * @Route("/admin/page/order/blocks/{page_id}", name="raindrop_admin_order_blocks")
     * @Secure(roles="ROLE_ADMIN")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function orderBlocksAction()
    {
        try {
            $result = false;
            $page_id = $this->get('request')->get('page_id');

            $page = $this->getPage($page_id);

            if ($page) {
                $this->get('raindrop_page.block.manager')
                        ->reorderBlocks($page, $this->get('request')->get('ids'));

                $this->get('raindrop_page.block.manager')
                    ->moveBlock($this->get('request')->get('move'), $this->get('request')->get('to'));

                $result = true;
            }
        } catch (\Exception $e) {
            return new JsonResponse(array('error' => $e->getMessage()), 500);
        }

        return new JsonResponse(array('result' => $result));
    }

    protected function getPage($id)
    {
        $orm = $this
                ->get('doctrine.orm.default_entity_manager');

        return $orm
                ->getRepository($this->container->getParameter('raindrop_page_bundle.page_class'))
                ->find($id);
    }

    protected function getBlock($id)
    {
        $orm = $this
                ->get('doctrine.orm.default_entity_manager');

        return $orm
                ->getRepository($this->container->getParameter('raindrop_page_bundle.block_class'))
                ->find($id);
    }

    /**
     * @Route("/admin/page/remove/block/{block_id}", name="raindrop_admin_remove_block")
     * @Secure(roles="ROLE_ADMIN")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeBlockAction()
    {
        $block_id = $this->get('request')->get('block_id');

        $result = $this->get('raindrop_page.block.manager')
                ->removeBlock($block_id);

        return new JsonResponse(array('result' => $result));
    }

    /**
     * @Route("/admin/page/reload/blocks/{page_id}", name="raindrop_admin_reload_layout")
     * @Secure(roles="ROLE_ADMIN")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function reloadLayoutAction()
    {
        $page_id = $this->get('request')->get('page_id');
        $page = $this->getPage($page_id);

        $result = false;
        if ($page) {
            $result = $this->render('RaindropPageBundle:Page:page_layout_list.html.twig', array('object' => $page));
        }

        return new JsonResponse(array(
            'result' => $result ? $result->getContent() : false
        ));
    }

    /**
     * @Route("/admin/page/preview/blocks/{id}", name="raindrop_admin_preview_block")
     * @Secure(roles="ROLE_ADMIN")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function blockPreview()
    {
        $block_id = $this->get('request')->get('id');
        $block = $this->getBlock($block_id);

        $theme = $this->get('request')->get('theme');

        if (!empty($theme)) {
            $this->get('liip_theme.active_theme')->setName($theme);
        }

        return $this->render('RaindropPageBundle:Block:block_preview.html.twig', array('block' => $block));
    }

    /**
     * @Route("/admin/page/switch/country/{country}", name="raindrop_admin_switch_country")
     * @Secure(roles="ROLE_ADMIN")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function switchCountryAction()
    {
        $country = $this->get('request')->get('country');

        /**
         * @TODO: check if country is valid
         */

        $this->get('session')->set('raindrop:admin:country', $country);

        $url = $this->get('request')->headers->get('referer');

        if (empty($url)) {
            $url = $this->get('router')->generate('sonata_admin_dashboard');
        }

        return $this->redirect($url);
    }

    /**
     * @Route("/admin/page/switch/locale/{locale}", name="raindrop_admin_switch_locale")
     * @Secure(roles="ROLE_ADMIN")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function switchLocaleAction()
    {
        $locale = $this->get('request')->get('locale');

        /**
         * @TODO: check if locale is valid
         */

        $this->get('session')->set('raindrop:admin:locale', $locale);

        $url = $this->get('request')->headers->get('referer');

        if (empty($url)) {
            $url = $this->get('router')->generate('sonata_admin_dashboard');
        }

        return $this->redirect($url);
    }

    public function showBlockVariablesAction($template, $children)
    {
        $variables = $this->get('raindrop.twig_loader.variable_extractor')->extract($template);

        $alreadyBound = array();
        array_walk($children, function ($child, $index) use (&$alreadyBound) {
            $alreadyBound []= $index;
        });

        $variablesLeft = array_filter($variables, function ($var) use ($alreadyBound) {
            return !in_array($var, $alreadyBound);
        });

        return $this->render('RaindropPageBundle:Block:template_variables.html.twig', array(
            'variables' => $variablesLeft,
            'alreadyBound' => $alreadyBound
        ));
    }
}
