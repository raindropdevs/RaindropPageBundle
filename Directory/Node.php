<?php

namespace Raindrop\PageBundle\Directory;

class Node {

    protected $path;

    protected $name;

    protected $parent;

    protected $children = array();

    public function __construct($name, $parent = '_ROOT_') {
        $this->name = $name;
        $this->parent = $parent;
        $this->initPath();
    }

    public function initPath() {

        $base = DIRECTORY_SEPARATOR;
        if ($this->parent instanceof Node) {
            $base = $this->parent->getPath() . DIRECTORY_SEPARATOR;
        }

        $this->setPath($base . $this->getName());
    }

    public function getPath() {
        return $this->path;
    }

    public function setPath($path) {
        $this->path = $path;

        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getParent() {
        return $this->parent;
    }

    public function setParent($parent) {
        $this->parent = $parent;
    }

    public function hasParent() {
        return $this->parent instanceof Node;
    }

    public function getParentPath() {
        if ($this->hasParent()) {
            return $this->getParent()->getPath();
        }

        return null;
    }

    public function getChildren() {
        return $this->children;
    }

    public function hasChildren() {
        return !empty($this->children);
    }

    public function hasChild($name) {
        return isset($this->children[$name]);
    }

    public function getChild($name) {
        return $this->children[$name];
    }

    public function addChild($node) {
        $this->children[$node->getName()] = $node;

        return $this;
    }

    public function toArray() {

        $return = array(
            'name' => $this->getName(),
            'path' => $this->getPath(),
            'parent' => $this->getParentPath(),
            'children' => array()
        );

        if ($this->hasChildren()) {
            foreach ($this->getChildren() as $child) {
                $return ['children'][$child->getName()] = $child->toArray();
            }
        }

        return $return;
    }

    public function dumpGraph($indent = 0) {

        $string = '';

        if ($indent > 0) {
            if ($indent > 1) {
                for ($i = 0; $i < $indent - 1; $i++) {
                    $string .= '   ';
                }
            }

            $string .= ' + ';
        }

        var_dump($string . $this->getName());

        if ($this->hasChildren()) {
            foreach ($this->getChildren() as $child) {
                $child->dumpGraph($indent+1);
            }
        }
    }
}