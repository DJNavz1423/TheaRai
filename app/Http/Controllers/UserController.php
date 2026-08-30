<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(){
        $currentUserId = auth()->id();
        $currentUserRole = auth()->user()->role;

        $usersQuery = DB::table('laravel.users')
            ->leftJoin('laravel.branches', 'users.branch_id', '=', 'branches.id')
            ->select('users.*', 'branches.name as branch_name')
            ->where('users.id', '!=', $currentUserId)
            ->whereNull('users.deleted_at');
        
        if (!in_array($currentUserRole, ['dev', 'owner'])) {
            $usersQuery->whereNotIn('users.role', ['dev', 'owner']);
        }

        $users = $usersQuery
            ->orderBy('users.created_at', 'desc')
            ->get();
            
        $branches = DB::table('laravel.branches')->orderBy('name')->get();

        return view('admin.peopleManagement.users', compact('users', 'branches'));
    }

    public function store(Request $request){

        $currentUserRole = auth()->user()->role;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required', 
                'email', 
                'unique:pgsql.laravel.users,email', 
                'regex:/^[a-zA-Z0-9._%+-]+@thearai\.com\.ph$/i'
            ], 
            'phone_number' => [
                'nullable',
                'digits:11',
            ],
            'password' => 'required|min:10',
            'role' => 'required|string|in:admin,staff,dev,owner',
            'branch_id' => ['nullable', 'integer', 'exists:pgsql.laravel.branches,id'],
        ], [
            'email.regex' => 'The email must end with a valid @thearai.com.ph domain.',
            'phone_number.digits' => 'The phone number must be exactly 11 digits.',
        ]);

        if (in_array($validated['role'], ['dev', 'owner']) && !in_array($currentUserRole, ['dev', 'owner'])) {
            return back()->with(
                'error',
                'You are not authorized to create this type of account.'
            );
        }

        $branchId = in_array($validated['role'], ['admin', 'dev', 'owner']) ? null : $validated['branch_id'];

        $userId = DB::table('laravel.users')->insertGetId([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'phone_number' => $validated['phone_number'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'branch_id' => $branchId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->logActivity('created', 'user', $userId, "Registered new user: {$validated['name']} ({$validated['role']})");

        return redirect()->back()->with('success', 'User added successfully!');
    }

    public function update(Request $request, $id) {
        $currentUserRole = auth()->user()->role;

        if (in_array($currentUserRole, ['dev', 'owner'])) {

            $allowedRoles = 'admin,staff,dev,owner';

        } else {

            $allowedRoles = 'admin,staff';
        }

        $targetUser = DB::table('laravel.users')
            ->where('id', $id)
            ->first();

        if (!$targetUser) {
            return back()->with(
                'error',
                'User not found.'
            );
        }

        if (
            in_array($targetUser->role, ['dev', 'owner']) &&
            !in_array($currentUserRole, ['dev', 'owner'])
        ) {
            return back()->with(
                'error',
                'You are not authorized to modify this account.'
            );
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',

            'email' => [
                'required',
                'email',
                'regex:/^[a-zA-Z0-9._%+-]+@thearai\.com\.ph$/i',
                'unique:pgsql.laravel.users,email,' . $id,
            ],
            'phone_number' => [
                'nullable',
                'digits:11',
            ],
            'role' => 'required|in:' . $allowedRoles,
            'branch_id' =>
                'exclude_if:role,admin'
                . '|exclude_if:role,dev'
                . '|exclude_if:role,owner'
                . '|required|exists:pgsql.laravel.branches,id',
        ], [
            'email.regex' => 'The email must end with a valid @thearai.com.ph domain.'
        ]);

        DB::table('laravel.users')
            ->where('id', $id)
            ->update([
                'name' => $validated['name'],
                'email' => strtolower($validated['email']),
                'phone_number' => $validated['phone_number'] ?? null,
                'role' => $validated['role'],
                'branch_id' => in_array($validated['role'],['admin', 'dev', 'owner']) ? null : $validated['branch_id'],
                'updated_at' => now(),
            ]);

        $this->logActivity('updated', 'user', $id, "Updated details for user: {$validated['name']}");

        return back()->with(
            'success',
            'User updated successfully.'
        );
    }

    public function updatePassword(Request $request, $id) {
        $validated = $request->validate([
            'new_password' => [
                'required',
                'string',
                'min:10',
                'confirmed'
            ]
        ]);

         $user = DB::table('laravel.users')
            ->where('id', $id)
            ->first();

        if (!$user) {
            return back()->with(
                'error',
                'User not found.'
            );
        }

        if (
            in_array($user->role, ['dev', 'owner']) &&
            !in_array(auth()->user()->role, ['dev', 'owner'])
        ) {
            return back()->with(
                'error',
                'You are not authorized to modify this account.'
            );
        }

        DB::table('laravel.users')
            ->where('id', $id)
            ->update([
                'password' => Hash::make(
                    $validated['new_password']
                ),
                'updated_at' => now()
            ]);

        $this->logActivity(
            'updated',
            'user',
            $id,
            "Changed password for user: {$user->name}"
        );

        return back()->with(
            'success',
            'Password updated successfully.'
        );
    }

    public function destroy($id){
        $user = DB::table('laravel.users')
            ->where('id', $id)
            ->first();

        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        if (
            in_array($user->role, ['dev', 'owner']) &&
            !in_array(auth()->user()->role, ['dev', 'owner'])
        ) {
            return back()->with(
                'error',
                'You are not authorized to remove this account.'
            );
        }

        DB::table('laravel.users')
            ->where('id', $id)
            ->update([
                'deleted_at' => now()
            ]);

        $this->logActivity('archived', 'user', $id, "Moved user to trash: {$user->name}");

        return back()->with(
            'success',
            'User moved to trash successfully.'
        );
    }
}