<?php

namespace App\Model;

use App\User;
use App\Model\Notification;
use Illuminate\Database\Eloquent\Model;

class CustomerIncentive extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function notification()
    {
        return $this->belongsTo(Notification::class, 'notification_id');
    }
}
