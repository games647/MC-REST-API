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
 */
class Player extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'uuid',
    ];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->timestamp;
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->timestamp;
    }
}
