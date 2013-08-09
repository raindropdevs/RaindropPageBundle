<?php

namespace Raindrop\PageBundle\Directory;

use Knp\Menu\NodeInterface;

/**
 * This class is used to represent website tree.
 */
class Node implements NodeInterface
{
    const ROOT = '__ROOT__';

    protected $path;

    protected $name;

    protected $label;

    protected $title;

    protected $class;

    protected $parent;

    protected $page_id;

    protected $menu_id;

    protected $image;

    protected $absolute = false;

    protected $children = array();

    public function __construct($name, $parent = self::ROOT, $label = null)
    {
        $this->name = $name;

        if ($label) {
            $this->setLabel($label);
        } else {
            $this->setLabel($name);
        }

        $this->parent = $parent;
        $this->initPath();
    }

    public function initPath()
    {
        $base = DIRECTORY_SEPARATOR;
        if ($this->parent instanceof Node && $this->parent->getName() != self::ROOT) {
            $base = $this->parent->getPath() . DIRECTORY_SEPARATOR;
        }

        $suffix = $this->name != self::ROOT ? $this->getName() : '';

        $this->setPath($base . $suffix);
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getCompletePath()
    {
        if ($this->isAbsolute()) {
            return $this->absolute . $this->path;
        }

        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setPageId($page_id)
    {
        $this->page_id = $page_id;
    }

    public function getPageId()
    {
        return $this->page_id;
    }

    public function setMenuId($menu_id)
    {
        $this->menu_id = $menu_id;
    }

    public function getMenuId()
    {
        return $this->menu_id;
    }

    public function hasMenuId()
    {
        return !empty($this->menu_id);
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    public function hasParent()
    {
        return $this->parent instanceof Node;
    }

    public function getParentPath()
    {
        if ($this->hasParent()) {
            return $this->getParent()->getPath();
        }

        return null;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function setChildren($children)
    {
        return $this->children = $children;
    }

    public function hasChildren()
    {
        return !empty($this->children);
    }

    public function hasChild($name)
    {
        return isset($this->children[$name]);
    }

    public function getChild($name)
    {
        return $this->children[$name];
    }

    public function addChild($node)
    {
        $this->children[$node->getName()] = $node;

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getOptions()
    {
        $options = array(
            'uri' => $this->getCompletePath(),
            'label' => $this->getLabel(),
            'childrenAttributes' => array(
                'class' => $this->class ?: 'links'
            )
        );

        if (!empty($this->image)) {
            $options['linkAttributes'] = array(
                'rel' => $this->getImage()
            );
        }

        return $options;
    }

    public function toArray()
    {
        $return = array(
            'name' => $this->getLabel(),
            'path' => $this->getPath(),
            'page_id' => $this->getPageId(),
            'title' => $this->getLabel(),
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

    public function dumpGraph($indent = 0)
    {
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

    public function setAbsolute($absolute)
    {
        $this->absolute = $absolute;
    }

    public function isAbsolute()
    {
        return !empty($this->absolute);
    }

    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function getDepth()
    {
        $arr = explode("/", $this->path);

        return count(array_filter($arr, function ($el) {
            return !empty($el);
        }));
    }
}
