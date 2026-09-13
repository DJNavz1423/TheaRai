<?php

use Illuminate\Support\Facades\Route; # import route class, handles url paths for website
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;

#import my authcontroller so routes know which file to use for login logic
use App\Http\Controllers\AuthController; 

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalyticsController;


use App\Http\Controllers\IngredientController;
use App\Http\Controllers\Category_UnitsController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\UserAccountController;

use App\Http\Controllers\MenuController;
use App\Http\Controllers\MenuCategoryController;
use App\Http\Controllers\TableController;

use App\Http\Controllers\ActivityLogsController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\ExpenseController;

use App\Http\Controllers\PosController;
use App\Http\Controllers\QrMenuController;
use App\Http\Controllers\QrOrderController;

use App\Http\Controllers\TransactionController;

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

    Route::get('/api/qr-orders/notifications', [QrOrderController::class, 'getNotifications'])
        ->name('qr.orders.notifications');


    Route::prefix('admin')->middleware('role:admin|dev|owner')->group(function(){
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.index');

        #inventory
        Route::get('/inventory', [IngredientController::class, 'index'])
            ->name('admin.inventory.index');

        Route::post('/inventory', [IngredientController::class, 'store']);

        Route::put('/inventory/{id}', [IngredientController::class, 'update']);

        Route::delete('/inventory/{id}', [IngredientController::class, 'destroy']);

        Route::post('/inventory/{id}/add-stock', [IngredientController::class, 'addStock']);
        
        Route::post('/inventory/{id}/reduce-stock', [IngredientController::class, 'reduceStock']);

        Route::get('/inventory/categories_units', [Category_UnitsController::class, 'index'])
            ->name('admin.inventory.categories_units');

        Route::post('/inventory/categories_units', [Category_UnitsController::class, 'store'])
            ->name('admin.inventory.categories_units.store');

        Route::put('/inventory/categories_units/{id}', [Category_UnitsController::class, 'update'])
            ->name('admin.inventory.categories_units.update');

        Route::delete('/inventory/categories_units/{id}', [Category_UnitsController::class, 'destroy'])
            ->name('admin.inventory.categories_units.destroy');
        


         # User Management Routes
        Route::get('/users', [UserController::class, 'index'])
            ->name('admin.peopleManagement.users');

        Route::post('/users', [UserController::class, 'store'])
            ->name('admin.users.store');

        Route::put('/users/{id}', [UserController::class, 'update']);

        Route::delete('/users/{id}', [UserController::class, 'destroy'])
            ->name('admin.users.destroy');

        Route::put('/users/{id}/password',[UserController::class, 'updatePassword']);

        # Menu Management Routes

        Route::get('/menu', [MenuController::class, 'index'])
            ->name('admin.menu.index');

        Route::post('/menu', [MenuController::class, 'store'])
            ->name('admin.menu.store');

        Route::get('/menu/{id}/edit', [MenuController::class, 'edit']);

        Route::put('/menu/{id}', [MenuController::class, 'update']);

        Route::get('/menu/{id}/branch-pricing', [MenuController::class, 'getBranchPricing']);
        
        Route::post('/menu/{id}/branch-pricing', [MenuController::class, 'updateBranchPricing']);

        Route::delete('/menu/{id}', [MenuController::class, 'destroy'])
            ->name('menu.destroy');

        Route::get('/menu/categories', [MenuCategoryController::class, 'index'])
            ->name('admin.menu.categories');

        Route::post('/menu/categories', [MenuCategoryController::class, 'store'])
            ->name('admin.menu.categories.store');

        Route::put('/menu/categories/{id}', [MenuCategoryController::class, 'update'])
            ->name('admin.menu.categories.update');

        Route::delete('/menu/categories/{id}', [MenuCategoryController::class, 'destroy'])
            ->name('admin.menu.categories.destroy');


        #tables
        Route::get('/tables', [TableController::class, 'index'])
            ->name('admin.tables');

        Route::post('/tables', [TableController::class, 'store'])
            ->name('admin.menu.tables.store');

        Route::delete('/tables/{id}', [TableController::class, 'destroy'])
            ->name('admin.menu.tables.destroy');

        Route::put('/tables/{id}', [TableController::class, 'update'])
            ->name('admin.menu.tables.update');

        #expense
        Route::get('/expenses', [ExpenseController::class, 'index'])
            ->name('admin.expenses');
        
        Route::post('/expenses/regular', [ExpenseController::class, 'storeRegular']);
        Route::post('/expenses/restock', [ExpenseController::class, 'storeRestock']);

        Route::put('/expenses/{id}', [ExpenseController::class, 'update'])
            ->name('admin.expenses.update');

        Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy'])
            ->name('admin.expenses.destroy');

        #activity log
        Route::get('/activity-logs', [ActivityLogsController::class, 'index'])
            ->name('admin.settings.logs');

        Route::put('/activity-logs/{id}', [ActivityLogsController::class, 'update'])
            ->name('admin.settings.logs.update');

        Route::delete('/activity-logs/{id}', [ActivityLogsController::class, 'destroy'])
            ->name('admin.settings.logs.destroy');

        #archive
        Route::get('/archive', [ArchiveController::class, 'index'])
            ->name('admin.settings.archive');

        Route::post('/archive/restore', [ArchiveController::class, 'restore']);
        Route::delete('/archive/force-delete', [ArchiveController::class, 'forceDelete']);

        #analytics
        Route::get('/analytics', [AnalyticsController::class, 'index'])
            ->name('admin.analytics');


        #POS
        Route::get('/pos/select-branch', [PosController::class, 'selectBranch'])
            ->name('admin.pos.select'); 

        Route::get('/pos/set-branch/{id}', [PosController::class, 'setBranch'])
            ->name('admin.pos.set');

        Route::get('/qr-orders', [QrOrderController::class, 'index'])
            ->name('admin.qr.orders');

        Route::post('/qr-orders/{id}/serve', [QrOrderController::class, 'serve'])
            ->name('admin.qr.orders.serve');

        #transactions
        Route::get('/transactions', [TransactionController::class, 'index'])
            ->name('admin.transactions.index');
    });


    
    Route::prefix('cashier')->group(function(){
        Route::get('/pos', [PosController::class, 'index'])
            ->middleware('role:admin|dev|owner|staff')
            ->name('cashier.pos');

        Route::post('/pos/order', [PosController::class, 'processOrder'])
            ->middleware('role:admin|dev|owner|staff')
            ->name('cashier.pos.order');

        Route::post('/pos/{id}/toggle-availability', [PosController::class, 'toggleAvailability'])
            ->middleware('role:admin|dev|owner|staff')
            ->name('cashier.pos.toggleAvailability');

        Route::get('/pos/receipt/{id}', [PosController::class, 'printReceipt'])
            ->name('cashier.pos.receipt');

        Route::get('/qr-orders', [QrOrderController::class, 'index'])
            ->name('cashier.qr.orders');

        Route::post('/qr-orders/{id}/serve', [QrOrderController::class, 'serve'])
            ->name('cashier.qr.orders.serve');

        Route::get('/transactions', [TransactionController::class, 'index'])
            ->name('cashier.transactions.index');
    });
    
    
});

 #QR menu routes
    Route::get('/qr-menu', [QrMenuController::class, 'index'])
        ->name('qr.menu');

    Route::post('/qr-menu/checkout', [QrMenuController::class, 'checkout'])
        ->name('qr.checkout');

    Route::get('/qr-menu/success', [QrMenuController::class, 'success']);

