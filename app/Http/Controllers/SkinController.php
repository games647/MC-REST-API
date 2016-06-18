<?php

namespace App\Http\Controllers;

use \Cache;
use \App\Skin;
use \MinecraftSkins\MinecraftSkins;

class SkinController extends ApiController
{

    const SKIN_URL = "http://sessionserver.mojang.com/session/minecraft/profile/<uuid>?unsigned=false";

    public function skin($uuid)
    {
        $cached = Cache::get('skin:' . $uuid);
        if ($cached !== NULL) {
            return collect($cached)->put('source', 'cache');
        }

        $response = $this->getPropertiesFromMojang($uuid);
        if (is_int($response)) {
            switch ($response) {
                case ApiController::RATE_LIMIT_CODE:
                    $player = Skin::whereUUID($uuid)->get();
                    if ($player == null) {
                        return response('', 429);
                    }

                    return collect($player)->put('source', 'database');
                case ApiController::UNKOWN_CODE:
                    return response('', 404);
                default:
                    return response("Please report this return: " . $response, 500);
            }
        }

        $response->save();
        Cache::put('skin:' . $uuid, $response, env('CACHE_LENGTH', 10));
        return collect($response)->put('source', 'mojang');
    }

    public function avatarImage($uuid)
    {
        $exists = file_exists(storage_path('app/avatar/' . $uuid . '.png'));
        if ($exists) {
            $image = imagecreatefrompng(storage_path('app/avatar/' . $uuid . '.png'));
            header('Content-type: image/png');
            imagepng($image);
            return;
        }

        /* @var $cached Skin */
        $cached = Cache::get('skin:' . $uuid);
        if ($cached !== NULL) {
            $skin_url = $cached->skin_url;
            if ($skin_url !== NULL) {
                $raw_skin = imagecreatefrompng($skin_url);
                $avatar = MinecraftSkins::head($raw_skin, 4);
                imagepng($avatar, storage_path('app/avatar/' . $uuid . '.png'));

                header('Content-type: image/png');
                imagepng($avatar);
            } else {
                return response('', 404);
            }
        }

        $response = $this->getPropertiesFromMojang($uuid);
        if (is_int($response)) {
            switch ($response) {
                case ApiController::RATE_LIMIT_CODE:
                    $player = Skin::whereUUID($uuid)->get();
                    if ($player == null) {
                        return response('', 429);
                    }

                    return collect($player)->put('source', 'database');
                case ApiController::UNKOWN_CODE:
                    return response('', 404);
                default:
                    return response("Please report this return: " . $response, 500);
            }
        }

        $response->save();
        Cache::put('skin:' . $uuid, $response, env('CACHE_LENGTH', 10));
        $skin_url = $response->skin_url;
        if ($skin_url !== NULL) {
            $raw_skin = imagecreatefrompng($skin_url);
            $avatar = MinecraftSkins::head($raw_skin, 4);
            imagepng($avatar, storage_path('app/avatar/' . $uuid . '.png'));
            header('Content-type: image/png');
            imagepng($avatar);
        } else {
            return response('', 404);
        }
    }

    public function skinImage($uuid)
    {
        $exists = file_exists(storage_path('app/skin/' . $uuid . '.png'));
        if ($exists) {
            $image = imagecreatefrompng(storage_path('app/skin/' . $uuid . '.png'));
            header('Content-type: image/png');
            imagepng($image);
            return;
        }

        /* @var $cached Skin */
        $cached = Cache::get('skin:' . $uuid);
        if ($cached !== NULL) {
            $skin_url = $cached->skin_url;
            if ($skin_url !== NULL) {
                $raw_skin = imagecreatefrompng($skin_url);
                $avatar = MinecraftSkins::skin($raw_skin, 2);
                imagepng($avatar, storage_path('app/skin/' . $uuid . '.png'));
                header('Content-type: image/png');
                imagepng($avatar);
            } else {
                return response('', 404);
            }
        }

        $response = $this->getPropertiesFromMojang($uuid);
        if (is_int($response)) {
            switch ($response) {
                case ApiController::RATE_LIMIT_CODE:
                    $player = Skin::whereUUID($uuid)->get();
                    if ($player == null) {
                        return response('', 429);
                    }

                    return collect($player)->put('source', 'database');
                case ApiController::UNKOWN_CODE:
                    return response('', 404);
                default:
                    return response("Please report this return: " . $response, 500);
            }
        }

        $response->save();
        Cache::put('skin:' . $uuid, $response, env('CACHE_LENGTH', 10));
        $skin_url = $response->skin_url;
        if ($skin_url !== NULL) {
            $raw_skin = imagecreatefrompng($skin_url);
            $avatar = MinecraftSkins::skin($raw_skin, 2);
            imagepng($avatar, storage_path('app/skin/' . $uuid . '.png'));
            header('Content-type: image/png');
            imagepng($avatar);
        } else {
            return response('', 404);
        }
    }

    /**
     *
     * @param str $uuid
     * @return Skin or int the response code
     */
    protected function getPropertiesFromMojang($uuid) {
        $url = str_replace("<uuid>", $uuid, self::SKIN_URL);
        $request = $this->getConnection($url);
        try {
            $response = curl_exec($request);

            $curl_info = curl_getinfo($request);
            $response_code = $curl_info['http_code'];
            if ($response_code !== 200) {
                return $response_code;
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
            if (isset($textures['SKIN'])) {
                //no skin set
                $skinTextures = $textures['SKIN'];
                $skin->skin_url = $skinTextures['url'];
                $skin->slim_model = isset($skinTextures['metadata']);

                if (isset($textures['CAPE'])) {
                    //user has a cape
                    $skin->cape_url = $textures['CAPE']['url'];
                }
            }

            return $skin;
        } finally {
            curl_close($request);
        }
    }
}
