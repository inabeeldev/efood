<?php

namespace App\Http\Controllers\Branch\Auth;

use App\CentralLogics\helpers;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Model\Branch;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;


class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:branch', ['except' => ['logout']]);
    }

    public function captcha($tmp)
    {
        $phrase = new PhraseBuilder;
        $code = $phrase->build(4);
        $builder = new CaptchaBuilder($code, $phrase);
        $builder->setBackgroundColor(220, 210, 230);
        $builder->setMaxAngle(25);
        $builder->setMaxBehindLines(0);
        $builder->setMaxFrontLines(0);
        $builder->build($width = 100, $height = 40, $font = null);
        $phrase = $builder->getPhrase();

        if(Session::has('default_captcha_code_branch')) {
            Session::forget('default_captcha_code_branch');
        }
        Session::put('default_captcha_code_branch', $phrase);
        header("Cache-Control: no-cache, must-revalidate");
        header("Content-Type:image/jpeg");
        $builder->output();
    }

    public function login()
    {
        return view('branch-views.auth.login');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        //recaptcha validation
        $recaptcha = Helpers::get_business_settings('recaptcha');
        if (isset($recaptcha) && $recaptcha['status'] == 1) {
            $request->validate([
                'g-recaptcha-response' => [
                    function ($attribute, $value, $fail) {
                        $secret_key = Helpers::get_business_settings('recaptcha')['secret_key'];
                        $response = $value;
                        $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $response;
                        $response = \file_get_contents($url);
                        $response = json_decode($response);
                        if (!$response->success) {
                            $fail(translate('ReCAPTCHA Failed'));
                        }
                    },
                ],
            ]);
        } else {
            if (strtolower($request->default_captcha_value) != strtolower(Session('default_captcha_code_branch'))) {
                return back()->withErrors(translate('Captcha Failed'));
            }
        }

        if(Session::has('default_captcha_code_branch')) {
            Session::forget('default_captcha_code_branch');
        }
        //end recaptcha validation

        if (auth('branch')->attempt([
            'email' => $request->email,
            'password' => $request->password,
            'status' => 1
        ], $request->remember)) {
            return redirect()->route('branch.dashboard');
        }

        return redirect()->back()->withInput($request->only('email', 'remember'))
            ->withErrors([translate('Credentials does not match.')]);
    }

    public function logout(Request $request)
    {
        auth()->guard('branch')->logout();
        return redirect()->route('branch.auth.login');
    }

    public function regitserForm(){
        return view('branch-views.auth.register');
    }


    public function regsiterRestaurant(Request $request)
    {

        $request->validate([
            'name' => 'required|max:255|unique:branches',
            'email' => 'required|max:255|unique:branches',
            'password' => 'required|min:8|max:255',
            'image' => 'required|max:255',
            'outdoor_slots' => 'required',
            'indoor_slots' => 'required',
        ], [
            'name.required' => translate('Name is required!'),
        ]);

        //image upload
        if (!empty($request->file('image'))) {
            $image_name = Helpers::upload('branch/', 'png', $request->file('image'));
        } else {
            $image_name = 'def.png';
        }

        $branch = new Branch();
        $branch->name = $request->name;
        $branch->email = $request->email;
        $branch->longitude = $request->longitude;
        $branch->latitude = $request->latitude;
        $branch->coverage = $request->coverage ? $request->coverage : 0;
        $branch->address = $request->address;
        $branch->password = bcrypt($request->password);
        $branch->image = $image_name;
        $branch->phone = $request->phone?? null;
        $branch->outdoor_slots = $request->outdoor_slots;
        $branch->indoor_slots = $request->indoor_slots;

        $branch->continent = $request->continent;
        // $branch->physical_address = $request->physical_address;
        $branch->country = $request->country;
        $branch->city = $request->city?? null;
        $branch->state = $request->state;
        $branch->zip_code = $request->zip_code;


        $branch->save();
        // Mail::to("vaharec805@camplvad.com")->send(new  WelcomeEmail($branch->name));

        Toastr::success(translate('Register successfully!'));
        return redirect()->route('branch.auth.login');
    }
}
