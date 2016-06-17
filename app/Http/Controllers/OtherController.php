<?php

namespace App\Http\Controllers;

use \App\Player;
use \Cache;

class OtherController extends ApiController
{
    public function domainRecords($domain)
    {
	if (ip2long($domain) !== FALSE) {
	    //it's not a domain it's a IP-Address which we cannot resolve
	    return response('', 400);
	}

	return dns_get_record($domain);
    }
}
