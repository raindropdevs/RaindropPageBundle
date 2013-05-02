<?php

namespace Raindrop\PageBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Knp\Menu\MenuFactory;

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
        $country = $page->getCountry();

        $locale = $this->container->get('request')->get('_locale');

        $treeBuilder = $this->container->get('raindrop_page.directory_tree');
        $factory = new MenuFactory();
        $pages = $this->container
            ->get('raindrop_page.page.manager')
            ->getCurrentMenu('main_menu', $country)
        ;

        $root = $treeBuilder->buildTree($pages)->getTree();
        $menu = $root->getChild($country)->getChild($locale);

        return $factory->createFromNode($menu);
    }
}
