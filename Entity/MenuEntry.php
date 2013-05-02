<?php

namespace Raindrop\PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Raindrop\PageBundle\Entity\Page;
use Raindrop\PageBundle\Entity\Menu;

/**
 * @ORM\Entity(repositoryClass="Raindrop\PageBundle\Entity\PageMenuRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="raindrop_menu_entry")
 */
class MenuEntry
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Raindrop\PageBundle\Entity\Page", inversedBy="menus")
     */
    protected $page;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $position;

    /**
     * @ORM\ManyToOne(targetEntity="Raindrop\PageBundle\Entity\Menu")
     */
    protected $menu;

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
     * Set position
     *
     * @param  integer   $position
     * @return MenuEntry
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set page
     *
     * @param  \Raindrop\PageBundle\Entity\Page $page
     * @return MenuEntry
     */
    public function setPage(Page $page = null)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return \Raindrop\PageBundle\Entity\Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set menu
     *
     * @param  \Raindrop\PageBundle\Entity\Menu $menu
     * @return MenuEntry
     */
    public function setMenu(Menu $menu = null)
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * Get menu
     *
     * @return \Raindrop\PageBundle\Entity\Menu
     */
    public function getMenu()
    {
        return $this->menu;
    }
}
