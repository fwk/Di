<?php
/**
 * Fwk
 *
 * Copyright (c) 2011-2015, Julien Ballestracci <julien@nitronet.org>.
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
 * @category  Dependency Injection
 * @package   Fwk\Di
 * @author    Julien Ballestracci <julien@nitronet.org>
 * @copyright 2011-2015 Julien Ballestracci <julien@nitronet.org>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://www.phpfwk.com
 */
namespace Fwk\Di;

use Fwk\Events\Event as BaseEvent;

/**
 * @category Listeners
 * @package  Fwk\Di
 * @author   Julien Ballestracci <julien@nitronet.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link     http://www.phpfwk.com
 */
class Event extends BaseEvent
{
    /**
     * Constructor
     *
     * @param string    $name
     * @param array     $data
     * @param Container $container
     *
     * @return void
     */
    public function __construct($name, $data = array(), Container $container = null)
    {
        parent::__construct(
            $name, array_merge(
                $data, array(
                'container'       => $container,
                'returnValue'     => null
                )
            )
        );
    }

    /**
     * Defines a returnValue which can be used to override standard return values by functions handling this event.
     *
     * @param mixed $value
     *
     * @return void
     */
    public function setReturnValue($value)
    {
        $this->returnValue = $value;
    }

    /**
     * Returns the "returnValue" if defined
     *
     * @return mixed
     */
    public function getReturnValue()
    {
        return $this->returnValue;
    }

    /**
     * Tells if the event has a return value defined
     *
     * @return boolean
     */
    public function hasReturnValue()
    {
        return isset($this->returnValue);
    }

    /**
     * Returns the Dependency Injection Container
     *
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }
}