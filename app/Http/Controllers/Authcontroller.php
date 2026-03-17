<?php

namespace App\Http\Controllers; # Defines the folder location of this controller

use Illuminate\Http\Request; #import the request class para mareceive ang html form data
use Illuminate\Support\Facades\Auth; #import auth class to handle login/logout logic gets? :)

class Authcontroller extends Controller
{
    # show login form to user
    public function showLogin(){
        return view('auth.login'); #loads the login.blade.php from the views
    }

    # handle login html form submission
    public function login(Request $request){
        #checks if email and pass are provided and follow correct format
         $request->validate([
            'email' => ['required', 'email', 'regex:/^.+@thearai\.com\.ph$/i'], #email must present and a valid email format
            'password' => ['required'],], #password is required
            [ 'email.regex' => 'Invalid email format!']);

            $credentials = $request->only('email', 'password'); //put requests to credentials variable
            $remember = $request->has('remember'); //store remember checkbox value

            //check if email is valid and existing
            $userExists = \App\Models\User::where('email', $request->email)->exists();

            if(!$userExists){
                return back()->withErrors(['email' => 'This email is not registered!'])->withInput();
            }

        if(Auth::attempt($credentials, $remember)){ #try to log in the user using the provided email and passw
            //generate new session id for user | prevents session fixation attacks
            $request->session()->regenerate(); 
            $user = Auth::user(); #retrieves the current authenticated user data

            # redirected based on role|send them to correct dashboard
            return match($user->role){
                'admin' => redirect()->intended('/admin/dashboard'),
                'staff' => redirect()->intended('/staff/POS'),
                'cook' => redirect()->intended('/cook/kitchen'),
                default => redirect('/login'),
            };
        }
        # if login fails, send user back to login page with error message
        return back()->withErrors([
            'password' => 'Please check your password again'
        ])->onlyInput('email'); # keep email in input box so they dont type it again
    }
    
    public function logout(Request $request){//logout function
        Auth::logout(); # remove user auth info from the session
        $request->session()->invalidate(); #clear all data from the current user session
        $request->session()->regenerateToken();#refresh CSRF token to prevent CSRF attacks
        return redirect('/login'); #sends user back to login page after logout
    }
}
