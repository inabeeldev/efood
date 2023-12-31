<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Model\Order;
use App\Model\Newsletter;
use App\Model\Conversation;
use App\Model\Notification;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Model\PointTransitions;
use App\Model\CustomerIncentive;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{

    public function add_point(Request $request, $id)
    {
        User::where(['id' => $id])->increment('point', $request['point']);
        DB::table('point_transitions')->insert([
            'user_id' => $id,
            'description' => 'admin added this point',
            'type' => 'point_in',
            'amount' => $request['point'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        if ($request->ajax()) {
            return response()->json([
                'updated_point' => User::where(['id' => $id])->first()->point
            ]);
        }
    }

    public function set_point_modal_data($id)
    {
        $customer = User::find($id);
        return response()->json([
            'view' => view('admin-views.customer.partials._add-point-modal-content', compact('customer'))->render()
        ]);
    }

    public function customer_list(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $customers = User::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('email', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $customers = new User();
        }

        $customers = $customers->with(['orders'])->where('user_type', null)->latest()->paginate(Helpers::getPagination())->appends($query_param);
        return view('admin-views.customer.list', compact('customers', 'search'));
    }

    public function search(Request $request)
    {
        $key = explode(' ', $request['search']);
        $customers = User::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('f_name', 'like', "%{$value}%")
                    ->orWhere('l_name', 'like', "%{$value}%")
                    ->orWhere('email', 'like', "%{$value}%")
                    ->orWhere('phone', 'like', "%{$value}%");
            }
        })->get();
        return response()->json([
            'view' => view('admin-views.customer.partials._table', compact('customers'))->render(),
        ]);
    }

    public function view($id, Request $request)
    {
        $search = $request->search;
        $customer = User::find($id);
        if (isset($customer)) {
            $orders = Order::latest()->where(['user_id' => $id])
                ->when($search, function ($query) use ($search) {
                    $key = explode(' ', $search);
                    foreach($key as $value) {
                        $query->where('id', 'like', "%$search%");
                    }
                })
                ->paginate(Helpers::getPagination())
                ->appends(['search' => $search]);
            return view('admin-views.customer.customer-view', compact('customer', 'orders', 'search'));
        }
        Toastr::error(translate('Customer not found!'));
        return back();
    }

    public function AddPoint(Request $request, $id)
    {
        $point = User::where(['id' => $id])->first()->point;

        $requestPoint = $request['point'];
        $point += $requestPoint;
        // dd($point);
        User::where(['id' => $id])->update([
            'point' => $point,
        ]);
        $p_trans = [
            'user_id' => $request['id'],
            'description' => 'admin Added point',
            'type' => 'point_in',
            'amount' => $request['point'],
            'created_at' => now(),
            'updated_at' => now(),

        ];
        DB::table('point_transitions')->insert($p_trans);

        Toastr::success(translate('Point Added Successfully !'));
        return back();

    }

    public function transaction(Request $request)
    {
        $query_param = ['search' => $request['search']];
        $search = $request['search'];
        $transition = PointTransitions::with(['customer'])->latest()
            ->when($request->has('search'), function ($q) use($search){
                $q->whereHas('customer', function ($query) use($search){
                    $key = explode(' ', $search);
                    foreach ($key as $value) {
                        $query->where('f_name', 'like', "%{$value}%")
                            ->orWhere('l_name', 'like', "%{$value}%");
                    }
                });
            })
            ->paginate(Helpers::getPagination())
            ->appends($query_param);

        return view('admin-views.customer.transaction-table', compact('transition', 'search'));
    }

    public function subscribed_emails(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $newsletters = Newsletter::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('email', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $newsletters = new Newsletter();
        }

        $newsletters = $newsletters->latest()->paginate(Helpers::getPagination())->appends($query_param);
        return view('admin-views.customer.subscribed-list', compact('newsletters', 'search'));
    }

    public function customer_transaction($id)
    {
        $search = '';
        $transition = PointTransitions::with(['customer'])->where(['user_id' => $id])->latest()->paginate(Helpers::getPagination());
        return view('admin-views.customer.transaction-table', compact('transition','search'));
    }

    public function get_user_info(Request $request)
    {
        $user = User::find($request['id']);
        $unchecked = Conversation::where(['user_id'=>$request['id'],'checked'=>0])->count();

        $output = [
            'id' => $user->id??'',
            'f_name' => $user->f_name??'',
            'l_name' => $user->l_name??'',
            'email' => $user->email??'',
            'image' => ($user && $user->image)? asset('storage/app/public/profile') . '/' . $user->image : asset('/public/assets/admin/img/160x160/img1.jpg'),
            'cm_firebase_token' => $user->cm_firebase_token??'',
            'unchecked' => $unchecked ?? 0

        ];

        $result=get_headers($output['image']);
        if(!stripos($result[0], "200 OK")) {
            $output['image'] = asset('/public/assets/admin/img/160x160/img1.jpg');
        }

        return response()->json($output);
    }

    public function message_notification(Request $request)
    {
        $user = User::find($request['id']);
        $fcm_token = $user->cm_firebase_token;

        $data = [
            'title' => 'New Message' . ($request->has('image_length') && $request->image_length > 0 ? (' (with ' . $request->image_length . ' attachment)') : ''),
            'description' => $request->message,
            'order_id' => '',
            'image' => $request->has('image_length') ? $request->image_length : null,
            'type'=>'order_status',
        ];

        try {
            Helpers::send_push_notif_to_device($fcm_token, $data);
            return $data;
        } catch (\Exception $exception) {
            return false;
        }

    }

    public function chat_image_upload(Request $request)
    {
        $id_img_names = [];
        if (!empty($request->file('images'))) {
            foreach ($request->images as $img) {
                $image = Helpers::upload('conversation/', 'png', $img);
                $image_url = asset('storage/app/public/conversation') . '/' . $image;
                array_push($id_img_names, $image_url);
            }
            $images = $id_img_names;
        } else {
            $images = null;
        }
        return response()->json(['image_urls' => $images], 200);
    }
    public function update_status(Request $request, $id)
    {
        $user = User::findOrFail($id)->update(['is_active'=> $request['status']]);
        return response()->json($request['status']);
    }
    public function destroy(Request $request)
    {
        try {
            $user = User::findOrFail($request['id'])->delete();
            Toastr::success(translate('user_deleted_successfully!'));
        }
        catch (\Exception $e) {
            Toastr::error(translate('user_not_found!'));
        }
        return back();
    }

    public function excel_import()
    {
        $users = User::select('f_name as First Name',
            'l_name as Last Name', 'email as Email', 'is_active as Active', 'phone as Phone', 'point as Point')->get();
        return (new FastExcel($users))->download('customers.xlsx');
    }

    public function topCustomer(Request $request)
    {

        $notifications = Notification::all();
        $customers = User::withCount('orders')->orderBy('orders_count', 'desc')->take(5)->get();
        // dd($customers);
        return view('admin-views.customer.top-customer', compact('customers','notifications'));
    }

    public function customerIncentive()
    {
        $customers = User::withCount('orders')->orderBy('orders_count', 'desc')->take(5)->get();
        $notifications = Notification::where('for_customer', 1)->get();

        $customerIncentives = CustomerIncentive::with('user', 'notification')->latest()->paginate(Helpers::getPagination());

        //  dd($customerIncentives);
        return view('admin-views.customer.incentive' , compact('customers','notifications','customerIncentives'));
    }

    public function storeIncentive(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|unique:customer_incentives,user_id',
            'notification_id' => 'required',
            'status' => 'required'
        ], [
            'user_id.required' => translate('Please select User!'),
            'notification_id.required' => translate('Please select Notification'),
            'user_id.unique' => translate('This user already have incentive'),
            'status.required' => translate('Status is required!')
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $incentive = new CustomerIncentive;

        $incentive->user_id = $request->user_id;
        $incentive->notification_id = $request->notification_id;
        $incentive->status = $request->status;
        $incentive->save();
        Toastr::success(translate('Incentive for Customer Added!'));
        return redirect()->route('admin.customer.customer-incentives');

    }

    public function incentiveUpdate(Request $request)
    {
        $incentive = CustomerIncentive::find($request->id);
        $incentive->status = $request->status;
        $incentive->save();
        Toastr::success(translate('Customer Incentive status updated!'));
        return back();
    }


    public function incentiveDelete(Request $request)
    {
        $incentive = CustomerIncentive::find($request->id);
        $incentive->delete();
        Toastr::success(translate('Customer Incentive removed!'));
        return back();
    }




}
