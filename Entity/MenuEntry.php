<?php

namespace Raindrop\PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Raindrop\PageBundle\Entity\Page;
use Raindrop\PageBundle\Entity\Menu;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * @ORM\Column(nullable=true)
     */
    protected $image;

    /**
     * @ORM\Column(nullable=true)
     */
    protected $label;

    protected $file;

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

    /**
     * Set image
     *
     * @param  string    $image
     * @return MenuEntry
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set label
     *
     * @param  string    $label
     * @return MenuEntry
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    public function getLabelString()
    {
        $label = $this->getLabel();
        if (empty($label)) {
            if ($this->getPage() instanceof Page) {
                $label = $this->getPage()->getTitle();
            }
        }

        return $label;
    }

    public function __toString()
    {
        return (string) $this->getLabelString();
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir($appPath)
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return $appPath . '/web/' . $this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/documents';
    }

    public function upload($appPath)
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues
        $cleanName = $this->cleanFileName($this->getFile()->getClientOriginalName());

        // move takes the target directory and then the
        // target filename to move to
        $this->getFile()->move(
            $this->getUploadRootDir($appPath),
            $cleanName
        );

        // set the path property to the filename where you've saved the file
        $this->image = $cleanName;

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }

    protected function cleanFileName()
    {
        $name = $this->getFile()->getClientOriginalName();
        $name = preg_replace('/[\s]+/', '_', $name);
        $name = preg_replace('/[\-]+/', '-', $name);

        return $name;
    }

        /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function getImagePath()
    {
        if (!empty($this->image)) {
            return DIRECTORY_SEPARATOR . $this->getUploadDir() . DIRECTORY_SEPARATOR . $this->getImage();
        }

        return null;
    }
}
