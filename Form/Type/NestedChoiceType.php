<?php

namespace Raindrop\PageBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Description of NestedChoiceType
 *
 * @author teito
 */
class NestedChoiceType extends ChoiceType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // it does nothing but maybe one day will do something?
        parent::buildView($view, $form, $options);

        $fullname = $this->getNestedName($view->vars['full_name'], $options['nested_name']);
        $view->vars['full_name'] = $fullname;
    }

    //put your code here
    public function getName()
    {
        return 'nested_choice';
    }

    protected function getNestedName($name, $nestedName)
    {
        $name = substr($name, 0, strpos($name, '['));

        return $name . $nestedName;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'nested_name'    => '',
        ));
    }
}
