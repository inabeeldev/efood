<?php

namespace App\Http\Controllers\Api\V1;

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
        $customerLocation = $this->getCustomerLocation($request['location']); // Get customer's location
        $products['products'] = $this->calculateDeliveryFee($products['products'], $customerLocation);


        return response()->json($products, 200);
    }

    private function getCustomerLocation($location)
    {
        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'address' => $location,
            'key' => 'AIzaSyAa92MP_Iv7GVx4gN7iN7z42cXwAsKFzwY',
        ]);
        $location = $response->json()['results'][0]['geometry']['location'];
        $latitude = $location['lat'];
        $longitude = $location['lng'];
        // Use geocoding service to get customer's location coordinates
        // You can follow the steps from my previous response for this part
        $customerLocation = [
            'latitude' => $latitude, // Replace with actual latitude
            'longitude' => $longitude // Replace with actual longitude
        ];

        return $customerLocation;
    }

    private function calculateDeliveryFee($products, $customerLocation)
    {
        foreach ($products as &$product) {
            // Calculate distance between the branch and customer
            $branchLocation = $this->getBranchLocation($product['branch_id']); // Get branch's location
            $distance = $this->calculateDistance($branchLocation, $customerLocation);

            // Calculate delivery fee based on distance (you may have your own logic)
            $deliveryFee = $this->calculateDeliveryFeeBasedOnDistance($distance);

            // Add delivery fee to the product data
            $product['delivery_fee'] = $deliveryFee;
        }

        return $products;
    }

    private function getBranchLocation($branchId)
    {
        // Retrieve the branch's location coordinates based on branch_id
        // Implement your logic to fetch the branch's location here
        // Return the location as an array with 'latitude' and 'longitude'
        $branch = Branch::where('id', $branchId)->first();

        if ($branch) {
            return [
                'latitude' => $branch->latitude, // Replace with the actual column names for latitude and longitude
                'longitude' => $branch->longitude, // Replace with the actual column names for latitude and longitude
            ];
        } else {
            // Handle the case where the branch is not found, e.g., return a default location
            return [
                'latitude' => 0.0, // Default latitude
                'longitude' => 0.0, // Default longitude
            ];
        }
    }

    private function calculateDistance($location1, $location2)
    {
        // Implement the Haversine formula to calculate distance between two coordinates
        // Return the distance in kilometers or miles, depending on your preference
        $lat1 = deg2rad($location1['latitude']);
        $lon1 = deg2rad($location1['longitude']);
        $lat2 = deg2rad($location2['latitude']);
        $lon2 = deg2rad($location2['longitude']);

        $earthRadius = 6371; // Earth's radius in kilometers

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return $distance;
    }

    private function calculateDeliveryFeeBasedOnDistance($distance)
    {
        // $config = Helpers::get_business_settings('delivery_management');
        // $delivery_charge = 0;
        // $min_shipping_charge = $config['min_shipping_charge'];
        // $shipping_per_km = $config['shipping_per_km'];
        // $deliveryFee = $shipping_per_km * $distance;
        // if ($delivery_charge > $min_shipping_charge) {
        //     return Helpers::set_price($delivery_charge);
        // } else {
        //     return Helpers::set_price($min_shipping_charge);
        // }
        $deliveryFeePerMile = 0.6;

        $deliveryFee = $distance * $deliveryFeePerMile;

        return $deliveryFee;
    }










    public function get_popular_products(Request $request)
    {
        $products = ProductLogic::get_popular_products($request['limit'], $request['offset'], $request['product_type']);
        $products['products'] = Helpers::product_data_formatting($products['products'], true);
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
        return response()->json($products, 200);
    }

    public function get_product($id)
    {
        try {
            $product = ProductLogic::get_product($id);
            $product = Helpers::product_data_formatting($product, false);
            return response()->json($product, 200);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => ['code' => 'product-001', 'message' => translate('no_data_found')]
            ], 404);
        }
    }

    public function get_related_products($id)
    {
        if (Product::find($id)) {
            $products = ProductLogic::get_related_products($id);
            $products = Helpers::product_data_formatting($products, true);
            return response()->json($products, 200);
        }
        return response()->json([
            'errors' => ['code' => 'product-001', 'message' =>  translate('no_data_found')]
        ], 404);
    }

    public function get_set_menus()
    {
        try {
            $products = Helpers::product_data_formatting(Product::active()->with(['rating'])->where(['set_menu' => 1, 'status' => 1])->latest()->get(), true);
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
