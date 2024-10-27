<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Client;
use App\Mail\ForgetMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\RouteService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller {
    public function login()
    {
        return view('auth.login');
    }

    public function loginPost(Request $request)
    {
        $request->validate([
            'email' => 'required|exists:users,email',
            'password' => 'required'
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            return RouteService::get_view_with_role()->with('success', 'Login Successfully');
        }

        return back()->with('error', 'Please enter the correct password');
    }

    public function logout()
    {
        Auth::logout();
        session()->flush();

        return redirect()->route('login')->with('success', 'Logout Successfuly');
    }

    public function forget_password_view()
    {
        return view('auth.forget');
    }

    public function forget_password(Request $request)
    {
        $request->validate([
            'email' => 'required|exists:users,email',
        ]);

        $str_token = Str::random(60);

        $user = User::where('email', $request->email)->first();

        $user->remember_token = $str_token;
        $user->save();

        Mail::to($request->email)->send(new ForgetMail($str_token));

        return back()->with('success', 'Please check your email for reset password link');

    }

    public function reset_password_view(Request $request)
    {
        // dd($request->all());
        $user = User::where('remember_token', $request->token)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Token is invalid or expired');
        }
        return view('auth.reset');
    }

    public function reset_password(Request $request)
    {
        $request->validate([
            'remember_token' => 'required|exists:users,remember_token',
            'password' => [
                'required',
                Password::min(8)->letters()->symbols()->mixedCase()->numbers()
            ],
            'confirm_password' => [
                'required',
                Password::min(8)->letters()->symbols()->mixedCase()->numbers(),
                'same:password'
            ],
        ]);

        $user = User::where('remember_token', $request->remember_token)->whereNotNull('remember_token')->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Token is invalid or expired');
        }

        // dd("working");

        $user->password = Hash::make($request->password);
        $user->remember_token = null;
        $user->save();

        return redirect()->route('login')->with('success', 'Password reset successfully');
    }
}