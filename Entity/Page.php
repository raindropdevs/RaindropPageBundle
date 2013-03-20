<?php

namespace Raindrop\PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Raindrop\RoutingBundle\Entity\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Raindrop\PageBundle\Entity\Block;

/**
 * @ORM\Entity(repositoryClass="Raindrop\PageBundle\Entity\PageRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="pages")
 */
class Page
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
    protected $locale;


    /**
     * @ORM\OneToOne(targetEntity="Raindrop\RoutingBundle\Entity\Route")
     */
    protected $route;

    /**
     * @ORM\OneToMany(targetEntity="Raindrop\PageBundle\Entity\Block", mappedBy="page")
     */
    protected $children;


    /**
     * @ORM\Column
     */
    protected $layout;

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
     * @param string $name
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
     * @param string $type
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
     * Set locale
     *
     * @param string $locale
     * @return Page
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
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
     * @param \DateTime $updated
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
     * Set controller on the target route
     *
     * @param string $controller
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
     * @param string $layout
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
    public function prePersist() {
        $this->setCreated(new \DateTime);
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function preUpdate() {
        $this->setUpdated(new \DateTime);
    }

    /**
     * Set route
     *
     * @param \Raindrop\RoutingBundle\Entity\Route $route
     * @return Page
     */
    public function setRoute(Route $route = null)
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

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * __toString method
     */
    public function __toString() {
        return $this->name;
    }


    /**
     * Add children
     *
     * @param \Raindrop\PageBundle\Entity\Block $children
     * @return Page
     */
    public function addChildren(Block $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Raindrop\PageBundle\Entity\Block $children
     */
    public function removeChildren(Block $children)
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
}