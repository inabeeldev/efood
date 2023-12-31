<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class DeliveryMan extends Authenticatable
{
    use Notifiable;
    protected $guarded = [];

    public function reviews()
    {
        return $this->hasMany(DMReview::class,'delivery_man_id');
    }

    public function withdrawRequest()
    {
        return $this->hasMany(DeliveryManWithdrawRequest::class, 'delivery_man_id', 'id');
    }

    public function rating()
    {
        return $this->hasMany(DMReview::class)
            ->select(DB::raw('avg(rating) average, delivery_man_id'))
            ->groupBy('delivery_man_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'delivery_man_id');
    }
}
