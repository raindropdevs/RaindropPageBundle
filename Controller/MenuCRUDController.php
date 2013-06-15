<?php

namespace Raindrop\PageBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;

use Symfony\Component\HttpFoundation\JsonResponse;

class MenuCRUDController extends CRUDController
{
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

        // the sources
//        $pages = $this->container
//            ->get('raindrop_page.page.manager')
//            ->getCountryPages($object->getCountry());

        // the actual menu
        $menuPages = $this
            ->get('raindrop_page.page.manager')
            ->getPagesForMenu($object);

        return $this->render($this->admin->getTemplate($templateKey), array(
            'action' => 'edit',
            'form'   => $view,
            'object' => $object,
//            'pages' => $pages,
            'menu_pages' => $this
                ->get('raindrop_page.directory_tree')
                ->buildTree($menuPages)
                ->getTree()
        ));
    }

    public function appendAction()
    {
        $request = $this->get('request');

        $menu_id = $request->get('menu_id');
        $page_id = $request->get('page_id');

        try {
            $this
                ->get('raindrop_page.menu.manager')
                ->appendPageToMenu($page_id, $menu_id);
        } catch (\Exception $e) {
            return new JsonResponse(array('result' => $e->getMessage()), 500);
        }

        return new JsonResponse(array('result' => true));
    }

    public function reorderAction()
    {
        try {
            $request = $this->get('request');

            $ids = $request->get('ids');

            if (!is_array($ids)) {
                return;
            }

            $this->get('raindrop_page.menu.manager')->reorderItems($ids);
        } catch (\Exception $e) {
            return new JsonResponse(array('result' => $e->getMessage()), 500);
        }

        return new JsonResponse(array('result' => true));
    }
}
