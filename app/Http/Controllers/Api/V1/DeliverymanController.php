<?php

namespace App\Http\Controllers\Api\V1;

use App\Model\Order;
use App\Model\DeliveryMan;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Model\DeliveryHistory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class DeliverymanController extends Controller
{

    public function get_profile(Request $request)
    {
        // dd('122');
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $dm = DeliveryMan::where(['auth_token' => $request['token']])->first();
        if (isset($dm) == false) {
            return response()->json([
                'errors' => [
                    ['code' => 'delivery-man', 'message' => translate('Invalid token!')]
                ]
            ], 401);
        }
        return response()->json($dm, 200);
    }

    public function get_current_orders(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $dm = DeliveryMan::where(['auth_token' => $request['token']])->first();
        if (isset($dm) == false) {
            return response()->json([
                'errors' => [
                    ['code' => 'delivery-man', 'message' => translate('Invalid token!')]
                ]
            ], 401);
        }
        $orders = Order::with(['customer'])->whereIn('order_status', ['pending', 'processing', 'out_for_delivery', 'confirmed', 'done', 'cooking'])
            ->where(['delivery_man_id' => $dm['id']])->get();
        return response()->json($orders, 200);
    }

    public function record_location_data(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'order_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $dm = DeliveryMan::where(['auth_token' => $request['token']])->first();
        if (isset($dm) == false) {
            return response()->json([
                'errors' => [
                    ['code' => 'delivery-man', 'message' => translate('Invalid token!')]
                ]
            ], 401);
        }
        DB::table('delivery_histories')->insert([
            'order_id' => $request['order_id'],
            'deliveryman_id' => $dm['id'],
            'longitude' => $request['longitude'],
            'latitude' => $request['latitude'],
            'time' => now(),
            'location' => $request['location'],
            'created_at' => now(),
            'updated_at' => now()
        ]);
        // return response()->json(['message' => translate('location recorded')], 200);

        $distanceResponse = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
            'origins' => $dm['latitude'] . ',' . $dm['longitude'],
            'destinations' => $request['latitude'] . ',' . $request['longitude'],
            'key' => 'YOUR_GOOGLE_MAPS_API_KEY',
        ]);

        $distanceData = $distanceResponse->json();

        // Extract distance in miles from the API response
        $distanceInMeters = $distanceData['rows'][0]['elements'][0]['distance']['value'];
        $distanceInMiles = $distanceInMeters / 1609.34; // 1 mile = 1609.34 meters

        // Calculate the delivery fee
        $deliveryFee = $distanceInMiles * 0.6; // $0.6 per mile

        // ... continue with your code to record the location ...

        return response()->json([
            'message' => translate('location recorded'),
            'distance_in_miles' => $distanceInMiles,
            'delivery_fee' => $deliveryFee,
        ], 200);
    }

    public function get_order_history(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'order_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $dm = DeliveryMan::where(['auth_token' => $request['token']])->first();
        if (isset($dm) == false) {
            return response()->json([
                'errors' => [
                    ['code' => 'delivery-man', 'message' => translate('Invalid token!')]
                ]
            ], 401);
        }

        $history = DeliveryHistory::where(['order_id' => $request['order_id'], 'deliveryman_id' => $dm['id']])->get();
        return response()->json($history, 200);
    }

    public function update_order_status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'order_id' => 'required',
            'status' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $dm = DeliveryMan::where(['auth_token' => $request['token']])->first();
        if (isset($dm) == false) {
            return response()->json([
                'errors' => [
                    ['code' => 'delivery-man', 'message' => translate('Invalid token!')]
                ]
            ], 401);
        }

        Order::where(['id' => $request['order_id'], 'delivery_man_id' => $dm['id']])->update([
            'order_status' => $request['status']
        ]);

        $order=Order::find($request['order_id']);
        $fcm_token=$order->customer->cm_firebase_token;

        if ($request['status']=='out_for_delivery'){
            $value=Helpers::order_status_update_message('ord_start');
        }elseif ($request['status']=='delivered'){
            $value=Helpers::order_status_update_message('delivery_boy_delivered');
        }

        try {
            if ($value){
                $data=[
                    'title'=> translate('Order'),
                    'description'=>$value,
                    'order_id'=>$order['id'],
                    'image'=>'',
                    'type'=>'order_status',
                ];
                Helpers::send_push_notif_to_device($fcm_token,$data);
            }
        } catch (\Exception $e) {

        }

        return response()->json(['message' => translate('Status updated')], 200);
    }

    public function get_order_details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $dm = DeliveryMan::where(['auth_token' => $request['token']])->first();
        if (isset($dm) == false) {
            return response()->json([
                'errors' => [
                    ['code' => 'delivery-man', 'message' => translate('Invalid token!')]
                ]
            ], 401);
        }
        $order = Order::with(['details'])->where(['delivery_man_id' => $dm['id'], 'id' => $request['order_id']])->first();
        $details = isset($order->details) ? Helpers::order_details_formatter($order->details) : null;
        foreach ($details as $det) {
            $det['delivery_time'] = $order->delivery_time;
            $det['delivery_date'] = $order->delivery_date;
            $det['preparation_time'] = $order->preparation_time;
        }
        return response()->json($details, 200);
    }

    public function get_all_orders(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $dm = DeliveryMan::where(['auth_token' => $request['token']])->first();
        if (isset($dm) == false) {
            return response()->json([
                'errors' => [
                    ['code' => 'delivery-man', 'message' => translate('Invalid token!')]
                ]
            ], 401);
        }
        $orders = Order::with(['delivery_address','customer'])->where(['delivery_man_id' => $dm['id']])->get();
        return response()->json($orders, 200);
    }

    public function get_last_location(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $last_data = DeliveryHistory::where(['order_id' => $request['order_id']])->latest()->first();
        return response()->json($last_data, 200);
    }

    public function order_payment_status_update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $dm = DeliveryMan::where(['auth_token' => $request['token']])->first();
        if (isset($dm) == false) {
            return response()->json([
                'errors' => [
                    ['code' => 'delivery-man', 'message' => translate('Invalid token!')]
                ]
            ], 401);
        }

        if (Order::where(['delivery_man_id' => $dm['id'], 'id' => $request['order_id']])->first()) {
            Order::where(['delivery_man_id' => $dm['id'], 'id' => $request['order_id']])->update([
                'payment_status' => $request['status']
            ]);
            return response()->json(['message' => translate('Payment status updated')], 200);
        }
        return response()->json([
            'errors' => [
                ['code' => 'order', 'message' => translate('not found!')]
            ]
        ], 404);
    }

    public function update_fcm_token(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $dm = DeliveryMan::where(['auth_token' => $request['token']])->first();
        if (isset($dm) == false) {
            return response()->json([
                'errors' => [
                    ['code' => 'delivery-man', 'message' => translate('Invalid token!')]
                ]
            ], 401);
        }

        DeliveryMan::where(['id' => $dm['id']])->update([
            'fcm_token' => $request['fcm_token']
        ]);

        return response()->json(['message'=>translate('successfully updated!')], 200);
    }

    public function order_model(Request $request)
    {
        $dm = DeliveryMan::where(['auth_token' => $request['token']])->first();

        if (!isset($dm)) {

            return response()->json([
                'errors' => [
                    ['code' => 'delivery-man', 'message' => translate('Invalid token!')]
                ]
            ], 401);
        }
        $order = Order::with(['customer'])
            ->whereIn('order_status', ['pending', 'processing', 'out_for_delivery', 'confirmed', 'done', 'cooking'])
            ->where(['delivery_man_id' => $dm['id'], 'id' => $request->id])
            ->first();
        return response()->json($order, 200);
    }
}
