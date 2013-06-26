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
        
        $definition = $this->_findDefinition($name)->value;
        $data       = $this->_findDefinitionData($name);
        
        if ($data['__fwk_di_shared'] === true && 
            isset($data['__fwk_di_shared_inst'])
        ) {
            return $this->getShared($name);
        }
        
        if (!$definition instanceof Definition) {
            $return = $definition;
        } else {
            $return = $definition->invoke($this);
        }
        
        if ($data['__fwk_di_shared'] === true) {
            $sharedId = md5(uniqid('__fwk_di_uniq_instance_'));
            $data['__fwk_di_shared_inst'] = $sharedId;
            $this->_updateData($name, $data);
            $this->set($sharedId, $return);
        }
        
        return $return;
    }
    
    public function getShared($name)
    {
        if (!$this->exists($name)) {
            throw new Exceptions\DefinitionNotFound($name);
        } elseif (!$this->isShared($name)) {
            throw new Exception(
                sprintf("Definition '%s' cannot be shared", $name)
            );
        }
        
        $data       = $this->_findDefinitionData($name);
        
        if ($data['__fwk_di_shared'] === true && 
            isset($data['__fwk_di_shared_inst'])
        ) {
            return $this->_findDefinition($data['__fwk_di_shared_inst'])->value;
        }
        
        return null;
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
        
        $this->store->detach($this->_findDefinition($name));
        
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
        return ($this->_findDefinition($offset) instanceof stdClass);
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
        return $this->register($offset, $value);
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
        foreach ($this->store as $obj) {
           $data = $this->store->getInfo();
           if ($data['__fwk_di_name'] == $name) {
                return $obj;
           }
        }
        
        return null;
    }
    
    /**
     *
     * @param string $name Identifier
     * 
     * @return array
     */
    protected function _findDefinitionData($name)
    {
        foreach ($this->store as $obj) {
           $data = $this->store->getInfo();
           if ($data['__fwk_di_name'] == $name) {
                return $data;
           }
        }
        
        return array();
    }
    
    protected function _updateData($name, array $newData)
    {
        foreach ($this->store as $obj) {
           $data = $this->store->getInfo();
           if ($data['__fwk_di_name'] == $name) {
                $this->store->setInfo(array_merge($newData, $data));
                return;
           }
        }
    }
}