<?php

namespace Raindrop\PageBundle\Form\Type;

use Raindrop\PageBundle\Form\Type\NestedTextType;

/**
 * Description of NestedTextType
 *
 * @author teito
 */
class NestedTextareaType extends NestedTextType
{
    public function getName()
    {
        return 'nested_textarea';
    }
}
