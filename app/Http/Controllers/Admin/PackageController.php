<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\AdminRole;
use App\Model\Plan;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function add_new()
    {
        $rls = AdminRole::whereNotIn('id', [1])->get();
        return view('admin-views.plan.add-new', compact('rls'));
    }

    public function list(Request $request){
        $search = $request['search'];
        $key = explode(' ', $request['search']);
        $packages = Plan::when($search!=null, function($query) use($key){
            foreach ($key as $value) {
                $query->where('title', 'like', "%{$value}%")
                    ->orWhere('decription', 'like', "%{$value}%");
            }
        })
        ->paginate(Helpers::getPagination());
        return view('admin-views.plan.list', compact('packages','search'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'title' => 'required',
            'price' => 'required',
            'plan_period'=>'required',
            'limit' => 'required',
            'order_limit' => 'required',
            'trial_period' => 'required',
            'description' => 'required',
        ]);

        Plan::insert([
            'title' => $request->title,
            'price' => $request->price,
            'package_type'=>$request->plan_period,
            'limit' => $request->limit,
            'order_limit' => $request->order_limit,
            'trial_period' => $request->trial_period,
            'description' => $request->description,
            'status' => $request->has('status') ? 1:0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Toastr::success(translate('Package added successfully!'));
        return redirect()->route('admin.package.list');
    }

    public function edit($id)
    {
        $package = Plan::where(['id' => $id])->first();
        return view('admin-views.plan.edit', compact('package'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'price' => 'required',
            'plan_period'=>'required',
            'limit' => 'required',
            'order_limit' => 'required',
            'trial_period' => 'required',
            'description' => 'required',
        ]);

        Plan::where(['id' => $id])->update([
            'title' => $request->title,
            'price' => $request->price,
            'package_type'=>$request->plan_period,
            'limit' => $request->limit,
            'order_limit' => $request->order_limit,
            'trial_period' => $request->trial_period,
            'description' => $request->description,
            'status' => $request->has('status') ? 1:0,
        ]);
        Toastr::success(translate('Package updated successfully!'));
        return back();
    }

    public function delete(Request $request)
    {
        $action = Plan::destroy($request->id);
        if ($action) {
            Toastr::success(translate('Package Deleted'));
        } else {
            Toastr::error(translate('Package Not Deleted'));
        }
        return back();
    }
}
