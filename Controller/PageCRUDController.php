<?php

namespace Raindrop\PageBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation\Secure;

class PageCRUDController extends CRUDController
{
    public function listAction()
    {
        if (false === $this->admin->isGranted('LIST')) {
            throw new AccessDeniedException();
        }

        $datagrid = $this->admin->getDatagrid();
        $formView = $datagrid->getForm()->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        $treeBuilder = $this->get('raindrop_page.directory_tree');

        return $this->render($this->admin->getTemplate('list'), array(
            'action'   => 'list',
            'form'     => $formView,
            'datagrid' => $datagrid,
            'root' => $treeBuilder->buildTree()->toArray()
        ));
    }

    public function previewAction() {
        $page_id = $this->get('request')->get('id');
            $orm = $this
                ->get('doctrine.orm.default_entity_manager');

        $page = $orm
                ->getRepository($this->container->getParameter('raindrop_page_bundle.page_class'))
                ->find($page_id);

        return $this
            ->get('raindrop.page.renderer')
            ->render($page);
    }
}
