<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class ApiController extends BaseController
{

    const RATE_LIMIT_CODE = 429;
    //no content
    const UNKOWN_CODE = 204;

    protected function getConnection($url)
    {
        $request = curl_init($url);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($request, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
        $source_ips = env('SOURCE_IPS');
        if (!empty($source_ips)) {
            $ips = explode('|', env('SOURCE_IPS'));
            $random_index = array_rand($ips);
            curl_setopt($request, CURLOPT_INTERFACE, $ips[$random_index]);
        }

        return $request;
    }
}
