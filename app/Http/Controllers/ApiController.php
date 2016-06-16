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
            $file_loc = $this->path('uuid:' . $name);
            $inserted = filemtime($file_loc);
            return ["id" => $cached, "name" => $name, 'source' => 'cache', 'timestamp' => $inserted];
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
            Cache::put('uuid:' . $name, $uuid, env('CACHE_LENGTH', 10));
            return ["id" => $data['id'], "name" => $data['name'], 'source' => 'mojang'];
        } catch (Exception $ex) {
            throw $ex;
        } finally {
            curl_close($request);
        }
    }

    /**
     * Get the full path for the given cache key.
     *
     * @param  string  $key
     * @return string
     */
    protected function path($key)
    {
        $parts = array_slice(str_split($hash = sha1($key), 2), 0, 2);
        return storage_path('framework/cache') . '/' . implode('/', $parts) . '/' . $hash;
    }
}
