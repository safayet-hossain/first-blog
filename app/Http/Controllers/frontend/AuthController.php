<?php

namespace App\Http\Controllers\frontend;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register()
    {
        return view('frontend.pages.auth.register');
    }

    public function registore(Request $request)
    {

        $validate = Validator::make($request->all(), [

            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validate->fails()) {
            toastr()->warning('Invalid Information');
            return redirect()->route('webhome');
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => "user"
        ]);
        Log::debug('User Registration with Email:' . $request->email);
        toastr()->success('Registration Successful');
        return redirect()->route('webhome');
    }

    public function weblogin()
    {

        return view('frontend.pages.auth.login');
    }

    public function doweblogin(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validate->fails()) {
            Log::debug('User login validation failed with Email:' . $request->email);
            toastr()->warning('Invalid Information');
            return redirect()->back();
        }

        $credentials = $request->only('email', 'password');
        if (auth()->attempt($credentials)) {
            Log::debug('User login with Email:' . $request->email);
            toastr()->success('Login Successful');
            return redirect()->route('webhome');
        }
        Log::debug('User login failed with Email:' . $request->email);
        toastr()->warning('Invalid Information');
        return redirect()->back();
    }

    public function weblogout()
    {
        Log::debug('User logout with Email:' . auth()->user()->email);
        session()->flush();
        auth()->logout();
        toastr()->success('Logout Successful');
        return redirect()->route('webhome');
    }
}
