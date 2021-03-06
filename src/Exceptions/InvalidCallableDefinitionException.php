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
namespace Fwk\Di\Exceptions;

use Fwk\Di\Exception;

/**
 * InvalidCallableDefinition
 * 
 * @category Exceptions
 * @package  Fwk\Di
 * @author   Julien Ballestracci <julien@nitronet.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link     http://www.nitronet.org/fwk
 */
class InvalidCallableDefinitionException extends Exception
{
    /**
     * Constructor
     * 
     * @param mixed           $callable   Callable
     * @param null|string     $definition Name of the current definition (if any)
     * @param null|\Exception $prev       Previous Exception
     * 
     * @return void
     */
    public function __construct($callable, $definition = null,
        $prev = null
    ) {
        if (is_array($callable)) {
            $class = (isset($callable[0]) ? $callable[0] : 'undefined');
            $method = (isset($callable[1]) ?$callable[1] : 'undefined');
            
            $txt = sprintf(
                "%s::%s()",
                (is_object($class) ? get_class($class) : (string)$class),
                (is_string($method) ? $method : print_r($method, true))
            );
        } else {
            $txt = (string)$callable;
        }
        
        parent::__construct(
            "[$definition] Callable $txt is invalid", 
            null, 
            $prev
        );
    }
}