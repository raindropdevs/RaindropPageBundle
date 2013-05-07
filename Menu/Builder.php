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
        $page = $this->container->get('raindrop.page.renderer')->guessPage();
        $country = strtolower($page->getCountry());

        $locale = substr($this->container->get('request')->get('_locale'), 0, 2);

        $treeBuilder = $this->container->get('raindrop_page.directory_tree');
        $factory = new MenuFactory();
        $pages = $this->container
            ->get('raindrop_page.page.manager')
            ->getCurrentMenu('main_menu', $country)
        ;

        $root = $treeBuilder->buildTree($pages)->getTree();

        $menu = new Node('dummy');

        if ($root->hasChild($country)) {
            if ($root->getChild($country)->hasChild($locale)) {
                $menu = $root->getChild($country)->getChild($locale);
            }
        }

        return $factory->createFromNode($menu);
    }
}
