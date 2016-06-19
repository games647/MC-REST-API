<?php

namespace App\Http\Controllers;

use \Cache;
use \Log;
use \App\Player;

class NameHistoryController extends ApiController
{

    const NAME_HISTORY_URL = "https://api.mojang.com/user/profiles/<uuid>/names";

    public function nameHistory($uuid)
    {
        //it's not possible to add a method call as a default paremeter ($uuid, $time = time())
        return $this->nameHistoryAt($uuid, round(microtime(true) * 1000));
    }

    public function nameHistoryAt($uuid, $time)
    {
        $result = $this->all($uuid);
        $histories = $result->get('histories');

        $requesting_history = $result->only(['name']);
        foreach ($histories as $history) {
            $changed_at = $history['changedToAt'];
            if ($changed_at <= $time) {
                $requesting_history = collect($history);
            }
        }

        return $result->forget(['offline_uuid', 'histories'])->merge($requesting_history);
    }

    public function all($uuid)
    {
        /* @var $player Player */
        $player = Player::firstOrNew(['uuid' => $uuid]);

        $url = str_replace("<uuid>", $uuid, self::NAME_HISTORY_URL);
        $request = $this->getConnection($url);
        try {
            $response = curl_exec($request);

            $curl_info = curl_getinfo($request);
            $response_code = $curl_info['http_code'];
            switch ($response_code) {
                case ApiController::RATE_LIMIT_CODE:
                    Log::info("RATE LIMITED on nameHistory", ["name" => $uuid, "time" => $time]);
                    //ASFAIK this isn't rate-limited
                    return response('', 500);
                case ApiController::UNKOWN_CODE:
                    return response('', 404);
                case 200:
                    break;
                default:
                    return response("Please report this return: " . $response_code . curl_error($request), 500);
            }

            $data = collect(json_decode($response, true));
            //the first entry always contains the currently used name and then no changedToAt entry
            $first_entry = $data->shift();

            $name = $first_entry['name'];
            //update the last recently name globally on success
            $player->name = $first_entry['name'];
            $player->save();
            Cache::put('uuid' . $name, $player, env('CACHE_LENGTH', 10));

            //override the update_at of the player to reflect all
            $result = collect($player)->put('updated_at', time());
            $histories = collect();

            foreach ($data as $entry) {
                $name = $entry['name'];
                $changedAt = $entry['changedToAt'];
                $name_history = $player->nameHistory()->firstOrNew(['changedToAt' => $changedAt, 'name' => $name]);
                $histories->push($name_history);
            }

            $result->put('histories', $histories);

            Cache::put('history:' . $uuid, $result, env('CACHE_LENGTH', 10));
            return collect($result)->put('source', 'mojang');
        } finally {
            curl_close($request);
        }
    }
}
