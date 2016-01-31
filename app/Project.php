<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Project extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description'];

    protected $dates = ['deleted_at'];

    function getMonthlyPledgedAttribute() {
        $_this = $this;
        $cached = app('cache')->remember('monthly_pledged:'.$this->id, 3, function () use ($_this) {
            return UserPledge::where('project_id', $_this->id)->sum('amount');
        });
        return $cached;
    }

    function getMonthlyUsersAttribute()
    {
        $_this = $this;
        $cached = app('cache')->remember('monthly_users:' . $this->id, 3, function () use ($_this) {
            return UserPledge::where('project_id', $_this->id)->count();
        });
        return $cached;
    }
}
