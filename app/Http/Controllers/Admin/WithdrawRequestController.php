<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\DeliveryManWithdrawRequest;
use App\CentralLogics\Helpers as AppHelpers;
use App\Model\BranchWithdrawRequest;
use Illuminate\Http\Request;

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
}
