<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Model\Reservation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Model\Branch;
use App\Model\Notification;


class ReservationController extends Controller
{
    public static function storeReservation(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'reservation_type' => 'required',
            'branch_id' => [
                'required',
                 function ($attribute, $value, $fail) use($request){
                    $reserveSlots = Reservation::where(['branch_id'=>$value,'reservation_type'=>$request->reservation_type,'date'=>$request->date,'status'=>1])->sum('number_of_reservations');
                    $branch = Branch::find($request->branch_id);
                    $remaining_slots = 0;
                    if($request->reservation_type == "indoor_reservation"){
                        $remaining_slots = ($branch->indoor_slots - $reserveSlots);
                    }else if($request->reservation_type == "outdoor_reservation"){
                        $remaining_slots = ($branch->outdoor_slots - $reserveSlots);
                    }else{
                        $remaining_slots = $reserveSlots;
                    }

                    if($remaining_slots < $request->number_of_reservations || $remaining_slots <= 0){
                        $fail('slots not available, remaining slots:'.$remaining_slots);
                    }

                }
            ],
            'number_of_reservations' => 'required',
            // 'date' => 'required|unique:reservations,date',
            'time' => 'date_format:H:i'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        try {
            Reservation::insert([
                'user_id' => $request->user_id,
                'branch_id' => $request->branch_id,
                'reservation_type' => $request->reservation_type,
                'number_of_reservations' => $request->number_of_reservations,
                'date' => $request->date,
                'time' => $request->time,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $reserveSlotsOutdoor = Reservation::where(['branch_id'=>$request->branch_id,'reservation_type'=>'outdoor_reservation','status'=>1])->sum('number_of_reservations');
            $reserveSlotsIndoor = Reservation::where(['branch_id'=>$request->branch_id,'reservation_type'=>'indoor_reservation','status'=>1])->sum('number_of_reservations');

            $branch = Branch::find($request->branch_id);
            $rsout = ($branch->outdoor_slots - $reserveSlotsOutdoor);
            $rsin = ($branch->indoor_slots - $reserveSlotsIndoor);


            if($rsout > 0 || $rsin){
                $notification = new Notification;
                $notification->title = "Restaurant Slots Avaiable";
                $notification->description = $remaining_slots." are availbel on ".$branch->name." restaurant Indoor Slots: ".$rsin." Outdoor Slots: ".$rsout;
                $notification->status = 1;
                $notification->branch_id = auth('branch')->id();
                $notification->save();
                Helpers::send_push_notif_to_topic($notification, 'notify','general');
            }

            return response()->json(['message' => translate('added_success')], 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }


    public function getReservations(Request $request){
        try {
            // return response()->json(Reservation::all(), 200);
            $reservations = Reservation::leftJoin('users','reservations.user_id','=','users.id')
            ->leftJoin('branches','reservations.branch_id','=','branches.id')
            ->where(function($query) use($request){
                if($request->user_id != ""){
                    $query->where('reservations.user_id',$request->user_id);
                }
            })
            ->select('reservations.*','users.f_name','users.l_name','branches.name as branch_name')->get();
            return response()->json($reservations, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }
}
