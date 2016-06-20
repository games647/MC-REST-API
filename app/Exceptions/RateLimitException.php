<?php


namespace App\Exceptions;

class RateLimitException extends MojangException
{

    const RESPONSE_CODE = 429;
}
