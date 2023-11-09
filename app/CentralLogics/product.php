<?php

namespace App\CentralLogics;

use App\Model\Branch;
use App\User;
use App\Model\Review;
use App\Model\Product;
use App\Model\Wishlist;
use Illuminate\Support\Facades\Http;

class ProductLogic
{
    public static function get_product($id)
    {
        return Product::active()->with(['rating'])->where('id', $id)->first();
    }

    public static function get_latest_products($limit, $offset, $product_type, $name, $category_ids)
    {
        $limit = is_null($limit) ? 10 : $limit;
        $offset = is_null($offset) ? 1 : $offset;

        $key = explode(' ', $name);
        $paginator = Product::active()
            ->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }})
            ->when(isset($product_type) && ($product_type == 'veg' || $product_type == 'non_veg'), function ($query) use ($product_type) {
                return $query->productType(($product_type == 'veg') ? 'veg' : 'non_veg');
            })
            ->when(isset($category_ids), function ($query) use ($category_ids) {
                return $query->whereJsonContains('category_ids', ['id'=>$category_ids]);
            })
            ->with(['rating'])
            ->latest()
            ->paginate($limit, ['*'], 'page', $offset);
        /*$paginator->count();*/
            $items = [];
          foreach ($paginator->items() as $value) {
              $value->colors = $value->colors ?? '';
              $items[] = $value;
                // $items[] = [
                //     "id" => $value->id ?? '',
                //     "name" => $value->name ?? '',
                //     "description" => $value->description ?? '',
                //     "image" => $value->image  ?? '',
                //     "price" => $value->price  ?? '',
                //     "variations" => $value->variations  ?? '',
                //     "add_ons" => $value->add_ons  ?? '',
                //     "tax" => $value->tax  ?? '',
                //     "available_time_starts" => $value->available_time_starts  ?? '',
                //     "available_time_ends" => $value->available_time_ends  ?? '',
                //     "status" => $value->status   ?? '',
                //     "created_at" => $value->created_at  ?? '',
                //     "updated_at" => $value->updated_at  ?? '',
                //     "attributes" => $value->attributes  ?? '',
                //     "category_ids" => $value->category_ids  ?? '',
                //     "choice_options" => $value->choice_options  ?? '',
                //     "discount" => $value->discount  ?? '',
                //     "discount_type" => $value->discount_type  ?? '',
                //     "tax_type" => $value->tax_type  ?? '',
                //     "set_menu" => $value->set_menu  ?? '',
                //     "branch_id" => $value->branch_id  ?? '',
                //     "colors" => $value->colors  ?? '',
                //     "popularity_count" => $value->popularity_count  ?? '',
                //     "product_type" => $value->product_type  ?? '',
                //     "rating" => $value->rating
                // ];
            }

        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $items,
        ];
    }

    public static function get_wishlished_products($limit, $offset, $request)
    {
        $product_ids = Wishlist::where('user_id', $request->user()->id)->get()->pluck('product_id')->toArray();
        $products = Product::active()->with(['rating'])
            ->whereIn('id', $product_ids)
            ->orderBy("created_at", 'desc')
            ->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $products->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $products->items()
        ];
    }

    public static function get_popular_products($limit, $offset, $product_type)
    {
        $limit = is_null($limit) ? 10 : $limit;
        $offset = is_null($offset) ? 1 : $offset;

        $paginator = Product::active()
            ->when(isset($product_type) && ($product_type == 'veg' || $product_type == 'non_veg'), function ($query) use ($product_type) {
                return $query->productType(($product_type == 'veg') ? 'veg' : 'non_veg');
            })
            ->with(['rating'])
            ->orderBy('popularity_count', 'desc')
            ->paginate($limit, ['*'], 'page', $offset);
        /*$paginator->count();*/
        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $paginator->items()
        ];
    }

    // public static function get_voted_products($limit, $offset, $product_type, $user_id)
    // {
    //     $limit = is_null($limit) ? 10 : $limit;
    //     $offset = is_null($offset) ? 1 : $offset;

    //     $user = User::find($user_id); // Replace $user_id with the actual user's ID
    //     $votedBranches = $user->votedBranches;
    //     $votedBranchProducts = [];

    //     foreach ($votedBranches as $branch) {
    //         $paginator = $branch->products; // Assuming 'products' is the relationship name in the Restaurant model
    //         $paginator->with(['rating'])
    //         ->orderBy('popularity_count', 'desc')
    //         ->paginate($limit, ['*'], 'page', $offset);
    //         $votedBranchProducts[$branch->name] = $paginator;
    //     }

    //     return [
    //         'total_size' => $paginator->total(),
    //         'limit' => $limit,
    //         'offset' => $offset,
    //         'products' => $paginator->items()
    //     ];
    // }


    public static function get_related_products($product_id)
    {
        $product = Product::find($product_id);
        return Product::active()->with(['rating'])->where('category_ids', $product->category_ids)
            ->where('id', '!=', $product->id)
            ->limit(10)
            ->get();
    }

    public static function search_products($name, $limit, $offset, $product_type)
    {
        $limit = is_null($limit) ? 10 : $limit;
        $offset = is_null($offset) ? 1 : $offset;

        if($product_type != 'veg' && $product_type != 'non_veg') {
            $product_type = 'all';
        }

        $key = explode(' ', $name);
        $paginator = Product::active()
            ->when(isset($product_type) && ($product_type != 'all'), function ($query) use ($product_type) {
                return $query->productType(($product_type == 'veg') ? 'veg' : 'non_veg');
            })
            ->with(['rating'])->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            })->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $paginator->items()
        ];
    }


    public static function get_product_review($id)
    {
        $reviews = Review::where('product_id', $id)->get();
        return $reviews;
    }

    public static function get_rating($reviews)
    {
        $rating5 = 0;
        $rating4 = 0;
        $rating3 = 0;
        $rating2 = 0;
        $rating1 = 0;
        foreach ($reviews as $key => $review) {
            if ($review->rating == 5) {
                $rating5 += 1;
            }
            if ($review->rating == 4) {
                $rating4 += 1;
            }
            if ($review->rating == 3) {
                $rating3 += 1;
            }
            if ($review->rating == 2) {
                $rating2 += 1;
            }
            if ($review->rating == 1) {
                $rating1 += 1;
            }
        }
        return [$rating5, $rating4, $rating3, $rating2, $rating1];
    }

    public static function get_overall_rating($reviews)
    {
        $totalRating = count($reviews);
        $rating = 0;
        foreach ($reviews as $key => $review) {
            $rating += $review->rating;
        }
        if ($totalRating == 0) {
            $overallRating = 0;
        } else {
            $overallRating = number_format($rating / $totalRating, 2);
        }

        return [$overallRating, $totalRating];
    }






    public static function getCustomerLocation($location)
    {
        $api_key = Helpers::get_business_settings('map_api_key');
        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'address' => $location,
            'key' => $api_key,
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

    public static function calculateDeliveryFee($products, $customerLocation)
    {
        if (is_array($products)) {
            foreach ($products as &$product) {
                $product = self::calculateDeliveryFeeForProduct($product, $customerLocation);
            }
        } else {
            // Handle a single product
            $products = self::calculateDeliveryFeeForProduct($products, $customerLocation);
        }

        return $products;
    }

    private static function calculateDeliveryFeeForProduct($product, $customerLocation)
    {
        // Calculate distance between the branch and customer
        $branchLocation = self::getBranchLocation($product['branch_id']); // Get branch's location
        $distance = self::calculateDistance($branchLocation, $customerLocation);

        // Calculate delivery fee based on distance (you may have your own logic)
        $deliveryFee = self::calculateDeliveryFeeBasedOnDistance($distance);

        // Add delivery fee to the product data
        $product['delivery_fee'] = $deliveryFee;

        return $product;
    }


    public static function getBranchLocation($branchId)
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

    public static function calculateDistance($location1, $location2)
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

    public static function calculateDeliveryFeeBasedOnDistance($distance)
    {
        $config = Helpers::get_business_settings('delivery_management');
        $delivery_charge = 0;
        $min_shipping_charge = $config['min_shipping_charge'];
        $shipping_per_km = $config['shipping_per_km'];
        $delivery_charge = $shipping_per_km * $distance;
        if ($delivery_charge > $min_shipping_charge) {
            return Helpers::set_price($delivery_charge);
        } else {
            return Helpers::set_price($min_shipping_charge);
        }

    }



}
