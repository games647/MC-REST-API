<?php

namespace App\Http\Controllers;

class OtherController
{
    public function domain_records($domain)
    {
	if (ip2long($domain) !== FALSE) {
	    //it's not a domain it's a IP-Address which we cannot resolve
	    return response('', 400);
	}

	return dns_get_record($domain);
    }
}
