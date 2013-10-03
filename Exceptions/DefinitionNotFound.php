<?php
/**
 * Fwk
 *
 */
namespace Fwk\Di\Exceptions;

use Fwk\Di\Exception;

/**
 */
class DefinitionNotFound extends Exception
{
    public function __construct($name, $prev = null)
    {
        parent::__construct("Definition '$name' is not registered", null, null);
    }
}
