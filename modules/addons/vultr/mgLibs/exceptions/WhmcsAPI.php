<?php

namespace MGModule\vultr\mgLibs\exceptions;

/**
 * Description of whmcsAPI
 */
class WhmcsAPI extends System
{
    public function __construct($message, $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
