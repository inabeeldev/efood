<?php

namespace App\Http\Controllers\Api\V1;

use App\Model\DeliveryMan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\CentralLogics\Helpers as AppHelpers;
use App\Model\DeliveryManWithdrawRequest;

class FundController extends Controller
{
    public function walletAmount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => AppHelpers::error_processor($validator)], 403);
        }
        $wallet = DeliveryMan::where(['auth_token' => $request['token']])->select('wallet_amount')->first();
        if (isset($wallet) == false) {
            return response()->json([
                'errors' => [
                    ['code' => 'delivery-man', 'message' => translate('Invalid token!')]
                ]
            ], 401);
        }
        return response()->json($wallet, 200);
    }

    public function deliveryManWithdraw(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_name' => 'required',
            'routing_number' => 'required',
            'account_title' => 'required',
            'account_no' => 'required',
            'amount' => 'required',
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => AppHelpers::error_processor($validator)], 403);
        }

        $dm = DeliveryMan::where(['auth_token' => $request->token])->first();

        if (!$dm) {
            return response()->json(['error' => 'Invalid token'], 403);
        }

        // Check if the wallet amount is less than the requested amount
        if ($dm->wallet_amount < $request->amount) {
            return response()->json(['error' => 'Insufficient balance in your wallet.'], 403);
        }

        // Update the wallet amount and create withdrawal request
        $dm->update([
            'wallet_amount' => $dm->wallet_amount - $request->amount
        ]);

        DeliveryManWithdrawRequest::create([
            'delivery_man_id' => $dm->id,
            'bank_name' => $request->bank_name,
            'routing_number' => $request->routing_number,
            'account_title' => $request->account_title,
            'account_no' => $request->account_no,
            'amount' => $request->amount,
            // Add other fields as needed
        ]);

        // Process withdrawal logic here (create withdrawal record, send notifications, etc.)

        return response()->json(['message' => 'Withdrawal request successful.']);
    }

    public function deliveryManWithdrawHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => AppHelpers::error_processor($validator)], 403);
        }

        $dm = DeliveryMan::where(['auth_token' => $request->token])->first();

        if (!$dm) {
            return response()->json(['error' => 'Invalid token'], 403);
        }

        // Retrieve withdrawal history for the delivery man
        $withdrawalHistory = DeliveryManWithdrawRequest::where('delivery_man_id', $dm->id)->get();

        return response()->json(['withdrawal_history' => $withdrawalHistory]);
    }




}
