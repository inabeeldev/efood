<?php

namespace App\Http\Controllers\Branch;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Coupon;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    public function add_new(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $coupons = Coupon::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('title', 'like', "%{$value}%")
                        ->orWhere('code', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }else{
            $coupons = new Coupon();
        }

        $coupons = $coupons->where('branch_id',auth('branch')->id())->latest()->paginate(Helpers::getPagination())->appends($query_param);
        return view('branch-views.coupon.index', compact('coupons', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'title' => 'required|max:255',
            'start_date' => 'required',
            'expire_date' => 'required',
            'discount' => 'required|max:9',
            'min_purchase' => 'max:9',
            'max_discount' => 'max:9',
        ], [
            'title.max' => translate('Title is too long!'),
        ]);

        if ($request->discount_type == 'percent' && (int)$request->discount > 100) {
            Toastr::error(translate('discount_can_not_be_more_than_100%'));
            return back();
        }

        DB::table('coupons')->insert([
            'title' => $request->title,
            'code' => $request->code,
            'limit' => $request->limit,
            'coupon_type' => $request->coupon_type,
            'start_date' => $request->start_date,
            'expire_date' => $request->expire_date,
            'min_purchase' => $request->min_purchase != null ? $request->min_purchase : 0,
            'max_discount' => $request->max_discount != null ? $request->max_discount : 0,
            'discount' => $request->discount,
            'discount_type' => $request->discount_type,
            'status' => 1,
            'branch_id' => auth('branch')->id(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        Toastr::success(translate('Coupon added successfully!'));
        return back();
    }

    public function edit($id)
    {
        $coupon = Coupon::where(['id' => $id])->first();
        return view('branch-views.coupon.edit', compact('coupon'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required',
            'title' => 'required|max:255',
            'start_date' => 'required',
            'expire_date' => 'required',
            'discount' => 'required|max:9',
            'min_purchase' => 'max:9',
            'max_discount' => 'max:9',
        ], [
            'title.max' => translate('Title is too long!'),
        ]);

        DB::table('coupons')->where(['id' => $id])->update([
            'title' => $request->title,
            'code' => $request->code,
            'limit' => $request->limit,
            'coupon_type' => $request->coupon_type,
            'start_date' => $request->start_date,
            'expire_date' => $request->expire_date,
            'min_purchase' => $request->min_purchase != null ? $request->min_purchase : 0,
            'max_discount' => $request->max_discount != null ? $request->max_discount : 0,
            'discount' => $request->discount_type == 'amount' ? $request->discount : $request['discount'],
            'discount_type' => $request->discount_type,
            'updated_at' => now()
        ]);

        Toastr::success(translate('Coupon updated successfully!'));
        return back();
    }

    public function status(Request $request)
    {
        $coupon = Coupon::find($request->id);
        $coupon->status = $request->status;
        $coupon->save();
        Toastr::success(translate('Coupon status updated!'));
        return back();
    }

    public function delete(Request $request)
    {
        $coupon = Coupon::find($request->id);
        $coupon->delete();
        Toastr::success(translate('Coupon removed!'));
        return back();
    }

    public function generate_coupon_code() {
        return response()->json(Str::random(10)) ;
    }

    public function coupon_details_modal(Request $request)
    {
        $coupon = Coupon::findOrFail($request->id);
        return response()->json([
            'success' => 1,
            'view' => view('branch-views.coupon.partials._coupon-view', compact('coupon'))->render(),
        ]);
    }

}
