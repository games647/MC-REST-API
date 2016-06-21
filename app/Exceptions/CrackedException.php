<?php

namespace App\Exceptions;

class CrackedException extends MojangException
{

    //mojang uses 204 -> no content for cracked players
    const RESPONSE_CODE = 204;

    function __construct()
    {
        $this->code = self::RESPONSE_CODE;
    }

}
