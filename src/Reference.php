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

use Fwk\Di\Exceptions\DefinitionNotFoundException;
use Fwk\Di\Exceptions\InvalidReferenceException;

/**
 * Reference
 * 
 * Reference another Definition in the Di Container
 *
 * @category Reference
 * @package  Fwk\Di
 * @author   Julien Ballestracci <julien@nitronet.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link     http://www.nitronet.org/fwk
 */
class Reference implements InvokableInterface
{
    /**
     * Container's reference name
     * @var string 
     */
    protected $name;
    
    /**
     * Constructor
     * 
     * @param string $name Container's reference name
     * 
     * @return void
     */
    public function __construct($name)
    {
        $this->name = (string)$name;
    }
    
    /**
     * Gets the container's reference name
     * 
     * @return string 
     */
    public function getName() 
    {
        return $this->name;
    }
    
    /**
     * Defines the container's reference name
     * 
     * @param string $name Container's reference name
     * 
     * @return Reference
     */
    public function setName($name)
    {
        $this->name = $name;
        
        return $this;
    }
    
    /**
     * Return the value of the referenced Definition
     * 
     * @param Container   $container The Di Container
     * @param null|string $name      Name of the current definition (if any)
     * 
     * @return mixed
     */
    public function invoke(Container $container, $name = null)
    {
        try {
            $return = $container->get($this->name);
        } catch(DefinitionNotFoundException $exp) {
            throw new InvalidReferenceException($this->name, $name, $exp);
        }
        
        return $return;
    }
    
    /**
     * Convenience method 
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}