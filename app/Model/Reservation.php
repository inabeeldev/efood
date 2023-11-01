<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    public function branch(){
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
