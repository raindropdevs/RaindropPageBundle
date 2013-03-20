<?php

namespace Raindrop\PageBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class BlockVariableAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $contentOptions = $this->getSubject()->getOptions();

        // required is set as default parameter to true.
        if (!isset($contentOptions['required'])) {
            $contentOptions['required'] = true;
        }

        $formMapper
                ->add('name', null, array('read_only' => true))
                ->add('content', $this->getSubject()->getType(), $contentOptions)
        ;
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
            ->addIdentifier('type')
            ->addIdentifier('content')

            // add custom action links
//            ->add('_action', 'actions', array(
//                'actions' => array(
//                    'view' => array(),
//                    'edit' => array()
//                )
//            ))
        ;
    }

    public function preUpdate($variable)
    {
    }

    public function postUpdate($variable)
    {
    }
}