<?php

namespace App\Http\Controllers\Branch;

use DateTime;
use Carbon\Carbon;
use App\Model\Order;
use App\Model\Branch;
use App\Model\DeliveryMan;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Model\DeliveryRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Rap2hpoutre\FastExcel\FastExcel;
use function App\CentralLogics\translate;

class OrderController extends Controller
{
    public function list($status, Request $request)
    {

        $from = $request['from'];
        $to = $request['to'];

        Order::where(['checked' => 0, 'branch_id' => auth('branch')->id()])->update(['checked' => 1]);
        if ($status == 'all') {
            $orders = Order::with(['customer'])->where(['branch_id' => auth('branch')->id()]);
        } elseif ($status == 'schedule') {
            $orders = Order::whereDate('delivery_date','>', \Carbon\Carbon::now()->format('Y-m-d'))
                ->where(['branch_id' => auth('branch')->id()]);
        } else {
            $orders = Order::with(['customer'])
                ->where(['order_status' => $status, 'branch_id' => auth('branch')->id()])
                ->whereDate('delivery_date','<=',\Carbon\Carbon::now()->format('Y-m-d'));
        }

        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $orders = Order::where(['branch_id' => auth('branch')->id()])
                ->whereDate('delivery_date', '<=', \Carbon\Carbon::now()->format('Y-m-d'))
                ->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('id', 'like', "%{$value}%")
                            ->orWhere('order_status', 'like', "%{$value}%")
                            ->orWhere('transaction_reference', 'like', "%{$value}%");
                    }
                });
            $query_param = ['search' => $request['search']];
        }
        if ($from && $to) {
            $orders = Order::whereBetween('created_at', [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()]);
            $query_param = ['from' => $from, 'to' => $to];
        }

        $order_count = [
            'pending' =>    Order::notPos()->notSchedule()->where(['order_status'=>'pending','branch_id'=>auth('branch')->id()])
                ->when(!is_null($from) && !is_null($to), function ($query) use($from, $to) {
                    $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                })->count(),
            'confirmed' =>  Order::notPos()->notSchedule()->where(['order_status'=>'confirmed','branch_id'=>auth('branch')->id()])
                ->when(!is_null($from) && !is_null($to), function ($query) use($from, $to) {
                    $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                })->count(),
            'processing' => Order::notPos()->notSchedule()->where(['order_status'=>'processing','branch_id'=>auth('branch')->id()])
                ->when(!is_null($from) && !is_null($to), function ($query) use($from, $to) {
                    $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                })->count(),
            'out_for_delivery' => Order::notPos()->notSchedule()->where(['order_status'=>'out_for_delivery','branch_id'=>auth('branch')->id()])
                ->when(!is_null($from) && !is_null($to), function ($query) use($from, $to) {
                    $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                })->count(),
            'delivered' =>  Order::notPos()->notSchedule()->where(['order_status'=>'delivered','branch_id'=>auth('branch')->id()])
                ->when(!is_null($from) && !is_null($to), function ($query) use($from, $to) {
                    $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                })->count(),
            'canceled' =>   Order::notPos()->notSchedule()->where(['order_status'=>'canceled','branch_id'=>auth('branch')->id()])
                ->when(!is_null($from) && !is_null($to), function ($query) use($from, $to) {
                    $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                })->count(),
            'returned' =>   Order::notPos()->notSchedule()->where(['order_status'=>'returned','branch_id'=>auth('branch')->id()])
                ->when(!is_null($from) && !is_null($to), function ($query) use($from, $to) {
                    $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                })->count(),
            'failed' =>     Order::notPos()->notSchedule()->where(['order_status'=>'failed','branch_id'=>auth('branch')->id()])
                ->when(!is_null($from) && !is_null($to), function ($query) use($from, $to) {
                    $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                })->count(),
        ];

        $orders = $orders->notPos()->notDineIn()->latest()->paginate(Helpers::getPagination())->appends($query_param);
        session()->put('order_data_export', $orders);
        return view('branch-views.order.list', compact('orders', 'status', 'search', 'from', 'to', 'order_count'));
    }

    public function search(Request $request)
    {
        $key = explode(' ', $request['search']);
        $orders = Order::where(['branch_id' => auth('branch')->id()])->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('id', 'like', "%{$value}%")
                    ->orWhere('order_status', 'like', "%{$value}%")
                    ->orWhere('transaction_reference', 'like', "%{$value}%");
            }
        })->get();
        return response()->json([
            'view' => view('branch-views.order.partials._table', compact('orders'))->render()
        ]);
    }

    public function details($id)
    {
        $order = Order::with(['details', 'customer', 'delivery_address', 'branch', 'delivery_man'])
            ->where(['id' => $id, 'branch_id' => auth('branch')->id()])
            ->first();

        if(!isset($order)) {
            Toastr::info(translate('No more orders!'));
            return back();
        }

        //remaining delivery time
        $delivery_date_time =  $order['delivery_date']. ' ' .$order['delivery_time'];
        $ordered_time = Carbon::createFromFormat('Y-m-d H:i:s', date("Y-m-d H:i:s", strtotime($delivery_date_time)));
        $remaining_time = $ordered_time->add($order['preparation_time'], 'minute')->format('Y-m-d H:i:s');
        $order['remaining_time'] = $remaining_time;
        // dd($order);
        $nearestDeliveryMan = $this->findNearestAvailableDeliveryMen([$order->branch->latitude, $order->branch->longitude]);
        // dd($nearestDeliveryMan);

        return view('branch-views.order.order-view', compact('order','nearestDeliveryMan'));
    }

    protected function findNearestAvailableDeliveryMen($branchLocation, $numOfDeliveryMen = 15)
    {
        // Query the database to find the available delivery guys with FCM tokens.
        $availableDeliveryGuys = DB::table('delivery_men')
            ->select('*')
            ->where('is_available', true)
            ->get();

        $nearestDeliveryGuys = [];
        $distances = [];

        // Calculate distances and find the nearest delivery guys.
        foreach ($availableDeliveryGuys as $deliveryGuy) {
            $deliveryGuyLocation = [$deliveryGuy->latitude, $deliveryGuy->longitude];
            $distance = $this->calculateDistance($branchLocation, $deliveryGuyLocation);

            // Add the distance as a key-value pair to the $deliveryGuy object.
            $deliveryGuy->distance = $distance;

            // Add the delivery guy to the list.
            $nearestDeliveryGuys[] = $deliveryGuy;

            // Keep the list sorted by distance.
            usort($nearestDeliveryGuys, function ($a, $b) {
                return $a->distance <=> $b->distance;
            });

            // If the list exceeds the specified number of delivery men, remove the last one.
            if (count($nearestDeliveryGuys) > $numOfDeliveryMen) {
                array_pop($nearestDeliveryGuys);
            }
        }

        return $nearestDeliveryGuys;
    }


    protected function calculateDistance($location1, $location2)
    {
        // Calculate the distance between two locations using the Haversine formula.
        $lat1 = deg2rad($location1[0]);
        $lon1 = deg2rad($location1[1]);
        $lat2 = deg2rad($location2[0]);
        $lon2 = deg2rad($location2[1]);

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat / 2)**2 + cos($lat1) * cos($lat2) * sin($dlon / 2)**2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // Radius of the Earth in kilometers
        $earthRadius = 6371;

        // Calculate the distance
        $distance = $earthRadius * $c;

        return $distance;
    }


    public function status(Request $request)
    {
        $order = Order::where(['id' => $request->id, 'branch_id' => auth('branch')->id()])->first();
        if (($request->order_status == 'delivered' || $request->order_status == 'out_for_delivery') && $order['delivery_man_id'] == null && $order['order_type'] != 'take_away') {
            Toastr::warning(translate('Please assign delivery man first!'));
            return back();
        }
        $order->order_status = $request->order_status;
        if($request->order_status == 'delivered') {
            $order->payment_status = 'paid';
        }
        $order->save();
        if($request->order_status == 'delivered' && $order->payment_status == 'paid') {
            $branch = Branch::find($order->branch_id);
            $nature_fee = Helpers::get_business_settings('nature_fee');
            if ($branch) {
                $amountToSubtract = ($order->order_amount/100) * $nature_fee;
                $remainingAmount = $order->order_amount - $amountToSubtract;
                $currentWalletAmount = $branch->wallet_amount;
                $newWalletAmount = $currentWalletAmount + $remainingAmount;

                $branch->update([
                    'wallet_amount' => $newWalletAmount
                ]);
            }
            $delivery_man = DeliveryMan::find($order->delivery_man_id);
            if ($delivery_man) {
                $currentWalletAmount = $delivery_man->wallet_amount;
                $newWalletAmount = $currentWalletAmount + $order->delivery_charge;

                $delivery_man->update([
                    'wallet_amount' => $newWalletAmount
                ]);
            }

        }
        $fcm_token=null;
        if($order->customer) {
            $fcm_token = $order->customer->cm_firebase_token;
        }
        $value = Helpers::order_status_update_message($request->order_status);
        try {
            if ($value) {
                $data = [
                    'title' => translate('Order'),
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                    'type'=>'order_status',
                ];
                if(isset($fcm_token)) {
                    Helpers::send_push_notif_to_device($fcm_token, $data);
                }
            }
        } catch (\Exception $e) {
            Toastr::warning(translate('Push notification failed for Customer!'));
        }

        //delivery man notification
        if ($request->ordeurrent_pager_status == 'processing' && $order->delivery_man != null) {
            $fcm_token = $order->delivery_man->fcm_token;
            $value = translate('One of your order is in processing');
            try {
                if ($value) {
                    $data = [
                        'title' => translate('Order'),
                        'description' => $value,
                        'order_id' => $order['id'],
                        'image' => '',
                        'type'=>'order_status',
                    ];
                    Helpers::send_push_notif_to_device($fcm_token, $data);
                }
            } catch (\Exception $e) {
                Toastr::warning(translate('Push notification failed for DeliveryMan!'));
            }
        }

        Toastr::success(translate('Order status updated!'));
        return back();
    }

    public function preparation_time(Request $request, $id)
    {
        $order = Order::with(['customer'])->find($id);
        $delivery_date_time =  $order['delivery_date']. ' ' .$order['delivery_time'];

        $ordered_time = Carbon::createFromFormat('Y-m-d H:i:s', date("Y-m-d H:i:s", strtotime($delivery_date_time)));
        $remaining_time = $ordered_time->add($order['preparation_time'], 'minute')->format('Y-m-d H:i:s');

        //if delivery time is not over
        if (strtotime(date('Y-m-d H:i:s')) < strtotime($remaining_time)) {
            $delivery_time = new DateTime($remaining_time); //time when preparation will be over
            $current_time = new DateTime(); // time now
            $interval = $delivery_time->diff($current_time);
            $remainingMinutes = $interval->i;
            $remainingMinutes += $interval->days * 24 * 60;
            $remainingMinutes += $interval->h * 60;

            $order->preparation_time += ($request->extra_minute - $remainingMinutes);

        } else {
            //if delivery time is over
            $delivery_time = new DateTime($remaining_time);
            $current_time = new DateTime();
            $interval = $delivery_time->diff($current_time);
            $diffInMinutes = $interval->i;
            $diffInMinutes += $interval->days * 24 * 60;
            $diffInMinutes += $interval->h * 60;

            $order->preparation_time += $diffInMinutes + $request->extra_minute;
        }
        $order->save();

        //notification send
        $customer = $order->customer;
        $fcm_token = null;
        if (isset($customer)) {
            $fcm_token = $customer->cm_firebase_token;
        }
        $value = Helpers::order_status_update_message('customer_notify_message_for_time_change');

        try {
            if ($value) {
                $data = [
                    'title' => translate('Order'),
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                    'type'=>'order_status',
                ];
                Helpers::send_push_notif_to_device($fcm_token, $data);
            } else {
                throw new \Exception(translate('failed'));
            }

        } catch (\Exception $e) {
            Toastr::warning(translate('Push notification send failed for Customer!'));
        }

        Toastr::success(translate('Order preparation time increased'));
        return back();
    }

    public function add_delivery_man($order_id, $delivery_man_id)
    {
        if ($delivery_man_id == 0) {
            return response()->json([], 401);
        }
        $order = Order::where(['id' => $order_id, 'branch_id' => auth('branch')->id()])->first();
        if($order->order_status == 'delivered' || $order->order_status == 'returned' || $order->order_status == 'failed' || $order->order_status == 'canceled' || $order->order_status == 'scheduled') {
            return response()->json(['status' => false], 200);
        }
        // Create a delivery request with 'pending' status
        $deliveryRequest = new DeliveryRequest([
            'order_id' => $order_id,
            'deliveryman_id' => $delivery_man_id,
            'status' => 'pending',
        ]);
        $deliveryRequest->save();

        $maxWaitTime = 15; // Maximum wait time in seconds
        $startTime = time(); // Record the start time

        while (true) {
            // Check if the maximum wait time has been reached
            if (time() - $startTime >= $maxWaitTime) {
                // Handle the case where the delivery man didn't respond within the allowed time
                return response()->json(['status' => false], 200);
            }

            // Check the status of the delivery request
            $deliveryRequest = DeliveryRequest::where('order_id', $order_id)
                ->where('deliveryman_id', $delivery_man_id)
                ->first();

            if ($deliveryRequest && $deliveryRequest->status === 'accepted') {
                // The delivery man accepted the request, proceed with assignment
                $order->delivery_man_id = $delivery_man_id;
                $order->save();

                // Send notifications and other processing here
                $fcm_token = $order->delivery_man->fcm_token;
            $customer_fcm_token = null;
                if(isset($order->customer)) {
                    $customer_fcm_token = $order->customer->cm_firebase_token;
                }
                $value = Helpers::order_status_update_message('del_assign');
                try {
                    if ($value) {
                        $data = [
                            'title' => translate('Order'),
                            'description' => $value,
                            'order_id' => $order_id,
                            'image' => '',
                            'type'=>'order_status',
                        ];
                        Helpers::send_push_notif_to_device($fcm_token, $data);
                        if(isset($order->customer)) {
                            $data['description'] = Helpers::order_status_update_message('customer_notify_message');
                        }
                        if(isset($customer_fcm_token)) {
                            Helpers::send_push_notif_to_device($customer_fcm_token, $data);
                        }
                    }
                } catch (\Exception $e) {
                    Toastr::warning(translate('Push notification failed for DeliveryMan!'));
                }

                return response()->json(['status' => true], 200);
            } elseif ($deliveryRequest && $deliveryRequest->status === 'rejected') {
                // The delivery man rejected the request
                // Handle the case where the delivery man rejects the request
                return response()->json(['status' => false], 200);
            }

            // Sleep for a short interval before checking again
            sleep(2); // Adjust the sleep time as needed
        }
    }


    public function payment_status(Request $request)
    {
        $order = Order::where(['id' => $request->id, 'branch_id' => auth('branch')->id()])->first();
        if ($request->payment_status == 'paid' && $order['transaction_reference'] == null && $order['payment_method'] != 'cash_on_delivery' && $order['order_type'] != 'dine_in') {
            Toastr::warning(translate('Add your payment reference code first!'));
            return back();
        }
        $order->payment_status = $request->payment_status;
        $order->save();
        Toastr::success(translate('Payment status updated!'));
        return back();
    }

    public function update_shipping(Request $request, $id)
    {
        $request->validate([
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'contact_person_number' => 'required',
            'address' => 'required'
        ]);

        $address = [
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address_type' => $request->address_type,
            'floor' => $request->floor,
            'house' => $request->house,
            'road' => $request->road,
            'address' => $request->address,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'created_at' => now(),
            'updated_at' => now()
        ];

        DB::table('customer_addresses')->where('id', $id)->update($address);
        Toastr::success(translate('Address updated!'));
        return back();
    }

    public function generate_invoice($id)
    {
        $order = Order::where(['id' => $id, 'branch_id' => auth('branch')->id()])->first();
        return view('branch-views.order.invoice', compact('order'));
    }

    public function add_payment_ref_code(Request $request, $id)
    {
        Order::where(['id' => $id, 'branch_id' => auth('branch')->id()])->update([
            'transaction_reference' => $request['transaction_reference']
        ]);

        Toastr::success(translate('Payment reference code is added!'));
        return back();
    }

    public function export_excel()
    {
        $data = [];
        $orders = session('order_data_export');
        foreach ($orders as $key => $order) {
            $data[$key]['SL'] = ++$key;
            $data[$key]['Order ID'] = $order->id;
            $data[$key]['Order Date'] = date('d M Y h:m A',strtotime($order['created_at']));
            $data[$key]['Customer Info'] = $order['user_id'] == null? 'Walk in Customer' : ($order->customer == null? 'Customer Unavailable' : $order->customer['f_name']. ' '. $order->customer['l_name']);
            $data[$key]['Branch'] = $order->branch? $order->branch->name : 'Branch Deleted';
            $data[$key]['Total Amount'] = Helpers::set_symbol($order['order_amount']);
            $data[$key]['Payment Status'] = $order->payment_status=='paid'? 'Paid' : 'Unpaid';
            $data[$key]['Order Status'] = $order['order_status']=='pending'? 'Pending' : ($order['order_status']=='confirmed'? 'Confirmed' : ($order['order_status']=='processing' ? 'Processing' : ($order['order_status']=='delivered'? 'Delivered': ($order['order_status']=='picked_up'? 'Out For Delivery' : str_replace('_',' ',$order['order_status'])))));
        };
        return (new FastExcel($data))->download('orders.xlsx');
    }
    public function ajax_change_delivery_time_date(Request $request)
    {
        $order = Order::where('id', $request->order_id)->first();
        if(!$order) {
            return response()->json(['status' => false]);
        }
        $order->delivery_date = $request->input('delivery_date')?? $order->delivery_date;
        $order->delivery_time = $request->input('delivery_time')?? $order->delivery_time;
        $order->save();

        return response()->json(['status' => true]);

    }
}
