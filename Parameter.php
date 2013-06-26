<?php
namespace Fwk\Di;

class Parameter
{
    /**
     * Parameter name
     * 
     * @var string 
     */
    protected $name;
    
    /**
     * Parameter's value
     * 
     * @var mixed
     */
    protected $value = null;
    
    /**
     * Constructor
     * 
     * @param string $name  Parameter name
     * @param mixed  $value Parameter value (if any)
     * 
     * @return void
     */
    public function __construct($name, $value = null)
    {
        $this->name     = $name;
        $this->value    = $value;
    }
    
    /**
     * Gets the parameter name
     * 
     * @return string 
     */
    public function getName() 
    {
        return $this->name;
    }
    
    /**
     * Defines the parameter name
     * 
     * @param string $name Parameter name
     * 
     * @return Parameter
     */
    public function setName($name)
    {
        $this->name = $name;
        
        return $this;
    }
    
    /**
     * Gets the param value
     * 
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Defines the parameter value
     * 
     * @param mixed $value Parameter value
     * 
     * @return Parameter 
     */
    public function setValue($value)
    {
        $this->value = $value;
        
        return $this;
    }

    /**
     * Convenience method 
     * 
     * @return string
     */
    public function __toString()
    {
        return json_encode(array($this->name => $this->value));
    }
}