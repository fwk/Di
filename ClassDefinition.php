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

/**
 * ClassDefinition
 * 
 * Represents a Definition returning an instance of some class.
 *
 * @category Definition
 * @package  Fwk\Di
 * @author   Julien Ballestracci <julien@nitronet.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link     http://www.nitronet.org/fwk
 */
class ClassDefinition extends AbstractDefinition implements Invokable
{
    /**
     * Name of the class to be instanciated
     * @var string
     */
    protected $className;
    
    /**
     * List of class' methods to be called after instanciation
     * @var array<string>
     */
    protected $methodCalls  = array();
    
    /**
     * Constructor
     * 
     * @param string       $className Name of the class to be instanciated
     * @param array<mixed> $arguments List of (constructor) arguments
     * 
     * @return void
     */
    public function __construct($className, array $arguments = array())
    {
        $this->className    = $className;
        $this->arguments    = $arguments;
    }
    
    /**
     * Instanciates $this->className and return the instance.
     * 
     * @param Container $container The Di Container
     * 
     * @return object
     * @throws Exceptions\InvalidClassDefinition
     */
    public function invoke(Container $container)
    {
        if ($this->className instanceof Invokable) {
            try {
                $this->className = $this->className->invoke($container);
            } catch(Exception $exp) {
                throw new Exceptions\InvalidClassDefinition($this->className, $exp);
            }
        } 
        
        if (!is_string($this->className)) {
            throw new Exceptions\InvalidClassDefinition(
                '???', 
                new \InvalidArgumentException(
                    sprintf(
                        'Classname must be a string or a Fwk\Di\Reference ' .
                        'instance (' . (is_object($this->className) 
                            ? get_class($this->className) 
                            : get_type($this->className)
                        ) . ' given)'
                    )
                )
            );
        }
        
        $instance = $this->newInstance($container);
        $this->executeMethodCalls($instance, $container);
        
        return $instance;
    }
    
    /**
     * Executes registered methods (in the order they were added)
     * 
     * @param object    $instance  The class instance
     * @param Container $container The Di Container
     * 
     * @return void
     * @throws Exceptions\InvalidClassDefinition
     */
    protected function executeMethodCalls($instance, Container $container)
    {
        foreach ($this->methodCalls as $methodCall) {
            $callable = $methodCall->getCallable();
            if (is_string($callable)) {
                $callable = $container->propertizeString($callable);
            }
            
            $methodCall->setCallable(array($instance, $callable));
            $methodCall->invoke($container);
            $methodCall->setCallable($callable);
        }
    }
    
    /**
     * Instanciates the class ($this->className) 
     * 
     * @param Container $container The Di Container
     * 
     * @return object
     * @throws Exceptions\ClassNotFound
     * @throws Exceptions\InvalidClassDefinition
     */
    protected function newInstance(Container $container)
    {
        if (is_string($this->className) && strpos($this->className, ':') >= 0) {
            $this->className = $container->propertizeString($this->className);
        }
        
        if (!class_exists($this->className, true)) {
            throw new Exceptions\ClassNotFound($this->className);
        }
        
        $reflect    = new \ReflectionClass($this->className);
        if (null !== $reflect->getConstructor()) {
            $args = array();
            try {
                $args = $this->getConstructorArguments($container);
            } catch(Exception $exp) {
                throw new Exceptions\InvalidClassDefinition($this->className, $exp);
            }
            
            $return = $reflect->newInstanceArgs($args);
        } else {
            $return = new $this->className();
        }
        
        return $return;
    }
    
    /**
     * Returns the Class name
     * 
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }
    
    /**
     * Defines the Class name
     * 
     * @param string $className The class name
     * 
     * @return void
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }

    /**
     * Adds a method to be called right after the class was instanciated
     * 
     * @param string       $methodName The name of the method to be called
     * @param array<mixed> $arguments  List of arguments (optional)
     * 
     * @return ClassDefinition 
     */
    public function addMethodCall($methodName, array $arguments = array())
    {
        return $this->methodCalls[] = new CallableDefinition(
            $methodName, 
            $arguments
        );
    }
    
    /**
     * Removes a call to a specified method
     * 
     * @param string $methodName The method name
     * 
     * @return ClassDefinition 
     */
    public function removeMethodClass($methodName)
    {
        $this->methodCalls = array_filter(
            $this->methodCalls, 
            function ($call) use ($methodName) {
                return $methodName !== $call->getCallable();
            }
        );
        
        return $this;
    }
}