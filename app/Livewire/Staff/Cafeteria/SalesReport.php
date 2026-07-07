<?php

namespace App\Livewire\Staff\Cafeteria;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\CafeteriaSale;
use App\Models\CafeteriaSaleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.staff')]
#[Title('Cafeteria Sales Report')]
class SalesReport extends Component
{
    public $dateFilter = 'this_month'; // today, yesterday, last_7_days, this_month, last_30_days, custom
    public $startDate = '';
    public $endDate = '';

    public function updatedDateFilter()
    {
        if ($this->dateFilter !== 'custom') {
            $this->startDate = '';
            $this->endDate = '';
        }
    }

    private function getDateRange()
    {
        $start = null;
        $end = null;

        switch ($this->dateFilter) {
            case 'today':
                $start = Carbon::today()->startOfDay();
                $end = Carbon::today()->endOfDay();
                break;
            case 'yesterday':
                $start = Carbon::yesterday()->startOfDay();
                $end = Carbon::yesterday()->endOfDay();
                break;
            case 'last_7_days':
                $start = Carbon::now()->subDays(6)->startOfDay();
                $end = Carbon::now()->endOfDay();
                break;
            case 'this_month':
                $start = Carbon::now()->startOfMonth()->startOfDay();
                $end = Carbon::now()->endOfMonth()->endOfDay();
                break;
            case 'last_30_days':
                $start = Carbon::now()->subDays(29)->startOfDay();
                $end = Carbon::now()->endOfDay();
                break;
            case 'custom':
                $start = $this->startDate ? Carbon::parse($this->startDate)->startOfDay() : Carbon::now()->startOfMonth()->startOfDay();
                $end = $this->endDate ? Carbon::parse($this->endDate)->endOfDay() : Carbon::now()->endOfDay();
                break;
            default:
                $start = Carbon::now()->startOfMonth()->startOfDay();
                $end = Carbon::now()->endOfDay();
        }

        return [$start, $end];
    }

    public function render()
    {
        [$start, $end] = $this->getDateRange();

        $salesQuery = CafeteriaSale::whereBetween('created_at', [$start, $end]);
        
        $totalSales = (float)$salesQuery->sum('grand_total');
        $transactionsCount = $salesQuery->count();
        $averageBill = $transactionsCount > 0 ? ($totalSales / $transactionsCount) : 0;
        $totalDiscounts = (float)$salesQuery->sum('discount_amount');

        $paymentBreakdown = CafeteriaSale::whereBetween('created_at', [$start, $end])
            ->select('payment_method', DB::raw('SUM(grand_total) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get()
            ->keyBy('payment_method')
            ->toArray();

        $tierBreakdown = CafeteriaSale::whereBetween('created_at', [$start, $end])
            ->select('price_type', DB::raw('SUM(grand_total) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('price_type')
            ->get()
            ->keyBy('price_type')
            ->toArray();

        $categoryBreakdown = DB::table('cafeteria_sale_items')
            ->join('cafeteria_sales', 'cafeteria_sale_items.sale_id', '=', 'cafeteria_sales.id')
            ->leftJoin('cafeteria_products', 'cafeteria_sale_items.product_id', '=', 'cafeteria_products.id')
            ->leftJoin('cafeteria_categories', 'cafeteria_products.category_id', '=', 'cafeteria_categories.id')
            ->whereBetween('cafeteria_sales.created_at', [$start, $end])
            ->select(
                DB::raw("COALESCE(cafeteria_categories.name, 'Custom/Uncategorized') as cat_name"),
                DB::raw('SUM(cafeteria_sale_items.quantity) as total_qty'),
                DB::raw('SUM(cafeteria_sale_items.subtotal) as total_revenue')
            )
            ->groupBy('cafeteria_categories.name')
            ->orderBy('total_revenue', 'desc')
            ->get();

        $topProducts = DB::table('cafeteria_sale_items')
            ->join('cafeteria_sales', 'cafeteria_sale_items.sale_id', '=', 'cafeteria_sales.id')
            ->whereBetween('cafeteria_sales.created_at', [$start, $end])
            ->select(
                'cafeteria_sale_items.product_name',
                'cafeteria_sale_items.product_code',
                DB::raw('SUM(cafeteria_sale_items.quantity) as qty_sold'),
                DB::raw('SUM(cafeteria_sale_items.subtotal) as total_revenue')
            )
            ->groupBy('cafeteria_sale_items.product_name', 'cafeteria_sale_items.product_code')
            ->orderBy('qty_sold', 'desc')
            ->limit(5)
            ->get();

        $dailyTrend = CafeteriaSale::whereBetween('created_at', [$start, $end])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(grand_total) as total'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'), 'asc')
            ->get()
            ->toArray();

        return view('livewire.staff.cafeteria.sales-report', [
            'summary' => [
                'total_sales' => $totalSales,
                'transactions_count' => $transactionsCount,
                'average_bill' => $averageBill,
                'total_discounts' => $totalDiscounts,
            ],
            'paymentBreakdown' => $paymentBreakdown,
            'tierBreakdown' => $tierBreakdown,
            'categoryBreakdown' => $categoryBreakdown,
            'topProducts' => $topProducts,
            'dailyTrend' => $dailyTrend,
            'startDateLabel' => $start->format('M d, Y'),
            'endDateLabel' => $end->format('M d, Y')
        ]);
    }
}
