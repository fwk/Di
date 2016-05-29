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
namespace Fwk\Di\Definitions;

use Fwk\Di\Container;
use Fwk\Di\DefinitionInterface;
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
 * @link     http://www.nitronet.org/fwk
 */
abstract class AbstractDefinition
{
    /**
     * Shared result/instance ?
     *
     * @var bool
     */
    protected $shared = false;

    /**
     * Definition's meta-data
     *
     * @var array
     */
    protected $data = array();

    /**
     * @param bool $bool
     *
     * @return DefinitionInterface
     */
    public function setShared($bool)
    {
        $this->shared = (bool)$bool;

        return $this;
    }

    /**
     * @return bool
     */
    public function isShared()
    {
        return $this->shared;
    }

    /**
     * Sets (erase) definition meta-data
     *
     * @param array $data The Definition's data
     *
     * @return DefinitionInterface
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Returns all definition meta-data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Returns a meta-data parameter ($param) or $default if not defined
     *
     * @param string     $param   Parameter key
     * @param null|mixed $default Default return value if not defined
     *
     * @return mixed
     */
    public function get($param, $default = null)
    {
        return (array_key_exists($param, $this->data) ? $this->data[$param] : $default);
    }

    /**
     * Defines a meta-data parameter
     *
     * @param string $param Parameter key
     * @param mixed  $value Parameter value
     *
     * @return DefinitionInterface
     */
    public function set($param, $value)
    {
        $this->data[$param] = $value;

        return $this;
    }

    /**
     * Tells if definition's meta-data matches $dataQuery
     *
     * @param array $query
     *
     * @return boolean
     */
    public function match(array $query)
    {

    }
}
