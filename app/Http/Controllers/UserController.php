<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(){
        $currentUserId = auth()->id();

        $users = DB::table('laravel.users')
            ->leftJoin('laravel.branches', 'users.branch_id', '=', 'branches.id')
            ->select('users.*', 'branches.name as branch_name')
            ->where('users.id', '!=', $currentUserId)
            ->orderBy('users.created_at', 'desc')
            ->get();
            
        $branches = DB::table('laravel.branches')->orderBy('name')->get();

        return view('admin.peopleManagement.users', compact('users', 'branches'));
    }

    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required', 
                'email', 
                'unique:pgsql.laravel.users,email', 
                'regex:/^[a-zA-Z0-9._%+-]+@thearai\.com\.ph$/i'
            ], 
            'password' => 'required|min:10',
            'role' => 'required|in:admin,staff',
            'branch_id' => 'exclude_if:role,admin|required|exists:pgsql.laravel.branches,id'
        ], [
            'email.regex' => 'The email must end with a valid @thearai.com.ph domain.'
        ]);

        $userId = DB::table('laravel.users')->insertGetId([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'branch_id' => $validated['role'] === 'admin' ? null : $validated['branch_id'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->logActivity('created', 'user', $userId, "Registered new user: {$validated['name']}");

        return redirect()->back()->with('success', 'User added successfully!');
    }
}