<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (!isset($_COOKIE['address_name'])) {
            \Redirect::to('set-location')->send();
        }
    }

    public function login()
    {
        if (\Auth::check()) {
            return redirect(route('profile'));
        } else {
            return view('auth.loginuser');
        }
    }

    public function signup()
    {
        if (\Auth::check()) {
            return redirect(route('profile'));
        } else {
            return view('auth.signup');
        }
    }

    public function socialsignup(Request $request)
    {

        $phoneNumber = $request->input('phoneNumber', '');
        $uuid = $request->input('uuid', '');
        $firstName = $request->input('firstName', '');
        $lastName = $request->input('lastName', '');
        $email = $request->input('email', '');
        $photoURL = $request->input('photoURL', '');
        $createdAt = $request->input('createdAt', '');

        if (\Auth::check()) {
            return redirect(route('profile'));
        } else {
            return view('auth.socialsignup', compact('uuid','phoneNumber', 'firstName', 'lastName', 'email', 'photoURL', 'createdAt'));
        }
    }

    public function forgotPassword()
    {
        if (\Auth::check()) {
            return redirect(route('profile'));
        } else {

            return view('auth.forgot_password');
        }
    }
}
