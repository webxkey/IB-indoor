<div class="pos-sales-analytics py-2">
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
                    <span class="p-2 bg-brand-100 text-brand-700 rounded-xl flex items-center justify-center shadow-sm"><i class="fas fa-chart-line text-lg"></i></span>
                    Sales Analytics
                </h1>
                <p class="text-xs text-slate-500 mt-1">Monitor revenue metrics, product shares, payment distributions, and performance graphs.</p>
            </div>
            <div>
                <span class="inline-block bg-white border border-slate-200 rounded-xl px-4 py-2 text-xs font-bold text-slate-700 shadow-sm">
                    Period: <span class="text-brand-600 font-extrabold">{{ $startDateLabel }} - {{ $endDateLabel }}</span>
                </span>
            </div>
        </div>

        <!-- Filters Bar -->
        <div class="glass-card rounded-2xl shadow-sm p-4 mb-6">
            <div class="flex flex-col md:flex-row gap-3 items-end">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 flex-grow">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5"><i class="fas fa-calendar-alt text-muted me-1"></i> Date Filter</label>
                        <div class="relative">
                            <select class="w-full pl-3 pr-8 py-2 bg-slate-50/50 border border-slate-200 rounded-xl outline-none text-xs font-semibold appearance-none focus:ring-1 focus:ring-brand-500 focus:border-brand-500" wire:model.live="dateFilter">
                                <option value="today">Today</option>
                                <option value="yesterday">Yesterday</option>
                                <option value="last_7_days">Last 7 Days</option>
                                <option value="this_month">This Month</option>
                                <option value="last_30_days">Last 30 Days</option>
                                <option value="custom">Custom Range</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-lg">expand_more</span>
                        </div>
                    </div>
                    
                    @if($dateFilter === 'custom')
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Start Date</label>
                            <input type="date" class="w-full text-xs font-semibold bg-slate-50/50 border border-slate-200 rounded-xl px-2 py-1.5 focus:ring-1 focus:ring-brand-500" wire:model.live="startDate">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">End Date</label>
                            <input type="date" class="w-full text-xs font-semibold bg-slate-50/50 border border-slate-200 rounded-xl px-2 py-1.5 focus:ring-1 focus:ring-brand-500" wire:model.live="endDate">
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Summary Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Revenue Card -->
            <div class="bg-gradient-to-br from-emerald-600 to-teal-700 text-white rounded-2xl shadow-sm p-4 relative overflow-hidden">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] uppercase font-bold tracking-widest opacity-80">Net Revenue</p>
                        <h3 class="text-2xl font-black mt-1">LKR {{ number_format($summary['total_sales'], 2) }}</h3>
                        <p class="text-[10px] opacity-75 mt-1">Total billing value</p>
                    </div>
                    <span class="material-symbols-outlined text-white/20 text-4xl absolute -right-2 -bottom-2">monetization_on</span>
                </div>
            </div>

            <!-- Transaction Count -->
            <div class="glass-card rounded-2xl shadow-sm p-4 relative overflow-hidden border-l-4 border-l-indigo-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] uppercase font-bold tracking-widest text-slate-400">Transactions count</p>
                        <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $summary['transactions_count'] }}</h3>
                        <p class="text-[10px] text-slate-500 mt-1">Invoices processed</p>
                    </div>
                    <span class="material-symbols-outlined text-slate-200 text-4xl absolute -right-2 -bottom-2">shopping_bag</span>
                </div>
            </div>

            <!-- Average Bill -->
            <div class="glass-card rounded-2xl shadow-sm p-4 relative overflow-hidden border-l-4 border-l-cyan-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] uppercase font-bold tracking-widest text-slate-400">Average Order Value</p>
                        <h3 class="text-2xl font-black text-slate-900 mt-1">LKR {{ number_format($summary['average_bill'], 2) }}</h3>
                        <p class="text-[10px] text-slate-500 mt-1">Average sale ticket size</p>
                    </div>
                    <span class="material-symbols-outlined text-slate-200 text-4xl absolute -right-2 -bottom-2">calculate</span>
                </div>
            </div>

            <!-- Total Discount Allowed -->
            <div class="glass-card rounded-2xl shadow-sm p-4 relative overflow-hidden border-l-4 border-l-amber-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] uppercase font-bold tracking-widest text-slate-400">Total Discounts Allowed</p>
                        <h3 class="text-2xl font-black text-slate-900 mt-1">LKR {{ number_format($summary['total_discounts'], 2) }}</h3>
                        <p class="text-[10px] text-slate-500 mt-1">Total reductions given</p>
                    </div>
                    <span class="material-symbols-outlined text-slate-200 text-4xl absolute -right-2 -bottom-2">sell</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
            <!-- LEFT SECTION -->
            <div class="flex flex-col gap-6">
                <!-- Top Selling Products -->
                <div class="glass-card rounded-2xl shadow-sm overflow-hidden flex flex-col">
                    <div class="p-4 border-b border-slate-200/60 bg-slate-50/50">
                        <h3 class="font-extrabold text-sm text-slate-800 flex items-center gap-1.5"><span class="material-symbols-outlined text-brand-600 text-lg">hotel_class</span> Top 5 Selling Products</h3>
                    </div>
                    <div class="p-0 overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse text-xs align-middle">
                            <thead>
                                <tr class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                    <th class="pl-4 py-3" style="width: 50px;">Rank</th>
                                    <th class="py-3">Product details</th>
                                    <th class="py-3 text-center" style="width: 100px;">Units Sold</th>
                                    <th class="pr-4 py-3 text-right" style="width: 120px;">Revenue</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($topProducts as $idx => $prod)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="pl-4 py-3">
                                            <span class="w-6 h-6 bg-slate-100 text-slate-800 font-bold rounded-lg flex items-center justify-center border border-slate-200 text-[11px]">{{ $idx + 1 }}</span>
                                        </td>
                                        <td>
                                            <div class="font-bold text-slate-800 text-sm">{{ $prod->product_name }}</div>
                                            @if($prod->product_code)
                                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $prod->product_code }}</div>
                                            @endif
                                        </td>
                                        <td class="text-center font-bold text-slate-900">{{ $prod->qty_sold }}</td>
                                        <td class="pr-4 py-3 text-right font-black text-brand-600">LKR {{ number_format($prod->total_revenue, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-10 text-slate-400">No sales items registered in this period.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Payment Method Distribution -->
                <div class="glass-card rounded-2xl shadow-sm p-4 flex flex-col gap-4">
                    <div class="border-b border-slate-200/60 pb-2">
                        <h3 class="font-extrabold text-sm text-slate-800 flex items-center gap-1.5"><span class="material-symbols-outlined text-brand-600 text-lg">wallet</span> Payment Mode Distribution</h3>
                    </div>
                    <div class="space-y-4">
                        @php
                            $paymentMethods = ['cash' => 'Cash', 'card' => 'Card', 'upi' => 'UPI / Online'];
                            $totalRev = $summary['total_sales'] ?: 1;
                        @endphp
                        @foreach($paymentMethods as $key => $name)
                            @php
                                $row = $paymentBreakdown[$key] ?? ['total' => 0, 'count' => 0];
                                $percentage = ($row['total'] / $totalRev) * 100;
                                $barColor = $key === 'cash' ? 'bg-emerald-500' : ($key === 'card' ? 'bg-indigo-500' : 'bg-cyan-500');
                                $barBg = $key === 'cash' ? 'bg-emerald-50' : ($key === 'card' ? 'bg-indigo-50' : 'bg-cyan-50');
                            @endphp
                            <div>
                                <div class="flex justify-between items-baseline mb-1">
                                    <span class="text-xs font-bold text-slate-700">{{ $name }}</span>
                                    <div class="text-[11px]">
                                        <span class="bg-slate-100 text-slate-600 font-bold px-1.5 py-0.5 rounded border border-slate-200">{{ $row['count'] }} bills</span>
                                        <span class="font-black text-slate-900 ml-2">LKR {{ number_format($row['total'], 2) }} ({{ number_format($percentage, 0) }}%)</span>
                                    </div>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2">
                                    <div class="{{ $barColor }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- RIGHT SECTION -->
            <div class="flex flex-col gap-6">
                <!-- Category Breakdown -->
                <div class="glass-card rounded-2xl shadow-sm p-4 flex flex-col gap-4">
                    <div class="border-b border-slate-200/60 pb-2">
                        <h3 class="font-extrabold text-sm text-slate-800 flex items-center gap-1.5"><span class="material-symbols-outlined text-brand-600 text-lg">local_offer</span> Category breakdown</h3>
                    </div>
                    <div class="space-y-4">
                        @forelse($categoryBreakdown as $catRow)
                            @php
                                $totalRev = $summary['total_sales'] ?: 1;
                                $percentage = ($catRow->total_revenue / $totalRev) * 100;
                            @endphp
                            <div>
                                <div class="flex justify-between items-baseline mb-1">
                                    <div>
                                        <span class="text-xs font-bold text-slate-700">{{ $catRow->cat_name }}</span>
                                        <small class="text-slate-400 font-semibold ml-1.5">({{ $catRow->total_qty }} items)</small>
                                    </div>
                                    <span class="text-xs font-black text-slate-900">LKR {{ number_format($catRow->total_revenue, 2) }} ({{ number_format($percentage, 0) }}%)</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2">
                                    <div class="bg-brand-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-xs text-slate-400 py-10">No category breakdown metrics available.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Daily Performance Ledger -->
                <div class="glass-card rounded-2xl shadow-sm overflow-hidden flex flex-col">
                    <div class="p-4 border-b border-slate-200/60 bg-slate-50/50">
                        <h3 class="font-extrabold text-sm text-slate-800 flex items-center gap-1.5"><span class="material-symbols-outlined text-brand-600 text-lg">calendar_view_day</span> Daily Ledger Performance</h3>
                    </div>
                    <div class="p-0 overflow-y-auto custom-scrollbar" style="max-height: 290px;">
                        <table class="w-full text-left border-collapse text-xs align-middle">
                            <thead>
                                <tr class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 sticky top-0">
                                    <th class="pl-4 py-3">Ledger Date</th>
                                    <th class="pr-4 py-3 text-right" style="width: 150px;">Revenue Collected</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse(array_reverse($dailyTrend) as $dt)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="pl-4 py-3 font-semibold text-slate-800">
                                            {{ \Carbon\Carbon::parse($dt['date'])->format('l, M d, Y') }}
                                        </td>
                                        <td class="pr-4 py-3 text-right font-black text-brand-600">
                                            LKR {{ number_format($dt['total'], 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center py-10 text-slate-400">No daily transactions registered in this period.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
