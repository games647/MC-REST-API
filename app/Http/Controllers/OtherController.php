<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use \Cache;

class OtherController extends BaseController
{
    public function domainRecords($domain)
    {
        $cached = Cache::get('domain:' . $domain);
        if ($cached !== NULL) {
            return collect($cached)->put('source', 'cache');
        }

	if (ip2long($domain) !== FALSE) {
	    //it's not a domain it's a IP-Address which we cannot resolve
	    return response('', 400);
	}

        $records = collect(dns_get_record($domain))->put('updated_at', time());
        Cache::put('domain:' . $domain, $records, env('CACHE_LENGTH', 10));
	return $records;
    }
}
