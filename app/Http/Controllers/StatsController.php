<?php

namespace App\Http\Controllers;

use \App\Player;

class StatsController extends ApiController
{
    public function info()
    {
        $total_players = Player::all()->count();

        //cache minutes into milliseconds
        $expired = time() - 60 * 1000 * env('CACHE_LENGTH', 10);
        $expired_players = Player::where('updated_at', '<=', time() - $expired)->count();

        return [
            'current_time' => time(),
            'total-players' => $total_players,
            'expired-players' => $expired_players];
    }
}
