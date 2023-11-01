<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Branch;
use App\Model\BranchPromotion;
use App\Model\BusinessSetting;
use App\Model\Currency;
use App\Model\SocialMedia;
use App\Model\TimeSchedule;

class ConfigController extends Controller
{
    public function configuration()
    {
        $currency_symbol = Currency::where(['currency_code' => Helpers::currency_code()])->first()->currency_symbol;
        $cod = json_decode(BusinessSetting::where(['key' => 'cash_on_delivery'])->first()->value, true);
        $dp = json_decode(BusinessSetting::where(['key' => 'digital_payment'])->first()->value, true);

        $dm_config = Helpers::get_business_settings('delivery_management');
        $delivery_management = array(
            "status" => (int)$dm_config['status'],
            "min_shipping_charge" => (float)$dm_config['min_shipping_charge'],
            "shipping_per_km" => (float)$dm_config['shipping_per_km'],
        );
        $play_store_config = Helpers::get_business_settings('play_store_config');
        $app_store_config = Helpers::get_business_settings('app_store_config');

        //schedule time
        $schedules = TimeSchedule::select('day', 'opening_time', 'closing_time')->get();

        $branch_promotion = Branch::with('branch_promotion')
            ->where(['branch_promotion_status' => 1])
            ->get();

        $branchPromotion = [];
        foreach ($branch_promotion as $key => $value) {
            $branchPromotion[] = [
                "restaurant_id" => $value->restaurant_id ?? '',
                "name" =>  $value->restaurant_id ?? '',
                "email" =>  $value->email ?? '',
                "latitude" =>  $value->latitude ?? '',
                "longitude" =>  $value->longitude ?? '',
                "address" =>  $value->address ?? '',
                "status" =>  $value->status ?? '',
                "branch_promotion_status" =>  $value->branch_promotion_status ?? '',
                "created_at" =>  $value->created_at ?? '',
                "updated_at" =>  $value->updated_at ?? '',
                "coverage" =>  $value->coverage ?? 0,
                "remember_token" =>  $value->remember_token ?? '',
                "image" =>  $value->image ?? '',
                "phone" =>  $value->phone ?? '',
                "outdoor_slots" =>  $value->outdoor_slots ?? '',
                "indoor_slots" =>  $value->indoor_slots ?? '',
                "plan_id" =>  $value->plan_id ?? '',
                "continent" =>  $value->continent ?? '',
                "physical_address" =>  $value->physical_address ?? '',
                "city" =>  $value->city ?? '',
                "state" =>  $value->state ?? '',
                "country" =>  $value->country ?? '',
                "zip_code" =>  $value->zip_code ?? '',
                "image_url" => $value->image_url ?? '',
                "branch_promotion" =>  $value->restaurant_id ?? [],
            ];
        }

        $branch_promotion =  $branchPromotion;

        $google = BusinessSetting::where(['key' => 'google_social_login'])->first()->value?? 0 ;
        $facebook = BusinessSetting::where(['key' => 'facebook_social_login'])->first()->value?? 0 ;

        return response()->json([
            'restaurant_name' => BusinessSetting::where(['key' => 'restaurant_name'])->first()->value ?? '',
            'restaurant_open_time' => BusinessSetting::where(['key' => 'restaurant_open_time'])->first()->value ?? '',
            'restaurant_close_time' => BusinessSetting::where(['key' => 'restaurant_close_time'])->first()->value ?? '',
            'restaurant_schedule_time' => $schedules,
            'restaurant_logo' => BusinessSetting::where(['key' => 'logo'])->first()->value ?? '',
            'restaurant_address' => BusinessSetting::where(['key' => 'address'])->first()->value ?? '',
            'restaurant_phone' => BusinessSetting::where(['key' => 'phone'])->first()->value ?? '',
            'restaurant_email' => BusinessSetting::where(['key' => 'email_address'])->first()->value ?? '',
            'restaurant_location_coverage' => Branch::where(['id' => 1])->first(['longitude', 'latitude', 'coverage']),
            'minimum_order_value' => (float)BusinessSetting::where(['key' => 'minimum_order_value'])->first()->value ?? 0,
            'base_urls' => [
                'product_image_url' => asset('storage/app/public/product'),
                'customer_image_url' => asset('storage/app/public/profile'),
                'banner_image_url' => asset('storage/app/public/banner'),
                'category_image_url' => asset('storage/app/public/category'),
                'category_banner_image_url' => asset('storage/app/public/category/banner'),
                'review_image_url' => asset('storage/app/public/review'),
                'notification_image_url' => asset('storage/app/public/notification'),
                'restaurant_image_url' => asset('storage/app/public/restaurant'),
                'delivery_man_image_url' => asset('storage/app/public/delivery-man'),
                'chat_image_url' => asset('storage/app/public/conversation'),
                'promotional_url' => asset('storage/app/public/promotion'),
                'kitchen_image_url' => asset('storage/app/public/kitchen'),
            ],
            'currency_symbol' => $currency_symbol,
            'delivery_charge' => (float)BusinessSetting::where(['key' => 'delivery_charge'])->first()->value ?? 0,
            'delivery_management' => $delivery_management,
            'cash_on_delivery' => $cod['status'] == 1 ? 'true' : 'false',
            'digital_payment' => $dp['status'] == 1 ? 'true' : 'false',
            'branches' => Branch::all(['id', 'name', 'email', 'longitude', 'latitude', 'address', 'coverage']),
            'terms_and_conditions' => BusinessSetting::where(['key' => 'terms_and_conditions'])->first()->value ?? '',
            'privacy_policy' => BusinessSetting::where(['key' => 'privacy_policy'])->first()->value ?? '',
            'about_us' => BusinessSetting::where(['key' => 'about_us'])->first()->value ?? '',
            /*'terms_and_conditions' => route('terms-and-conditions'),
            'privacy_policy' => route('privacy-policy'),
            'about_us' => route('about-us')*/
            'email_verification' => (boolean)Helpers::get_business_settings('email_verification') ?? 0,
            'phone_verification' => (boolean)Helpers::get_business_settings('phone_verification') ?? 0,
            'currency_symbol_position' => Helpers::get_business_settings('currency_symbol_position') ?? 'right',
            'maintenance_mode' => (boolean)Helpers::get_business_settings('maintenance_mode') ?? 0,
            'country' => Helpers::get_business_settings('country') ?? 'BD',
            'self_pickup' => (boolean)Helpers::get_business_settings('self_pickup') ?? 1,
            'delivery' => (boolean)Helpers::get_business_settings('delivery') ?? 1,
            'play_store_config' => [
                "status" => isset($play_store_config) ? (boolean)$play_store_config['status'] : false,
                "link" => isset($play_store_config) ? $play_store_config['link'] : '',
                "min_version" => isset($play_store_config) && array_key_exists('min_version', $app_store_config) ? $play_store_config['min_version'] : ''
            ],
            'app_store_config' => [
                "status" => isset($app_store_config) ? (boolean)$app_store_config['status'] : false,
                "link" => isset($app_store_config) ? $app_store_config['link'] : '',
                "min_version" => isset($app_store_config) && array_key_exists('min_version', $app_store_config) ? $app_store_config['min_version'] : ''
            ],
            'social_media_link' => SocialMedia::orderBy('id', 'desc')->active()->get(),
            'software_version' => (string)env('SOFTWARE_VERSION') ?? null,
            'footer_text' => Helpers::get_business_settings('footer_text'),
            'decimal_point_settings' => (int)(Helpers::get_business_settings('decimal_point_settings') ?? 2),
            'schedule_order_slot_duration' => (int)(Helpers::get_business_settings('schedule_order_slot_duration') ?? 30),
            'time_format' => (string)(Helpers::get_business_settings('time_format') ?? '12'),
            'promotion_campaign' => $branch_promotion,
            'social_login' => [
                'google' => (integer)$google,
                'facebook' => (integer)$facebook,
            ]
        ], 200);
    }
}
