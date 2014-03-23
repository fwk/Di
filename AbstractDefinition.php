<?php
namespace Fwk\Di;

abstract class AbstractDefinition
{
    /**
     *
     * @var array
     */
    protected $arguments = array();
    
    abstract function __construct($name, array $arguments = array());
    
    /**
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }
    
    /**
     *
     * @param mixed $argument
     * 
     * @return Definition 
     */
    public function addArgument($argument)
    {
        $this->arguments[] = $argument;
        
        return $this;
    }
    
    /**
     *
     * @param array $arguments 
     * 
     * @return Definition
     */
    public function addArguments(array $arguments)
    {
        $this->arguments += $arguments;
        
        return $this;
    }
    
    /**
     *
     * @param Container $container
     * 
     * @return array
     */
    protected function getConstructorArguments(Container $container)
    {
        return $this->propertizeArguments($this->arguments, $container);
    }
    
    protected function propertizeArguments(array $args, Container $container)
    {
        $return = array();
        foreach ($args as $idx => $arg) {
            if (is_string($arg) && strpos($arg, '@', 0) === 0) {
                $arg = new Reference(substr($arg,1));
            }
            
            elseif (is_array($arg)) {
                $arg = $this->propertizeArguments($arg, $container);
            }
            
            try {
                $return[$idx] = (($arg instanceof Invokable) 
                    ? $arg->invoke($container) 
                    : $arg
                );
            } catch(\Fwk\Di\Exception $exp) {
                throw new Exceptions\InvalidArgument($idx, $exp);
            }
        }
        
        return $return;
    }
    
    public static function factory($name, array $arguments = array())
    {
        return new static($name);
    }
}