# User Account Routes
    Route::get('/my-account', [UserAccountController::class, 'index'])
        ->name('settings.myAccount');

    Route::put('/my-account', [UserAccountController::class, 'update'])
        ->name('settings.myAccount.update');

    

Route::post('/heartbeat', function () {

    auth()->user()->update([
        'last_seen_at' => now(),
    ]);

    return response()->noContent();

})->middleware('auth');


################## indexeddb routes for offline pos functionality ##################
Route::post('/offline/register', function () {

    if (!auth()->check()) {
        return response()->json([
            'success' => false
        ], 401);
    }

    $user = auth()->user();

    return response()->json([
        'success' => true,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => strtolower($user->email),
            'role' => $user->role,
            'branch_id' => $user->branch_id,
            'sync_token' => Crypt::encryptString(json_encode([
                'user_id' => $user->id,
                'role' => $user->role,
            ])),
        ]
    ]);
})->middleware('auth');

### OFFLINE POS ROUTES ##
Route::get('/offline-select-branch', function () {
    return view('admin.pos.offline-select-branch');
})  ->name('offline.selectBranch');

Route::get('/offline/data',
    [PosController::class, 'offlineData']
)   ->name('offline.data');

Route::post('/offline/orders/sync', [PosController::class, 'syncOfflineOrders'])
    ->name('offline.orders.sync');

Route::post('/offline/session', function (Request $request) {
    try {
        $claims = json_decode(
            Crypt::decryptString($request->input('sync_token')),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $user = \App\Models\User::findOrFail($claims['user_id']);

        if ($user->role !== $claims['role']) {
            return response()->json(['success' => false], 401);
        }

        auth()->login($user);
        $request->session()->regenerate();

        return response()->json(['success' => true]);
    } catch (\Throwable $error) {
        return response()->json(['success' => false], 401);
    }
})->name('offline.session');

Route::get('/offline-pos', function () {
    return view('pos.offline-pos');
}) ->name('offline.pos');