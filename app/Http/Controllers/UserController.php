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
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:laravel.users,email',
            'password' => 'required|min:10',
            'role' => 'required'
        ]);

        return redirect()->back()->with('success', 'User added successfully!');
    }
}
