<?php
/**
 * Fwk
 *
 */
namespace Fwk\Di\Exceptions;

use Fwk\Di\Exception;

/**
 */
class InvalidReference extends Exception
{
    
    public function __construct($name, $prev = null)
    {
        parent::__construct(
            "No data is stored in the Container referenced as '$name'", 
            null, 
            $prev
        );
    }
}