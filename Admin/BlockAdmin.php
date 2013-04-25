<?php

namespace Raindrop\PageBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Raindrop\PageBundle\Form\EventListener\AddAssetsFieldSubscriber;
use Symfony\Component\DependencyInjection\Container;
use Raindrop\PageBundle\Form\EventListener\AddBlockVariablesSubscriber;

class BlockAdmin extends Admin
{
    protected $blockService, $container;

    public function setBlockService($blockService) {
        $this->blockService = $blockService;
    }

    public function setContainer($container) {
        $this->container = $container;
    }


    protected function configureFormFields(FormMapper $formMapper)
    {
//        $formMapper
//                ->add('name', null, array('required' => true))
//        ;

        $variables = $this->getSubject()->getVariables();

        foreach ($variables as $variable) {
            $this->mapVariables($formMapper, $variable);
        }

        $builder = $formMapper->getFormBuilder();

        /**
         * Adds assets input fields and cleans up data on submit.
         */
//        $builder->addEventSubscriber(new AddAssetsFieldSubscriber($builder->getFormFactory()));

        /**
         * following listener does nothing but unset 'variables'
         * array key before binding to skip validation
         */
        $builder->addEventSubscriber(new AddBlockVariablesSubscriber($builder->getFormFactory()));
    }

    protected function mapVariables($formMapper, $variable) {
        $options = $variable->getOptions();

        if (!isset($options['required'])) {
            $options['required'] = false;
        }

        switch ($variable->getType()) {
            case 'service':
                $services = $this->container->get('raindrop_page.services.provider')->provide();
                $formMapper
                    ->add($variable->getName(), 'nested_choice', array(
                        'required' => true,
                        'choices' => $services,
                        'data' => $variable->getContent() ?: '',
                        'mapped' => false,
                        'required' => $options['required'],
                        'nested_name' => '[variables][' . $variable->getName() . '][content]'
                    ));
                break;
            case 'entity':
                $orm = $this->container->get('doctrine.orm.default_entity_manager');
                $allEntities = $orm->getRepository($options['model'])->findAll();

                $choices = array();
                $getter = 'get' . Container::camelize($options['human-identifier']);

                array_walk($allEntities, function ($entity) use (&$choices, $getter) {
                    $choices [$entity->getId()]= $entity->$getter();
                });

                $formMapper
                    ->add($variable->getName(), 'nested_choice', array(
                        'label' => $options['label'],
                        'required' => true,
                        'choices' => $choices,
                        'data' => $variable->getContent() ?: '',
                        'mapped' => false,
                        'required' => $options['required'],
                        'nested_name' => '[variables][' . $variable->getName() . '][content]'
                    ));

                break;
            case 'text':
                $formMapper
                    ->add($variable->getName(), 'nested_text', array(
                        'data' => $variable->getContent() ?: '',
                        'nested_name' => '[variables][' . $variable->getName() . '][content]',
                        'mapped' => false,
                        'required' => $options['required'],
                        'attr' => array(
                            'class' => 'span5'
                        )
                    ));
                break;
            case 'textarea':
                $formMapper
                    ->add($variable->getName(), 'nested_textarea', array(
                        'data' => $variable->getContent() ?: '',
                        'nested_name' => '[variables][' . $variable->getName() . '][content]',
                        'mapped' => false,
                        'required' => $options['required'],
                        'attr' => array(
                            'class' => 'span5'
                        )
                    ));
            default:
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
            ->addIdentifier('page')
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

    public function postUpdate($block) {
        $query = $this->container->get('request')->query->all();
        $uniqid = $query['uniqid'];
        $requestParams = $this->container->get('request')->request->all();
        $formParams = $requestParams[$uniqid];

        if (isset($formParams['variables'])) {
            $variables = $formParams['variables'];
            $orm = $this->container->get('doctrine.orm.default_entity_manager');
            $blockVariablesRepo = $orm->getRepository('Raindrop\PageBundle\Entity\BlockVariable');

            if (!empty($variables)) {
                foreach ($variables as $name => $content) {
                    $previous = $blockVariablesRepo->findOnyByNameAndBlock($name, $block);
                    if ($previous) {
                        $previous->setContent($content['content']);
                    }
                }
            }
        }
        $orm->flush();
    }
}