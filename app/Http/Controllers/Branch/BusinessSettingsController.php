<?php

namespace App\Http\Controllers\Branch;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Branch;
use App\Model\BusinessSetting;
use App\Model\BranchBusinessSetting;
use App\Model\Currency;
use App\Model\SocialMedia;
use App\Model\TimeSchedule;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class BusinessSettingsController extends Controller
{
    public function restaurant_index()
    {
        if (BusinessSetting::where(['branch_id' => auth('branch')->id(),'key' => 'minimum_order_value'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'minimum_order_value'], [
                'branch_id' => auth('branch')->id(),
                'value' => 1,
            ]);
        }

        return view('branch-views.business-settings.restaurant-index');
    }

    public function maintenance_mode()
    {
        $mode = Helpers::get_branch_business_settings('maintenance_mode');
        DB::table('business_settings')->updateOrInsert([
            'key' => 'maintenance_mode',
            'branch_id' => auth('branch')->id()
            ], [
            'value' => isset($mode) ? !$mode : 1,
            'branch_id' => auth('branch')->id()
        ]);
        if (!$mode){
            return response()->json(['message' => translate('Maintenance Mode is On.')]);
        }
        return response()->json(['message' => translate('Maintenance Mode is Off.')]);
    }

    public function currency_symbol_position($side)
    {
        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'currency_symbol_position'], [
            'branch_id' => auth('branch')->id(),
            'value' => $side
        ]);
        return response()->json(['message' => translate('Symbol position is ') . $side]);
    }

    public function restaurant_setup(Request $request)
    {
        // dd($request->All());
//        return $request->all();
        if ($request->has('email_verification')) {
            $request['email_verification'] = 1;
            $request['phone_verification'] = 0;
        } elseif ($request->has('phone_verification')) {
            $request['email_verification'] = 0;
            $request['phone_verification'] = 1;
        }

//        return $request;


        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'country'], [
            'branch_id' => auth('branch')->id(),
            'value' => $request['country']
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'time_zone'], [
            'branch_id' => auth('branch')->id(),
            'value' => $request['time_zone'],
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'phone_verification'], [
            'branch_id' => auth('branch')->id(),
            'value' => $request['phone_verification']
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'email_verification'], [
            'branch_id' => auth('branch')->id(),
            'value' => $request['email_verification']
        ]);


