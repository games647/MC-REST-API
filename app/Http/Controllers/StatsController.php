<?php

namespace App\Http\Controllers;

use \App\Player;
use \App\Server;
use \App\Skin;
use \Carbon\Carbon;

class StatsController extends ApiController
{
    public function info()
    {
        $total_players = Player::all()->count();

        //cache minutes into milliseconds
        $expired = Carbon::now()->subMinute(60 * 1000 * env('CACHE_LENGTH', 10));
        $expired_players = Player::where('updated_at', '<=', $expired)->count();

        $total_servers = Server::all()->count();
        $expired_servers = Server::where('updated_at', '<=', $expired)->count();

        $total_skins = Skin::all()->count();
        $expired_skins = Skin::where('updated_at', '<=', $expired)->count();

        return [
            'current_time' => time(),
            'total-players' => $total_players,
            'expired-players' => $expired_players,
            'total-servers' => $total_servers,
            'expired-servers' => $expired_servers,
            'total-skins' => $total_skins,
            'expired-skins' => $expired_skins];
    }
}
