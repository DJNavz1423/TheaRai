<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ActivityLogsController extends Controller
{
    public function index(): View{
        $activityLogs = DB::table('laravel.activity_logs as logs')
            ->leftJoin('laravel.users as users', 'logs.user_id', '=', 'users.id')
            ->whereNull('logs.deleted_at')
            ->select(
                'logs.*',
                'users.name as user_name'
            )
            ->orderBy('logs.created_at', 'desc')
            ->get();

        return view(
            'admin.activityLog.activityLog',
            compact('activityLogs')
        );
    }

     public function update(Request $request, $id){
        $validated = $request->validate([
            'description' => 'required|string|max:1000',
        ]);

        $log = DB::table('laravel.activity_logs')
            ->where('id', $id)
            ->first();

        if (!$log) {
            return back()->with('error', 'Activity log not found.');
        }

        DB::table('laravel.activity_logs')
            ->where('id', $id)
            ->update([
                'description' => $validated['description'],
            ]);

        return back()->with(
            'success',
            'Activity log description updated successfully.'
        );
    }

    public function destroy($id){
        $log = DB::table('laravel.activity_logs')
            ->where('id', $id)
            ->first();

        if (!$log) {
            return back()->with('error', 'Activity log not found.');
        }

        DB::table('laravel.activity_logs')
            ->where('id', $id)
            ->update([
                'deleted_at' => now(),
            ]);

        return back()->with(
            'success',
            'Activity log moved to trash successfully.'
        );
    }
}