//        if ($request['self_pickup'] == 0) {
//            $request['delivery'] = 1;
//        } elseif ($request['delivery'] == 0) {
//            $request['self_pickup'] = 1;
//        }

        if ($request->has('self_pickup')) {
            $request['self_pickup'] =1;
        }
        if($request->has('delivery')) {
            $request['delivery'] = 1;
        }


        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'self_pickup'], [
            'branch_id' => auth('branch')->id(),
            'value' => $request['self_pickup'],
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'delivery'], [
            'branch_id' => auth('branch')->id(),
            'value' => $request['delivery'],
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'restaurant_open_time'], [
            'branch_id' => auth('branch')->id(),
            'value' => $request['restaurant_open_time'],
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'restaurant_close_time'], [
            'branch_id' => auth('branch')->id(),
            'value' => $request['restaurant_close_time'],
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'restaurant_name'], [
            'branch_id' => auth('branch')->id(),
            'value' => $request['restaurant_name'],
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'currency'], [
            'branch_id' => auth('branch')->id(),
            'value' => $request['currency'],
        ]);

        $curr_logo = Branch::where(['id' => auth('branch')->id()])->first();
        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'logo'], [
            'branch_id' => auth('branch')->id(),
            'value' => $request->has('logo') ? Helpers::update('restaurant/', $curr_logo->image, 'png', $request->file('logo')) : $curr_logo->image
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'phone'], [
            'branch_id' => auth('branch')->id(),
            'value' => $request['phone'],
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'email_address'], [
            'branch_id' => auth('branch')->id(),
            'value' => $request['email'],
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'address'], [
            'branch_id' => auth('branch')->id(),
            'value' => $request['address'],
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'email_verification'], [
            'branch_id' => auth('branch')->id(),
            'value' => $request['email_verification'],
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'footer_text'], [
            'branch_id' => auth('branch')->id(),
            'value' => $request['footer_text'],
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'minimum_order_value'], [
            'branch_id' => auth('branch')->id(),
            'value' => $request['minimum_order_value'],
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'point_per_currency'], [
            'branch_id' => auth('branch')->id(),
            'value' => $request['point_per_currency'],
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'pagination_limit'], [
            'branch_id' => auth('branch')->id(),
            'value' => $request['pagination_limit'],
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'default_preparation_time'], [
            'branch_id' => auth('branch')->id(),
            'value' => $request['default_preparation_time'],
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'decimal_point_settings'], [
            'branch_id' => auth('branch')->id(),
            'value' => $request['decimal_point_settings']
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'schedule_order_slot_duration'], [
            'branch_id' => auth('branch')->id(),
            'value' => $request['schedule_order_slot_duration']
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'time_format'], [
            'branch_id' => auth('branch')->id(),
            'value' => $request['time_format']
        ]);

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    public function mail_index()
    {
        return view('branch-views.business-settings.mail-index');
    }

    public function mail_config(Request $request)
    {
        $request->has('status') ? $request['status'] = 1 : $request['status'] = 0;
        BusinessSetting::where(['branch_id' => auth('branch')->id(),'key' => 'mail_config'])->update([
            'branch_id' => auth('branch')->id(),
            'value' => json_encode([
                "status" => $request['status'],
                "name" => $request['name'],
                "host" => $request['host'],
                "driver" => $request['driver'],
                "port" => $request['port'],
                "username" => $request['username'],
                "email_id" => $request['email'],
                "encryption" => $request['encryption'],
                "password" => $request['password'],
            ]),
        ]);
        Toastr::success(translate('Configuration updated successfully!'));
        return back();
    }

    public function mail_send(Request $request)
    {
        $response_flag = 0;
        try {
            $emailServices = Helpers::get_branch_business_settings('mail_config');

            if (isset($emailServices['status']) && $emailServices['status'] == 1) {
                Mail::to($request->email)->send(new \App\Mail\TestEmailSender());
                $response_flag = 1;
            }
        } catch (\Exception $exception) {
            $response_flag = 2;
        }

        return response()->json(['success' => $response_flag]);
    }

    public function payment_index()
    {
        return view('branch-views.business-settings.payment-index');
    }

    public function payment_update(Request $request, $name)
    {
//        return $request;

        if ($name == 'cash_on_delivery') {
            $payment = BusinessSetting::where('branch_id' , auth('branch')->id())->where('key', 'cash_on_delivery')->first();
            if (isset($payment) == false) {
                DB::table('business_settings')->insert([
                    'branch_id' => auth('branch')->id(),
                    'key' => 'cash_on_delivery',
                    'value' => json_encode([
                        'status' => $request['status'],
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('business_settings')->where(['branch_id' => auth('branch')->id(),'key' => 'cash_on_delivery'])->update([
                    'branch_id' => auth('branch')->id(),
                    'key' => 'cash_on_delivery',
                    'value' => json_encode([
                        'status' => $request['status'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        }
        elseif ($name == 'digital_payment') {
            $payment = BusinessSetting::where('branch_id' , auth('branch')->id())->where('key', 'digital_payment')->first();
            if (isset($payment) == false) {
                DB::table('business_settings')->insert([
                    'branch_id' => auth('branch')->id(),
                    'key' => 'digital_payment',
                    'value' => json_encode([
                        'status' => $request['status'],
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('business_settings')->where(['branch_id' => auth('branch')->id(),'key' => 'digital_payment'])->update([
                    'branch_id' => auth('branch')->id(),
                    'key' => 'digital_payment',
                    'value' => json_encode([
                        'status' => $request['status'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        }
        elseif ($name == 'ssl_commerz_payment') {
            $payment = BusinessSetting::where('branch_id' , auth('branch')->id())->where('key', 'ssl_commerz_payment')->first();
            if (isset($payment) == false) {
                DB::table('business_settings')->insert([
                    'branch_id' => auth('branch')->id(),
                    'key' => 'ssl_commerz_payment',
                    'value' => json_encode([
                        'status' => 1,
                        'store_id' => '',
                        'store_password' => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('business_settings')->where(['branch_id' => auth('branch')->id(),'key' => 'ssl_commerz_payment'])->update([
                    'branch_id' => auth('branch')->id(),
                    'key' => 'ssl_commerz_payment',
                    'value' => json_encode([
                        'status' => $request['status'] == 'on' ? 1 : 0,
                        'store_id' => $request['store_id'],
                        'store_password' => $request['store_password'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        }
        elseif ($name == 'razor_pay') {
            $payment = BusinessSetting::where('branch_id' , auth('branch')->id())->where('key', 'razor_pay')->first();
            if (isset($payment) == false) {
                DB::table('business_settings')->insert([
                    'branch_id' => auth('branch')->id(),
                    'key' => 'razor_pay',
                    'value' => json_encode([
                        'status' => 1,
                        'razor_key' => '',
                        'razor_secret' => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('business_settings')->where(['branch_id' => auth('branch')->id(),'key' => 'razor_pay'])->update([
                    'branch_id' => auth('branch')->id(),
                    'key' => 'razor_pay',
                    'value' => json_encode([
                        'status' => $request['status'] == 'on' ? 1 : 0,
                        'razor_key' => $request['razor_key'],
                        'razor_secret' => $request['razor_secret'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        }
        elseif ($name == 'paypal') {
            $payment = BusinessSetting::where('branch_id' , auth('branch')->id())->where('key', 'paypal')->first();
            if (isset($payment) == false) {
                DB::table('business_settings')->insert([
                    'branch_id' => auth('branch')->id(),
                    'key' => 'paypal',
                    'value' => json_encode([
                        'status' => 1,
                        'paypal_client_id' => '',
                        'paypal_secret' => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('business_settings')->where(['branch_id' => auth('branch')->id(),'key' => 'paypal'])->update([
                    'branch_id' => auth('branch')->id(),
                    'key' => 'paypal',
                    'value' => json_encode([
                        'status' => $request['status'] == 'on' ? 1 : 0,
                        'paypal_client_id' => $request['paypal_client_id'],
                        'paypal_secret' => $request['paypal_secret'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        }
        elseif ($name == 'stripe') {
            $payment = BusinessSetting::where('branch_id' , auth('branch')->id())->where('key', 'stripe')->first();
            if (isset($payment) == false) {
                DB::table('business_settings')->insert([
                    'branch_id' => auth('branch')->id(),
                    'key' => 'stripe',
                    'value' => json_encode([
                        'status' => 1,
                        'api_key' => '',
                        'published_key' => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('business_settings')->where(['branch_id' => auth('branch')->id(),'key' => 'stripe'])->update([
                    'branch_id' => auth('branch')->id(),
                    'key' => 'stripe',
                    'value' => json_encode([
                        'status' => $request['status'] == 'on' ? 1 : 0,
                        'api_key' => $request['api_key'],
                        'published_key' => $request['published_key'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        }
        elseif ($name == 'senang_pay') {
            $payment = BusinessSetting::where('branch_id' , auth('branch')->id())->where('key', 'senang_pay')->first();
            if (isset($payment) == false) {
                DB::table('business_settings')->insert([
                    'branch_id' => auth('branch')->id(),
                    'key' => 'senang_pay',
                    'value' => json_encode([
                        'status' => 1,
                        'secret_key' => '',
                        'merchant_id' => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('business_settings')->where(['branch_id' => auth('branch')->id(),'key' => 'senang_pay'])->update([
                    'key' => 'senang_pay',
                    'branch_id' => auth('branch')->id(),
                    'value' => json_encode([
                        'status' => $request['status'] == 'on' ? 1 : 0,
                        'secret_key' => $request['secret_key'],
                        'merchant_id' => $request['merchant_id'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        }
        elseif ($name == 'paystack') {
            $payment = BusinessSetting::where('branch_id' , auth('branch')->id())->where('key', 'paystack')->first();
            if (isset($payment) == false) {
                DB::table('business_settings')->insert([
                    'branch_id' => auth('branch')->id(),
                    'key' => 'paystack',
                    'value' => json_encode([
                        'status' => 1,
                        'publicKey' => '',
                        'secretKey' => '',
                        'paymentUrl' => '',
                        'merchantEmail' => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                DB::table('business_settings')->where(['branch_id' => auth('branch')->id(),'key' => 'paystack'])->update([
                    'branch_id' => auth('branch')->id(),
                    'key' => 'paystack',
                    'value' => json_encode([
                        'status' => $request['status'] == 'on' ? 1 : 0,
                        'publicKey' => $request['publicKey'],
                        'secretKey' => $request['secretKey'],
                        'paymentUrl' => $request['paymentUrl'],
                        'merchantEmail' => $request['merchantEmail'],
                    ]),
                    'updated_at' => now()
                ]);
            }
        }
        elseif ($name == 'internal_point') {
            $payment = BusinessSetting::where('branch_id' , auth('branch')->id())->where('key', 'internal_point')->first();
            if (isset($payment) == false) {
                DB::table('business_settings')->insert([
                    'branch_id' => auth('branch')->id(),
                    'key' => 'internal_point',
                    'value' => json_encode([
                        'status' => $request['status'] == 'on' ? 1 : 0,
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('business_settings')->where('branch_id' , auth('branch')->id())->where(['key' => 'internal_point'])->update([
                    'branch_id' => auth('branch')->id(),
                    'key' => 'internal_point',
                    'value' => json_encode([
                        'status' => $request['status'] == 'on'? 1 : 0,
                    ]),
                    'updated_at' => now(),
                ]);
            }
        }
        elseif ($name == 'bkash') {
            DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'bkash'], [
                'branch_id' => auth('branch')->id(),
                'value' => json_encode([
                    'status' => $request['status'] == 'on' ? 1: 0,
                    'api_key' => $request['api_key'],
                    'api_secret' => $request['api_secret'],
                    'username' => $request['username'],
                    'password' => $request['password'],
                ])
            ]);
        }
        elseif ($name == 'paymob') {
            DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'paymob'], [
                'branch_id' => auth('branch')->id(),
                'value' => json_encode([
                    'status' => $request['status'] == 'on'? 1: 0,
                    'api_key' => $request['api_key'],
                    'iframe_id' => $request['iframe_id'],
                    'integration_id' => $request['integration_id'],
                    'hmac' => $request['hmac']
                ])
            ]);
        }
        elseif ($name == 'flutterwave') {
            DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'flutterwave'], [
                'branch_id' => auth('branch')->id(),
                'value' => json_encode([
                    'status' => $request['status'] == 'on'? 1: 0,
                    'public_key' => $request['public_key'],
                    'secret_key' => $request['secret_key'],
                    'hash' => $request['hash']
                ])
            ]);
        }
        elseif ($name == 'mercadopago') {
            DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'mercadopago'], [
                'branch_id' => auth('branch')->id(),
                'value' => json_encode([
                    'status' => $request['status'] == 'on'? 1: 0,
                    'public_key' => $request['public_key'],
                    'access_token' => $request['access_token']
                ])
            ]);
        }

        Toastr::success(translate('payment settings updated!'));
        return back();
    }

    public function currency_index()
    {
        return view('branch-views.business-settings.currency-index');
    }

    public function currency_store(Request $request)
    {
        $request->validate([
            'currency_code' => 'required|unique:currencies',
        ]);

        Currency::create([
            "country" => $request['country'],
            "currency_code" => $request['currency_code'],
            "currency_symbol" => $request['symbol'],
            "exchange_rate" => $request['exchange_rate'],
        ]);
        Toastr::success(translate('Currency added successfully!'));
        return back();
    }

    public function currency_edit($id)
    {
        $currency = Currency::find($id);
        return view('branch-views.business-settings.currency-update', compact('currency'));
    }

    public function currency_update(Request $request, $id)
    {
        Currency::where(['id' => $id])->update([
            "country" => $request['country'],
            "currency_code" => $request['currency_code'],
            "currency_symbol" => $request['symbol'],
            "exchange_rate" => $request['exchange_rate'],
        ]);
        Toastr::success(translate('Currency updated successfully!'));
        return redirect('admin/business-settings/currency-add');
    }

    public function currency_delete($id)
    {
        Currency::where(['id' => $id])->delete();
        Toastr::success(translate('Currency removed successfully!'));
        return back();
    }

    public function terms_and_conditions()
    {
        $tnc = BusinessSetting::where(['branch_id' => auth('branch')->id(),'key' => 'terms_and_conditions'])->first();
        if ($tnc == false) {
            BusinessSetting::insert([
                'branch_id' => auth('branch')->id(),
                'key' => 'terms_and_conditions',
                'value' => '',
            ]);
        }
        return view('branch-views.business-settings.terms-and-conditions', compact('tnc'));
    }

    public function terms_and_conditions_update(Request $request)
    {
        BusinessSetting::where(['branch_id' => auth('branch')->id(),'key' => 'terms_and_conditions'])->update([
            'branch_id' => auth('branch')->id(),
            'value' => $request->tnc,
        ]);

        Toastr::success(translate('Terms and Conditions updated!'));
        return back();
    }

    public function privacy_policy()
    {
        $data = BusinessSetting::where(['branch_id' => auth('branch')->id(),'key' => 'privacy_policy'])->first();
        if ($data == false) {
            $data = [
                'branch_id' => auth('branch')->id(),
                'key' => 'privacy_policy',
                'value' => '',
            ];
            BusinessSetting::insert($data);
        }
        return view('branch-views.business-settings.privacy-policy', compact('data'));
    }

    public function privacy_policy_update(Request $request)
    {
        BusinessSetting::where(['branch_id' => auth('branch')->id(),'key' => 'privacy_policy'])->update([
            'branch_id' => auth('branch')->id(),
            'value' => $request->privacy_policy,
        ]);

        Toastr::success(translate('Privacy policy updated!'));
        return back();
    }

    public function about_us()
    {
        $data = BusinessSetting::where(['branch_id' => auth('branch')->id(),'key' => 'about_us'])->first();
        if ($data == false) {
            $data = [
                'branch_id' => auth('branch')->id(),
                'key' => 'about_us',
                'value' => '',
            ];
            BusinessSetting::insert($data);
        }
        return view('branch-views.business-settings.about-us', compact('data'));
    }

    public function about_us_update(Request $request)
    {
        BusinessSetting::where(['branch_id' => auth('branch')->id(),'key' => 'about_us'])->update([
            'branch_id' => auth('branch')->id(),
            'value' => $request->about_us,
        ]);

        Toastr::success(translate('About us updated!'));
        return back();
    }

    //return page
    public function return_page_index(Request $request)
    {
        $data = BusinessSetting::where(['branch_id' => auth('branch')->id(),'key' => 'return_page'])->first();

        if ($data == false) {
            $data = [
                'branch_id' => auth('branch')->id(),
                'key' => 'return_page',
                'value' => json_encode([
                    'status' => 0,
                    'content' => ''
                ]),
            ];
            BusinessSetting::insert($data);
        }

        return view('branch-views.business-settings.return_page-index',compact('data'));
    }

    public function return_page_update(Request $request)
    {
        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'return_page'], [
            'branch_id' => auth('branch')->id(),
            'key' => 'return_page',
            'value' => json_encode([
                'status' => $request['status']== 1 ? 1 : 0,
                'content' => $request['content']
            ]),
            'created_at' => now(),
            'updated_at' => now(),

        ]);

        Toastr::success(translate('Updated Successfully'));
        return back();
    }

     //refund page
    public function refund_page_index(Request $request)
    {
        $data = BusinessSetting::where(['branch_id' => auth('branch')->id(),'key' => 'refund_page'])->first();

        if ($data == false) {
            $data = [
                'branch_id' => auth('branch')->id(),
                'key' => 'refund_page',
                'value' => json_encode([
                    'status' => 0,
                    'content' => ''
                ]),
            ];
            BusinessSetting::insert($data);
        }
        return view('branch-views.business-settings.refund_page-index',compact('data'));
    }

    public function refund_page_update(Request $request)
    {
        //dd($request->all());
        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'refund_page'], [
            'branch_id' => auth('branch')->id(),
            'key' => 'refund_page',
            'value' => json_encode([
                'status' => $request['status']==1 ? 1 : 0,
                'content' => $request['content']==null ? null : $request['content']
            ]),
            'created_at' => now(),
            'updated_at' => now(),

        ]);


        Toastr::success(translate('Updated Successfully'));
        return back();
    }


     //cancellation page
     public function cancellation_page_index(Request $request)
     {
         $data = BusinessSetting::where(['branch_id' => auth('branch')->id(),'key' => 'cancellation_page'])->first();

         if ($data == false) {
             $data = [
                'branch_id' => auth('branch')->id(),
                 'key' => 'cancellation_page',
                 'value' => json_encode([
                    'status' => 0,
                    'content' => ''
                ]),
             ];
             BusinessSetting::insert($data);
         }

         return view('branch-views.business-settings.cancellation_page-index',compact('data'));
     }

     public function cancellation_page_update(Request $request)
     {
         DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'cancellation_page'], [
            'branch_id' => auth('branch')->id(),
             'key' => 'cancellation_page',
             'value' => json_encode([
                 'status' => $request['status']==1 ? 1 : 0,
                 'content' => $request['content']
             ]),
             'created_at' => now(),
             'updated_at' => now(),

         ]);

         Toastr::success(translate('Updated Successfully'));
         return back();
     }

    public function fcm_index()
    {
        if (BusinessSetting::where(['branch_id' => auth('branch')->id(),'key' => 'fcm_topic'])->first() == false) {
            BusinessSetting::insert([
                'branch_id' => auth('branch')->id(),
                'key' => 'fcm_topic',
                'value' => '',
            ]);
        }
        if (BusinessSetting::where(['branch_id' => auth('branch')->id(),'key' => 'fcm_project_id'])->first() == false) {
            BusinessSetting::insert([
                'branch_id' => auth('branch')->id(),
                'key' => 'fcm_project_id',
                'value' => '',
            ]);
        }
        if (BusinessSetting::where(['branch_id' => auth('branch')->id(),'key' => 'push_notification_key'])->first() == false) {
            BusinessSetting::insert([
                'branch_id' => auth('branch')->id(),
                'key' => 'push_notification_key',
                'value' => '',
            ]);
        }

        if (BusinessSetting::where(['branch_id' => auth('branch')->id(),'key' => 'order_pending_message'])->first() == false) {
            BusinessSetting::insert([
                'branch_id' => auth('branch')->id(),
                'key' => 'order_pending_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (BusinessSetting::where(['branch_id' => auth('branch')->id(),'key' => 'order_confirmation_msg'])->first() == false) {
            BusinessSetting::insert([
                'branch_id' => auth('branch')->id(),
                'key' => 'order_confirmation_msg',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (BusinessSetting::where(['branch_id' => auth('branch')->id(),'key' => 'order_processing_message'])->first() == false) {
            BusinessSetting::insert([
                'branch_id' => auth('branch')->id(),
                'key' => 'order_processing_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (BusinessSetting::where(['branch_id' => auth('branch')->id(),'key' => 'out_for_delivery_message'])->first() == false) {
            BusinessSetting::insert([
                'branch_id' => auth('branch')->id(),
                'key' => 'out_for_delivery_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (BusinessSetting::where(['branch_id' => auth('branch')->id(),'key' => 'order_delivered_message'])->first() == false) {
            BusinessSetting::insert([
                'branch_id' => auth('branch')->id(),
                'key' => 'order_delivered_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (BusinessSetting::where(['branch_id' => auth('branch')->id(),'key' => 'delivery_boy_assign_message'])->first() == false) {
            BusinessSetting::insert([
                'branch_id' => auth('branch')->id(),
                'key' => 'delivery_boy_assign_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (BusinessSetting::where(['branch_id' => auth('branch')->id(),'key' => 'delivery_boy_start_message'])->first() == false) {
            BusinessSetting::insert([
                'branch_id' => auth('branch')->id(),
                'key' => 'delivery_boy_start_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (BusinessSetting::where(['branch_id' => auth('branch')->id(),'key' => 'delivery_boy_delivered_message'])->first() == false) {
            BusinessSetting::insert([
                'branch_id' => auth('branch')->id(),
                'key' => 'delivery_boy_delivered_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (BusinessSetting::where(['branch_id' => auth('branch')->id(),'key' => 'customer_notify_message'])->first() == false) {
            BusinessSetting::insert([
                'branch_id' => auth('branch')->id(),
                'key' => 'customer_notify_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (BusinessSetting::where(['branch_id' => auth('branch')->id(),'key' => 'customer_notify_message_for_time_change'])->first() == false) {
            BusinessSetting::insert([
                'branch_id' => auth('branch')->id(),
                'key' => 'customer_notify_message_for_time_change',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }

        return view('branch-views.business-settings.fcm-index');
    }

    public function update_fcm(Request $request)
    {
        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'fcm_project_id'], [
            'branch_id' => auth('branch')->id(),
            'value' => $request['fcm_project_id'],
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'push_notification_key'], [
            'branch_id' => auth('branch')->id(),
            'value' => $request['push_notification_key'],
        ]);

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    public function update_fcm_messages(Request $request)
    {
//        return $request;
        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'order_pending_message'], [
            'branch_id' => auth('branch')->id(),
            'value' => json_encode([
                'status' => $request['pending_status'] == 1 ? 1 : 0,
                'message' => $request['pending_message'],
            ]),
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'order_confirmation_msg'], [
            'branch_id' => auth('branch')->id(),
            'value' => json_encode([
                'status' => $request['confirm_status'] == 1 ? 1 : 0,
                'message' => $request['confirm_message'],
            ]),
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'order_processing_message'], [
            'branch_id' => auth('branch')->id(),
            'value' => json_encode([
                'status' => $request['processing_status'] == 1 ? 1 : 0,
                'message' => $request['processing_message'],
            ]),
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'out_for_delivery_message'], [
            'branch_id' => auth('branch')->id(),
            'value' => json_encode([
                'status' => $request['out_for_delivery_status'] == 1 ? 1 : 0,
                'message' => $request['out_for_delivery_message'],
            ]),
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'order_delivered_message'], [
            'branch_id' => auth('branch')->id(),
            'value' => json_encode([
                'status' => $request['delivered_status'] == 1 ? 1 : 0,
                'message' => $request['delivered_message'],
            ]),
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'delivery_boy_assign_message'], [
            'branch_id' => auth('branch')->id(),
            'value' => json_encode([
                'status' => $request['delivery_boy_assign_status'] == 1 ? 1 : 0,
                'message' => $request['delivery_boy_assign_message'],
            ]),
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'delivery_boy_start_message'], [
            'branch_id' => auth('branch')->id(),
            'value' => json_encode([
                'status' => $request['delivery_boy_start_status'] == 1 ? 1 : 0,
                'message' => $request['delivery_boy_start_message'],
            ]),
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'delivery_boy_delivered_message'], [
            'branch_id' => auth('branch')->id(),
            'value' => json_encode([
                'status' => $request['delivery_boy_delivered_status'] == 1 ? 1 : 0,
                'message' => $request['delivery_boy_delivered_message'],
            ]),
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'customer_notify_message'], [
            'branch_id' => auth('branch')->id(),
            'value' => json_encode([
                'status' => $request['customer_notify_status'] == 1 ? 1 : 0,
                'message' => $request['customer_notify_message'],
            ]),
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'customer_notify_message_for_time_change'], [
            'branch_id' => auth('branch')->id(),
            'value' => json_encode([
                'status' => $request['customer_notify_status_for_time_change'] == 1 ? 1 : 0,
                'message' => $request['customer_notify_message_for_time_change'],
            ]),
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'returned_message'], [
            'branch_id' => auth('branch')->id(),
            'value' => json_encode([
                'status' => $request['returned_status'] == 1 ? 1 : 0,
                'message' => $request['returned_message'],
            ]),
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'failed_message'], [
            'branch_id' => auth('branch')->id(),
            'value' => json_encode([
                'status' => $request['failed_status'] == 1 ? 1 : 0,
                'message' => $request['failed_message'],
            ]),
        ]);

        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'canceled_message'], [
            'branch_id' => auth('branch')->id(),
            'value' => json_encode([
                'status' => $request['canceled_status'] == 1 ? 1 : 0,
                'message' => $request['canceled_message'],
            ]),
        ]);

        Toastr::success(translate('Message updated!'));
        return back();
    }

    public function map_api_settings() {
        return view('branch-views.business-settings.map-api');
    }

    public function update_map_api(Request $request) {
        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'map_api_key'], [
            'branch_id' => auth('branch')->id(),
            'value' => $request->map_api_key,
        ]);

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    public function recaptcha_index(Request $request)
    {
        return view('branch-views.business-settings.recaptcha-index');
    }

    public function recaptcha_update(Request $request)
    {
        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'recaptcha'], [
            'branch_id' => auth('branch')->id(),
            'key' => 'recaptcha',
            'value' => json_encode([
                'status' => $request['status'],
                'site_key' => $request['site_key'],
                'secret_key' => $request['secret_key']
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        Toastr::success(translate('Updated Successfully'));
        return back();
    }

    //app_setting_index
    public function app_setting_index()
    {
        return view('branch-views.business-settings.app-setting-index');
    }

    public function app_setting_update(Request $request)
    {
        if($request->platform == 'android')
        {
            DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'play_store_config'], [
                'branch_id' => auth('branch')->id(),
                'value' => json_encode([
                    'status' => $request['play_store_status'],
                    'link' => $request['play_store_link'],
                    'min_version' => $request['android_min_version'],

                ]),
            ]);

            Toastr::success(translate('Updated Successfully for Android'));
            return back();
        }

        if($request->platform == 'ios')
        {
            DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'app_store_config'], [
                'branch_id' => auth('branch')->id(),
                'value' => json_encode([
                    'status' => $request['app_store_status'],
                    'link' => $request['app_store_link'],
                    'min_version' => $request['ios_min_version'],
                ]),
            ]);
            Toastr::success(translate('Updated Successfully for IOS'));
            return back();
        }

        Toastr::error(translate('Updated failed'));
        return back();
    }

    public function firebase_message_config_index()
    {
        return view('branch-views.business-settings.firebase-config-index');
    }

    public function firebase_message_config(Request $request)
    {
        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'firebase_message_config'], [
            'key' => 'firebase_message_config',
            'branch_id' => auth('branch')->id(),
            'value' => json_encode([
                'apiKey' => $request['apiKey'],
                'authDomain' => $request['authDomain'],
                'projectId' => $request['projectId'],
                'storageBucket' => $request['storageBucket'],
                'messagingSenderId' => $request['messagingSenderId'],
                'appId' => $request['appId'],
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        self::firebase_message_config_file_gen();

        Toastr::success(translate('Config Updated Successfully'));
        return back();
    }

    function firebase_message_config_file_gen()
    {
        //configs
        $config=\App\CentralLogics\Helpers::get_branch_business_settings('firebase_message_config');
        $apiKey = $config['apiKey'] ?? '';
        $authDomain = $config['authDomain'] ?? '';
        $projectId = $config['projectId'] ?? '';
        $storageBucket = $config['storageBucket'] ?? '';
        $messagingSenderId = $config['messagingSenderId'] ?? '';
        $appId = $config['appId'] ?? '';

        try {
            $old_file = fopen("firebase-messaging-sw.js", "w") or die("Unable to open file!");

            $new_text = "importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js');\n";
            $new_text .= "importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js');\n";
            $new_text .= 'firebase.initializeApp({apiKey: "' . $apiKey . '",authDomain: "' . $authDomain . '",projectId: "' . $projectId . '",storageBucket: "' . $storageBucket . '", messagingSenderId: "' . $messagingSenderId . '", appId: "' . $appId . '"});';
            $new_text .= "\nconst messaging = firebase.messaging();\n";
            $new_text .= "messaging.setBackgroundMessageHandler(function (payload) { return self.registration.showNotification(payload.data.title, { body: payload.data.body ? payload.data.body : '', icon: payload.data.icon ? payload.data.icon : '' }); });";
            $new_text .= "\n";

            fwrite($old_file, $new_text);
            fclose($old_file);

        }catch (\Exception $exception) {}

    }

    // Social Media
    public function social_media()
    {
        return view('branch-views.business-settings.social-media');
    }

    public function fetch(Request $request)
    {
        if ($request->ajax()) {
            $data = SocialMedia::orderBy('id', 'desc')->get();
            return response()->json($data);
        }
    }

    public function social_media_store(Request $request)
    {
        try {
            SocialMedia::updateOrInsert([
                'name' => $request->get('name'),
            ], [
                'name' => $request->get('name'),
                'link' => $request->get('link'),
            ]);

            return response()->json([
                'success' => 1,
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'error' => 1,
            ]);
        }

    }

    public function social_media_edit(Request $request)
    {
        $data = SocialMedia::where('id', $request->id)->first();
        return response()->json($data);
    }

    public function social_media_update(Request $request)
    {
        $social_media = SocialMedia::find($request->id);
        $social_media->name = $request->name;
        $social_media->link = $request->link;
        $social_media->save();
        return response()->json();
    }

    public function social_media_delete(Request $request)
    {
        $br = SocialMedia::find($request->id);
        $br->delete();
        return response()->json();
    }

    public function social_media_status_update(Request $request)
    {
        SocialMedia::where(['id' => $request['id']])->update([
            'status' => $request['status'],
        ]);
        return response()->json([
            'success' => 1,
        ], 200);
    }

    public function web_footer_index()
    {
        return View('branch-views.business-settings.web-footer-index');
    }

    public function delivery_fee_setup()
    {
        return view('branch-views.business-settings.restaurant.delivery-fee');
    }

    public function update_delivery_fee(Request $request)
    {
        if($request->delivery_charge == null) {
            $request->delivery_charge = BusinessSetting::where(['branch_id' => auth('branch')->id(),'key' => 'delivery_charge'])->first()->value;
        }
        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'delivery_charge'], [
            'branch_id' => auth('branch')->id(),
            'value' => $request->delivery_charge,
        ]);

        if($request['min_shipping_charge'] == null) {
            $request['min_shipping_charge'] = Helpers::get_branch_business_settings('delivery_management')['min_shipping_charge'];
        }
        if($request['shipping_per_km'] == null) {
            $request['shipping_per_km'] = Helpers::get_branch_business_settings('delivery_management')['shipping_per_km'];
        }
        if ($request['shipping_status'] == 1) {
            $request->validate([
                'min_shipping_charge' => 'required',
                'shipping_per_km' => 'required',
            ],
                [
                    'min_shipping_charge.required' => 'Minimum shipping charge is required while shipping method is active',
                    'shipping_per_km.required' => 'Shipping charge per Kilometer is required while shipping method is active',
                ]);
        }


        DB::table('business_settings')->updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'delivery_management'], [
            'branch_id' => auth('branch')->id(),
            'value' => json_encode([
                'status' => $request['shipping_status'],
                'min_shipping_charge' => $request['min_shipping_charge'],
                'shipping_per_km' => $request['shipping_per_km'],
            ]),
        ]);

        Toastr::success(\App\CentralLogics\translate('Delivery_fee_updated_successfully'));
        return back();
    }

    public function main_branch_setup()
    {
        $branch = Branch::find(1);
        return view('branch-views.business-settings.restaurant.main-branch', compact('branch'));
    }

    public function social_login() {
        return view('branch-views.business-settings.social-login');
    }

    public function social_login_status(Request $request) {

        if ($request->btn_name == 'google_social_login') {
            BusinessSetting::updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'google_social_login'], [
                'branch_id' => auth('branch')->id(),
                'value' => $request->status,
                
            ]);
        }
        if ($request->btn_name == 'facebook') {
            BusinessSetting::updateOrInsert(['branch_id' => auth('branch')->id(),'key' => 'facebook_social_login'], [
                'branch_id' => auth('branch')->id(),
                'value' => $request->status,
            ]);
        }

        return response()->json(['status' => $request->status], 200);
    }

}
