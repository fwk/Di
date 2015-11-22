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

use Fwk\Di\Exceptions\InvalidCallableDefinitionException;

/**
 * CallableDefinition
 * 
 * Represents a Definition that require a function (callable) to be executed
 * to obtain its value.
 *
 * @category Definition
 * @package  Fwk\Di
 * @author   Julien Ballestracci <julien@nitronet.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link     http://www.nitronet.org/fwk
 */
class CallableDefinition extends AbstractDefinition implements InvokableInterface
{
    /**
     * The callable function
     * @var callable
     */
    protected $callable;
    
    /**
     * Constructor
     * 
     * @param callable     $callable  The function to be called
     * @param array<mixed> $arguments List of arguments
     * 
     * @return void
     */
    public function __construct($callable, array $arguments = array())
    {
        $this->callable     = $callable;
        $this->arguments    = $arguments;
    }
    
    /**
     * Calls $this->callable and return its value
     * 
     * @param Container   $container The Di Container
     * @param null|string $name      Name of the definition (if any)
     * 
     * @return mixed
     * @throws Exceptions\InvalidCallableDefinitionException
     */
    public function invoke(Container $container, $name = null)
    {
        if (!is_callable($this->callable)) {
            throw new InvalidCallableDefinitionException($this->callable, $name);
        }
        
        $args = array();
        try {
            $args = $this->getConstructorArguments($container, $name);
        } catch(Exception $exp) {
            throw new InvalidCallableDefinitionException($this->callable, $name, $exp);
        }
        
        return call_user_func_array($this->callable, $args);
    }
    
    /**
     * Returns the callable function
     * 
     * @return callable
     */
    public function getCallable()
    {
        return $this->callable;
    }
    
    /**
     * Defines the callable function
     * 
     * @param callable $callable The callable function
     * 
     * @return void
     */
    public function setCallable($callable)
    {
        $this->callable = $callable;
    }
}