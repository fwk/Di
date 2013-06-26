<?php
namespace Fwk\Di;

use Fwk\Di\Container;

interface Definition
{
    public function invoke(Container $container);
    
    public function setContainer(Container $container);
    
    public function getContainer();
}