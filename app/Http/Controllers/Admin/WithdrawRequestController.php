<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\BranchWithdrawRequest;
use Brian2694\Toastr\Facades\Toastr;
use App\Model\DeliveryManWithdrawRequest;
use App\CentralLogics\Helpers as AppHelpers;

class WithdrawRequestController extends Controller
{
    public function branchWithdraw()
    {
        $bwr = BranchWithdrawRequest::with('branch')->paginate(AppHelpers::getPagination());
        // dd($bwr);
        return view('admin-views.withdraw_request.branch',compact('bwr'));
    }

    public function deliveryManWithdraw()
    {
        $dwr = DeliveryManWithdrawRequest::with('deliveryMan')->paginate(AppHelpers::getPagination());;
        return view('admin-views.withdraw_request.delivery_man',compact('dwr'));
    }

    public function branchPaymentStatus(Request $request)
    {
        // dd($request->all());
        $withdrawRequest = BranchWithdrawRequest::find($request->id);

        $withdrawRequest->status = $request->status;
        $withdrawRequest->save();
        Toastr::success(translate('Payment status updated!'));
        return back();
    }

    public function deliveryPaymentStatus(Request $request)
    {
        // dd($request->all());
        $withdrawRequest = DeliveryManWithdrawRequest::find($request->id);

        $withdrawRequest->status = $request->status;
        $withdrawRequest->save();
        Toastr::success(translate('Payment status updated!'));
        return back();
    }

}
