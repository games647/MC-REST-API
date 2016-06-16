<?php
/**
 * An helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App{
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
	class Player extends \Eloquent {}
}

namespace App{
/**
 * App\User
 *
 * @mixin \Eloquent
 */
	class User extends \Eloquent {}
}

