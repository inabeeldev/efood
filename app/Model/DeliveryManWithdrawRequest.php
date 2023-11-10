<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DeliveryManWithdrawRequest extends Model
{

    protected $guarded = [];

    public function deliveryMan()
    {
        return $this->belongsTo(DeliveryMan::class, 'delivery_man_id');
    }
}
