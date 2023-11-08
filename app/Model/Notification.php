<?php

namespace App\Model;

use App\Model\CustomerIncentive;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{

    protected $casts = [
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }

    public function customerIncentives()
    {
        return $this->hasMany(CustomerIncentive::class, 'notification_id');
    }

}
