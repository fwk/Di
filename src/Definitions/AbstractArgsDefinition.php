<?php
/**
 * Fwk
 *
 * Copyright (c) 2011-2016, Julien Ballestracci <julien@nitronet.org>.
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
 * @copyright 2011-2016 Julien Ballestracci <julien@nitronet.org>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://fwk.io/di
 */
namespace Fwk\Di\Definitions;

use Fwk\Di\Container;
use Fwk\Di\InvokableInterface;
use Fwk\Di\Exceptions;
use Fwk\Di\Reference;

/**
 * Abstract Definition Utility
 *
 * @category Utilities
 * @package  Fwk\Di
 * @author   Julien Ballestracci <julien@nitronet.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link     http://fwk.io/di
 */
abstract class AbstractArgsDefinition extends AbstractDefinition
{
    /**
     * List of arguments
     * @var array<mixed>
     */
    protected $arguments = array();
    
    /**
     * Constructor
     * 
     * @param mixed        $arg       Mixed argument
     * @param array<mixed> $arguments List of arguments
     * 
     * @abstract
     * @return   void
     */
    abstract public function __construct($arg, array $arguments = array());
            
    /**
     * Return the list of arguments
     * 
     * @return array<mixed>
     */
    public function getArguments()
    {
        return $this->arguments;
    }
    
    /**
     * Adds an argument to the Definition.
     * 
     * For a ClassDefinition these arguments are passed to the constructor.
     * 
     * @param string|InvokableInterface $argument The Argument
     * 
     * @return self
     */
    public function addArgument($argument)
    {
        $this->arguments[] = $argument;
        
        return $this;
    }
    
    /**
     * Add multiples arguments (merge)
     * 
     * @param array<mixed> $arguments List of new arguments
     * 
     * @return self
     */
    public function addArguments(array $arguments)
    {
        $this->arguments += $arguments;
        
        return $this;
    }
    
    /**
     * Returns all arguments (computed)
     * 
     * @param Container   $container  The Di Container
     * @param null|string $definition Name of the current definition (if any)
     * 
     * @return array<mixed>
     */
    protected function getConstructorArguments(Container $container, 
        $definition = null
    ) {
        if (!count($this->arguments)) {
            return array($container);
        }

        return $this->propertizeArguments(
            $this->arguments, 
            $container, 
            $definition
        );
    }
    
    /**
     * Transform arguments to their real value if they are instance of InvokableInterface
     * or a @reference.
     * 
     * @param array<mixed> $args       List of arguments
     * @param Container    $container  The Di Container
     * @param null|string  $definition Name of the current definition (if any)
     * 
     * @return array<mixed>
     * @throws Exceptions\InvalidArgumentException
     */
    protected function propertizeArguments(array $args, Container $container,
        $definition = null
    ) {
        $return = array();
        foreach ($args as $idx => $arg) {
            $arg = $this->transformValueType($arg);
            if (is_string($arg)) {
                $arg = $container->propertizeString($arg);
            }
            
            if (is_string($arg) && strpos($arg, '@', 0) === 0) {
                $arg = new Reference(substr($arg, 1));
            } elseif (is_array($arg)) {
                $arg = $this->propertizeArguments($arg, $container, $definition);
            }
            
            try {
                $return[$idx] = (($arg instanceof InvokableInterface)
                    ? $arg->invoke($container, $definition) 
                    : $arg
                );
            } catch(\Fwk\Di\Exception $exp) {
                throw new Exceptions\InvalidArgumentException($idx, $definition, $exp);
            }
        }
        
        return $return;
    }
    
    /**
     * Transforms a string to a type, if known:
     * 
     * - boolean: true / false
     * - null: null
     * 
     * @param string $value The initial string value
     * 
     * @return mixed
     */
    protected function transformValueType($value)
    {
        if (!is_string($value)) {
            return $value;
        }
        
        $value = trim($value);
        if (strtolower($value) === "true") {
            $value = true;
        } elseif (strtolower($value) === "false") {
            $value = false;
        } elseif (strtolower($value) === "null") {
            $value = null;
        }
        
        return $value;
    }
    
    /**
     * Factory method
     * 
     * @param mixed        $arg       Mixed argument
     * @param array<mixed> $arguments List of arguments
     * 
     * @return self
     * @static
     */
    public static function factory($arg, array $arguments = array())
    {
        return new static($arg, $arguments);
    }
}
