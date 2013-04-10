<?php

namespace Raindrop\PageBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\Container;
use Sonata\AdminBundle\Route\RouteCollection;

class BlockVariableAdmin extends Admin
{
    protected $container;

    public function setContainer($container) {
        $this->container = $container;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $contentOptions = $this->getSubject()->getOptions();

        switch ($this->getSubject()->getType()) {
            case 'entity':
                $options = $this->getSubject()->getOptions();
                $orm = $this->container->get('doctrine.orm.default_entity_manager');
                $allEntities = $orm->getRepository($options['model'])->findAll();

                $choices = array();
                $getter = 'get' . Container::camelize($options['human-identifier']);

                array_walk($allEntities, function ($entity) use (&$choices, $getter) {
                    $choices [$entity->getId()]= $entity->$getter();
                });

                $formMapper
                    ->add('name', null, array('read_only' => true))
                    ->add('content', 'choice', array(
                        'label' => $options['label'],
                        'required' => true,
                        'choices' => $choices,
                        'data' => $this->getSubject()->getContent() ?: ''
                    ));

                break;
            default:
                // required is set as default parameter to true.
                if (!isset($contentOptions['required'])) {
                    $contentOptions['required'] = true;
                }

                $formMapper
                    ->add('name', null, array('read_only' => true))
                    ->add('content', $this->getSubject()->getType(), $contentOptions);

                break;
        }
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
        ;
    }

    public function preUpdate($variable)
    {
    }

    public function postUpdate($variable)
    {
    }
}