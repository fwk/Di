<?php
/**
 * Fwk
 *
 * Copyright (c) 2011-2012, Julien Ballestracci <julien@nitronet.org>.
 * All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * PHP Version 5.3
 *
 * @category  DependencyInjection
 * @package   Fwk\Di
 * @author    Julien Ballestracci <julien@nitronet.org>
 * @copyright 2011-2014 Julien Ballestracci <julien@nitronet.org>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://www.nitronet.org/fwk
 */
namespace Fwk\Di;

use \stdClass;
use \ArrayAccess;

/**
 * Container
 * 
 * THE Dependency Injection Container.
 *
 * @category Container
 * @package  Fwk\Di
 * @author   Julien Ballestracci <julien@nitronet.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link     http://www.nitronet.org/fwk
 */
class Container implements ArrayAccess
{
    /**
     * The objects store
     * @var array
     */
    protected $store = array();

    /**
     * Informations about stored objects
     * @var array
     */
    protected $storeData = array();
    
    /**
     * Container Properties
     * @var array
     */
    protected $properties = array();
    
    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct()
    {
        $this->set('self', $this, true);
    }
    
    /**
     * Registers a definition
     * 
     * @param string  $name       Identifier
     * @param mixed   $definition Definition, callable or value
     * @param boolean $shared     Should the instance be "shared" (singleton)
     * @param array   $data       Meta-data associated with this definition
     * 
     * @return Container 
     */
    public function set($name, $definition, $shared = false, 
        array $data = array()
    ) {
        $data = array_merge(
            array(
                '__fwk_di_shared'   => $shared
            ), 
            $data
        );

        $this->store[$name] = $definition;
        $this->storeData[$name] = $data;

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
        if ($name instanceof Reference) {
            $name = $name->getName();
        }
        
        if (!$this->exists($name)) {
            throw new Exceptions\DefinitionNotFound($name);
        }
        
        $data       = $this->storeData[$name];
        
        if ($data['__fwk_di_shared'] === true 
            && isset($data['__fwk_di_shared_inst'])
        ) {
            return $this->get($data['__fwk_di_shared_inst']);
        }
        
        $definition = $this->store[$name];
         
        if ($definition instanceof Invokable) {
            $return = $definition->invoke($this, $name);
        } elseif (is_callable($definition)) {
            $return = call_user_func_array($definition, array($this));
        } else {
            $return = $definition;
        }
        
        if ($data['__fwk_di_shared'] === true) {
            $sharedId = md5(uniqid('__fwk_instances_'));
            $this->storeData[$name]['__fwk_di_shared_inst'] = $sharedId;
            $this->set(
                $sharedId,
                $return, 
                true, 
                array('__fwk_di_shareof' => $name)
            );
        }
        
        return $return;
    }
    
    /**
     * Loads properties from an INI file as definitions. 
     * Theses properties can then be referenced like @propName in other 
     * definitions.
     * 
     * @param string      $iniFile  Path/to/file.ini
     * @param null|string $category The INI category to be parsed
     * 
     * @return Container
     * @throws Exception
     */
    public function iniProperties($iniFile, $category = null)
    {
        if (!is_file($iniFile) || !is_readable($iniFile)) {
            throw new Exception('INI file not found/readable: '. $iniFile);
        }
        
        $props = parse_ini_file($iniFile, ($category !== null));
        if ($category !== null) {
            $props = (isset($props[$category]) ? $props[$category] : false);
        }
        
        if (!is_array($props)) {
            throw new Exception("No properties found in: $iniFile [$category]");
        }
        
        foreach ($props as $key => $prop) {
            $props[$key] = str_replace(':packageDir', dirname($iniFile), $prop);
        }
        
        $this->properties = array_merge($props, $this->properties);
        
        return $this;
    }
    
    /**
     * Returns a property (or $default if not defined) 
     * 
     * @param string $propName The property name
     * @param mixed  $default  Default value if the property is not defined
     * 
     * @return mixed
     */
    public function getProperty($propName, $default = null)
    {
        return (array_key_exists($propName, $this->properties) ? 
            $this->propertizeString($this->properties[$propName]) : 
            (is_string($default) ?
                $this->propertizeString($default) :
                $default
            )
        );
    }
    
    /**
     * Defines a property.
     * 
     * If the $value is null, the property will be unset.
     * 
     * It recommended to store only strings as property values. Register a
     * new Di definition for any other type.
     * 
     * @param string      $propName Property name
     * @param null|string $value    The prop value
     * 
     * @return Container
     */
    public function setProperty($propName, $value = null)
    {
        if (array_key_exists($propName, $this->properties) && $value === null) {
            unset($this->properties[$propName]);
            return $this;
        }
        
        $this->properties[(string)$propName] = (string)$value;
        
        return $this;
    }
    
    
    /**
     * Transform properties references to their respective value
     * 
     * @param string $str String to be transformed
     * 
     * @return string
     */
    public function propertizeString($str)
    {
        return str_replace(
            array_map(function($val) { return ":". $val; }, array_keys($this->properties)),
            array_values($this->properties),
            $str
        );
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
        
        $data = $this->storeData[$name];
        
        if ($data['__fwk_di_shared'] === true) {
            if ($this->exists($data['__fwk_di_shared_inst'])) {
                unset($this->store[$data['__fwk_di_shared_inst']]);
                unset($this->storeData[$data['__fwk_di_shared_inst']]);
            }
        }

        unset($this->storeData[$name]);
        unset($this->store[$name]);
        
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
        
        $data = $this->storeData[$name];
        
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
        return array_key_exists($name, $this->store);
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
}