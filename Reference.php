<?php
namespace Fwk\Di;

use Fwk\Di\Exceptions\DefinitionNotFound;
use Fwk\Di\Exceptions\InvalidReference;

class Reference implements Invokable
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
        $this->name = (string)$name;
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
     * @return Container
     */
    public function setName($name)
    {
        $this->name = $name;
        
        return $this;
    }
    
    /**
     *
     * @param Container $container 
     * 
     * @return mixed
     */
    public function invoke(Container $container)
    {
        $return = false;
        
        try {
            $return = $container->get($this->name);
        } catch(DefinitionNotFound $exp) {
            throw new InvalidReference($this->name, $exp);
        }
        
        return $return;
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