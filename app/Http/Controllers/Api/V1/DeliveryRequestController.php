<?php

namespace App\Http\Controllers\Api\V1;

use App\Model\Order;
use App\Model\Branch;
use App\Model\DeliveryMan;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Model\DeliveryRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DeliveryRequestController extends Controller
{
    public function allRequests(Request $request)
    {

        $dr = DeliveryRequest::all();

        return response()->json($dr, 200);
    }

    public function getDeliveryRequests(Request $request)
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

        $deliveryRequest = DeliveryRequest::where('deliveryman_id', $dm->id)
        ->where('status', 'pending')
        ->orderBy('created_at', 'desc')
        ->get();
        return response()->json($deliveryRequest, 200);
    }

    public function changeStatus(Request $request, $order_id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:accepted,rejected',
            'token' => 'required',
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

        $deliveryRequest = DeliveryRequest::where('order_id', $order_id)
            ->where('deliveryman_id', $dm->id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc') // Order by the creation date in descending order
            ->first();

        if (!$deliveryRequest) {
            return response()->json(['status' => false, 'message' => 'No pending delivery request found for the given order and deliveryman'], 200);
        }

        $newStatus = $request->input('status');

        if ($newStatus === 'accepted') {

            $deliveryRequest->status = 'accepted';
            $deliveryRequest->save();

            return response()->json(['status' => true, 'message' => 'Delivery request accepted successfully'], 200);
        } elseif ($newStatus === 'rejected') {

            $deliveryRequest->status = 'rejected';
            $deliveryRequest->save();

            return response()->json(['status' => true, 'message' => 'Delivery request rejected successfully'], 200);
        }

        // If the status is neither 'accepted' nor 'rejected'
        return response()->json(['status' => false, 'message' => 'Invalid status provided'], 400);
    }

    public function changeOrderStatus(Request $request, $orderId)
    {
        $validator = Validator::make($request->all(), [
            'order_status' => 'required|in:delivered',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $deliveryMan = DeliveryMan::where('auth_token', $request['token'])->first();
        if (!$deliveryMan) {
            return response()->json([
                'errors' => [
                    ['code' => 'delivery-man', 'message' => translate('Invalid token!')]
                ]
            ], 401);
        }

        $order = Order::where(['id' => $orderId, 'delivery_man_id' => $deliveryMan->id])->first();
        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Order not found'], 404);
        }

        $order->order_status = $request->order_status;

        if ($request->order_status == 'delivered' && $order->branch_payment_status != 'paid' && $order->delivery_payment_status != 'paid') {
            $this->handlePayment($order);
        }

        $order->save();

        return response()->json(['status' => true, 'message' => 'Order status changed to delivered successfully'], 200);
    }

    private function handlePayment(Order $order)
    {
        $branch = Branch::find($order->branch_id);
        $platformFeeRestaurants = Helpers::get_business_settings('platform_fee_restaurants');
        $platformFeeDeliveryMen = Helpers::get_business_settings('platform_fee_delivery_men');

        if ($branch) {
            $this->handleBranchPayment($order, $branch, $platformFeeRestaurants);
        }

        $this->handleDeliveryManPayment($order, $platformFeeDeliveryMen);
    }

    private function handleBranchPayment(Order $order, Branch $branch, $platformFeeRestaurants)
    {
        $platformFeeBranch = ($order->order_amount / 100) * $platformFeeRestaurants;
        $remainingAmount = $order->order_amount - $platformFeeBranch;

        $branch->update(['wallet_amount' => $branch->wallet_amount + $remainingAmount]);
        $order->branch_payment_status = 'paid';
    }

    private function handleDeliveryManPayment(Order $order, $platformFeeDeliveryMen)
    {
        $platformFeeDelivery = ($order->delivery_charge / 100) * $platformFeeDeliveryMen;
        $remainingDeliveryAmount = $order->delivery_charge - $platformFeeDelivery;

        $deliveryMan = DeliveryMan::find($order->delivery_man_id);

        if ($deliveryMan) {
            $deliveryMan->update(['wallet_amount' => $deliveryMan->wallet_amount + $remainingDeliveryAmount]);
        }

        $order->delivery_payment_status = 'paid';
    }




}
