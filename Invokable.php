<?php
namespace Fwk\Di;

use Fwk\Di\Container;

interface Invokable
{
    public function invoke(Container $container);
}