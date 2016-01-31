<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPledge extends Model
{
    function project() {
        return $this->belongsTo('App\Project');
    }
    function user() {
        return $this->belongsTo('App\User');
    }
}
