<?php

namespace App\Http\Controllers\Branch;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Product;
use App\Model\Review;
use Illuminate\Http\Request;

class ReviewsController extends Controller
{
    public function list(){
        $reviews=Review::with(['product','customer'])->latest()->paginate(Helpers::getPagination());
        return view('branch-views.reviews.list',compact('reviews'));
    }

    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $products=Product::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->pluck('id')->toArray();
        $reviews=Review::whereIn('product_id',$products)->get();
        return response()->json([
            'view'=>view('branch-views.reviews.partials._table',compact('reviews'))->render(),
            'count' => $reviews->count()
        ]);
    }
}
