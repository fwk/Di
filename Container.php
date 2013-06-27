<?php
namespace Fwk\Di;

use \SplObjectStorage;
use \ArrayAccess;
use \stdClass;

class Container implements \ArrayAccess
{
    /**
     * @var SplObjectStorage
     */
    protected $store;
    
    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct()
    {
        $this->store = new SplObjectStorage();
    }
    
    /**
     * Registers a definition
     * 
     * @param string  $name         Identifier
     * @param mixed   $definition   Definition, callable or value
     * @param boolean $shared       Should the instance be "shared" (singleton)
     * @param array   $data         Meta-data associated with this definition
     * 
     * @return Container 
     */
    public function set($name, $definition, $shared = false, 
        array $data = array()
    ) {
        $data = array_merge(array(
            '__fwk_di_name'     => $name,
            '__fwk_di_shared'   => $shared
        ), $data);

        $object = new stdClass();
        $object->value = $definition;
        $this->store->attach($object, $data);
        
        return $this;
    }
    
    /**
     * Load and returns a definition
     * 
     * @param string $name Identifier
     * 
     * @throws Exceptions\DefinitionNotFound if $name isn't a valid identifier
     * @return mixed
     */
    public function get($name)
    {
        if (!$this->exists($name)) {
            throw new Exceptions\DefinitionNotFound($name);
        }
        
        $data       = $this->_findDefinitionData($name);
        
        if ($data['__fwk_di_shared'] === true 
            && isset($data['__fwk_di_shared_inst'])
        ) {
            return $this->_findDefinition(
                $data['__fwk_di_shared_inst']
            )->value;
        }
        
        $definition = $this->_findDefinition($name)->value;
         
        if ($definition instanceof Invokable) {
            $return = $definition->invoke($this);
        } elseif (is_callable($definition)) {
            $return = call_user_func_array($definition, array($this));
        } else {
            $return = $definition;
        }
        
        if ($data['__fwk_di_shared'] === true) {
            $sharedId = md5(uniqid('__fwk_instances_'));
            $this->_updateData(
                $name, 
                array('__fwk_di_shared_inst' => $sharedId)
            );
            $this->set($sharedId, $return, array('__fwk_di_shareof' => $name));
        }
        
        return $return;
    }
    
    /**
     * Unregisters a definition
     * 
     * @param string $name Identifier
     * 
     * @throws Exceptions\DefinitionNotFound if $name isn't a valid identifier
     * @return boolean true on success
     */
    public function unregister($name)
    {
        if (!$this->exists($name)) {
            throw new Exceptions\DefinitionNotFound($name);
        }
        
        $definition = $this->_findDefinition($name);
        $data = $this->_findDefinitionData($name);
        
        if ($data['__fwk_di_shared'] === true) {
            $this->store->detach(
                $this->_findDefinition($data['__fwk_di_shared_inst'])
            );
        }
        
        $this->store->detach($definition);
        
        return true;
    }
    
    /**
     * Tells if a definition has been flagged has "shared" (singleton)
     * 
     * @param string $name Identifier
     * 
     * @throws Exceptions\DefinitionNotFound if $name isn't a valid identifier
     * @return boolean
     */
    public function isShared($name)
    {
        if (!$this->exists($name)) {
            throw new Exceptions\DefinitionNotFound($name);
        }
        
        $data = $this->_findDefinitionData($name);
        
        return (bool)$data['__fwk_di_shared'];
    }
    
    /**
     * Tells if a definition exists at $offset
     * 
     * @param string $name Identifier
     * 
     * @return boolean
     */
    public function exists($name)
    {
        return ($this->_findDefinition($name) instanceof stdClass);
    }
    
    /**
     * Tells if a definition is registered at $offset
     * 
     * @param string $offset Identifier
     * 
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return $this->exists($offset);
    }
    
    /**
     * Loads and returns a definition 
     * 
     * @param string $offset Identifier
     * 
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }
    
    /**
     * Registers a definition
     * 
     * @param string $offset Identifier
     * @param mixed  $value  Definition
     * 
     * @return Container
     */
    public function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }
    
    /**
     * Unregisters a Definition
     * 
     * @param string $offset Identifier
     * 
     * @return boolean
     */
    public function offsetUnset($offset)
    {
        return $this->unregister($offset);
    }
    
    /**
     *
     * @param string $name Identifier
     * 
     * @return stdClass
     */
    protected function _findDefinition($name)
    {
        $object = null;
        foreach ($this->store as $obj) {
           $data = $this->store->getInfo();
           if ($data['__fwk_di_name'] == $name) {
                $object = $obj;
                break;
           }
        }
        
        return $object;
    }
    
    /**
     *
     * @param string $name Identifier
     * 
     * @return array
     */
    protected function _findDefinitionData($name)
    {
        $return = array();
        foreach ($this->store as $obj) {
           $data = $this->store->getInfo();
           if ($data['__fwk_di_name'] == $name) {
                $return = $data;
                break;
           }
        }
        
        return $return;
    }
    
    protected function _updateData($name, array $newData)
    {
        foreach ($this->store as $obj) {
           $data = $this->store->getInfo();
           if ($data['__fwk_di_name'] == $name) {
                $this->store->setInfo(array_merge($newData, $data));
                break;
           }
        }
    }
}