<?php

namespace Raindrop\PageBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Description of NestedTextType
 *
 * @author teito
 */
class NestedTextareaType extends TextType {

    public function buildView(FormView $view, FormInterface $form, array $options) {

        parent::buildView($view, $form, $options);

        $fullname = $this->getNestedName($view->vars['full_name'], $options['nested_name']);
        $view->vars['full_name'] = $fullname;
    }

    protected function getNestedName($name, $nestedName) {
        $name = substr($name, 0, strpos($name, '['));
        return $name . $nestedName;
    }

    //put your code here
    public function getName() {
        return 'nested_textarea';
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

?>
