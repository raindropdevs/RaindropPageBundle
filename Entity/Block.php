<?php

namespace Raindrop\PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sonata\BlockBundle\Model\Block as BaseBlock;

/**
 * @ORM\Entity(repositoryClass="Raindrop\PageBundle\Entity\BlockRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="blocks")
 */
class Block extends BaseBlock
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(unique=true)
     */
    protected $name;

    /**
     * @ORM\Column
     */
    protected $template;

    /**
     * @ORM\ManyToOne(targetEntity="Raindrop\PageBundle\Entity\Page")
     */
    protected $page;

    /**
     * @ORM\ManyToOne(targetEntity="Raindrop\PageBundle\Entity\Block")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Raindrop\PageBundle\Entity\Block", mappedBy="parent")
     */
    protected $children;

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

    public function getSettings() {
        return array(

        );
    }

    public function getType() {
        return 'raindrop_page.block.service.template';
    }

//    /**
//     * Set created
//     *
//     * @param \DateTime $created
//     * @return Block
//     */
//    public function setCreated($created)
//    {
//        $this->created = $created;
//
//        return $this;
//    }
//
//    /**
//     * Get created
//     *
//     * @return \DateTime
//     */
//    public function getCreated()
//    {
//        return $this->created;
//    }

//    /**
//     * Set updated
//     *
//     * @param \DateTime $updated
//     * @return Block
//     */
//    public function setUpdated($updated)
//    {
//        $this->updated = $updated;
//
//        return $this;
//    }
//
    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated;
    }

//    /**
//     * Set name
//     *
//     * @param string $name
//     * @return Block
//     */
//    public function setName($name)
//    {
//        $this->name = $name;
//
//        return $this;
//    }
//
//    /**
//     * Get name
//     *
//     * @return string
//     */
//    public function getName()
//    {
//        return $this->name;
//    }

    /**
     * Set template
     *
     * @param string $template
     * @return Block
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set page
     *
     * @param string $page
     * @return Block
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return string
     */
    public function getPage()
    {
        return $this->page;
    }
}