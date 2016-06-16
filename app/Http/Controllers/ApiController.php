<?php

namespace App\Http\Controllers;

use \Cache;

class ApiController extends Controller
{

    const UUID_URL = "https://api.mojang.com/users/profiles/minecraft/<username>";
    const UUID_TIME_URL = "https://api.mojang.com/users/profiles/minecraft/<username>?at=<timestamp>";
    const MULTIPLE_UUID_URL = "https://api.mojang.com/profiles/minecraft";

    public function uuid($name)
    {
        $cached = Cache::get('uuid:' . $name);
        if ($cached !== NULL) {
            return array("id" => $cached, "name" => $name, 'source' => 'cache');
        }

        $url = str_replace("<username>", $name, self::UUID_URL);

        $request = curl_init($url);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($request, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
        try {
            $response = curl_exec($request);

            $curl_info = curl_getinfo($request);
            if ($curl_info['http_code'] !== 200) {
                return response("Return code: " . $curl_info['http_code'] . curl_error($request));
            }

            $data = json_decode($response, true);
            $uuid = $data['id'];
            Cache::put('uuid:' . $name, $uuid, 10);
            return array("id" => $data['id'], "name" => $data['name'], 'source' => 'mojang');
        } catch (Exception $ex) {
            throw $ex;
        } finally {
            curl_close($request);
        }
    }
}
