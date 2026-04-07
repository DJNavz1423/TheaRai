<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;


abstract class Controller
{
    protected function logActivity($action, $modelType, $modelId, $description){
        DB::table('laravel.activity_logs')->insert([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'description' => $description,
            'created_at' => now()
        ]);
    }
}
