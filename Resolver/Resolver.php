<?php

namespace Raindrop\PageBundle\Resolver;

/**
 * Description of Resolver
 *
 * @author teito
 */
class Resolver {

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
                case 'entity':
                    $content = $this->resolveEntity($variable);
                    break;
                case 'text':
                case 'textarea':
                    $content = $variable->getContent();
                    break;
                case 'service':
                    $content = $this->resolveService($variable);
                    break;
            }

            if (!empty($content)) {
                $return[$variable->getName()] = $content;
            }
        }
        return $return;
    }

    protected function resolveEntity($variable) {
        $orm = $this->container
                ->get('doctrine.orm.default_entity_manager');

        $options = $variable->getOptions();
        if ($variable->getContent()) {
            return $orm
                    ->getRepository($options['model'])
                    ->find($variable->getContent());
        }

        return null;
    }

    protected function resolveService($variable) {
        $service_id = $variable->getContent();
        if ($service_id) {
            return $this->container->get($service_id);
        }
    }
}

?>
