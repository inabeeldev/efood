<?php

namespace App\Http\Controllers\Branch\Auth;

use App\CentralLogics\helpers;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Mail\WelcomeEmail;

class RegisterController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('guest:branch', ['except' => ['logout']]);
    // }
    
    
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
        $branch->save();
        $res = Mail::to($branch->email)->send(new WelcomeEmail($branch->name));

        Toastr::success(translate('Branch added successfully!'));
        return redirect()->route('branch.login');
    }
}
