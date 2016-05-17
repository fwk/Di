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
namespace Fwk\Di\Events;

use Fwk\Di\Container;
use Fwk\Di\Event;

class AfterServiceLoadedEvent extends Event
{
    public function __construct(Container $container, $serviceName, &$definition, $definitionData, &$valueObject)
    {
        parent::__construct(
            'afterServiceLoaded',
            array(
                'definition'    => $definition,
                'serviceName'   => $serviceName,
                'valueObject'   => $valueObject,
                'definitionData'    => $definitionData
            ),
            $container
        );
    }

    /**
     * Returns the service's name
     *
     * @return string
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * Returns the service's definition
     *
     * @return mixed
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * Returns the service's value
     *
     * @return mixed
     */
    public function getValueObject()
    {
        return $this->valueObject;
    }

    /**
     * Returns the data associated with the definition
     *
     * @return array
     */
    public function getDefinitionData()
    {
        return $this->definitionData;
    }

    /**
     * Override/update definition data
     *
     * @param array $definitionData
     *
     * @return void
     */
    public function setDefinitionData(array $definitionData)
    {
        $this->definitionData = $definitionData;
    }
}