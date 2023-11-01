<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\AdminRole;
use App\Model\Membership;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Symfony\Component\Console\Helper\Helper;

class MembershipController extends Controller
{

    public function add_new()
    {
        $rls = AdminRole::whereNotIn('id', [1])->get();
        return view('admin-views.membership.add-new', compact('rls'));
    }

    public function list(Request $request){
        $search = $request['search'];
        $key = explode(' ', $request['search']);
        $members = Membership::join('users','memberships.user_id','=','users.id')
        ->join('plans','memberships.plan_id','=','plans.id')
        ->when($search!=null, function($query) use($key){
            foreach ($key as $value) {
                $query->where('users.l_name', 'like', "%{$value}%")
                    ->orWhere('plans.title', 'like', "%{$value}%")
                    ->orWhere('users.f_name', 'like', "%{$value}%");
            }
        })
        ->select('memberships.*','users.f_name','users.l_name','users.email','users.phone','plans.title as package_name')
        ->paginate(Helpers::getPagination());
        return view('admin-views.membership.list', compact('members','search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'persons' => 'required',
            'email' => 'required',
            'phone'=>'required',
            'date' => 'required'
        ], [
            'name.required' => translate('Member name is required!'),
            'persons.required' => translate('Persons is Required'),
            'email.required' => translate('Email id is Required'),
            'date.required' => translate('Date is Required'),

        ]);

        Membership::insert([
            'name' => $request->name,
            'phone_number' => $request->phone,
            'email' => $request->email,
            'persons' => $request->persons,
            'date' => $request->date,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Toastr::success(translate('Member added successfully!'));
        return redirect()->route('admin.membership.list');
    }

    public function edit($id)
    {
        $m = Membership::where(['id' => $id])->first();
        return view('admin-views.membership.edit', compact('m'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'persons' => 'required',
            'email' => 'required',
            'phone'=>'required',
            'date' => 'required'
        ], [
            'name.required' => translate('Member name is required!'),
            'persons.required' => translate('Persons is Required'),
            'email.required' => translate('Email id is Required'),
            'date.required' => translate('Date is Required'),

        ]);
        Membership::where(['id' => $id])->update([
            'name' => $request->name,
            'phone_number' => $request->phone,
            'email' => $request->email,
            'persons' => $request->persons,
            'date' => $request->date,
        ]);
        Toastr::success(translate('Member updated successfully!'));
        return back();
    }

    public function delete(Request $request)
    {
        $action = Membership::destroy($request->id);
        if ($action) {
            Toastr::success(translate('Mmbership Deleted'));
        } else {
            Toastr::error(translate('Membership Not Deleted'));
        }
        return back();
    }

}
