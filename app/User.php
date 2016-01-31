<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

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
}
