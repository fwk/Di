<?php
/**
 * Fwk
 *
 */
namespace Fwk\Di\Exceptions;

use Fwk\Di\Exception;

/**
 */
class InvalidArgument extends Exception
{
    
    public function __construct($index, $prev = null)
    {
        parent::__construct(
            "Argument #'$index' is invalid", 
            null, 
            $prev
        );
    }
}