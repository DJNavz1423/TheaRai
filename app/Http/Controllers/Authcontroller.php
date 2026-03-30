<?php

/* This PHP class is an AuthController that handles user authentication, login, and logout
functionality in a Laravel application. */

namespace App\Http\Controllers; 

use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth; 

class Authcontroller extends Controller
{
    public function showLogin(){
        return view('auth.login'); 
    }

    public function login(Request $request){
         $request->validate([
            'email' => ['required', 'email', 'regex:/^.+@thearai\.com\.ph$/i'], 
            'password' => ['required'],], 
            [ 'email.regex' => 'Invalid email format!']);

            $credentials = $request->only('email', 'password'); 
            $remember = $request->has('remember'); 

            $userExists = \App\Models\User::where('email', $request->email)->exists();

            if(!$userExists){
                return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
            }

        if(Auth::attempt($credentials, $remember)){ 
            $request->session()->regenerate(); 
            $user = Auth::user(); 
            return match($user->role){
                'admin' => redirect()->intended('/admin/dashboard'),
                'staff' => redirect()->intended('/cashier/pos'),
                default => redirect('/login'),
            };
        }
    
        return back()->withErrors([
            'password' => 'Please check your password again'
        ])->onlyInput('email'); 
    }
    
    public function logout(Request $request){
        Auth::logout(); 
        $request->session()->invalidate(); 
        $request->session()->regenerateToken();
        return redirect('/login'); 
    }

    /**
     * The above PHP code defines functions for user login, validation, authentication, and logout in a
     * Laravel application.
     * 
     * return the `showLogin` function returns a view for the login page, the `login` function handles
     * the login process, and the `logout` function handles the logout process.
     */
}
