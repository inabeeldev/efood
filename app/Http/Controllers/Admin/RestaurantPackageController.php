<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\AdminRole;
use App\Model\RestaurantPackage;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class RestaurantPackageController extends Controller
{
    public function add_new()
    {
        $rls = AdminRole::whereNotIn('id', [1])->get();
        return view('admin-views.branch-plan.add-new', compact('rls'));
    }

    public function list(Request $request){
        $search = $request['search'];
        $key = explode(' ', $request['search']);
        $packages = RestaurantPackage::when($search!=null, function($query) use($key){
            foreach ($key as $value) {
                $query->where('name', 'like', "%{$value}%")
                    ->orWhere('decription', 'like', "%{$value}%");
            }
        })
        ->paginate(Helpers::getPagination());
        return view('admin-views.branch-plan.list', compact('packages','search'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'title' => 'required',
            'price' => 'required',
            'description' => 'required',
        ]);

        RestaurantPackage::insert([
            'name' => $request->title,
            'price' => $request->price,
            'description' => $request->description,
            'status' => $request->has('status') ? 1:0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Toastr::success(translate('Package added successfully!'));
        return redirect()->route('admin.rpackage.list');
    }

    public function edit($id)
    {
        $package = RestaurantPackage::where(['id' => $id])->first();
        return view('admin-views.branch-plan.edit', compact('package'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'price' => 'required',
            'description' => 'required',
        ]);

        RestaurantPackage::where(['id' => $id])->update([
            'name' => $request->title,
            'price' => $request->price,
            'description' => $request->description,
            'status' => $request->has('status') ? 1:0,
        ]);
        Toastr::success(translate('Package updated successfully!'));
        return back();
    }

    public function delete(Request $request)
    {
        $action = RestaurantPackage::destroy($request->id);
        if ($action) {
            Toastr::success(translate('Package Deleted'));
        } else {
            Toastr::error(translate('Package Not Deleted'));
        }
        return back();
    }
}
