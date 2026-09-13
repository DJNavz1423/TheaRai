<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller {
    public function index()
    {
        $user = auth()->user();

        $isAdminLevel =
            in_array($user->role, ['admin', 'dev', 'owner']);

        /*
        |--------------------------------------------------------------------------
        | Branches
        |--------------------------------------------------------------------------
        */

        if ($isAdminLevel) {

            $branches = DB::table('laravel.branches')
                ->orderBy('name')
                ->get();

        } else {

            $branches = DB::table('laravel.branches')
                ->where('id', $user->branch_id)
                ->get();
        }


        /*
        |--------------------------------------------------------------------------
        | Transactions
        |--------------------------------------------------------------------------
        */

        $query = DB::table('laravel.orders as orders')
            ->leftJoin(
                'laravel.branches as branches',
                'orders.branch_id',
                '=',
                'branches.id'
            )
            ->select(
                'orders.id',
                'orders.receipt_no',
                'orders.total_amount',
                'orders.cash_tendered',
                'orders.change_amount',
                'orders.payment_method',
                'orders.reference_number',
                'orders.payment_status',
                'orders.status',
                'orders.created_at',
                'orders.branch_id',
                'branches.name as branch_name'
            )
            ->orderByDesc('orders.created_at');

        /*
        |--------------------------------------------------------------------------
        | Staff can only see their assigned branch
        |--------------------------------------------------------------------------
        */

        if (!$isAdminLevel) {
            $query->where(
                'orders.branch_id',
                $user->branch_id
            );
        }

        $transactions = $query->get();


        /*
        |--------------------------------------------------------------------------
        | Payment methods for filter
        |--------------------------------------------------------------------------
        */

        $paymentMethods = $transactions
            ->pluck('payment_method')
            ->filter()
            ->unique()
            ->values();


        /*
        |--------------------------------------------------------------------------
        | Payment statuses for filter
        |--------------------------------------------------------------------------
        */

        $paymentStatuses = $transactions
            ->pluck('payment_status')
            ->filter()
            ->unique()
            ->values();


        /*
        |--------------------------------------------------------------------------
        | Layout
        |--------------------------------------------------------------------------
        */

        $layout = $isAdminLevel
            ? 'layouts.admin'
            : 'layouts.cashier';


        return view(
            'admin.transaction.transaction',
            compact(
                'transactions',
                'branches',
                'paymentMethods',
                'paymentStatuses',
                'layout',
                'isAdminLevel'
            )
        );
    }
}