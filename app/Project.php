<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * App\Project
 *
 * @property-read mixed $monthly_pledged
 * @property-read mixed $monthly_users
 * @property integer $id
 * @property string $name
 * @property string $ltc_address
 * @property string $project_img
 * @property string $description
 * @property float $total_pledged
 * @property float $project_balance
 * @property integer $user_id
 * @property integer $verified
 * @property \Carbon\Carbon $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
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
