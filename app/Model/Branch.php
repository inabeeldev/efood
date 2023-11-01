<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Branch extends Authenticatable
{
    use Notifiable;

    protected $casts = [
        'coverage' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    protected $appends = ['image_url'];



    public function branch_promotion(){
        return $this->hasMany(BranchPromotion::class);
    }

    public function table(){
        return $this->hasMany(Table::class, 'branch_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reservation()
    {
        return $this->hasMany(Reservation::class);
    }
    
    public function getImageUrlAttribute(){
        $imageUrl = "http://efood.startifier.co/storage/app/public/branch/".$this->image;
        // if(file_exists(public_path($imageUrl))){
            return $imageUrl;
        // }
        return null;
    }

}
