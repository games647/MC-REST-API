<?php

namespace App\Http\Controllers;

use \Cache;
use \Log;

class NameHistoryController extends MojangController
{

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
        try {
            $result = $this->nameHistoryMojang($uuid);

            Cache::put('history:' . $uuid, $result, env('CACHE_LENGTH', 10));
            return collect($result)->put('source', 'mojang');
        } catch (RateLimitException $ex) {
            Log::info("RATE LIMITED on nameHistory", ["name" => $uuid, "time" => $time]);
            //AFAIK this isn't rate-limited
            return response('', 500);
        } catch (CrackedException $ex) {
            return response('', 404);
        }
    }
}
