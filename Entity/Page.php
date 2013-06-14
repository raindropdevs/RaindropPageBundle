<?php

namespace Raindrop\PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Raindrop\RoutingBundle\Routing\Base\RouteObjectInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Raindrop\PageBundle\Entity\Block;
use Raindrop\PageBundle\Renderer\RenderableObjectInterface;

/**
 * @ORM\Entity(repositoryClass="Raindrop\PageBundle\Entity\PageRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="pages")
 */
class Page implements RenderableObjectInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column
     */
    protected $name;

    /**
     * @ORM\Column(nullable=true)
     */
    protected $type;

    /**
     * @ORM\Column(nullable=true)
     */
    protected $country;

    /**
     * @ORM\ManyToOne(targetEntity="Raindrop\RoutingBundle\Entity\Route")
     */
    protected $route;

    /**
     * @ORM\OneToMany(targetEntity="Raindrop\PageBundle\Entity\Block", mappedBy="page")
     */
    protected $blocks;

    /**
     * @ORM\OneToMany(targetEntity="Raindrop\PageBundle\Entity\Page", mappedBy="parent")
     */
    protected $children;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Raindrop\PageBundle\Entity\Page", inversedBy="children")
     */
    protected $parent;

    /**
     * @ORM\Column
     */
    protected $layout;

    /**
     * @ORM\Column
     */
    protected $title;

    /**
     * @ORM\Column(type="array")
     */
    protected $metas_name;

    /**
     * @ORM\Column(type="array")
     */
    protected $metas_property;

    /**
     * @ORM\Column(type="array")
     */
    protected $metas_http_equiv;

    /**
     * @ORM\OneToMany(targetEntity="Raindrop\PageBundle\Entity\MenuEntry", mappedBy="page")
     */
    protected $menus;

    /**
     * @ORM\Column(nullable=true)
     *
     * @var type
     */
    protected $expiresAfter;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param  string $name
     * @return Page
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type
     *
     * @param  string $type
     * @return Page
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->route->getLocale();
    }

    /**
     * Set created
     *
     * @param  \DateTime $created
     * @return Page
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param  \DateTime $updated
     * @return Page
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * A convenient proxy method
     *
     * @return type
     */
    public function getLastModified()
    {
        return $this->getUpdated();
    }

    /**
     * Set controller on the target route
     *
     * @param  string $controller
     * @return Page
     */
    public function setController($controller)
    {
        $this->route->setController($controller);

        return $this;
    }

    /**
     * Get controller
     *
     * @return string
     */
    public function getController()
    {
        return $this->route->getController();
    }

    /**
     * Set layout
     *
     * @param  string $layout
     * @return Page
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * Get layout
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setCreated(new \DateTime);
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->setUpdated(new \DateTime);
    }

    /**
     * Set route
     *
     * @param  \Raindrop\RoutingBundle\Entity\Route $route
     * @return Page
     */
    public function setRoute(RouteObjectInterface $route = null)
    {
        if ($this->getId()) {
            $route->setContent($this);
        }

        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return \Raindrop\RoutingBundle\Entity\Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    public function hasRoute()
    {
        return $this->route instanceof RouteObjectInterface;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->blocks = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * __toString method
     */
    public function __toString()
    {
        return (string) $this->name;
    }

    public function setChildren($children)
    {
        $this->children = $children;
    }

    /**
     * Add children
     *
     * @param  \Raindrop\PageBundle\Entity\Page $children
     * @return Page
     */
    public function addChildren(Page $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Raindrop\PageBundle\Entity\Page $children
     */
    public function removeChildren(Page $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        $iterator = $this->children->getIterator();

        $iterator->uasort(function ($first, $second) {
            return (int) $first->getPosition() > (int) $second->getPosition() ? 1 : -1;
        });

        return $iterator;
    }

    /**
     * Set title
     *
     * @param  string $title
     * @return Page
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set metas_name
     *
     * @param  array $metasName
     * @return Page
     */
    public function setMetasName($metasName)
    {
        $this->metas_name = $metasName;

        return $this;
    }

    /**
     * Get metas_name
     *
     * @return array
     */
    public function getMetasName()
    {
        return $this->metas_name;
    }

    /**
     * Set metas_property
     *
     * @param  array $metasProperty
     * @return Page
     */
    public function setMetasProperty($metasProperty)
    {
        $this->metas_property = $metasProperty;

        return $this;
    }

    /**
     * Get metas_property
     *
     * @return array
     */
    public function getMetasProperty()
    {
        return $this->metas_property;
    }

    /**
     * Set metas_http_equiv
     *
     * @param  array $metasHttpEquiv
     * @return Page
     */
    public function setMetasHttpEquiv($metasHttpEquiv)
    {
        $this->metas_http_equiv = $metasHttpEquiv;

        return $this;
    }

    /**
     * Get metas_http_equiv
     *
     * @return array
     */
    public function getMetasHttpEquiv()
    {
        return $this->metas_http_equiv;
    }

    /**
     * Set country
     *
     * @param  string $country
     * @return Page
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set menus
     *
     * @param  string $menus
     * @return Page
     */
    public function setMenus($menus)
    {
        $this->menus = $menus;

        return $this;
    }

    /**
     * Get menus
     *
     * @return string
     */
    public function getMenus()
    {
        return $this->menus;
    }

    /**
     * Add menus
     *
     * @param  \Raindrop\PageBundle\Entity\MenuEntry $menus
     * @return Page
     */
    public function addMenu(\Raindrop\PageBundle\Entity\MenuEntry $menus)
    {
        $this->menus[] = $menus;

        return $this;
    }

    /**
     * Remove menus
     *
     * @param \Raindrop\PageBundle\Entity\MenuEntry $menus
     */
    public function removeMenu(\Raindrop\PageBundle\Entity\MenuEntry $menus)
    {
        $this->menus->removeElement($menus);
    }

    /**
     * Set expiresAfter
     *
     * @param  string $expiresAfter
     * @return Page
     */
    public function setExpiresAfter($expiresAfter)
    {
        $this->expiresAfter = $expiresAfter;

        return $this;
    }

    /**
     * Get expiresAfter
     *
     * @return string
     */
    public function getExpiresAfter()
    {
        /**
         * On empty expiresAfter, default is one week
         */
        if (empty($this->expiresAfter)) {
            return 86400 * 7;
        }

        return $this->expiresAfter;
    }

    public function getParameters()
    {
        return array(
            'blocks' => $this->getBlocks(),
            'raindrop_locale' => $this->getRoute()->getLocale(),
            'raindrop_country' => $this->getCountry(),
            'raindrop_page' => $this
        );
    }

    public function getParentUrl()
    {
        if ($this->hasRoute()) {
            $path = $this->getRoute()->getPath();

            return dirname($path);
        }

        return false;
    }

    public function getGrandpaUrl()
    {
        if ($this->getParentUrl()) {
            return dirname($this->getParentUrl());
        }

        return false;
    }

    public function getUrl()
    {
        if ($this->hasRoute()) {
            return $this->getRoute()->getPath();
        }

        return false;
    }

    public function getPageDepth()
    {
        $arr = explode("/", $this->getRoute()->getPath());

        return count(array_filter($arr, function ($el) {
            return !empty($el);
        }));
    }

    /**
     * Add blocks
     *
     * @param  \Raindrop\PageBundle\Entity\Block $blocks
     * @return Page
     */
    public function addBlock(\Raindrop\PageBundle\Entity\Block $blocks)
    {
        $this->blocks[] = $blocks;

        return $this;
    }

    /**
     * Remove blocks
     *
     * @param \Raindrop\PageBundle\Entity\Block $blocks
     */
    public function removeBlock(\Raindrop\PageBundle\Entity\Block $blocks)
    {
        $this->blocks->removeElement($blocks);
    }

    /**
     * Get blocks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * Set parent
     *
     * @param  \Raindrop\PageBundle\Entity\Page $parent
     * @return Page
     */
    public function setParent(\Raindrop\PageBundle\Entity\Page $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Raindrop\PageBundle\Entity\Page
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function hasParent()
    {
        return $this->parent instanceof Page;
    }
}
