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

            switch ($variable->getType()) {
                case 'entity':
                    $return [$variable->getName()]= $this->resolveEntity($variable);
                    break;
                case 'text':
                case 'textarea':
                    $return [$variable->getName()]= $variable->getContent();
                    break;
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
    }
}

?>
