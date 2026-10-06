<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockAuditController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'branch_id' => 'nullable|integer|exists:pgsql.laravel.branches,id',
            'source_type' => 'nullable|in:all,new_ingredient,restock,order,manual_reduction,unclassified',
            'search' => 'nullable|string|max:100',
        ]);

        /*
         * Default report period = Today in Philippine time.
         */
        $today = Carbon::now('Asia/Manila');

        $dateFrom = $request->input(
            'date_from',
            $today->format('Y-m-d')
        );

        $dateTo = $request->input(
            'date_to',
            $today->format('Y-m-d')
        );

        $startDate = Carbon::createFromFormat(
            'Y-m-d',
            $dateFrom,
            'Asia/Manila'
        )->startOfDay()->utc();

        $endDate = Carbon::createFromFormat(
            'Y-m-d',
            $dateTo,
            'Asia/Manila'
        )->endOfDay()->utc();

        /*
         * Main stock audit query.
         */
        $baseQuery = DB::table('laravel.stock_logs as stock_logs')

            ->leftJoin(
                'laravel.ingredients as ingredients',
                'stock_logs.ingredient_id',
                '=',
                'ingredients.id'
            )

            ->leftJoin(
                'laravel.units as units',
                'ingredients.primary_unit_id',
                '=',
                'units.id'
            )

            ->leftJoin(
                'laravel.branches as branches',
                'stock_logs.branch_id',
                '=',
                'branches.id'
            )

            ->leftJoin(
                'laravel.orders as orders',
                'stock_logs.order_id',
                '=',
                'orders.id'
            )

            ->leftJoin(
                'laravel.expenses as expenses',
                'stock_logs.expense_id',
                '=',
                'expenses.id'
            )

            ->whereBetween(
                'stock_logs.created_at',
                [$startDate, $endDate]
            )

            /*
             * Order stock consumption should represent
             * successful paid orders only.
             */
            ->where(function ($query) {

                $query->where(
                    'stock_logs.source_type',
                    '!=',
                    'order'
                )

                ->orWhere(function ($query) {

                    $query->where(
                        'stock_logs.source_type',
                        'order'
                    )
                    ->where(
                        'orders.payment_status',
                        'paid'
                    );
                })

                ->orWhere(function ($query) {

                    $query->whereNull(
                        'stock_logs.source_type'
                    );
                });
            });

        /*
         * Branch filter.
         */
        if ($request->filled('branch_id')) {

            $baseQuery->where(
                'stock_logs.branch_id',
                $request->branch_id
            );
        }

        /*
         * Source filter.
         */
        $sourceType = $request->input('source_type', 'all');

        if ($sourceType === 'unclassified') {

            $baseQuery->whereNull(
                'stock_logs.source_type'
            );

        } elseif ($sourceType !== 'all') {

            $baseQuery->where(
                'stock_logs.source_type',
                $sourceType
            );
        }

        /*
         * Search.
         */
        if ($request->filled('search')) {

            $search = trim($request->search);

            $baseQuery->where(function ($query) use ($search) {

                $query
                    ->where(
                        'stock_logs.ingredient_name',
                        'ilike',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'orders.receipt_no',
                        'ilike',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'expenses.description',
                        'ilike',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'branches.name',
                        'ilike',
                        '%' . $search . '%'
                    );
            });
        }

        /*
         * Summary for the entire selected period,
         * not just the current pagination page.
         */
        $summary = (clone $baseQuery)
            ->selectRaw('
                COUNT(stock_logs.id) AS total_logs,

                COALESCE(
                    SUM(
                        CASE
                            WHEN stock_logs.quantity_change > 0
                            THEN stock_logs.quantity_change
                            ELSE 0
                        END
                    ),
                    0
                ) AS total_stock_in,

                COALESCE(
                    SUM(
                        CASE
                            WHEN stock_logs.quantity_change < 0
                            THEN ABS(stock_logs.quantity_change)
                            ELSE 0
                        END
                    ),
                    0
                ) AS total_stock_out,

                COUNT(
                    DISTINCT CASE
                        WHEN stock_logs.source_type = \'new_ingredient\'
                        THEN stock_logs.ingredient_id
                    END
                ) AS new_ingredients,

                COUNT(
                    DISTINCT CASE
                        WHEN stock_logs.source_type = \'restock\'
                        THEN stock_logs.expense_id
                    END
                ) AS restocks,

                COUNT(
                    DISTINCT CASE
                        WHEN stock_logs.source_type = \'order\'
                        THEN stock_logs.order_id
                    END
                ) AS orders,

                COUNT(
                    CASE
                        WHEN stock_logs.source_type = \'manual_reduction\'
                        THEN 1
                    END
                ) AS reductions
            ')
            ->first();

        /*
         * Paginated audit records.
         */
        $stockLogs = (clone $baseQuery)
            ->select(
                'stock_logs.id',
                'stock_logs.created_at',
                'stock_logs.ingredient_id',
                DB::raw(
                    'COALESCE(
                        stock_logs.ingredient_name,
                        ingredients.name
                    ) as ingredient_name'
                ),
                'stock_logs.branch_id',
                'branches.name as branch_name',
                'stock_logs.quantity_change',
                'stock_logs.type',
                'stock_logs.remarks',
                'stock_logs.running_balance',
                'stock_logs.unit_cost',
                'stock_logs.source_type',
                'stock_logs.source_id',
                'stock_logs.order_id',
                'stock_logs.expense_id',
                'units.abbreviation as unit',
                'orders.receipt_no',
                'orders.payment_method',
                'orders.payment_status',
                'expenses.description as expense_description',
                'expenses.total_amount as expense_amount'
            )
            ->orderByDesc('stock_logs.created_at')
            ->paginate(50)
            ->withQueryString();

        $branches = DB::table('laravel.branches')
            ->orderBy('name')
            ->get();

        return view(
            'admin.inventory.stockAudit',
            compact(
                'stockLogs',
                'summary',
                'branches',
                'dateFrom',
                'dateTo',
                'sourceType'
            )
        );
    }
}