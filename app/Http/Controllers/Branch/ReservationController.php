<?php

namespace App\Http\Controllers\Branch;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Reservation;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;


class ReservationController extends Controller
{
    public function list(Request $request){
        $search = $request['search'];
        $key = explode(' ', $request['search']);
        $reservations = Reservation::leftJoin('users','reservations.user_id','=','users.id')
        ->leftJoin('branches','reservations.branch_id','=','branches.id')
        ->when($search!=null, function($query) use($key){
                foreach ($key as $value) {
                    $query->where('branch.name', 'like', "%{$value}%")
                        ->orWhere('user.name', 'like', "%{$value}%");
                }
            })
        ->select('reservations.*','users.f_name','users.l_name','branches.name as branch_name')
        ->where('branch_id',auth('branch')->id())
        ->paginate(Helpers::getPagination());
        
        $reservationsPending = Reservation::leftJoin('users','reservations.user_id','=','users.id')
        ->leftJoin('branches','reservations.branch_id','=','branches.id')
        ->where('branch_id',auth('branch')->id())->where('reservations.order_status','Pending')->count();


        return view('branch-views.reservation.list', compact('reservations','search','reservationsPending'));
    }

    public function status(Request $request)
    {
        $reservation = Reservation::find($request->id);
        $reservation->status = $request->status;
        $reservation->save();

        Toastr::success(translate('Reservation status updated!'));
        return back();
    }
    
    
    public function orderStatus(Request $request)
    {
        $reservation = Reservation::find($request->id);
        $reservation->order_status = $request->order_status;
        $reservation->save();
        Toastr::success(translate('Order status updated to '.$request->order_status));
        return back();
    }
    
     public function excel_import()
    {
        $reservations = Reservation::leftJoin('users','reservations.user_id','=','users.id')
        ->leftJoin('branches','reservations.branch_id','=','branches.id')
        ->select('users.f_name','users.l_name','branches.name as branch_name','reservations.number_of_reservations','reservations.date','reservations.time')
        ->where('branch_id',auth('branch')->id())->get();
        return (new FastExcel($reservations))->download('reservations.xlsx');
    }
    
    
    
}
