<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Pool
 *
 * @property-read \App\User $user
 * @property integer $id
 * @property string $address
 * @property string $used
 * @property integer $user_id
 * @property integer $project_id
 */
class Pool extends Model
{
    public $timestamps = false;
    public $table = "pool";

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}