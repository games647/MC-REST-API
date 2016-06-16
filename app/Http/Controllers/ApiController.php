<?php

namespace App\Http\Controllers;

class ApiController extends Controller
{

    const UUID_URL = "https://api.mojang.com/users/profiles/minecraft/<username>";
    const UUID_TIME_URL = "https://api.mojang.com/users/profiles/minecraft/<username>?at=<timestamp>";
    const MULTIPLE_UUID_URL = "https://api.mojang.com/profiles/minecraft";

    public function uuid($name)
    {
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
            return array("id" => $data['id'], "name" => $data['name']);
        } catch (Exception $ex) {
            throw $ex;
        } finally {
            curl_close($request);
        }
    }
}
