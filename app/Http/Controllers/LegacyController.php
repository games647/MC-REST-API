<?php

namespace App\Http\Controllers;

use \App\Exceptions\RateLimitException;
use \App\Exceptions\CrackedException;
use \Cache;
use \App\Player;
use \App\Skin;

class LegacyController extends MojangController
{

    const HAS_JOINED_URL = "https://sessionserver.mojang.com/session/minecraft/hasJoined?username=<username>&serverId=<hash>";

    public function uuid($name)
    {
        $cached = Cache::get('uuid:' . $name);
        if ($cached !== NULL) {
            return collect($cached)->put('id', $cached->uuid)->put('source', 'cache');
        }

        try {
            /* @var $player Player */
            $player = $this->uuidMojang($name);
            Cache::put('uuid:' . $name, $player, env('CACHE_LENGTH', 10));
            return collect($player)->put('id', $player->uuid);
        } catch (RateLimitException $ex) {
            return response('', 429);
        } catch (CrackedException $ex) {
            return response('', 204);
        }
    }

    public function uuidAtTime($name, $time)
    {
        $cached = Cache::get('uuid:' . $name . $time);
        if ($cached !== NULL) {
            return collect($cached)->put('id', $cached->uuid)->put('source', 'cache');
        }

        try {
            /* @var $player Player */
            $player = $this->uuidTimeMojang($name, $time);
            Cache::put('uuid:' . $name . $time, $player, env('CACHE_LENGTH', 10));
            return collect($player)->put('id', $player->uuid);
        } catch (RateLimitException $ex) {
            return response('', 429);
        } catch (CrackedException $ex) {
            return response('', 204);
        }
    }

    public function nameHistory($uuid)
    {
        $cached = Cache::get('history' . $uuid);
        if ($cached !== NULL) {
            return $cached->put('source', 'cache');
        }

        try {
            $result = $this->nameHistoryMojang($uuid);

            Cache::put('history:' . $uuid, $result, env('CACHE_LENGTH', 10));
            return collect($result)->put('source', 'mojang');
        } catch (RateLimitException $ex) {
            Log::info("RATE LIMITED on nameHistory", ["name" => $uuid, "time" => $time]);
            //AFAIK this isn't rate-limited
            return response('', 429);
        } catch (CrackedException $ex) {
            return response('', 204);
        }
    }

    public function multipleUuid($names)
    {
        $result = collect();

        $pending = collect();
        foreach ($names as $name) {
            /* @var $cached Player */
            $cached = Cache::get('uuid:' . $name);
            if ($cached !== NULL) {
                $result->push(collect($cached)->put('id', $cached->uuid));
            } else {
                $pending->push($name);
            }
        }

        try {
            $response = $this->uuidMultipleMojang($pending);
            foreach ($response as $player) {
                Cache::put('uuid:' . $player->name, $player, env('CACHE_LENGTH', 10));
                $result->push(collect($player)->put('id', $player->uuid));
            }

            return $result;
        } catch (RateLimitException $ex) {
            return response('', 429);
        }
    }

    public function skin($uuid)
    {
        /* @var $cached Skin */
        $cached = Cache::get('skin:' . $uuid);
        if ($cached !== NULL) {
            return [
                'id' => $cached->profile_id,
                'name' => $cached->profile_name,
                'properties' => [
                    'name' => 'textures',
                    'value' => $cached->encoded_data,
                    'signauture' => $cached->encoded_signature
                    ],
                'source' => 'cache',
                'updated_at' => $cached->updated_at
                ];
        }

        $skin = $this->propertiesMojang($uuid);
        Cache::put('skin:' . $uuid, $skin, env('CACHE_LENGTH', 10));
        return [
                'id' => $cached->profile_id,
                'name' => $cached->profile_name,
                'properties' => [
                    'name' => 'textures',
                    'value' => $cached->encoded_data,
                    'signauture' => $cached->encoded_signature],
                'source' => 'mojang'
                ];
    }

    public function hasJoined($name, $hash)
    {
        $url = str_replace('<hash>', $hash, str_replace("<username>", $name, self::HAS_JOINED_URL));
        $request = $this->getConnection($url);
        try {
            $response = curl_exec($request);

            $data = json_decode($response, true);
            $uuid = $data['id'];

            $player = Player::firstOrNew(['uuid' => $uuid, 'name' => $name]);
            Cache::put('uuid:' . $name, $player, env('CACHE_LENGTH', 10));
            if (count($player->getDirty()) > 0) {
                $player->save();
            } else {
                $player->touch();
            }

            $skin = $this->extractProperties($data);
            Cache::put('skin:' . $uuid, $skin, env('CACHE_LENGTH', 10));
            if (count($skin->getDirty()) > 0) {
                $skin->save();
            } else {
                $skin->touch();
            }

            return $data;
        } finally {
            curl_close($request);
        }
    }
}
