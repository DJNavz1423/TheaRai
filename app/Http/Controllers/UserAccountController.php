<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserAccountController extends Controller
{
    public function index(){
        $user = auth()->user();   

        return view('admin.peopleManagement.myAccount', compact('user'));
    }

     public function update(Request $request){
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',

            'email' => [
                'required',
                'email',
                'regex:/^[a-zA-Z0-9._%+-]+@thearai\.com\.ph$/i',
                'unique:pgsql.laravel.users,email,' . $user->id,
            ],

            'phone_number' => [
                'nullable',
                'digits:11',
            ],
        ], [
            'email.regex' => 'The email must end with a valid @thearai.com.ph domain.',
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
}
