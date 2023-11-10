<?php

namespace App\Http\Controllers\Branch;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Branch;
use App\Model\BranchWithdrawRequest;
use App\Model\Order;
use App\Model\OrderDetail;
use Barryvdh\DomPDF\Facade as PDF;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use App\CentralLogics\Helpers as AppHelpers;
use Illuminate\Http\Request;

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
        $input = $request->all();
        // dd($input);
        BranchWithdrawRequest::create($input);
        $branch = Branch::find($request->branch_id);
        $branch->update([
            'wallet_amount' => $branch->wallet_amount - $request->amount
        ]);
        Toastr::info(translate('We have received your request. You will soon get your amount!'));
        return redirect()->back();
    }
}
