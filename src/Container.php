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

use Fwk\Di\Definitions\ArrayDefinition;
use Fwk\Di\Definitions\CallableDefinition;
use Fwk\Di\Definitions\ScalarDefinition;
use Fwk\Di\Events\AfterServiceLoadedEvent;
use Fwk\Di\Events\AfterServiceRegisteredEvent;
use Fwk\Di\Events\BeforeServiceLoadedEvent;
use Fwk\Di\Events\BeforeServiceRegisteredEvent;
use Fwk\Di\Exceptions\SearchException;
use Fwk\Events\Dispatcher;
use \ArrayAccess;
use Interop\Container\ContainerInterface;
use \SplObjectStorage;

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
class Container extends Dispatcher implements ArrayAccess, ContainerInterface
{
    /**
     * The objects store
     * @var SplObjectStorage
     */
    protected $store = array();

    /**
     * Shared instances store
     * @var SplObjectStorage
     */
    private $_sharedInstances;

    /**
     * Container Properties
     * @var array
     */
    protected $properties = array();

    /**
     * Properties Keys (cached)
     * @var array
     */
    protected $propertiesMap = array();

    /**
     * Delegates Containers
     * @var SplObjectStorage
     */
    protected $delegates;
    
    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct()
    {
        $this->store = new SplObjectStorage();
        $this->delegates = new SplObjectStorage();
        $this->_sharedInstances = new SplObjectStorage();
        $this->set('self', $this);
    }
    
    /**
     * Registers a definition
     * 
     * @param string  $name             Identifier
     * @param DefinitionInterface|mixed $definition Definition, callable or value
     *
     * @return Container
     */
    public function set($name, $definition)
    {
        if (!$definition instanceof DefinitionInterface) {
            if (is_callable($definition)) {
                $definition = CallableDefinition::factory($definition);
            } elseif (is_array($definition)) {
                $definition = ArrayDefinition::factory($definition);
            } else {
                $wasObj = is_object($definition);
                $definition = ScalarDefinition::factory($definition);
                if ($wasObj) {
                    $definition->setShared(true);
                    $this->_sharedInstances->attach($definition, $name);
                }
            }
        }

        $event = new BeforeServiceRegisteredEvent($this, $name, $definition);
        $this->notify($event);

        if ($event->isStopped()) {
            return $this;
        }

        $this->store->attach($definition, $name);
        $this->notify(new AfterServiceRegisteredEvent($this, $name, $definition));

        return $this;
    }
    
    /**
     * Load and returns a definition
     * 
     * @param string $name Identifier
     * 
     * @throws Exceptions\DefinitionNotFoundException if $name isn't a valid identifier
     * @return mixed
     */
    public function get($name)
    {
        if ($name instanceof Reference) {
            $name = $name->getName();
        }
        
        if (!$this->has($name)) {
            return $this->getFromDelegate($name);
        }

        foreach ($this->store as $def) {
            if ($this->store->getInfo() === $name) {
                $definition = $def;
                break;
            }
        }

        /** @var DefinitionInterface $definition */
        if ($definition->isShared()) {
            foreach ($this->_sharedInstances as $inst) {
                if ($this->_sharedInstances->getInfo() === $name) {
                    return $inst;
                }
            }
        }

        $event      = new BeforeServiceLoadedEvent($this, $name, $definition);
        $this->notify($event);

        // the event has been stopped
        if ($event->isStopped()) {
            $return = $event->getReturnValue();

            if ($definition->isShared()) {
                $this->_sharedInstances->attach($return, $name);
            }

            return $return;
        }

        $return = $definition->invoke($this, $name);
        if ($definition->isShared()) {
            $this->_sharedInstances->attach($return, $name);
        }

        $afterEvent = new AfterServiceLoadedEvent($this, $name, $definition, $return);
        $this->notify($afterEvent);

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
            $this->properties[$key] = str_replace(':packageDir', dirname($iniFile), $prop);
            $this->propertiesMap[$key] = ":". $key;
        }
        
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
            unset($this->propertiesMap[$propName]);
            return $this;
        }
        
        $this->properties[(string)$propName] = (string)$value;
        $this->propertiesMap[(string)$propName] = ":". (string)$propName;
        
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
            array_values($this->propertiesMap),
            array_values($this->properties),
            $str
        );
    }
    
    /**
     * Unregisters a definition
     * 
     * @param string $name Identifier
     * 
     * @throws Exceptions\DefinitionNotFoundException if $name isn't a valid identifier
     * @return boolean true on success
     */
    public function unregister($name)
    {
        if (!$this->has($name)) {
            throw new Exceptions\DefinitionNotFoundException($name);
        }

        $this->store->detach($this->getDefinition($name));
        foreach ($this->_sharedInstances as $inst => $defName) {
            if ($defName === $name) {
                $this->_sharedInstances->detach((object)$inst);
                break;
            }
        }

        return true;
    }
    
    /**
     * Tells if a definition exists at $offset
     * 
     * @param string $name Identifier
     * 
     * @return boolean
     */
    public function has($name)
    {
        foreach ($this->store as $def) {
            if ($this->store->getInfo() === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns a Definition
     *
     * @param string $name Identifier
     *
     * @return DefinitionInterface
     * @throws Exceptions\DefinitionNotFoundException if $name isn't a valid identifier
     */
    public function getDefinition($name)
    {
        foreach ($this->store as $def) {
            if ($this->store->getInfo() === $name) {
                return $def;
            }
        }

        throw new Exceptions\DefinitionNotFoundException($name);
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
        return $this->has($offset);
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
     * Adds a delegate/backup Container.
     *
     * @param ContainerInterface $container
     *
     * @return ContainerInterface
     */
    public function delegate(ContainerInterface $container)
    {
        if ($this->delegates->contains($container)) {
            return $this;
        }

        $this->delegates->attach($container);

        return $this;
    }

    /**
     * Tells if a service is in a delegated Container
     *
     * @param string $name
     *
     * @return boolean
     */
    public function hasInDelegate($name)
    {
        foreach ($this->delegates as $container) {
            /** @var ContainerInterface $container */
            if ($container->has($name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Loads a definition from the first delegated Container having in (FIFO)
     *
     * @param string $name Service identifier
     *
     * @throws Exceptions\DefinitionNotFoundException when the service is not found
     * @return mixed
     */
    public function getFromDelegate($name)
    {
        foreach ($this->delegates as $container) {
            /** @var ContainerInterface $container */
            if ($container->has($name)) {
                return $container->get($name);
            }
        }

        throw new Exceptions\DefinitionNotFoundException($name);
    }

    /**
     * Search definitions
     *
     * @param array $query Search query
     *
     * @return array<DefinitionInterface>
     */
    public function search(array $query)
    {
        $results = array();
        foreach ($this->store as $def) {
            $name = $this->store->getInfo();
            /** @var DefinitionInterface $def */
            if ($def->match($query, $this)) {
                $results[$name] = $def;
            }
        }

        return $results;
    }
}