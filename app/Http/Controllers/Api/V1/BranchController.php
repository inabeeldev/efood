<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Model\Branch;
use App\Model\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    public function getBranches(Request $request){
        \Log::info(json_encode($request->all()));
        try {
            $branhes = Branch::where('branches.status',1)
            ->where(function($query) use($request){
                if($request->branch_id != ""){
                    $query->where('branches.id',$request->branch_id);
                }
            })
            ->select(
                'branches.id',
                'branches.name',
                'branches.latitude',
                'branches.longitude',
                'branches.outdoor_slots',
                'branches.indoor_slots',
                'branches.status',
                'branches.coverage',
                'branches.image',
                DB::raw("(branches.indoor_slots - (SELECT sum(reservations.number_of_reservations) FROM reservations WHERE branches.id = reservations.branch_id  and reservations.reservation_type = 'indoor_reservation' )) as remaianing_indoor_slots"),
                DB::raw("(branches.outdoor_slots - (SELECT sum(reservations.number_of_reservations) FROM reservations WHERE branches.id = reservations.branch_id  and reservations.reservation_type = 'outdoor_reservation' )) as remaianing_outoor_slots")
            )->get();
            foreach($branhes as $b){
                $date['date'] = $request->date;
                if($request->date){
                    
                }
                $b->remaianing_indoor_slots = ($b->indoor_slots - DB::table('reservations')->where(function($query) use($request,$date){
                    if(count($date) > 0){
                        $query->where($date);
                    }
                })->where(['branch_id' => $b->id, 'reservation_type' => 'indoor_reservation','status' => 1])->sum('number_of_reservations'));
                $b->remaianing_outoor_slots = ($b->outdoor_slots - DB::table('reservations')->where(function($query) use($request,$date){
                    if(count($date) > 0){
                        $query->where($date);
                    }
                })->where(['branch_id' => $b->id, 'reservation_type' => 'outdoor_reservation','status' => 1])->sum('number_of_reservations'));
                $branches[] = $b; 
            }
            return response()->json($branhes, 200);
        } catch (\Exception $e) {
            return response()->json([$e->getMessage()], 200);
        }
    }

    public function getNearestBranches(Request $request){
       try {
            $branch = Branch::find($request->branch_id);
            $branhes = DB::select("SELECT
            branches.name,branches.id,
                6371 * acos(
                    cos(
                        radians( $branch->latitude ))  * cos(
                        radians( branches.latitude ))  * cos(
                        radians( branches.longitude ) - radians( $branch->longitude ))  + sin(
                        radians( $branch->latitude ))  * sin(
                    radians( branches.latitude ))) AS distance 
            FROM
                `branches` 
            GROUP BY
                `branches`.`id`");
            return response()->json($branhes, 200);
       } catch (\Throwable $e) {
            return response()->json([$e->getMessage()], 200);
        }
    }

    public function getBranchSlots(Request $request){
        try {
            $branhes = Branch::where(function($query) use($request){
                if($request->branch_id != ""){
                    $query->where('branches.id',$request->branch_id);
                }
                if($request->date != ""){   
                    // $query->where('reservations.date',$request->date);
                }
            })
            ->leftJoin('reservations','branches.id','reservations.branch_id')
            ->select(
                'branches.id',
                'branches.name',
                'branches.image',
                'branches.outdoor_slots as total_outdoor_slots',
                'branches.indoor_slots as total_indoor_slots',
                'reservations.date',
                DB::raw("(branches.indoor_slots - (SELECT sum(reservations.number_of_reservations) FROM reservations WHERE branches.id = reservations.branch_id  and reservations.reservation_type = 'indoor_reservation' )) as remaianing_indoor_slots"),
                DB::raw("(branches.outdoor_slots - (SELECT sum(reservations.number_of_reservations) FROM reservations WHERE branches.id = reservations.branch_id  and reservations.reservation_type = 'outdoor_reservation' )) as remaianing_outoor_slots")
                )
            ->limit(1)
            ->get();
            
            foreach($branhes as $b){
                $b->remaianing_indoor_slots = ($b->total_indoor_slots - DB::table('reservations')->where(['branch_id' => $b->id, 'reservation_type' => 'indoor_reservation','status' => 1, 'reservations.date'=> $request->date])->sum('number_of_reservations'));
                $b->remaianing_outoor_slots = ($b->total_outdoor_slots - DB::table('reservations')->where(['branch_id' => $b->id, 'reservation_type' => 'outdoor_reservation','status' => 1, 'reservations.date'=> $request->date])->sum('number_of_reservations'));
                $branches[] = $b; 
            }
           
            return response()->json($branhes, 200);
        } catch (\Exception $e) {
            return response()->json([$e->getMessage()], 200);
        }
    }
}
