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

use Fwk\Di\Definitions\ClassDefinition;
use Fwk\Di\Definitions\LazyClassDefinition;
use Fwk\Di\Exception;
use Fwk\Xml\Map;
use Fwk\Di\Container;
use Fwk\Xml\XmlFile;
use Fwk\Di\Definitions\ArrayDefinition;

/**
 * ContainerBuilder
 * 
 * Builds or extends an existing Container using an Xml Map
 *
 * @category Xml
 * @package  Fwk\Di
 * @author   Julien Ballestracci <julien@nitronet.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link     http://www.nitronet.org/fwk
 */
class ContainerBuilder
{
    /**
     * The Map that should return definitions from an Xml file
     * @var Map
     */
    protected $map;
    
    /**
     * Constructor
     * 
     * If no Map is specified, the builder will use the ContainerXmlMap.
     * 
     * @param null|Map $map The Xml Map used to parse the Xml file
     * 
     * @return void
     */
    public function __construct(Map $map = null)
    {
        if (is_null($map)) {
            $map = new ContainerXmlMap();
        }
        
        $this->map = $map;
    }
    
    /**
     * Registers Xml definitions to the provided Container.
     * If no Container is provided, a new one will be created.
     * 
     * @param string|XmlFile $file      Path to Xml (or XmlFile instance)
     * @param null|Container $container Container where definitions are added
     * 
     * @return Container
     */
    public function execute($file, Container $container = null)
    {
        if (!$file instanceof XmlFile) {
            $file = new XmlFile($file);
        }
        if (null === $container) {
            $container = new Container();
        }
        
        $results = $this->map->execute($file);
        
        $container->setProperty('packageDir', dirname($file->getRealPath()));
        $this->applyIniFiles($results['ini'], $container, $file);
        $this->applyDefinitions($results['definitions'], $container);
        $this->applyArrayDefinitions($results['arrayDefs'], $container);
        $this->applyClassDefinitions($results['classDefs'], $container);
        $this->applyListeners($results['listeners'], $container);

        return $container;
    }
    
    
    /**
     * Converts XML definitions from parsing results
     * 
     * @param array     $inis      Parsing results
     * @param Container $container The Di Container
     * @param XmlFile   $file      The XmlFile instance
     * 
     * @return void
     */
    protected function applyIniFiles(array $inis, Container $container, 
        XmlFile $file
    ) {
        foreach ($inis as $infos) {
            $container->iniProperties(
                str_replace(
                    ':baseDir', 
                    dirname($file->getRealPath()), 
                    $infos['value']
                ), 
                $infos['category']
            );
        }
    }
    
    /**
     * Converts XML definitions from parsing results
     * 
     * @param array     $definitions Parsing results
     * @param Container $container   The Di Container
     * 
     * @return void
     */
    protected function applyDefinitions(array $definitions, 
        Container $container
    ) {
        foreach ($definitions as $name => $infos) {
            $container->set(
                $container->propertizeString($name), 
                $container->propertizeString($infos['value'])
            );
        }
    }
    
    /**
     * Converts XML class definitions from parsing results
     * 
     * @param array     $classDefs Parsing results
     * @param Container $container The Di Container
     * 
     * @return void
     */
    protected function applyClassDefinitions(array $classDefs, 
        Container $container
    ) {
        foreach ($classDefs as $name => $infos) {
            $shared = (bool)$this->transformValueType($infos['shared']);
            $lazy = (bool)$this->transformValueType($infos['lazy']);
            $defClass = ($lazy ? LazyClassDefinition::class : ClassDefinition::class);
            $def = new $defClass(
                $infos['className'], 
                $infos['arguments']
            );
            foreach ($infos['methodsCalls'] as $mnfos) {
                $def->addMethodCall(
                    $container->propertizeString($mnfos['method']), 
                    $mnfos['arguments']
                );
            }

            $def->setShared($shared)
                ->setData($infos['data']);
            
            $container->set($name, $def);
        }
    }
    
    /**
     * Converts XML Array definitions from parsing results
     * 
     * @param array     $arrayDefs Parsing results
     * @param Container $container The Di Container
     * 
     * @return void
     */
    protected function applyArrayDefinitions(array $arrayDefs, 
        Container $container
    ) {
        foreach ($arrayDefs as $name => $infos) {
            $shared = (bool)$this->transformValueType($infos['shared']);
            $array  = array();
            foreach ($infos['params'] as $mnfos) {
                $key = (empty($mnfos['key']) ? null : $mnfos['key']);
                $val = $this->transformValueType($mnfos['value']);
                
                if (!empty($key)) {
                    $array[$key] = $val;
                } else {
                    $array[] = $val;
                }
            }

            $def = ArrayDefinition::factory($array)
                    ->setShared($shared)
                    ->setData($infos['data']);

            $container->set($name, $def);
        }
    }

    /**
     * Converts XML definitions from parsing results
     *
     * @param array     $listeners Parsing results
     * @param Container $container The Di Container
     *
     * @return void
     */
    protected function applyListeners(array $listeners, Container $container)
    {
        foreach ($listeners as $infos) {
            $class = $infos['class'];
            $service = $infos['service'];

            if (empty($class) && empty($service)) {
                throw new Exception('Invalid Xml Listener: either "class" or "service" attribute must be defined.');
            }

            if (!empty($class)) {
                $def = new ClassDefinition($class);
                $container->addListener($def->invoke($container));
                continue;
            }

            if (!$container->has($service)) {
                throw new Exception(sprintf('Invalid Xml Listener service ID: "%s"', $service));
            }
            $container->addListener($container->get($service));
        }
    }
    
    /**
     * Transforms a string to a type, if known:
     * 
     * - boolean: true / false
     * - null: null
     * 
     * @param string $value The initial string value
     * 
     * @return null|string|boolean
     */
    protected function transformValueType($value)
    {
        $value = trim($value);
        if (strtolower($value) === "true") {
            $value = true;
        } elseif (strtolower($value) === "false") {
            $value = false;
        } elseif (strtolower($value) === "null") {
            $value = null;
        }
        
        return $value;
    }
    
    /**
     * Returns the Xml Map
     * 
     * @return Map
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * Defines the Xml Map used to parse definitions from the Xml file
     * 
     * @param Map $map The Xml Map 
     * 
     * @return void
     */
    public function setMap(Map $map)
    {
        $this->map = $map;
    }
}