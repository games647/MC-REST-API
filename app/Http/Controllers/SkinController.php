<?php

namespace App\Http\Controllers;

use \Cache;
use \App\Skin;

class SkinController extends ApiController
{

    const SKIN_URL = "http://sessionserver.mojang.com/session/minecraft/profile/<uuid>?unsigned=false";

    public function skin($uuid)
    {
        $cached = Cache::get('skin:' . $uuid);
        if ($cached !== NULL) {
            return collect($cached)->put('source', 'cache');
        }

        $url = str_replace("<uuid>", $uuid, self::SKIN_URL);
        $request = $this->getConnection($url);
        try {
            $response = curl_exec($request);

            $curl_info = curl_getinfo($request);
            $response_code = $curl_info['http_code'];
            switch ($response_code) {
                case ApiController::RATE_LIMIT_CODE:
                    $player = Skin::whereUUID($uuid)->getOrFail();
                    return collect($player)->put('source', 'database');
                case ApiController::UNKOWN_CODE:
                    return response('', 404);
                case 200:
                    break;
                default:
                    return response("Please report this return: " . $response_code . curl_error($request), 500);
            }

            $data = json_decode($response, true);
            $skinProperties = $data['properties'][0];

            $skin = new Skin();
            $skin->signature = base64_decode($skinProperties['signature']);

            $skinData = json_decode(base64_decode($skinProperties['value']), true);
            $skin->timestamp = $skinData['timestamp'];
            $skin->profile_id = $skinData['profileId'];
            $skin->profile_name = $skinData['profileName'];

            $textures = $skinData['textures'];
            if (!isset($textures['SKIN'])) {
                return response("No skin set", 500);
            }

            $skinTextures = $textures['SKIN'];
            $skin->skin_url = $skinTextures['url'];
            $skin->slim_model = isset($skinTextures['metadata']);

            if (isset($textures['CAPE'])) {
                //user has a cape
                $skin->cape_url = $textures['CAPE']['url'];
            }

            $skin->save();
            Cache::put('skin:' . $uuid, $skin, env('CACHE_LENGTH', 10));
            return collect($skin)->put('source', 'mojang');
        } finally {
            curl_close($request);
        }
    }
}
