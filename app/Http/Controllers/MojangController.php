<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use \App\Exceptions\RateLimitException;
use \App\Exceptions\CrackedException;
use \App\Exceptions\MojangException;
use \App\Player;
use \App\Skin;

class MojangController extends BaseController
{

    const UUID_URL = "https://api.mojang.com/users/profiles/minecraft/<username>";
    const UUID_TIME_URL = "https://api.mojang.com/users/profiles/minecraft/<username>?at=<timestamp>";
    const MULTIPLE_UUID_URL = "https://api.mojang.com/profiles/minecraft";
    
    const NAME_HISTORY_URL = "https://api.mojang.com/user/profiles/<uuid>/names";

    const SKIN_URL = "http://sessionserver.mojang.com/session/minecraft/profile/<uuid>?unsigned=false";

    protected function getConnection($url)
    {
        $request = curl_init($url);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($request, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
        $source_ips = env('SOURCE_IPS');
        if (!empty($source_ips)) {
            $ips = explode('|', env('SOURCE_IPS'));
            $random_index = array_rand($ips);
            curl_setopt($request, CURLOPT_INTERFACE, $ips[$random_index]);
        }

        return $request;
    }

    public function uuidTimeMojang($name, $time)
    {
        $url = str_replace('<timestamp>', $time, str_replace("<username>", $name, self::UUID_TIME_URL));
        $request = $this->getConnection($url);
        try {
            $response = curl_exec($request);
            $this->handleMojangException($request);

            $data = json_decode($response, true);

            $player = new Player;
            $player->uuid = $data['id'];
            $player->name = $data['name'];
            $this->saveOrTouch($player);

            //do not save it, because we don't know if it's up-to-date
            return $player;
        } finally {
            curl_close($request);
        }
    }

    public function uuidMojang($name)
    {
        $url = str_replace("<username>", $name, self::UUID_URL);
        $request = $this->getConnection($url);
        try {
            $response = curl_exec($request);
            $this->handleMojangException($request);

            $data = json_decode($response, true);
            $uuid = $data['id'];

            /* @var $player Player */
            $player = Player::firstOrNew(['uuid' => $uuid]);
            $player->name = $data['name'];
            $this->saveOrTouch($player);

            return $player;
        } finally {
            curl_close($request);
        }
    }

    public function uuidMultipleMojang($names)
    {
        if (empty($names)) {
            return [];
        }

        $url = str_replace("<username>", $name, self::UUID_URL);
        $request = $this->getConnection($url);
        curl_setopt($request, CURLOPT_POST, TRUE);
        curl_setopt($request, CURLOPT_POSTFIELDS, $names);
        try {
            $response = curl_exec($request);
            $this->handleMojangException($request);

            $data = json_decode($response, true);

            $result = collect();
            foreach ($data as $entry) {
                $uuid = $entry['id'];

                $player = Player::firstOrNew(['uuid' => $uuid]);
                $player->name = $entry['name'];
                $this->saveOrTouch($player);
            }

            return $result;
        } finally {
            curl_close($request);
        }
    }

    public function nameHistoryMojang($uuid)
    {
        /* @var $player Player */
        $player = Player::firstOrNew(['uuid' => $uuid]);

        $url = str_replace("<uuid>", $uuid, self::NAME_HISTORY_URL);
        $request = $this->getConnection($url);
        try {
            $response = curl_exec($request);
            $this->handleMojangException($request);

            $data = collect(json_decode($response, true));
            //the first entry always contains the currently used name and then no changedToAt entry
            $first_entry = $data->shift();

            $name = $first_entry['name'];
            //update the last recently name globally on success
            $player->name = $first_entry['name'];
            $this->saveOrTouch($player);

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
            return $result;
        } finally {
            curl_close($request);
        }
    }

    public function propertiesMojang($uuid)
    {
        $url = str_replace("<uuid>", $uuid, self::SKIN_URL);
        $request = $this->getConnection($url);
        try {
            $response = curl_exec($request);
            $this->handleMojangException($request);

            $data = json_decode($response, true);
            $skin = $this->extractProperties($data);
            $this->saveOrTouch($skin);

            return $skin;
        } finally {
            curl_close($request);
        }
    }

    protected function extractProperties($data)
    {
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
    }

    protected function handleMojangException($request)
    {
        $curl_info = curl_getinfo($request);
        $response_code = $curl_info['http_code'];
        switch ($response_code) {
            case RateLimitException::RESPONSE_CODE:
                throw new RateLimitException();
            case CrackedException::RESPONSE_CODE:
                throw new CrackedException;
            case 200:
                break;
            default:
                throw new MojangException;
        }
    }

    /**
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    protected function saveOrTouch($model)
    {
        if (count($model->getDirty()) > 0) {
            $model->save();
        } else {
            $model->touch();
        }
    }
}
