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
namespace Fwk\Di\Xml;

use Fwk\Xml\Map;
use Fwk\Xml\Path;

/**
 * ContainerXmlMap
 * 
 * Describes the Map to parse an Xml container.
 *
 * @category Xml
 * @package  Fwk\Di
 * @author   Julien Ballestracci <julien@nitronet.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link     http://www.nitronet.org/fwk
 */
class ContainerXmlMap extends Map
{
    /**
     * Constructor
     * 
     * Initialize Map paths to return an array of definitions
     * 
     * @void
     */
    public function __construct()
    {
        $this->add(
            Path::factory(
                '/dependency-injection/class-definition', 
                'classDefs', 
                array()
            )->loop(true, '@name')
            ->attribute('shared')
            ->attribute('lazy')
            ->attribute('class', 'className')
            ->addChildren(
                Path::factory('argument', 'arguments', array())
                ->loop(true)
                ->value('value')
            )->addChildren(
                Path::factory('call', 'methodsCalls', array())
                ->loop(true)
                ->attribute('method')
                ->addChildren(
                    Path::factory('argument', 'arguments', array())
                    ->loop(true)
                    ->value('value')
                )
            )
        );
        
        $this->add(
            Path::factory(
                '/dependency-injection/definition', 
                'definitions', 
                array()
            )->loop(true, '@name')
            ->attribute('shared')
            ->value('value')
        );
        
        $this->add(
            Path::factory('/dependency-injection/ini', 'ini', array())
            ->loop(true)
            ->attribute('category')
            ->value('value')
        );
        
        $this->add(
            Path::factory(
                '/dependency-injection/array-definition', 
                'arrayDefs', 
                array()
            )->loop(true, '@name')
            ->attribute('shared')
            ->addChildren(
                Path::factory('param', 'params', array())
                ->loop(true)
                ->attribute('key')
                ->value('value')
            )    
        );
    }
}