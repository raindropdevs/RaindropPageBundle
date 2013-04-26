<?php

namespace Raindrop\PageBundle\Resolver;

/**
 * Description of Resolver
 *
 * @author teito
 */
class ResolverFactory {

    protected $container;

    public function setContainer($container) {
        $this->container = $container;
    }

    //put your code here
    public function resolve($variables) {

        $return = array();

        foreach ($variables as $variable) {

            /**
             * and assign to array only if not empty
             * resolve $variable->getContent() to $content
             */
            switch ($variable->getType()) {
                case 'text':
                case 'textarea':
                    $resolver = new SimpleResolver();
                    break;
                case 'entity':
                    $resolver = new EntityResolver($this->container);
                    break;
                case 'service':
                    $resolver = new ServiceResolver($this->container);
                    break;
            }

            $content = $resolver->resolve($variable);

            if (!empty($content)) {
                $return[$variable->getName()] = $content;
            }
        }
        return $return;
    }
}

?>
