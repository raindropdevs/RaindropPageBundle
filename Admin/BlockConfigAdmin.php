<?php

namespace Raindrop\PageBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Raindrop\PageBundle\Form\EventListener\AddAssetsFieldSubscriber;
use Raindrop\PageBundle\Form\EventListener\AddOptionsFieldSubscriber;

/**
 * Description of BlockConfigAdmin
 *
 * @author teito
 */
class BlockConfigAdmin extends Admin {

    protected $container;

    public function setContainer($container) {
        $this->container = $container;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
                ->add('name', null, array('required' => false))
                ->add('type', null, array('required' => true))
                ->add('template', null, array('required' => true))
                ->add('description', null, array('required' => false))
                ->add('options', 'extensible_array', array(
                    'required' => false,
                    'data' => $this->getSubject()->getOptions()
                ));
        ;

        $builder = $formMapper->getFormBuilder();
        $builder->addEventSubscriber(new AddAssetsFieldSubscriber($builder->getFormFactory()));
        $builder->addEventSubscriber(new AddOptionsFieldSubscriber());
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->addIdentifier('template')
        ;
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'edit':
                return 'RaindropPageBundle:Block:block_editor.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }

    /**
     * Filter empty input
     *
     * @param type $block
     * @return type
     */
    public function preUpdate($block)
    {
        $jss = array_filter($block->getJavascripts(), function ($js) {
            return !empty($js);
        });

        $css = array_filter($block->getStylesheets(), function ($cs) {
            return !empty($cs);
        });

        $block->setJavascripts($jss);
        $block->setStylesheets($css);
    }

    /**
     * Since there's no way to submit a nested array into a form
     * field without making validation fail because of extra fields,
     * on bind we set the field to an empty array and then recover
     * values after validation and bind.
     * @param type $page
     */
    public function postPersist($block) {
        $orm = $this->container->get('doctrine.orm.default_entity_manager');

        $query = $this->container->get('request')->query->all();
        $uniqid = $query['uniqid'];
        $requestParams = $this->container->get('request')->request->all();
        $formParams = $requestParams[$uniqid];

        if (isset($formParams['options'])) {
            $block->setOptions($formParams['options']);
            $orm->flush();
        }

    }

    public function postUpdate($block) {
        $this->postPersist($block);
    }
}

?>
