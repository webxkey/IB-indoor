<?php

namespace App\Livewire\Staff\Cafeteria;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\CafeteriaSale;
use Carbon\Carbon;

#[Layout('components.layouts.staff')]
#[Title('Cafeteria Sales Ledger')]
class SalesList extends Component
{
    use WithPagination;

    // Search and filters
    public $search = '';
    public $dateFilter = 'all'; // all, today, yesterday, this_week, this_month, custom
    public $startDate = '';
    public $endDate = '';
    public $paymentStatusFilter = '';
    public $perPage = 15;

    // Receipt reprint state
    public $showReceiptModal = false;
    public $completedSale = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'dateFilter' => ['except' => 'all'],
        'paymentStatusFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDateFilter()
    {
        $this->resetPage();
    }

    public function updatingPaymentStatusFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->dateFilter = 'all';
        $this->startDate = '';
        $this->endDate = '';
        $this->paymentStatusFilter = '';
        $this->resetPage();
    }

    public function viewReceipt($id)
    {
        $this->completedSale = CafeteriaSale::with('items', 'user', 'customer')->findOrFail($id);
        $this->showReceiptModal = true;
    }

    public function closeReceiptModal()
    {
        $this->showReceiptModal = false;
        $this->completedSale = null;
    }

    public function togglePaymentStatus($id)
    {
        $sale = CafeteriaSale::findOrFail($id);
        $sale->update([
            'payment_status' => $sale->payment_status === 'paid' ? 'pending' : 'paid'
        ]);
        session()->flash('success', "Invoice status updated to " . strtoupper($sale->payment_status));
    }

    public function deleteSale($id)
    {
        $sale = CafeteriaSale::findOrFail($id);
        
        foreach ($sale->items as $item) {
            if ($item->product_id) {
                $item->product()->increment('stock', $item->quantity);
            }
        }

        $sale->delete();
        session()->flash('success', "Sale invoice deleted successfully. Inventory stock returned.");
        $this->resetPage();
    }

    public function getImageUrl($image)
    {
        if (empty($image)) {
            return 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSrn_80I-lMAa0pVBNmFmQ7VI6l4rr74JW-eQ&s';
        }
        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }
        return asset('storage/' . $image);
    }

    public function render()
    {
        $query = CafeteriaSale::with(['user', 'customer']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('billing_no', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_phone', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->paymentStatusFilter) {
            $query->where('payment_status', $this->paymentStatusFilter);
        }

        switch ($this->dateFilter) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'yesterday':
                $query->whereDate('created_at', Carbon::yesterday());
                break;
            case 'this_week':
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'this_month':
                $query->whereMonth('created_at', Carbon::now()->month)
                      ->whereYear('created_at', Carbon::now()->year);
                break;
            case 'custom':
                if ($this->startDate) {
                    $query->whereDate('created_at', '>=', Carbon::parse($this->startDate));
                }
                if ($this->endDate) {
                    $query->whereDate('created_at', '<=', Carbon::parse($this->endDate));
                }
                break;
        }

        $statsQuery = clone $query;
        $summary = [
            'count' => $statsQuery->count(),
            'revenue' => $statsQuery->sum('grand_total'),
            'discounts' => $statsQuery->sum('discount_amount'),
            'pending_count' => (clone $statsQuery)->where('payment_status', 'pending')->count(),
            'pending_amt' => (clone $statsQuery)->where('payment_status', 'pending')->sum('grand_total'),
        ];

        $sales = $query->latest()->paginate($this->perPage);

        return view('livewire.staff.cafeteria.sales-list', [
            'sales' => $sales,
            'summary' => $summary
        ]);
    }
}
