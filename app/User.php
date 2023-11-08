<?php

namespace App;

use App\Model\Plan;
use App\Model\Order;
use App\Model\Branch;
use App\Model\ChefBranch;
use App\Model\Notification;
use App\Model\CustomerAddress;
use App\Model\CustomerIncentive;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','f_name', 'l_name', 'phone', 'email', 'password','point', 'is_active', 'user_type'
    ];

    protected $appends = ['Package'];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_phone_verified' => 'integer',
        'point' => 'integer',
    ];

   /* protected $appends = [ 'branch_id' ];*/

    public function orders(){
        return $this->hasMany(Order::class,'user_id');
    }

    public function addresses(){
        return $this->hasMany(CustomerAddress::class,'user_id');
    }

    public function chefBranch(){
        return $this->hasOne(ChefBranch::class,'user_id', 'id');
    }

    public function customerIncentives()
    {
        return $this->hasMany(CustomerIncentive::class, 'user_id');
    }

    public static function get_chef_branch_name($chef)
    {
        $branch = DB::table('chef_branch')->where('user_id', $chef->id)->get();
        foreach ($branch as $value){
            $branch_name = Branch::where('id', $value->branch_id)->get();
            foreach ($branch_name as $bn){
                return $bn->name;                                               }
        }
    }

    /*public function getBranchIdAttribute()
    {
            $chef = DB::table('chef_branch')->where('user_id', auth()->user()->id)->first('branch_id');
            if (isset($chef)){
                $branch = Branch::where('id', $chef->branch_id)->first();
                return $branch->id;
            }


    }*/

    public function scopeOfType($query, $user_type)
    {
        if ($user_type != 'customer') {
            return $query->where('user_type', $user_type);
        }
    }

    public function getPackageAttribute()
    {
        if ($this->plan_id && $this->plan_id != "") {
            return Plan::where('id',$this->plan_id)->where('status',1)->first();
        }
        return null;
    }

    public function votedBranches()
    {
        return $this->belongsToMany(Branch::class, 'branch_votes', 'user_id', 'branch_id');
    }


}
