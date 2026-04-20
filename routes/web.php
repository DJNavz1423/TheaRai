<?php

use Illuminate\Support\Facades\Route; # import route class, handles url paths for website

#import my authcontroller so routes know which file to use for login logic
use App\Http\Controllers\AuthController; 
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\AnalyticsController;

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

    Route::prefix('admin')->middleware('role:admin')->group(function(){
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.index');

        #inventory
        Route::get('/inventory', [IngredientController::class, 'index'])
        ->name('admin.inventory.index');

        Route::post('/inventory', [IngredientController::class, 'store']);

        Route::put('/inventory/{id}', [IngredientController::class, 'update']);

        Route::delete('/inventory/{id}', [IngredientController::class, 'destroy']);

        Route::post('/inventory/{id}/add-stock', [IngredientController::class, 'addStock']);
        
        Route::post('/inventory/{id}/reduce-stock', [IngredientController::class, 'reduceStock']);


         # User Management Routes
        Route::get('/users', [UserController::class, 'index'])
        ->name('admin.peopleManagement.users');

        Route::post('/users', [UserController::class, 'store'])
        ->name('admin.users.store');


        Route::get('/menu', [MenuController::class, 'index'])
        ->name('admin.menu.index');

        Route::post('/menu', [MenuController::class, 'store'])
        ->name('admin.menu.store');

        Route::get('/menu/{id}/branch-pricing', [MenuController::class, 'getBranchPricing']);
        
        Route::post('/menu/{id}/branch-pricing', [MenuController::class, 'updateBranchPricing']);




        #expense
        Route::get('/expenses', [ExpenseController::class, 'index'])
            ->name('admin.expenses');
        
        Route::post('/expenses/regular', [ExpenseController::class, 'storeRegular']);
        Route::post('/expenses/restock', [ExpenseController::class, 'storeRestock']);

        #archive
        Route::get('/archive', [ArchiveController::class, 'index'])
            ->name('admin.archive');

        Route::post('/archive/restore', [ArchiveController::class, 'restore']);
        Route::post('/archive/force-delete', [ArchiveController::class, 'forceDelete']);

        #analytics
        Route::get('/analytics', [AnalyticsController::class, 'index'])
            ->name('admin.analytics');


        #POS
        Route::get('/pos/select-branch', [PosController::class, 'selectBranch'])
            ->name('admin.pos.select');

        Route::get('/pos/set-branch/{id}', [PosController::class, 'setBranch'])
            ->name('admin.pos.set');
    });
    
    Route::get('/cashier/pos', function(){ //allowed if user is logged in and has a role of staff
        return view('pos.pos');
    })->middleware('role:admin|staff');
    

    /*
        POS routes
    */
    Route::get('/cashier/pos', [PosController::class, 'index'])
        ->middleware('role:admin|staff')
        ->name('cashier.pos');

    Route::post('/cashier/pos/order', [PosController::class, 'processOrder'])
        ->middleware('role:admin|staff')
        ->name('cashier.pos.order');

    Route::post('/cashier/pos/{id}/toggle-availability', [PosController::class, 'toggleAvailability'])
        ->middleware('role:admin|staff')
        ->name('cashier.pos.toggleAvailability');

    Route::get('/cashier/pos/receipt/{id}', [PosController::class, 'printReceipt'])
        ->name('cashier.pos.receipt');
});

