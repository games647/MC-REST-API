<?php

namespace App\Exceptions;

class CrackedException extends MojangException
{

    //mojang uses 204 -> no content for cracked players
    const RESPONSE_CODE = 204;

}
