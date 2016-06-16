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
            $offline_uuid = $this->getOfflineUUID($name);
            return [
                "id" => $cached,
                "name" => $name,
                'source' => 'cache',
                'timestamp' => $inserted,
                'cracked' => $offline_uuid];
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
            $offline_uuid = $this->getOfflineUUID($name);
            Cache::put('uuid:' . $name, $uuid, env('CACHE_LENGTH', 10));
            return [
                'id' => $data['id'],
                'name' => $data['name'],
                'source' => 'mojang',
                'cracked' => $offline_uuid];
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

    /**
     * Generates a offline-mode player UUID.
     *
     * @param $username string
     * @return string
     */
    private function getOfflineUUID($username) {
        //extracted from the java code:
        //new GameProfile(UUID.nameUUIDFromBytes(("OfflinePlayer:" + name).getBytes(Charsets.UTF_8)), name));
        $data = hex2bin(md5("OfflinePlayer:" . $username));
        //set the version to 3 -> Name based md5 hash
        $data[6] = chr(ord($data[6]) & 0x0f | 0x30);
        //IETF variant
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return bin2hex($data);
    }
}
