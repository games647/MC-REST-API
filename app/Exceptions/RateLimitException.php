<?php


namespace App\Exceptions;

class RateLimitException extends MojangException
{

    const RESPONSE_CODE = 429;

    function __construct()
    {
        $this->code = self::RESPONSE_CODE;
    }

}
