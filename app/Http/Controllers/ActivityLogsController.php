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
                'logs.id',
                'logs.action',
                'logs.model_type',
                'logs.description',
                'logs.created_at',
                'users.name as user_name'
            )
            ->orderBy('logs.created_at', 'desc')
            ->orderBy('logs.id', 'desc')
            ->simplePaginate(50)
            ->withQueryString();

        $modelTypes = DB::table('laravel.activity_logs')
            ->whereNull('deleted_at')
            ->whereNotNull('model_type')
            ->distinct()
            ->orderBy('model_type')
            ->pluck('model_type');

        $userNames = DB::table('laravel.activity_logs as logs')
            ->join('laravel.users as users', 'logs.user_id', '=', 'users.id')
            ->whereNull('logs.deleted_at')
            ->whereNotNull('users.name')
            ->distinct()
            ->orderBy('users.name')
            ->pluck('users.name');

        return view(
            'admin.activityLog.activityLog',
            compact('activityLogs', 'modelTypes', 'userNames')
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
