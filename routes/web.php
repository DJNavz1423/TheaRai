<?php

use Illuminate\Support\Facades\Route; # import route class, handles url paths for website

#import my authcontroller so routes know which file to use for login logic
use App\Http\Controllers\AuthController; 
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PosController;

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
        ->name('admin.index');

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

    Route::get('/cashier/pos', function(){ //allowed if user is logged in and has a role of staff
        return view('pos.pos');
    })->middleware('role:admin|staff');

     # User Management Routes
    Route::get('/admin/users', [UserController::class, 'index'])
        ->middleware('role:admin')
        ->name('admin.peopleManagement.users');

    Route::post('/admin/users', [UserController::class, 'store'])
        ->middleware('role:admin')
        ->name('admin.users.store');

    /*
        menu routes
    */
    // The GET route to view the page (hits the index method)
    Route::get('/admin/menu', [MenuController::class, 'index'])
        ->middleware('role:admin')
        ->name('admin.menu.index');

    // The POST route when the modal form is submitted (hits the store method)
    Route::post('/admin/menu', [MenuController::class, 'store'])
        ->middleware('role:admin')
        ->name('admin.menu.store');

    /*
        POS routes
    */
    Route::get('/cashier/pos', [PosController::class, 'index'])
        ->middleware('role:admin|staff')
        ->name('cashier.pos');

    Route::post('/cashier/pos/order', [PosController::class, 'processOrder'])
        ->middleware('role:admin|staff')
        ->name('cashier.pos.order');
});


/*Route::get('/', function () {
    return view('welcome');
});*/
