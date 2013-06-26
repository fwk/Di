<?php
namespace Fwk\Di;

class Reference
{
    /**
     * Container's reference name
     * 
     * @var string 
     */
    protected $name;
    
    /**
     * Constructor
     * 
     * @param string $name Container's reference name
     * 
     * @return void
     */
    public function __construct($name)
    {
        $this->name = $name;
    }
    
    /**
     * Gets the container's reference name
     * 
     * @return string 
     */
    public function getName() 
    {
        return $this->name;
    }
    
    /**
     * Defines the container's reference name
     * 
     * @param string $name Container's reference name
     * 
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    
    /**
     * Convenience method 
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}