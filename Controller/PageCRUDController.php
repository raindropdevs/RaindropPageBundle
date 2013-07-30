<?php

namespace Raindrop\PageBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Raindrop\PageBundle\Form\ClonePageForm;

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
        $country = $this->container->get('session')->get('raindrop:admin:country');
        $pages = $this->container->get('doctrine.orm.default_entity_manager')
            ->getRepository('RaindropPageBundle:Page')
            ->findByCountryWithMenu($country);

        return $this->render($this->admin->getTemplate('list'), array(
            'action'   => 'list',
            'form'     => $formView,
            'datagrid' => $datagrid,
            'root' => $treeBuilder->buildTree($pages)->toArray()
        ));
    }

    public function previewAction()
    {
        $page_id = $this->get('request')->get('id');
            $orm = $this
                ->get('doctrine.orm.default_entity_manager');

        $page = $orm
                ->getRepository($this->container->getParameter('raindrop_page_bundle.page_class'))
                ->find($page_id);

        $theme = $this->get('request')->get('theme');

        if (!empty($theme)) {
            $this->get('liip_theme.active_theme')->setName($theme);
        }

        return $this
            ->get('raindrop.page.renderer')
            ->render($page);
    }

    /**
     * return the Response object associated to the edit action
     *
     *
     * @param mixed $id
     *
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return Response
     */
    public function editAction($id = null)
    {
        // the key used to lookup the template
        $templateKey = 'edit';

        $id = $this->get('request')->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('EDIT', $object)) {
            throw new AccessDeniedException();
        }

        $this->admin->setSubject($object);

        /** @var $form \Symfony\Component\Form\Form */
        $form = $this->admin->getForm();
        $form->setData($object);

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
                $this->admin->update($object);
                $this->get('session')->setFlash('sonata_flash_success', 'flash_edit_success');

                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(array(
                        'result'    => 'ok',
                        'objectId'  => $this->admin->getNormalizedIdentifier($object)
                    ));
                }

                // redirect to edit mode
                return $this->redirectTo($object);
            }

            // show an error message if the form failed validation
            if (!$isFormValid) {
                if (!$this->isXmlHttpRequest()) {
                    $this->get('session')->setFlash('sonata_flash_error', 'flash_edit_error');
                }
            } elseif ($this->isPreviewRequested()) {
                // enable the preview template if the form was valid and preview was requested
                $templateKey = 'preview';
            }
        }

        $view = $form->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());

        return $this->render($this->admin->getTemplate($templateKey), array(
            'action' => 'edit',
            'form'   => $view,
            'object' => $object
        ));
    }

    /**
     * return the Response object associated to the create action
     *
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @return Response
     */
    public function createAction()
    {
        // the key used to lookup the template
        $templateKey = 'edit';

        if (false === $this->admin->isGranted('CREATE')) {
            throw new AccessDeniedException();
        }

        $object = $this->admin->getNewInstance();

        $type = $this->get('request')->get('type');
        if (!empty($type)) {
            $object->setType($type);
        }

        $this->admin->setSubject($object);

        /** @var $form \Symfony\Component\Form\Form */
        $form = $this->admin->getForm();
        $form->setData($object);

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
                $this->admin->create($object);

                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(array(
                        'result' => 'ok',
                        'objectId' => $this->admin->getNormalizedIdentifier($object)
                    ));
                }

                $this->get('session')->setFlash('sonata_flash_success','flash_create_success');
                // redirect to edit mode
                return $this->redirectTo($object);
            }

            // show an error message if the form failed validation
            if (!$isFormValid) {
                if (!$this->isXmlHttpRequest()) {
                    $this->get('session')->setFlash('sonata_flash_error', 'flash_create_error');
                }
            } elseif ($this->isPreviewRequested()) {
                // pick the preview template if the form was valid and preview was requested
                $templateKey = 'preview';
            }
        }

        $view = $form->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());

        $useTheme = $this->container->getParameter('use_liip_theme');
        $theme = $this->get('request')->get('theme');
        if (is_null($theme)) {
            $theme_suffix = '';
        } else {
            $theme_suffix = '|' . $theme;
        }

        return $this->render($this->admin->getTemplate($templateKey), array(
            'action' => 'create',
            'form'   => $view,
            'object' => $object,
            'use_liip_theme' => $useTheme,
            'liip_theme' => $theme,
            'liip_theme_suffix' => $theme_suffix
        ));
    }

    public function clonePageToUrlAction()
    {
        $request = $this->get('request');
        $page_id = $request->get('id');
        $object = $this->admin->getObject($page_id);
        $form = $this->createForm(new ClonePageForm);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $url = $form->get('url')->getData();
                // create new page and redirect to edit page
                $newPage = $this->container->get('raindrop_page.page.manager')->clonePageToUrl($object, $url);
                return $this->redirect($this->generateUrl('admin_raindrop_page_page_edit', array( 'id' => $newPage->getId() )));
            }
        }



        return $this->render('RaindropPageBundle:Page:clone_page.html.twig', array(
            'object' => $object,
            'form' => $form->createView(),
            'action' => 'clone_page_to_url'
        ));
    }
}
