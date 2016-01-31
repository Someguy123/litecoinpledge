<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\UserPledge
 *
 * @property-read \App\Project $project
 * @property-read \App\User $user
 * @property integer $id
 * @property integer $user_id
 * @property integer $project_id
 * @property float $amount
 * @property \Carbon\Carbon $last_pledge
 * @property string $frequency
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 */
class UserPledge extends Model
{
    use SoftDeletes;
    protected $dates = [
        'last_pledge', 'last_email',
        'created_at', 'updated_at'
    ];

    function project() {
        return $this->belongsTo('App\Project');
    }
    function user() {
        return $this->belongsTo('App\User');
    }
}
