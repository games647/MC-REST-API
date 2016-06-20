<?php

namespace App\Http\Controllers;

use \Cache;
use \App\Skin;
use \Log;
use \MinecraftSkins\MinecraftSkins;

class SkinController extends MojangController
{

    const SKIN_URL = "http://sessionserver.mojang.com/session/minecraft/profile/<uuid>?unsigned=false";

    public function skin($uuid)
    {
        $cached = Cache::get('skin:' . $uuid);
        if ($cached !== NULL) {
            return collect($cached)->put('source', 'cache');
        }

        try {
            $skin = $this->propertiesMojang($uuid);

            Cache::put('skin:' . $uuid, $skin, env('CACHE_LENGTH', 10));
            return collect($skin)->put('source', 'mojang');
        } catch (RateLimitException $ex) {
            Log::info("RATE LIMITED on skin downloading", ["uuid" => $uuid]);

            $player = Skin::whereUUID($uuid)->get();
            if ($player == null) {
                return response('', 429);
            }

            return collect($player)->put('source', 'database');
        } catch (CrackedException $ex) {
            return response('', 404);
        }
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

        try {
            $skin = $this->propertiesMojang($uuid);

            Cache::put('skin:' . $uuid, $skin, env('CACHE_LENGTH', 10));
            $skin_url = $skin->skin_url;
            if ($skin_url !== NULL) {
                $raw_skin = imagecreatefrompng($skin_url);
                $avatar = MinecraftSkins::head($raw_skin, 4);
                imagepng($avatar, storage_path('app/avatar/' . $uuid . '.png'));
                header('Content-type: image/png');
                imagepng($avatar);
            } else {
                return response('', 204);
            }

            return collect($skin)->put('source', 'mojang');
        } catch (RateLimitException $ex) {
            Log::info("RATE LIMITED on skin downloading", ["uuid" => $uuid]);

            $player = Skin::whereUUID($uuid)->get();
            if ($player == null) {
                return response('', 429);
            }

            return collect($player)->put('source', 'database');
        } catch (CrackedException $ex) {
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

        try {
            $skin = $this->propertiesMojang($uuid);

            Cache::put('skin:' . $uuid, $skin, env('CACHE_LENGTH', 10));
            $skin_url = $skin->skin_url;
            if ($skin_url !== NULL) {
                $raw_skin = imagecreatefrompng($skin_url);
                $avatar = MinecraftSkins::skin($raw_skin, 2);
                imagepng($avatar, storage_path('app/skin/' . $uuid . '.png'));
                header('Content-type: image/png');
                imagepng($avatar);
            } else {
                return response('', 204);
            }

            return collect($skin)->put('source', 'mojang');
        } catch (RateLimitException $ex) {
            Log::info("RATE LIMITED on skin downloading - Cache is too low", ["uuid" => $uuid]);

            return response('', 500);
        } catch (CrackedException $ex) {
            return response('', 404);
        }
    }
}
