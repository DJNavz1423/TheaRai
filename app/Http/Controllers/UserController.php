<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(){
        $users = User::all();
        return view('admin.peopleManagement.users', compact('users'));
    }

    public function store(Request $request){
        // 1. Validate the data (using your working schema format for the unique check)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:pgsql.laravel.users,email', 
            'password' => 'required|min:10',
            'role' => 'required'
        ]);

        // 2. ACTUALLY STORE THE DATA IN THE DATABASE
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']), // CRITICAL: Passwords must be hashed!
            'role' => $validated['role'],
        ]);

        // 3. Redirect back with success message
        return redirect()->back()->with('success', 'User added successfully!');
    }
}
