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
    public function __construct($name)
    {
        parent::__construct("Definition '$name' is unregistered", null, null);
    }
}