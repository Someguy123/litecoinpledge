<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Transaction
 *
 * @property-read \App\User $user
 * @property integer $id
 * @property integer $user_id
 * @property string $type
 * @property string $address
 * @property string $tx
 * @property string $confirmation_key
 * @property integer $confirmations
 * @property float $amount
 * @property string $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Transaction extends Model
{
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function project()
    {
        return $this->belongsTo('App\Project');
    }
}