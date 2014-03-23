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
 * Abstract Definition Utility
 *
 * @category Utilities
 * @package  Fwk\Di
 * @author   Julien Ballestracci <julien@nitronet.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link     http://www.nitronet.org/fwk
 */
abstract class AbstractDefinition
{
    /**
     * List of arguments
     * @var array<mixed>
     */
    protected $arguments = array();
    
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
     * @param string|Invokable $argument The Argument
     * 
     * @return Definition 
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
     * @return Definition
     */
    public function addArguments(array $arguments)
    {
        $this->arguments += $arguments;
        
        return $this;
    }
    
    /**
     * Returns all arguments (computed)
     * 
     * @param Container $container The Di Container
     * 
     * @return array<mixed>
     */
    protected function getConstructorArguments(Container $container)
    {
        return $this->propertizeArguments($this->arguments, $container);
    }
    
    /**
     * Transform arguments to their real value if they are instance of Invokable
     * or a @reference.
     * 
     * @param array<mixed> $args      List of arguments
     * @param Container    $container The Di Container
     * 
     * @return array<mixed>
     * @throws Exceptions\InvalidArgument
     */
    protected function propertizeArguments(array $args, Container $container)
    {
        $return = array();
        foreach ($args as $idx => $arg) {
            if (is_string($arg) && strpos($arg, '@', 0) === 0) {
                $arg = new Reference(substr($arg, 1));
            } elseif (is_array($arg)) {
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
    
    /**
     * Factory method
     * 
     * @param string       $name      Name of the Definition
     * @param array<mixed> $arguments List of arguments
     * 
     * @return Definition
     * @static
     */
    public static function factory($name, array $arguments = array())
    {
        return new static($name, $arguments);
    }
}
