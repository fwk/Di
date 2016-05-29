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
use Fwk\Di\DefinitionInterface;

/**
 * ScalarDefinition
 * 
 * Represents a scalar-typed definition: string, integer, boolean, float
 *
 * @category Definition
 * @package  Fwk\Di
 * @author   Julien Ballestracci <julien@nitronet.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link     http://fwk.io/di
 */
class ScalarDefinition extends AbstractDefinition implements DefinitionInterface
{
    /**
     * The value
     * @var mixed
     */
    protected $value;
    
    /**
     * Constructor
     * 
     * @param mixed $value     The scalar value
     *
     * @return void
     */
    public function __construct($value)
    {
        $this->value = $value;
    }
    
    /**
     * Calls $this->callable and return its value
     * 
     * @param Container   $container The Di Container
     * @param null|string $name      Name of the definition (if any)
     * 
     * @return array<mixed>
     */
    public function invoke(Container $container, $name = null)
    {
        return $this->value;
    }
    
    /**
     * Returns the value
     * 
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * Defines the array
     * 
     * @param mixed $value The scalar-typed value
     * 
     * @return void
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Factory
     *
     * @param mixed $value The scalar-typed definition
     *
     * @return ScalarDefition
     */
    public static function factory($value)
    {
        return new static($value);
    }
}