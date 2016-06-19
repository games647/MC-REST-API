<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Spirit55555\Minecraft\MinecraftJsonColors;
use \Carbon\Carbon;

/**
 * App\Server
 *
 * @property integer $id
 * @property string $address
 * @property string $motd
 * @property string $version
 * @property boolean $online
 * @property boolean $onlinemode
 * @property integer $players
 * @property integer $maxplayers
 * @property integer $ping
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Server whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Server whereAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Server whereMotd($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Server whereVersion($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Server whereOnline($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Server whereOnlinemode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Server wherePlayers($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Server whereMaxplayers($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Server wherePing($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Server whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Server whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property integer $port
 * @method static \Illuminate\Database\Query\Builder|\App\Server wherePort($value)
 * @property-read mixed $raw_motd
 * @property-read mixed $plain_motd
 */
class Server extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'address', 'port',
    ];

    const LEGACY_CHAR = "§";

    //add these additional data to the JSON
    protected $appends = ['plain_motd'];

    public function setMotdAttribute($motd)
    {
        if (is_array($motd)) {
            $motd = MinecraftJsonColors::convertToLegacy($motd);
        }

        $this->attributes['motd'] = $motd;
    }

    public function getPlainMotdAttribute()
    {
        return preg_replace("/(?i)" . self::LEGACY_CHAR . "[0-9A-FK-OR]/", "", $this->motd);
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
