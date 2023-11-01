<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use App\User;


class MembershipController extends Controller
{
    public function getMemebrships(Request $request){
        try {
            $memberships = Membership::where(function($query) use($request){
                if($request->user_id != ""){
                    $query->where('memberships.user_id',$request->user_id);
                }
                if($request->package_id != ""){
                    $query->where('memberships.plan_id',$request->package_id);
                }
            })
            ->join('users','memberships.user_id','=','users.id')
            ->join('plans','memberships.plan_id','=','plans.id')
            ->select('memberships.*','users.f_name','users.l_name','plans.title as package_name','plans.price')
            ->get();
            return response()->json($memberships, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    public function SubscribeMembership(Request $request){
                    DB::table('logsss')->insert(['data' => "ok"]);

        $validator = Validator::make($request->all(), [
            'package_id' => 'required',
            'user_id' => [
                'required',
                 function ($attribute, $value, $fail) use($request){
                    if(Membership::where(['user_id' => $request->user_id,'plan_id'=>$request->package_id,'status' => 1])->exists()){
                        $fail('membership already exists');
                    }

                }
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        try {
            Membership::insert([
                'user_id' => $request->user_id,
                'plan_id' => $request->package_id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $user = User::where(['id' => $request->user_id ])->update(['plan_id' => $request->package_id]);
            return response()->json(['message' => translate('added_success')], 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }
}
