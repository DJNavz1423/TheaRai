<?php

use Illuminate\Support\Facades\Route; # import route class, handles url paths for website

#import my authcontroller so routes know which file to use for login logic
use App\Http\Controllers\AuthController; 
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\UserController;

Route::get('/', function(){ #if someone visits the main website, automatically send to login page
    return redirect('/login');
});

# display login page when user visits /login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

#process data when user click the login button
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:4,1');

#process logout request when user clicks logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth');

// auth middleware | check if user is logged in before letting them inside this group
Route::middleware(['auth'])->group(function(){

    #dashboard route
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:admin')
        ->name('admin.dashboard');

    #Inventory routes
      // 1. READ (The list)
    Route::get('/admin/inventory', [IngredientController::class, 'index'])
        ->middleware('role:admin')
        ->name('admin.inventory.index');

    // 2. CREATE (Saving the new ingredient)
    Route::post('/admin/inventory', [IngredientController::class, 'store'])->middleware('role:admin');

    // 3. UPDATE (Saving edits)
    Route::put('/admin/inventory/{id}', [IngredientController::class, 'update'])->middleware('role:admin');

    // 4. DELETE (Removing an item)
    Route::delete('/admin/inventory/{id}', [IngredientController::class, 'destroy'])->middleware('role:admin');

    Route::get('/staff/pos', function(){ //allowed if user is logged in and has a role of staff
        return view('staff.pos');
    })->middleware('role:admin|staff');

    Route::get('/cook/dashboard', function(){ //allowed if user is logged in and has a role of cook
        return view('cook.dashboard');
    })->middleware('role:cook');

     # User Management Routes
    Route::get('/admin/users', [UserController::class, 'index'])
        ->middleware('role:admin')
        ->name('admin.peopleManagement.users');

    Route::post('/admin/users', [UserController::class, 'store'])
        ->middleware('role:admin')
        ->name('admin.users.store');
});


/*Route::get('/', function () {
    return view('welcome');
});*/
