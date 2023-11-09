<?php

namespace App\Http\Controllers\Api\V1;

use App\User;
use App\Model\Branch;
use App\Model\Review;
use App\Model\Product;
use App\Model\Translation;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\CentralLogics\ProductLogic;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function get_latest_products(Request $request)
    {
        $products = ProductLogic::get_latest_products($request['limit'], $request['offset'], $request['product_type'], $request['name'], $request['category_ids']);
        $products['products'] = Helpers::product_data_formatting($products['products'], true);


        // Calculate delivery fee for each product
        $customerLocation = ProductLogic::getCustomerLocation($request['location']); // Get customer's location
        $products['products'] = ProductLogic::calculateDeliveryFee($products['products'], $customerLocation);


        return response()->json($products, 200);
    }


    public function featuredProduct(Request $request)
    {
        $user_id = $request->user()->id;
        $user = User::find($user_id); // Replace $user_id with the actual user's ID
        $votedBranches = $user->votedBranches;

        $votedBranchProducts = [];
        $customerLocation = ProductLogic::getCustomerLocation($request['location']);

        foreach ($votedBranches as $branch) {
            $products = $branch->products; // Assuming 'products' is the relationship name in the Restaurant model
            $products = Helpers::product_data_formatting($products, true);

            $productsWithDeliveryFee = ProductLogic::calculateDeliveryFee($products, $customerLocation);

            $votedBranchProducts[$branch->name] = $productsWithDeliveryFee;
        }
        return response()->json($votedBranchProducts, 200);
    }



    public function get_popular_products(Request $request)
    {
        $products = ProductLogic::get_popular_products($request['limit'], $request['offset'], $request['product_type']);
        $products['products'] = Helpers::product_data_formatting($products['products'], true);

        $customerLocation = ProductLogic::getCustomerLocation($request['location']); // Get customer's location
        $products['products'] = ProductLogic::calculateDeliveryFee($products['products'], $customerLocation);

        return response()->json($products, 200);
    }

    public function get_searched_products(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $product_type = $request['product_type'];
        $products = ProductLogic::search_products($request['name'], $request['limit'], $request['offset'], $product_type);

        if($product_type != 'veg' && $product_type != 'non_veg') {
            $product_type = 'all';
        }

        if (count($products['products']) == 0) {
            $key = explode(' ', $request['name']);
            $ids = Translation::where(['key' => 'name'])->where(function ($query) use ($key) {
                foreach ($key as $value) {
                    $query->orWhere('value', 'like', "%{$value}%");
                }
            })->pluck('translationable_id')->toArray();
            $paginator = Product::active()->whereIn('id', $ids)->withCount(['wishlist'])->with(['rating'])
                ->when(isset($product_type) && ($product_type != 'all'), function ($query) use ($product_type) {
                    return $query->productType(($product_type == 'veg') ? 'veg' : 'non_veg');
                })
                ->paginate($request['limit'], ['*'], 'page', $request['offset']);

            $products = [
                'total_size' => $paginator->total(),
                'limit' => $request['limit'],
                'offset' => $request['offset'],
                'products' => $paginator->items()
            ];
        }
        $products['products'] = Helpers::product_data_formatting($products['products'], true);

        $customerLocation = ProductLogic::getCustomerLocation($request['location']); // Get customer's location
        $products['products'] = ProductLogic::calculateDeliveryFee($products['products'], $customerLocation);
        return response()->json($products, 200);
    }

    public function get_product(Request $request, $id)
    {
        try {
            $product = ProductLogic::get_product($id);
            $product = Helpers::product_data_formatting($product, false);
            $customerLocation = ProductLogic::getCustomerLocation($request['location']); // Get customer's location
            $product = ProductLogic::calculateDeliveryFee($product, $customerLocation);
            return response()->json($product, 200);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => ['code' => 'product-001', 'message' => translate('no_data_found')]
            ], 404);
        }
    }

    public function get_related_products(Request $request, $id)
    {
        if (Product::find($id)) {
            $products = ProductLogic::get_related_products($id);
            $products = Helpers::product_data_formatting($products, true);
            $customerLocation = ProductLogic::getCustomerLocation($request['location']); // Get customer's location
            $products = ProductLogic::calculateDeliveryFee($products, $customerLocation);
            return response()->json($products, 200);
        }
        return response()->json([
            'errors' => ['code' => 'product-001', 'message' =>  translate('no_data_found')]
        ], 404);
    }

    public function get_set_menus(Request $request)
    {
        try {
            $products = Helpers::product_data_formatting(Product::active()->with(['rating'])->where(['set_menu' => 1, 'status' => 1])->latest()->get(), true);
            $customerLocation = ProductLogic::getCustomerLocation($request['location']); // Get customer's location
            $products = ProductLogic::calculateDeliveryFee($products, $customerLocation);
            return response()->json($products, 200);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => ['code' => 'product-001', 'message' =>  translate('no_data_found')]
            ], 404);
        }
    }

    public function get_product_reviews($id)
    {
        $reviews = Review::with(['customer'])->where(['product_id' => $id])->get();

        $storage = [];
        foreach ($reviews as $item) {
            $item['attachment'] = json_decode($item['attachment']);
            array_push($storage, $item);
        }

        return response()->json($storage, 200);
    }

    public function get_product_rating($id)
    {
        try {
            $product = Product::find($id);
            $overallRating = ProductLogic::get_overall_rating($product->reviews);
            return response()->json(floatval($overallRating[0]), 200);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
    }

    public function submit_product_review(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'order_id' => 'required',
            'comment' => 'required',
            'rating' => 'required|numeric|max:5',
        ]);

        $product = Product::find($request->product_id);
        if (isset($product) == false) {
            $validator->errors()->add('product_id', translate('no_data_found'));
        }

        $multi_review = Review::where(['product_id' => $request->product_id, 'user_id' => $request->user()->id])->first();
        if (isset($multi_review)) {
            $review = $multi_review;
        } else {
            $review = new Review;
        }

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $image_array = [];
        if (!empty($request->file('attachment'))) {
            foreach ($request->file('attachment') as $image) {
                if ($image != null) {
                    if (!Storage::disk('public')->exists('review')) {
                        Storage::disk('public')->makeDirectory('review');
                    }
                    array_push($image_array, Storage::disk('public')->put('review', $image));
                }
            }
        }

        $review->user_id = $request->user()->id;
        $review->product_id = $request->product_id;
        $review->order_id = $request->order_id;
        $review->comment = $request->comment;
        $review->rating = $request->rating;
        $review->attachment = json_encode($image_array);
        $review->save();

        return response()->json(['message' => translate('review_submit_success')], 200);
    }
}
