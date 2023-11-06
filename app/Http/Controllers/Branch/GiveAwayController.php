<?php

namespace App\Http\Controllers\Branch;

use App\Model\GiveAway;
use App\Model\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;
use App\CentralLogics\Helpers as AppHelpers;

class GiveAwayController extends Controller
{
    public function list()
    {
        $products = GiveAway::where('branch_id', auth('branch')->id())
        ->with('product')
        ->paginate(AppHelpers::getPagination());
        // dd($products);
        return view('branch-views.give_away.list', compact('products'));
    }

    public function addNew()
    {
        $products = Product::where('branch_id', auth('branch')->id())->get();
        return view('branch-views.give_away.add-new',compact('products'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|unique:give_aways,product_id',
            'status' => 'required'
        ], [
            'product_id.required' => translate('Product name is required!'),
            'product_id.unique' => translate('This Give-away already exists!'),
            'status.required' => translate('Status is required!')
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $giveway = new GiveAway;

        $giveway->product_id = $request->product_id;
        $giveway->status = $request->status;
        $giveway->branch_id = auth('branch')->id();
        $giveway->save();
        Toastr::success(translate('Give-Away Product Added!'));
        return redirect()->route('branch.give-away.list');

    }

    public function status(Request $request)
    {
        $giveway = GiveAway::find($request->id);
        $giveway->status = $request->status;
        $giveway->save();
        Toastr::success(translate('Give-Away Product status updated!'));
        return back();
    }


    public function delete(Request $request)
    {
        $giveway = GiveAway::find($request->id);
        $giveway->delete();
        Toastr::success(translate('Give-Away Product removed!'));
        return back();
    }
}
