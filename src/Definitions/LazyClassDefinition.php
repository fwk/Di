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
 * @category  DependencyInjection
 * @package   Fwk\Di
 * @author    Julien Ballestracci <julien@nitronet.org>
 * @copyright 2011-2015 Julien Ballestracci <julien@nitronet.org>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://www.nitronet.org/fwk
 */
namespace Fwk\Di\Definitions;

use Fwk\Di\Container;
use ProxyManager\Configuration;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\GeneratorStrategy\EvaluatingGeneratorStrategy;
use ProxyManager\Proxy\LazyLoadingInterface;
use ProxyManager\Proxy\VirtualProxyInterface;
use Fwk\Di\Exceptions;

/**
 * LazyClassDefinition
 * 
 * Represents a Definition returning an Proxied instance of some class,
 * using the Proxy Pattern / ProxyManager
 *
 * @category Definition
 * @package  Fwk\Di
 * @author   Julien Ballestracci <julien@nitronet.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link     http://www.nitronet.org/fwk
 */
class LazyClassDefinition extends ClassDefinition
{
    /**
     * Instanciates $this->className and return the instance.
     * 
     * @param Container   $container The Di Container
     * @param null|string $name      Name of the current definition (if any)
     * 
     * @return VirtualProxyInterface
     * @throws Exceptions\InvalidClassDefinitionException
     */
    public function invoke(Container $container, $name = null)
    {
        $proxy = $this->getProxyFactory()->createProxy(
            $container->propertizeString($this->className),
            function (&$wrappedInstance, LazyLoadingInterface $proxy) use ($container, $name) {
                $wrappedInstance = parent::invoke($container, $name);
                $proxy->setProxyInitializer(null);
                return true;
            }
        );

        return $proxy;
    }

    /**
     * @return LazyLoadingValueHolderFactory
     */
    protected function getProxyFactory()
    {
        $config = new Configuration();
        $config->setGeneratorStrategy(new EvaluatingGeneratorStrategy());

        return new LazyLoadingValueHolderFactory($config);
    }
}