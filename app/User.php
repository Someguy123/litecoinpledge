<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\User
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserPledge[] $u_pledges
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Project[] $projects
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $ltc_address
 * @property integer $group
 * @property float $balance
 * @property string $remember_token
 * @property string $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    function owns($relation) {
        return $this->id == $relation->user_id;
    }

    function is_admin() {
        return $this->group >= 10;
    }

    function is_mod()
    {
        return $this->group >= 5;
    }

    function u_pledges() {
        return $this->hasMany('App\UserPledge');
    }
    function projects() {
        return $this->hasMany('App\Project');
    }
}
