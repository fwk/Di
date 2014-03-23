<?php
/**
 * Fwk
 *
 */
namespace Fwk\Di\Exceptions;

use Fwk\Di\Exception;

/**
 */
class InvalidCallableDefinition extends Exception
{
    
    public function __construct($callable, $prev = null)
    {
        parent::__construct(
            "Callable ". (is_array($callable) ? implode(', ', $callable) : $callable) ." is invalid", 
            null, 
            $prev
        );
    }
}