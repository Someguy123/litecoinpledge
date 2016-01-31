<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Pledge
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $type
 * @property integer $project_id
 * @property float $amount
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Pledge extends Model
{
    //
}
