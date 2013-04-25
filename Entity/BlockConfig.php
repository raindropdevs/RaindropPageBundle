<?php


namespace Raindrop\PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="block_config")
 */
class BlockConfig
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
     * @ORM\Column(nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column
     */
    protected $type;

    /**
     * @ORM\Column
     */
    protected $template;

    /**
     * @ORM\Column(type="array")
     */
    protected $options;

    /**
     * @ORM\Column(type="array")
     */
    protected $javascripts;

    /**
     * @ORM\Column(type="array")
     */
    protected $stylesheets;

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
     * @return BlockConfig
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
     * @return BlockConfig
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
     * Set template
     *
     * @param string $template
     * @return BlockConfig
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
     * Set options
     *
     * @param array $options
     * @return BlockConfig
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    public function toJson() {
        return json_encode(array(
            'name' => $this->getName(),
            'type' => $this->getType(),
            'options' => $this->getOptions()
        ));
    }

    /**
     * Set description
     *
     * @param string $description
     * @return BlockConfig
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function hasJavascripts() {
        return !empty($this->javascripts);
    }

    /**
     * Set javascripts
     *
     * @param array $javascripts
     * @return Block
     */
    public function setJavascripts($javascripts)
    {
        $this->javascripts = $javascripts;

        return $this;
    }

    /**
     * Get javascripts
     *
     * @return array
     */
    public function getJavascripts()
    {
        return $this->javascripts;
    }

    public function hasStylesheets() {
        return !empty($this->stylesheets);
    }

    /**
     * Set stylesheets
     *
     * @param array $stylesheets
     * @return Block
     */
    public function setStylesheets($stylesheets)
    {
        $this->stylesheets = $stylesheets;

        return $this;
    }

    /**
     * Get stylesheets
     *
     * @return array
     */
    public function getStylesheets()
    {
        return $this->stylesheets;
    }

    public function __toString() {
        return (string) $this->getName();
    }
}