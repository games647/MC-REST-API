<?php

namespace App\Http\Controllers;

use \Cache;
use \App\Player;

class NameController extends ApiController
{

    const UUID_URL = "https://api.mojang.com/users/profiles/minecraft/<username>";
    const UUID_TIME_URL = "https://api.mojang.com/users/profiles/minecraft/<username>?at=<timestamp>";
    const MULTIPLE_UUID_URL = "https://api.mojang.com/profiles/minecraft";

    public function uuid($name)
    {
        $cached = Cache::get('uuid:' . $name);
        if ($cached !== NULL) {
            return collect($cached)->put('source', 'cache');
        }

        $url = str_replace("<username>", $name, self::UUID_URL);
        $request = $this->getConnection($url);
        try {
            $response = curl_exec($request);

            $curl_info = curl_getinfo($request);
            $response_code = $curl_info['http_code'];
            switch ($response_code) {
                case ApiController::RATE_LIMIT_CODE:
                    $player = Player::whereName($name)->getOrFail();
                    return collect($player)->put('source', 'database');
                case ApiController::UNKOWN_CODE:
                    $player = Player::firstOrNew(['name' => $name]);
                    $player->offline_uuid = $this->getOfflineUUID($name);
                    $player->name = $name;
                    $player->save();

                    Cache::put('uuid:' . $name, $player, env('CACHE_LENGTH', 10));
                    return collect($player)->put('source', 'mojang');
                case 200:
                    break;
                default:
                    return response("Please report this return: " . $response_code . curl_error($request), 500);
            }

            $data = json_decode($response, true);

            $uuid = $data['id'];

            $player = Player::firstOrNew(['uuid' => $uuid]);
            $player->uuid = $uuid;
            $player->offline_uuid = $this->getOfflineUUID($name);
            $player->name = $data['name'];
            $player->save();

            Cache::put('uuid:' . $name, $player, env('CACHE_LENGTH', 10));
            return collect($player)->put('source', 'mojang');
        } finally {
            curl_close($request);
        }
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
