<?php

namespace Raindrop\PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Raindrop\RoutingBundle\Routing\Base\RouteObjectInterface;
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
    protected $country;

    /**
     * @ORM\Column(nullable=true)
     */
    protected $locale;

    /**
     * @ORM\ManyToOne(targetEntity="Raindrop\RoutingBundle\Entity\Route")
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
     * A convenient proxy method
     *
     * @return type
     */
    public function getLastModified() {
        return $this->getUpdated();
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

    public function hasRoute() {
        return $this->route instanceof RouteObjectInterface;
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

    /**
     * Set title
     *
     * @param string $title
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
     * @param array $metasName
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
     * @param array $metasProperty
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
     * @param array $metasHttpEquiv
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
     * @param string $country
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
}