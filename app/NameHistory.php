<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Carbon\Carbon;

/**
 * App\NameHistory
 *
 * @property integer $id
 * @property integer $player_id
 * @property string $name
 * @property integer $changedToAt
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Player $player
 * @method static \Illuminate\Database\Query\Builder|\App\NameHistory whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\NameHistory wherePlayerId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\NameHistory whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\NameHistory whereChangedToAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\NameHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\NameHistory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class NameHistory extends Model
{

    protected $hidden = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'changedToAt',
    ];
    //hide this from JSON, because it's useless
    protected $hidden = ['player_id'];

    public function player()
    {
        return $this->belongsTo('App\Player');
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
