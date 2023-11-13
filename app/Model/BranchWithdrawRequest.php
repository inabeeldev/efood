<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BranchWithdrawRequest extends Model
{
    protected $guarded = [];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }


}
