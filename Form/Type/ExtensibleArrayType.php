<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Raindrop\PageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ExtensibleArrayType extends AbstractType
{
    protected $builder;

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->builder = $builder;

        $this->addNestedGroups($options['data']);

        if ($options['prototype']) {
            $prototype = $builder->create($options['prototype_name'], 'text', array_replace(array(
                'label' => $options['prototype_name'] . 'label__',
            ), $options['options']));
            $builder->setAttribute('prototype', $prototype->getForm());
        }
    }

    protected function addNestedGroups($array) {

        if (empty($array)) {
            return;
        }

        foreach ($array as $name => $config) {
            if ($config instanceof FormBuilderInterface) {
                $this->builder->add($config);
            } else {
                if (is_array($config)) {
                    $this->addGroup($name, $config);
                }
            }
        }
    }

    protected function addGroup($name, $config) {
        // type options
        $this->builder->add($name . ':type', 'nested_text', array(
            'label' => 'Type',
            'nested_name' => '[options][' . $name . '][type]',
            'data' => $config['type'],
            'attr' => array(
                'class' => 'span5'
            ),
            'label_attr' => array(
                'class' => 'span2 raindropLabel'
            )
        ));

        // form field options
        switch ($config['type']) {
            case 'text':
                break;
            case 'entity':
                $this->builder->add($name . ':options:model', 'nested_text', array(
                    'label' => 'Model',
                    'nested_name' => '[options][' . $name . '][options][model]',
                    'data' => $config['options']['model'],
                    'attr' => array(
                        'class' => 'span5'
                    ),
                    'label_attr' => array(
                        'class' => 'span2 raindropLabel'
                    )
                ));
                $this->builder->add($name . ':options:label', 'nested_text', array(
                    'label' => 'Label',
                    'nested_name' => '[options][' . $name . '][options][label]',
                    'data' => $config['options']['label'],
                    'attr' => array(
                        'class' => 'span5'
                    ),
                    'label_attr' => array(
                        'class' => 'span2 raindropLabel'
                    )
                ));
                $this->builder->add($name . ':options:human-identifier', 'nested_text', array(
                    'label' => 'Human identifier',
                    'nested_name' => '[options][' . $name . '][options][human-identifier]',
                    'data' => $config['options']['human-identifier'],
                    'attr' => array(
                        'class' => 'span5'
                    ),
                    'label_attr' => array(
                        'class' => 'span2 raindropLabel'
                    )
                ));
                break;
        }
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        if ($form->getConfig()->hasAttribute('prototype')) {
            $view->vars['prototype'] = $form->getConfig()->getAttribute('prototype')->createView($view);
        }

        $groups = array();

        foreach ($form as $widget) {
            $name = $widget->getName();
            $arr = explode(':', $name);
            $fieldName = $arr[0];
            $type = $arr[1];

            if (!isset($groups[$fieldName])) {
                $groups[$fieldName] = array(
                    'type' => $fieldName,
                    'children' => array()
                );
            }

            // utilizzo il tipo per formattare la label
            if ($type === 'type') {
                $groups[$fieldName]['type']= $widget->getData();
            }

            $groups[$fieldName]['children'][]= $widget->getName();
        }

        $view->vars['form_widgets'] = $groups;
    }

    protected function addNestedVars($array, $root = '') {

        $vars = array();

        foreach ($array as $name => $value) {
            if (is_array($value)) {
                $vars += $this->addNestedVars($value, $name);
            } else {
                $vars[$value] = '[' . $root . '][' . $name . ']';
            }
        }

        return $vars;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'keys'    => array(),
            'prototype' => true,
            'prototype_name' => 'placeholder',
            'options' => array()
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'extensible_array';
    }
}
