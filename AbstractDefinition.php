<?php
namespace Fwk\Di;

abstract class AbstractDefinition
{
    /**
     *
     * @var array
     */
    protected $arguments = array();
    
    abstract function __construct($name, array $parameters = array());
    
    /**
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }
    
    public function getComputedParameters(Container $container)
    {
        $result = array();
        foreach ($this->parameters as $param) {
            if ($param instanceof Parameter) {
                $value = $param->getValue();
                if ($value instanceof Reference) {
                    $value = $container->get($value);
                }
                $result[] = $value;
            } elseif ($param instanceof Reference) {
                $result[] = $container->get($param);
            } else {
                $result[] = $param;
            }
        }
        
        return $result;
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
        $return = array();
        foreach ($this->arguments as $idx => $arg) {
            if (is_string($arg) && strpos($arg, '@', 0) === 0) {
                $arg = new Reference(substr($arg,1));
            }
            
            try {
                $return[] = (($arg instanceof Invokable) 
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
