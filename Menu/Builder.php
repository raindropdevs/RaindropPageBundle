<?php

namespace Raindrop\PageBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Knp\Menu\MenuFactory;
use Raindrop\PageBundle\Directory\Node;

/**
 * Description of Builder
 *
 * @author teito
 */
class Builder implements ContainerAwareInterface
{
    //put your code here

    protected $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $country = $this->container->get('request')->get('country');
        if (empty($country)) {
            $page = $this->container->get('raindrop.page.renderer')->guessPage();
            $country = strtolower($page->getCountry());
        }

        $locale = $this->container->get('request')->get('locale');
        if (empty($locale)) {
            $locale = substr($page->getRoute()->getLocale(), 0, 2);
        }

        $treeBuilder = $this->container->get('raindrop_page.directory_tree');
        $factory = new MenuFactory();
        $pages = $this->container
            ->get('raindrop_page.page.manager')
            ->getCurrentMenu('main_menu', $country)
        ;

        $absolute = $this->container->get('request')->get('absolute_path');

        $root = $treeBuilder->buildTree($pages, $absolute)->getTree();

        $menu = new Node('dummy');

        if ($root->hasChild($country)) {
            if ($root->getChild($country)->hasChild($locale)) {
                $menu = $root->getChild($country)->getChild($locale);
            }
        }

        $menu_items = $factory->createFromNode($menu);

        if ($page) {
            $current_uri = $page->getRoute()->getPath();

            foreach ($menu_items as $menu_item) {
                if (strpos($current_uri, $menu_item->getUri()) !== false) {
                    $menu_item->setCurrent(true);
                }
            }
        }

        return $menu_items;
    }
}
