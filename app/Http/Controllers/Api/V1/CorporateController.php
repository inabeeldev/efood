<?php

namespace App\Http\Controllers\Api\V1;

use App\User;
use App\Model\CorporateProduct;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;

class CorporateController extends Controller
{
    public function list(Request $request)
    {
        $user = User::find($request->user()->id);

        // Check if the user is marked as corporate
        if ($user->is_corporate == true) {
            $corporateProducts = CorporateProduct::with('branch')->where('status', 1)
                ->latest()
                ->paginate(Helpers::getPagination());

            return response()->json($corporateProducts);
        } else {
            // Handle the case where the user is not marked as corporate
            return response()->json(['message' => 'Unauthorized. User is not corporate.'], 403);
        }
    }

    public function getProduct(Request $request, $id)
    {
        $user = User::find($request->user()->id);

        // Check if the user is marked as corporate
        if ($user->is_corporate == true) {
            $corporateProduct = CorporateProduct::with('branch')->find($id);

            return response()->json($corporateProduct);
        } else {
            // Handle the case where the user is not marked as corporate
            return response()->json(['message' => 'Unauthorized. User is not corporate.'], 403);
        }
    }
}
