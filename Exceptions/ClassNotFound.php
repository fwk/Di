<?php
/**
 * Fwk
 *
 */
namespace Fwk\Di\Exceptions;

use Fwk\Di\Exception;

/**
 */
class ClassNotFound extends Exception
{
    
    public function __construct($name, $prev = null)
    {
        parent::__construct(
            "Class '$name' does not exists", 
            null, 
            $prev
        );
    }
}