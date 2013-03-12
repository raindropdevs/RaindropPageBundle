<?php

namespace Raindrop\PageBundle\Directory;

class Tree {

    protected $routeRepository, $tree;

    public function __construct($routeRepository) {
        $this->routeRepository = $routeRepository;
    }

    public function buildTree() {
        $routes = $this->routeRepository->findAll();

        $this->tree = array();

        foreach ($routes as $route) {

            $array = $this->getCleanPathArray($route->getPath());
            $root = array_shift($array);

            if (!isset($this->tree[$root])) {
                $this->tree[$root] = new Node($root);
            }

            $current = $this->tree[$root];

            foreach ($array as $dir) {

                if (!empty($dir)) {
                    if (!$current->hasChild($dir)) {
                        $current->addChild(new Node($dir, $current));
                    }

                    $current = $current->getChild($dir);
                }
            }
        }

        return $this;
    }

    public function toArray() {
        $return = array();
        foreach ($this->tree as $node) {
            $return []= $node->toArray();
        }
        return $return;
    }

    public function dumpGraph() {
        foreach ($this->tree as $node) {
            echo $node->dumpGraph();
        }
    }

    public function getCleanPathArray($path) {
        return array_filter(explode("/", $path), function ($element) {
            return !empty($element);
        });
    }

    public function getTree() {

    }
}