<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Model\Plan;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\CentralLogics\Helpers;
use DB;



class PackageController extends Controller
{
    public function getPackages(Request $request){
        try {
            $packages = Plan::where(function($query) use($request){
                if($request->package_id != ""){
                    $query->where('id',$request->package_id);
                }
            })->get();
            return response()->json($packages, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }
    
    public function userSubscription(Request $request){
                    DB::table('logsss')->insert(['data' => json_encode($request->all())]);

        $validator = Validator::make($request->all(), [
            'user_id' => ['required','exists:users,id'],
            'package_id' => ['required','exists:plans,id']
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        try {

            User::where('id',$request->user_id)->update(['plan_id' => $request->package_id]);
            return response()->json(['message' => translate('added_success')], 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
        
    }
}
