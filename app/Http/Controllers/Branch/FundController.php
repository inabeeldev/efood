<?php

namespace App\Http\Controllers\Branch;

use Carbon\Carbon;
use App\Model\Order;
use App\Model\Branch;
use App\Model\OrderDetail;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Controllers\Controller;
use App\Model\BranchWithdrawRequest;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;
use App\CentralLogics\Helpers as AppHelpers;

class FundController extends Controller
{
    public function fundReceived()
    {
        $bwr = BranchWithdrawRequest::where('branch_id', auth('branch')->id())
        ->paginate(AppHelpers::getPagination());;
        return view('branch-views.fund.sale-report',compact('bwr'));
    }

    public function sale_filter(Request $request)
    {
        $fromDate = Carbon::parse($request->from)->startOfDay();
        $toDate = Carbon::parse($request->to)->endOfDay();

        if ($request['branch_id'] == 'all') {
            $orders = Order::whereBetween('created_at', [$fromDate, $toDate])->pluck('id')->toArray();
        } else {
            $orders = Order::where(['branch_id' => $request['branch_id']])
                ->whereBetween('created_at', [$fromDate, $toDate])->pluck('id')->toArray();
        }

        $data = [];
        $total_sold = 0;
        $total_qty = 0;

        foreach (OrderDetail::whereIn('order_id', $orders)->latest()->get() as $detail) {
            $price = $detail['price'] - $detail['discount_on_product'];
            $ord_total = $price * $detail['quantity'];
            array_push($data, [
                'order_id' => $detail['order_id'],
                'date' => $detail['created_at'],
                'price' => $ord_total,
                'quantity' => $detail['quantity'],
            ]);
            $total_sold += $ord_total;
            $total_qty += $detail['quantity'];
        }

        return response()->json([
            'order_count' => count($data),
            'item_qty' => $total_qty,
            'order_sum' => Helpers::set_symbol($total_sold),
            'view' => view('branch-views.fund.partials._table', compact('data'))->render(),
        ]);
    }

    public function export_sale_report()
    {
        $data = session('export_sale_data');
        $pdf = PDF::loadView('branch-views.report.partials._report', compact('data'));
        return $pdf->download('sale_report_'.rand(00001,99999) . '.pdf');
    }

    public function withdrawFund()
    {
        return view('branch-views.fund.withdraw-fund');
    }

    public function requestWithdraw(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_name' => 'required',
            'routing_number' => 'required',
            'account_title' => 'required',
            'account_no' => 'required',
            'amount' => 'required',
            'branch_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $input = $request->all();
        $branch = Branch::find($request->branch_id);

        // Check if the wallet amount is less than the requested amount
        if ($branch->wallet_amount < $request->amount) {
            Toastr::error(translate('Insufficient balance in your wallet.'));
            return redirect()->back();
        }

        // Update the wallet amount and create withdrawal request
        $branch->update([
            'wallet_amount' => $branch->wallet_amount - $request->amount
        ]);

        BranchWithdrawRequest::create($input);

        Toastr::info(translate('We have received your request. You will soon get your amount!'));
        return redirect()->back();
    }

}
