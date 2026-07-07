<div class="pos-sales-ledger py-2">
    <!-- Tailwind CSS Play CDN to ensure dynamic compilation of premium styles -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#16a34a',
                            600: '#15803d',
                            700: '#166534',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24; }
        .glass-card { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(226, 232, 240, 0.8); }
    </style>

    <div class="min-h-screen bg-slate-50/50 p-1 font-sans text-slate-800 antialiased">
        <!-- Header area -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 border-b border-slate-200/60 pb-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                    <span class="p-2 bg-brand-100 text-brand-700 rounded-xl flex items-center justify-center shadow-sm"><i class="fas fa-history text-lg"></i></span>
                    Sales Ledger
                </h1>
                <p class="text-xs text-slate-500 mt-1">Audit past transactions, filter by cashier or payment status, reprint receipts, and track revenues.</p>
            </div>
            <div>
                <a href="{{ route('staff.cafeteria.billing') }}" class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl text-xs flex items-center gap-1.5 transition-colors shadow-lg shadow-brand-600/10">
                    <span class="material-symbols-outlined text-lg">point_of_sale</span> POS Billing Terminal
                </a>
            </div>
        </div>

        <!-- Alert messages -->
        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-2 shadow-sm animate-fade-in" role="alert">
                <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                <span class="text-sm font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Summary Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Count Card -->
            <div class="bg-gradient-to-br from-emerald-600 to-teal-700 text-white rounded-2xl shadow-sm p-4 relative overflow-hidden">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] uppercase font-bold tracking-widest opacity-80">Sales count</p>
                        <h3 class="text-2xl font-black mt-1">{{ $summary['count'] }}</h3>
                        <p class="text-[10px] opacity-75 mt-1">Processed transactions</p>
                    </div>
                    <span class="material-symbols-outlined text-white/20 text-4xl absolute -right-2 -bottom-2">receipt</span>
                </div>
            </div>

            <!-- Revenue Card -->
            <div class="glass-card rounded-2xl shadow-sm p-4 relative overflow-hidden border-l-4 border-l-emerald-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] uppercase font-bold tracking-widest text-slate-400">Net revenue</p>
                        <h3 class="text-2xl font-black text-slate-900 mt-1">LKR {{ number_format($summary['revenue'], 2) }}</h3>
                        <p class="text-[10px] text-slate-500 mt-1">Net check values</p>
                    </div>
                    <span class="material-symbols-outlined text-slate-200 text-4xl absolute -right-2 -bottom-2">payments</span>
                </div>
            </div>

            <!-- Discount Card -->
            <div class="glass-card rounded-2xl shadow-sm p-4 relative overflow-hidden border-l-4 border-l-amber-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] uppercase font-bold tracking-widest text-slate-400">Discounts granted</p>
                        <h3 class="text-2xl font-black text-slate-900 mt-1">LKR {{ number_format($summary['discounts'], 2) }}</h3>
                        <p class="text-[10px] text-slate-500 mt-1">Itemized & global reductions</p>
                    </div>
                    <span class="material-symbols-outlined text-slate-200 text-4xl absolute -right-2 -bottom-2">percent</span>
                </div>
            </div>

            <!-- Unpaid Invoices Card -->
            <div class="glass-card rounded-2xl shadow-sm p-4 relative overflow-hidden border-l-4 border-l-rose-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] uppercase font-bold tracking-widest text-slate-400">Pending collections</p>
                        <h3 class="text-2xl font-black text-rose-600 mt-1">LKR {{ number_format($summary['pending_amt'], 2) }}</h3>
                        <p class="text-[10px] text-rose-500 font-bold mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-xs">warning</span> {{ $summary['pending_count'] }} invoices unpaid</p>
                    </div>
                    <span class="material-symbols-outlined text-rose-100 text-4xl absolute -right-2 -bottom-2">pending_actions</span>
                </div>
            </div>
        </div>

        <!-- Filters Box -->
        <div class="glass-card rounded-2xl shadow-sm p-4 mb-6">
            <div class="flex flex-col md:flex-row gap-3 items-end justify-between">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 flex-grow">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Search ledger</label>
                        <input type="text" class="w-full text-xs font-semibold bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-1 focus:ring-brand-500 focus:border-brand-500" placeholder="Invoice No, customer name..." wire:model.live.debounce.300ms="search">
                    </div>
                    
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Collection Status</label>
                        <div class="relative">
                            <select class="w-full pl-3 pr-8 py-2 bg-slate-50/50 border border-slate-200 rounded-xl outline-none text-xs font-semibold appearance-none focus:ring-1 focus:ring-brand-500 focus:border-brand-500" wire:model.live="paymentStatusFilter">
                                <option value="">All Transactions</option>
                                <option value="paid">Paid</option>
                                <option value="pending">Pending</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-lg">expand_more</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Date Period</label>
                        <div class="relative">
                            <select class="w-full pl-3 pr-8 py-2 bg-slate-50/50 border border-slate-200 rounded-xl outline-none text-xs font-semibold appearance-none focus:ring-1 focus:ring-brand-500 focus:border-brand-500" wire:model.live="dateFilter">
                                <option value="all">All Dates</option>
                                <option value="today">Today</option>
                                <option value="yesterday">Yesterday</option>
                                <option value="this_week">This Week</option>
                                <option value="this_month">This Month</option>
                                <option value="custom">Custom Range</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-lg">expand_more</span>
                        </div>
                    </div>

                    @if($dateFilter === 'custom')
                        <div class="flex gap-2">
                            <div class="flex-grow">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Start Date</label>
                                <input type="date" class="w-full text-xs font-semibold bg-slate-50/50 border border-slate-200 rounded-xl px-2 py-1.5 focus:ring-1 focus:ring-brand-500" wire:model.live="startDate">
                            </div>
                            <div class="flex-grow">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">End Date</label>
                                <input type="date" class="w-full text-xs font-semibold bg-slate-50/50 border border-slate-200 rounded-xl px-2 py-1.5 focus:ring-1 focus:ring-brand-500" wire:model.live="endDate">
                            </div>
                        </div>
                    @endif
                </div>

                <button class="px-4 py-2 bg-white border border-slate-200 hover:border-brand-500 hover:text-brand-700 text-slate-600 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-colors shadow-sm w-full md:w-auto" wire:click="clearFilters">
                    <span class="material-symbols-outlined text-lg">undo</span> Reset Filters
                </button>
            </div>
        </div>

        <!-- Ledger Entries Table -->
        <div class="glass-card rounded-2xl shadow-sm overflow-hidden flex flex-col">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse text-xs align-middle">
                    <thead>
                        <tr class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                            <th class="pl-4 py-3">Invoice No</th>
                            <th class="py-3">Date & Time</th>
                            <th class="py-3">Customer Details</th>
                            <th class="py-3">Payment Mode</th>
                            <th class="py-3 text-center" style="width: 105px;">Status</th>
                            <th class="py-3 text-right" style="width: 130px;">Grand Total</th>
                            <th class="pr-4 py-3 text-center" style="width: 110px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($sales as $sale)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="pl-4 py-3 font-mono font-bold text-slate-900 text-sm">{{ $sale->billing_no }}</td>
                                <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('M d, Y h:i A') }}</td>
                                <td>
                                    @if($sale->customer_id)
                                        <div class="font-bold text-slate-800">{{ $sale->customer->first_name }} {{ $sale->customer->last_name }}</div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">{{ $sale->customer->phone_number }}</div>
                                    @elseif($sale->customer_name)
                                        <div class="font-bold text-slate-800">{{ $sale->customer_name }}</div>
                                        @if($sale->customer_phone)
                                            <div class="text-[10px] text-slate-400 mt-0.5">{{ $sale->customer_phone }}</div>
                                        @endif
                                    @else
                                        <span class="text-slate-400 italic">Walking Customer</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center gap-1.5 text-slate-600">
                                        <span class="material-symbols-outlined text-sm text-slate-400">
                                            @if($sale->payment_method === 'cash') payments @elseif($sale->payment_method === 'card') credit_card @else qr_code_2 @endif
                                        </span>
                                        <span class="font-bold uppercase text-[10px]">{{ $sale->payment_method }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold border cursor-pointer {{ $sale->payment_status === 'paid' ? 'bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-700' : 'bg-rose-100 text-rose-700 border-rose-200 hover:bg-rose-200' }}" 
                                            wire:click="togglePaymentStatus({{ $sale->id }})" 
                                            title="Click to toggle status">
                                        {{ strtoupper($sale->payment_status) }}
                                    </button>
                                </td>
                                <td class="text-right font-black text-slate-950 text-sm">LKR {{ number_format($sale->grand_total, 2) }}</td>
                                <td class="pr-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button class="p-1.5 text-emerald-600 hover:bg-emerald-50 hover:text-emerald-800 rounded-lg transition-colors flex items-center justify-center border border-slate-100 bg-white" wire:click="viewReceipt({{ $sale->id }})" title="View Invoice">
                                            <span class="material-symbols-outlined text-[15px]">description</span>
                                        </button>
                                        <button class="p-1.5 text-rose-600 hover:bg-rose-50 hover:text-rose-800 rounded-lg transition-colors flex items-center justify-center border border-slate-100 bg-white" 
                                                onclick="confirm('Are you sure you want to delete this invoice? The products stock will be restored.') || event.stopImmediatePropagation()" 
                                                wire:click="deleteSale({{ $sale->id }})" 
                                                title="Delete Invoice">
                                            <span class="material-symbols-outlined text-[15px]">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-20 text-slate-400">
                                    <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">find_in_page</span>
                                    <p class="font-bold text-xs text-slate-500 uppercase tracking-widest">No sales items found</p>
                                    <p class="text-[10px] text-slate-400 mt-1">Adjust filters or search parameters and try again.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $sales->links() }}
            </div>
        </div>
    </div>

    <!-- Receipt Print Modal -->
    @if($showReceiptModal && $completedSale)
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[2000] flex items-center justify-center" wire:click.self="closeReceiptModal()">
            <div class="bg-white border rounded-2xl shadow-2xl max-w-sm w-full mx-4 overflow-hidden animate-zoom-in">
                <div class="p-3 bg-slate-50 border-b border-slate-200/60 flex justify-between items-center">
                    <h5 class="font-extrabold text-sm text-slate-800 flex items-center gap-1.5"><span class="material-symbols-outlined text-brand-600 text-lg">description</span> Invoice receipt</h5>
                    <button type="button" class="text-slate-400 hover:text-slate-600" wire:click="closeReceiptModal()"><span class="material-symbols-outlined">close</span></button>
                </div>
                <div class="p-0 max-h-[70vh] overflow-y-auto custom-scrollbar">
                    <!-- Printed Receipt Mockup -->
                    <div id="printable-receipt" class="bg-white p-6 text-slate-800 font-mono text-[11px] leading-relaxed">
                        <div class="text-center mb-4">
                            <h4 class="text-sm font-extrabold uppercase text-slate-900">Sportynix Cafeteria</h4>
                            <div class="text-[10px] text-slate-500">Complex Administration Division</div>
                            <div class="text-[10px] text-slate-500">Tel: +94 11 234 5678</div>
                        </div>
                        <div class="border-b border-dashed border-slate-300 my-2"></div>
                        <div class="space-y-0.5 text-slate-600">
                            <div><strong>Invoice:</strong> {{ $completedSale->billing_no }}</div>
                            <div><strong>Date:</strong> {{ \Carbon\Carbon::parse($completedSale->created_at)->format('Y-m-d H:i:s') }}</div>
                            <div><strong>Cashier:</strong> {{ $completedSale->user->name ?? 'Staff' }}</div>
                            @if($completedSale->customer_name)
                                <div><strong>Customer:</strong> {{ $completedSale->customer_name }}</div>
                                @if($completedSale->customer_phone)
                                    <div><strong>Phone:</strong> {{ $completedSale->customer_phone }}</div>
                                @endif
                            @endif
                        </div>
                        <div class="border-b border-dashed border-slate-300 my-2"></div>
                        <table class="w-full text-left table-auto">
                            <thead>
                                <tr class="font-bold border-b border-slate-300 text-slate-900">
                                    <th class="pb-1">Item</th>
                                    <th class="pb-1 text-center" style="width: 35px;">Qty</th>
                                    <th class="pb-1 text-right" style="width: 60px;">Price</th>
                                    <th class="pb-1 text-right" style="width: 70px;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($completedSale->items as $sItem)
                                    <tr class="text-slate-700">
                                        <td class="py-1">
                                            {{ $sItem->product_name }}
                                            @if($sItem->discount_percentage > 0)
                                                <div class="text-[9px] text-slate-400 font-italic">(Disc: {{ number_format($sItem->discount_percentage, 0) }}%)</div>
                                            @endif
                                        </td>
                                        <td class="py-1 text-center">{{ $sItem->quantity }}</td>
                                        <td class="py-1 text-right">{{ number_format($sItem->unit_price, 0) }}</td>
                                        <td class="py-1 text-right">{{ number_format($sItem->subtotal, 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="border-b border-dashed border-slate-300 my-2"></div>
                        <div class="space-y-1">
                            <div class="flex justify-between text-slate-600">
                                <span>Subtotal:</span>
                                <span>LKR {{ number_format($completedSale->subtotal, 2) }}</span>
                            </div>
                            @if($completedSale->discount_amount > 0)
                                <div class="flex justify-between text-rose-600">
                                    <span>Total Discount:</span>
                                    <span>- LKR {{ number_format($completedSale->discount_amount, 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between font-bold text-slate-900 border-t border-slate-200 pt-1 text-xs">
                                <span>GRAND TOTAL:</span>
                                <span>LKR {{ number_format($completedSale->grand_total, 2) }}</span>
                            </div>
                        </div>
                        <div class="border-b border-dashed border-slate-300 my-2"></div>
                        <div class="text-slate-600 space-y-0.5">
                            <div><strong>Payment mode:</strong> {{ strtoupper($completedSale->payment_method) }}</div>
                            <div><strong>Status:</strong> {{ strtoupper($completedSale->payment_status) }}</div>
                        </div>
                        <div class="text-center mt-5 text-[10px] text-slate-400">
                            <div>Thank you for your visit!</div>
                            <div>Sportynix Hub Grounds Management</div>
                        </div>
                    </div>
                </div>
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-600" wire:click="closeReceiptModal()">Close</button>
                    <button type="button" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold flex items-center gap-1.5" onclick="printInvoice()"><span class="material-symbols-outlined text-base">print</span> Print</button>
                </div>
            </div>
        </div>
        
        <script>
            function printInvoice() {
                var printContents = document.getElementById('printable-receipt').innerHTML;
                var popupWin = window.open('', '_blank', 'width=420,height=600');
                popupWin.document.open();
                popupWin.document.write('<html><head><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"><style>body{font-family: monospace; padding: 20px;} hr{border-top:1px dashed #000;}</style></head><body onload="window.print();window.close()">' + printContents + '</body></html>');
                popupWin.document.close();
            }
        </script>
    @endif
</div>
