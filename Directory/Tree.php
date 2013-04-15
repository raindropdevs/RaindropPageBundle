<?php

namespace Raindrop\PageBundle\Directory;

use Raindrop\PageBundle\Directory\Node;

class Tree {

    protected $pagesRepository, $root;

    public function __construct($pagesRepository) {
        $this->pagesRepository = $pagesRepository;
    }

    public function buildTree() {
        $pages = $this->pagesRepository->findAll();

        $this->root = new Node(Node::ROOT);

        foreach ($pages as $page) {

            if (!$page->hasRoute()) {
                continue;
            }

            if ($page->getRoute()->getPath() == '/') {
                $this->root->setPageId($page->getId());
            }

            if (is_null($page->getRoute())) {
                continue;
            }

            $array = $this->getCleanPathArray($page->getRoute()->getPath());

            $current = $this->root;

            foreach ($array as $dir) {

                if (!empty($dir)) {
                    if (!$current->hasChild($dir)) {
                        $node = new Node($dir, $current, $page->getTitle());

                        if ($node->getPath() == $page->getRoute()->getPath()) {
                            $node->setPageId($page->getId());
                            $node->setTitle($page->getTitle());
                        }

                        $current->addChild($node);
                    }

                    $current = $current->getChild($dir);
                }
            }
        }

        return $this;
    }

    public function fromRootArray($root) {

        $this->root = new Node(Node::ROOT);
        $this->importChildren($this->root, $root);

        return $this;
    }

    public function importChildren($node, $array) {
        foreach ($array['children'] as $child) {
            $node->addChild(new Node($child['name'], $node));

            if (!empty($child['children'])) {
                $this->importChildren($node->getChild($child['name']), $child);
            }
        }
    }

    public function toArray() {
        return $this->root->toArray();
    }

    public function dumpGraph() {
        echo $this->root->dumpGraph();
    }

    public function getCleanPathArray($path) {
        return array_filter(explode("/", $path), function ($element) {
            return !empty($element);
        });
    }

    public function getTree() {
        return $this->root;
    }
}