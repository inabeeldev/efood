<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Reservation;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

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
            ->orderBy('id','DESC')
        ->select('reservations.*','users.f_name','users.l_name','branches.name as branch_name')
        ->paginate(Helpers::getPagination());
        return view('admin-views.reservation.list', compact('reservations','search'));
    }

    public function status(Request $request)
    {
        $reservation = Reservation::find($request->id);
        $reservation->status = $request->status;
        $reservation->save();

        Toastr::success(translate('Reservation status updated!'));
        return back();
    }
}
