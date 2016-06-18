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
                    $player = Player::whereName($name)->firstOrFail();
                    return collect($player)->put('source', 'database');
                case ApiController::UNKOWN_CODE:
                    $player = Player::firstOrNew(['name' => $name]);
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
            $player->name = $data['name'];
            $player->save();

            Cache::put('uuid:' . $name, $player, env('CACHE_LENGTH', 10));
            return collect($player)->put('source', 'mojang');
        } finally {
            curl_close($request);
        }
    }

    public function hasPaid($name)
    {
        /* @var $cached Player */
        $cached = Cache::get('uuid:' . $name);
        if ($cached !== NULL) {
            return [
                'username' => $cached->name,
                'premium' => !is_null($cached->uuid),
                'source' => 'cache',
                'updated_at' => $cached->updated_at];
        }

        $url = str_replace("<username>", $name, NameController::UUID_URL);
        $request = $this->getConnection($url);
        try {
            $response = curl_exec($request);

            $curl_info = curl_getinfo($request);
            $response_code = $curl_info['http_code'];
            switch ($response_code) {
                case ApiController::RATE_LIMIT_CODE:
                    $player = Player::whereName($name)->firstOrFail();
                    return [
                        'username' => $cached->name,
                        'premium' => !is_null($player->uuid),
                        'source' => 'database',
                        'updated_at' => $cached->updated_at];
                case ApiController::UNKOWN_CODE:
                    $player = Player::firstOrNew(['name' => $name]);
                    $player->name = $name;
                    $player->save();

                    Cache::put('uuid:' . $name, $player, env('CACHE_LENGTH', 10));
                    return ['username' => $cached->name, 'premium' => !is_null($player->uuid), 'source' => 'mojang'];
                case 200:
                    break;
                default:
                    return response("Please report this return: " . $response_code . curl_error($request), 500);
            }

            $data = json_decode($response, true);

            $uuid = $data['id'];

            $player = Player::firstOrNew(['uuid' => $uuid]);
            $player->uuid = $uuid;
            $player->name = $data['name'];
            $player->save();

            Cache::put('uuid:' . $name, $player, env('CACHE_LENGTH', 10));
            return [
                'username' => $player->name,
                'premium' => !is_null($player->uuid),
                'source' => 'mojang'];
        } finally {
            curl_close($request);
        }
    }
}
