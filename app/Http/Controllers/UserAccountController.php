<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserAccountController extends Controller
{
    public function index(){
        $user = auth()->user();   

        $layout = match ($user->role) {
            'admin', 'dev', 'owner' => 'layouts.admin',
            'staff' => 'layouts.cashier',
            'waiter' => 'layouts.waiter',
            default => 'layouts.cashier',
        };

        return view('admin.peopleManagement.myAccount', compact('user', 'layout'));
    }

     public function update(Request $request){
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',

            'email' => [
                'required',
                'email',
                'regex:/^[a-zA-Z0-9._%+-]+@thearai\.com$/i',
                'unique:pgsql.laravel.users,email,' . $user->id,
            ],

            'phone_number' => [
                'nullable',
                'digits:11',
            ],
        ], [
            'email.regex' => 'The email must end with a valid @thearai.com domain.',
            'phone_number.digits' => 'Phone number must be exactly 11 digits.',
        ]);

        DB::table('laravel.users')
            ->where('id', $user->id)
            ->update([
                'name' => $validated['name'],
                'email' => strtolower($validated['email']),
                'phone_number' => $validated['phone_number'] ?: null,
                'updated_at' => now(),
            ]);

        $this->logActivity(
            'updated',
            'user',
            $user->id,
            "Updated personal information for user: {$validated['name']}"
        );

        return back()->with('success', 'Personal information updated successfully.');
    }

    public function updatePassword(Request $request){
        $user = auth()->user();

        $validated = $request->validate([
            'new_password' => [
                'required',
                'string',
                'min:10',
                'confirmed'
            ]
        ]);

        DB::table('laravel.users')
            ->where('id', $user->id)
            ->update([
                'password' => Hash::make(
                    $validated['new_password']
                ),
                'updated_at' => now(),
            ]);

        $this->logActivity(
            'updated',
            'user',
            $user->id,
            "Changed password for user: {$user->name}"
        );

        return back()->with(
            'success',
            'Password changed successfully.'
        );
    }
}
