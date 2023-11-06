<?php

namespace App\Http\Controllers\Api\V1;

use App\Model\BranchVote;
use App\Model\ProductVote;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\CentralLogics\Helpers as AppHelpers;

class VoteController extends Controller
{
    public function addProductVote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => AppHelpers::error_processor($validator)], 403);
        }

        $productVote = ProductVote::where('user_id', $request->user()->id)->where('product_id', $request->product_id)->first();

        if (empty($productVote)) {
            $productVote = new ProductVote;
            $productVote->user_id = $request->user()->id;
            $productVote->product_id = $request->product_id;
            $productVote->save();
            return response()->json(['message' => translate('added_success')], 200);
        }

        return response()->json(['message' => translate('already_added')], 200);
    }

    public function addRestaurantVote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => AppHelpers::error_processor($validator)], 403);
        }

        $productVote = BranchVote::where('user_id', $request->user()->id)->where('branch_id', $request->branch_id)->first();

        if (empty($productVote)) {
            $productVote = new BranchVote;
            $productVote->user_id = $request->user()->id;
            $productVote->branch_id = $request->branch_id;
            $productVote->save();
            return response()->json(['message' => translate('added_success')], 200);
        }

        return response()->json(['message' => translate('already_added')], 200);
    }

    public function productVote(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $count = ProductVote::where('product_id', $request->product_id)->count();
        return response()->json(['count' => $count], 200);
    }

    public function restaurantVote(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'branch_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $count = BranchVote::where('branch_id', $request->branch_id)->count();
        return response()->json(['count' => $count], 200);
    }

}
