<?php

namespace App\Http\Controllers\Branch;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\DeliveryMan;
use App\Model\DMReview;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Rap2hpoutre\FastExcel\FastExcel;

class DeliveryManController extends Controller
{
    public function index()
    {
        return view('branch-views.delivery-man.index');
    }

    public function list(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $delivery_men = DeliveryMan::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }else{
            $delivery_men = new DeliveryMan();
        }

        $delivery_men = $delivery_men
            ->withCount('orders')
            ->where(function($q){
                $q->where('branch_id',auth('branch')->id())->orWhere('branch_id','');
            })
            ->latest()
            ->paginate(Helpers::getPagination())->appends($query_param);

        return view('branch-views.delivery-man.list', compact('delivery_men', 'search'));
    }

    public function search(Request $request)
    {
        $key = explode(' ', $request['search']);
        $delivery_men = DeliveryMan::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('f_name', 'like', "%{$value}%")
                    ->orWhere('l_name', 'like', "%{$value}%")
                    ->orWhere('email', 'like', "%{$value}%")
                    ->orWhere('phone', 'like', "%{$value}%")
                    ->orWhere('identity_number', 'like', "%{$value}%");
            }
        })->get();
        return response()->json([
            'view' => view('branch-views.delivery-man.partials._table', compact('delivery_men'))->render()
        ]);
    }

    public function reviews_list(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $delivery_men = DeliveryMan::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%");
                }
            })->pluck('id')->toArray();
            $reviews = DMReview::whereIn('delivery_man_id', $delivery_men);
            $query_param = ['search' => $request['search']];
        }else{
            $reviews = new DMReview();
        }

        $reviews = $reviews->with(['delivery_man', 'customer'])->latest()->paginate(Helpers::getPagination())->appends($query_param);
        return view('branch-views.delivery-man.reviews-list', compact('reviews', 'search'));
    }

    public function preview($id)
    {
        $dm = DeliveryMan::with(['reviews'])->where(['id' => $id])->first();
        $reviews = DMReview::where(['delivery_man_id' => $id])->latest()->paginate(Helpers::getPagination());
        return view('branch-views.delivery-man.view', compact('dm', 'reviews'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'f_name' => 'required',
            'email' => 'required|regex:/(.+)@(.+)\.(.+)/i|unique:delivery_men',
            'phone' => 'required|unique:delivery_men',
            'confirm_password' => 'same:password'
        ], [
            'f_name.required' => translate('First name is required!')
        ]);

        $id_img_names = [];
        if (!empty($request->file('identity_image'))) {
            foreach ($request->identity_image as $img) {
                array_push($id_img_names, Helpers::upload('delivery-man/', 'png', $img));
            }
            $identity_image = json_encode($id_img_names);
        } else {
            $identity_image = json_encode([]);
        }

        $dm = new DeliveryMan();
        $dm->f_name = $request->f_name;
        $dm->l_name = $request->l_name;
        $dm->email = $request->email;
        $dm->phone = $request->phone;
        $dm->identity_number = $request->identity_number;
        $dm->identity_type = $request->identity_type;
        $dm->branch_id = $request->branch_id;
        $dm->identity_image = $identity_image;
        $dm->image = Helpers::upload('delivery-man/', 'png', $request->file('image'));
        $dm->password = bcrypt($request->password);
        $dm->save();

        Toastr::success(translate('Delivery-man added successfully!'));
        return redirect('branch/delivery-man/list');
    }

    public function edit($id)
    {
        $delivery_man = DeliveryMan::find($id);
        return view('branch-views.delivery-man.edit', compact('delivery_man'));
    }

    public function ajax_is_active(Request $request)
    {
        $delivery_man = DeliveryMan::find($request->id);
        $delivery_man->is_active = $request->status;
        $delivery_man->save();
        return response()->json('status changed successfully');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'f_name' => 'required',
            'email' => 'required|regex:/(.+)@(.+)\.(.+)/i',
        ], [
            'f_name.required' => translate('First name is required!')
        ]);

        if ($request->password) {
            $request->validate([
                'confirm_password' => 'same:password'
            ]);
        }

        $delivery_man = DeliveryMan::find($id);

        if ($delivery_man['email'] != $request['email']) {
            $request->validate([
                'email' => 'required|unique:delivery_men',
            ]);
        }

        if ($delivery_man['phone'] != $request['phone']) {
            $request->validate([
                'phone' => 'required|unique:delivery_men',
            ]);
        }

        if (!empty($request->file('identity_image'))) {
            foreach (json_decode($delivery_man['identity_image'], true) as $img) {
                if (Storage::disk('public')->exists('delivery-man/' . $img)) {
                    Storage::disk('public')->delete('delivery-man/' . $img);
                }
            }
            $img_keeper = [];
            foreach ($request->identity_image as $img) {
                array_push($img_keeper, Helpers::upload('delivery-man/', 'png', $img));
            }
            $identity_image = json_encode($img_keeper);
        } else {
            $identity_image = $delivery_man['identity_image'];
        }
        $delivery_man->f_name = $request->f_name;
        $delivery_man->l_name = $request->l_name;
        $delivery_man->email = $request->email;
        $delivery_man->phone = $request->phone;
        $delivery_man->identity_number = $request->identity_number;
        $delivery_man->identity_type = $request->identity_type;
        $delivery_man->branch_id = $request->branch_id;
        $delivery_man->identity_image = $identity_image;
        $delivery_man->image = $request->has('image') ? Helpers::update('delivery-man/', $delivery_man->image, 'png', $request->file('image')) : $delivery_man->image;
        $delivery_man->password = strlen($request->password) > 1 ? bcrypt($request->password) : $delivery_man['password'];
        $delivery_man->save();

        Toastr::success(translate('Earrand Guy updated successfully!'));
        return redirect('branch/delivery-man/list');
    }

    public function delete(Request $request)
    {
        $delivery_man = DeliveryMan::find($request->id);
        if (Storage::disk('public')->exists('delivery-man/' . $delivery_man['image'])) {
            Storage::disk('public')->delete('delivery-man/' . $delivery_man['image']);
        }

        foreach (json_decode($delivery_man['identity_image'], true) as $img) {
            if (Storage::disk('public')->exists('delivery-man/' . $img)) {
                Storage::disk('public')->delete('delivery-man/' . $img);
            }
        }

        $delivery_man->delete();
        Toastr::success(translate('Earrand Guy removed!'));
        return back();
    }

    public function excel_export()
    {
        $dm = DeliveryMan::select('f_name as First Name', 'l_name as Last Name', 'phone as Phone', 'identity_type', 'identity_number')->get();
        return (new FastExcel($dm))->download('delivery_men.xlsx');

    }
}
