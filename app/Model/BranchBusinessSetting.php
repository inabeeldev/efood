<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BranchBusinessSetting extends Model
{
    protected $table = "branch_business_settings";
    
    public function scopeBranch($query){
          return $query->where('branch_id', auth('branch')->id());
    }
    
}
