<?php
/**
 * Fwk
 *
 */
namespace Fwk\Di\Exceptions;

use Fwk\Di\Exception;

/**
 */
class InvalidClassDefinition extends Exception
{
    
    public function __construct($name, $prev = null)
    {
        parent::__construct(
            "Definition for class '$name' is invalid", 
            null, 
            $prev
        );
    }
}