<?php

namespace Raindrop\PageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Description of ClonePageForm
 *
 * @author teito
 */
class ClonePageForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('url', 'text', array('required' => true));
    }

    public function getName()
    {
        return 'clone_page';
    }
}

?>
