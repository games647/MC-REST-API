<?php

namespace App\Http\Controllers;

use \Cache;
use \App\Player;
use \App\Exceptions\RateLimitException;
use \App\Exceptions\CrackedException;

class NameController extends MojangController
{

    public function uuid($name)
    {
        $cached = Cache::get('uuid:' . $name);
        if ($cached !== NULL) {
            return collect($cached)->put('source', 'cache');
        }

        try {
            $player = $this->uuidMojang($name);
            Cache::put('uuid:' . $name, $player, env('CACHE_LENGTH', 10));
            return collect($player)->put('source', 'mojang');
        } catch (RateLimitException $ex) {
            Log::info("RATE LIMITED on name->uuid", ["name" => $name]);

            $player = Player::whereName($name)->firstOrFail();
            return collect($player)->put('source', 'database');
        } catch (CrackedException $ex) {
            $player = Player::firstOrCreate(['name' => $name]);

            Cache::put('uuid:' . $name, $player, env('CACHE_LENGTH', 10));
            return collect($player)->put('source', 'mojang');
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

        try {
            $player = $this->uuidMojang($name);
            Cache::put('uuid:' . $name, $player, env('CACHE_LENGTH', 10));
            return [
                'username' => $player->name,
                'premium' => !is_null($player->uuid),
                'source' => 'mojang'];
        } catch (RateLimitException $ex) {
            Log::info("RATE LIMITED on haspaid", ["name" => $name]);

            $player = Player::whereName($name)->firstOrFail();
            return [
                'username' => $player->name,
                'premium' => !is_null($player->uuid),
                'source' => 'database',
                'updated_at' => $cached->updated_at];
        } catch (CrackedException $ex) {
            $player = Player::firstOrCreate(['name' => $name]);

            Cache::put('uuid:' . $name, $player, env('CACHE_LENGTH', 10));
            return ['username' => $cached->name, 'premium' => !is_null($player->uuid), 'source' => 'mojang'];
        }
    }
}
