<?php

namespace Raindrop\PageBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * Description of MultipleTextType
 *
 * @author teito
 */
class MultipleTextType extends TextType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // it does nothing but maybe one day will do something?
        parent::buildView($view, $form, $options);

        $view->vars['full_name'] = $view->vars['full_name'].'[]';
    }

    //put your code here
    public function getName()
    {
        return 'multiple_text';
    }
}
