<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Carbon\Carbon;

/**
 * App\Player
 *
 * @property integer $id
 * @property string $uuid
 * @property string $offline_uuid
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Query\Builder|\App\Player whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Player whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Player whereUuid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Player whereOfflineUuid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Player whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Player whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\NameHistory[] $nameHistory
 */
class Player extends Model
{

    const VALID_USERNAME = "/^\w{2,16}$/";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'uuid',
    ];

    protected $appends = ['offline_uuid'];

    /**
     * Generates a offline-mode player UUID.
     *
     * @return string
     */
    public function getOfflineUuidAttribute()
    {
        //extracted from the java code:
        //new GameProfile(UUID.nameUUIDFromBytes(("OfflinePlayer:" + name).getBytes(Charsets.UTF_8)), name));
        $data = hex2bin(md5("OfflinePlayer:" . $this->name));
        //set the version to 3 -> Name based md5 hash
        $data[6] = chr(ord($data[6]) & 0x0f | 0x30);
        //IETF variant
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return bin2hex($data);
    }

    public function nameHistory()
    {
        return $this->hasMany('App\NameHistory');
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->timestamp;
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->timestamp;
    }
}
