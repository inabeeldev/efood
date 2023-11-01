<?php

namespace App\Http\Controllers\Branch;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\TimeSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TimeScheduleController extends Controller
{
    //time-schedule
    public function time_schedule_index()
    {
        $schedules = TimeSchedule::where('branch_id',auth('branch')->id())->get();
        return view('branch-views.business-settings.time-schedule-index', compact('schedules'));
    }

    public function add_schedule(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'start_time'=>'required|date_format:H:i',
            'end_time'=>'required|date_format:H:i|after:start_time',
        ],[
            'end_time.after'=>translate('End time must be after the start time')
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $temp = TimeSchedule::where('branch_id',auth('branch')->id())->where('day', $request->day)
            ->where(function($q)use($request){
                return $q->where(function($query)use($request){
                    return $query->where('opening_time', '<=' , $request->start_time)->where('closing_time', '>=', $request->start_time);
                })->orWhere(function($query)use($request){
                    return $query->where('opening_time', '<=' , $request->end_time)->where('closing_time', '>=', $request->end_time);
                });
            })
            ->first();

        if(isset($temp))
        {
            return response()->json(['errors' => [
                ['code'=>'time', 'message'=>translate('schedule_overlapping_warning')]
            ]]);
        }

        $time_schedule = TimeSchedule::insert(['day'=>$request->day,'opening_time'=>$request->start_time,'closing_time'=>$request->end_time,'branch_id' => auth('branch')->id()]);

        $schedules = TimeSchedule::where('branch_id',auth('branch')->id())->get();
        return response()->json([
            'view' => view('branch-views.business-settings.partials._schedule', compact('schedules'))->render(),
        ]);
    }

    public function remove_schedule(Request $request)
    {
        $schedule = TimeSchedule::find($request['schedule_id']);
        if(!$schedule)
        {
            return response()->json([],404);
        }
        $restaurant = $schedule->restaurant;
        $schedule->delete();

        $schedules = TimeSchedule::where('branch_id',auth('branch')->id())->get();
        return response()->json([
            'view' => view('branch-views.business-settings.partials._schedule', compact('schedules'))->render(),
        ]);
    }
}
