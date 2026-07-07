<div class="completed-bookings-ledger py-2" wire:poll.15s>
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
                    <span class="p-2 bg-brand-100 text-brand-700 rounded-xl flex items-center justify-center shadow-sm"><i class="fas fa-check-double text-lg"></i></span>
                    Completed Bookings Ledger
                </h1>
                <p class="text-xs text-slate-500 mt-1">Review finalized sport bookings, filter dates using the datepicker, and reprint client booking receipts.</p>
            </div>
            <div>
                <a href="{{ route('staff.bookings') }}" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl text-xs flex items-center gap-1.5 transition-colors shadow-md">
                    <span class="material-symbols-outlined text-lg">calendar_month</span> Manage Live Scheduler
                </a>
            </div>
        </div>

        <!-- Filters Box -->
        <div class="glass-card rounded-2xl shadow-sm p-4 mb-6">
            <div class="flex flex-col md:flex-row gap-3 items-end justify-between">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 flex-grow">
                    <!-- Text Search -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Search Bookings</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                            <input type="text" 
                                   class="w-full pl-8 pr-3 py-2 bg-slate-50/50 border border-slate-200 rounded-xl outline-none text-xs font-semibold focus:ring-1 focus:ring-brand-500 focus:border-brand-500" 
                                   placeholder="Customer name, phone, or game..." 
                                   wire:model.live.debounce.300ms="search">
                        </div>
                    </div>
                    
                    <!-- Date Picker Filter -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5"><i class="fas fa-calendar-day text-slate-400 me-1"></i> Filter by Date</label>
                        <div class="relative">
                            <input type="date" 
                                   class="w-full py-2 px-3 bg-slate-50/50 border border-slate-200 rounded-xl outline-none text-xs font-semibold focus:ring-1 focus:ring-brand-500 focus:border-brand-500" 
                                   wire:model.live="filterDate">
                        </div>
                    </div>
                </div>

                <!-- Reset Filters Button -->
                <button class="px-4 py-2 bg-white border border-slate-200 hover:border-brand-500 hover:text-brand-700 text-slate-600 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-colors shadow-sm w-full md:w-auto" wire:click="clearFilters">
                    <span class="material-symbols-outlined text-lg">undo</span> Reset Filters
                </button>
            </div>
        </div>

        <!-- Bookings Ledger List -->
        <div class="glass-card rounded-2xl shadow-sm overflow-hidden flex flex-col">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse text-xs align-middle">
                    <thead>
                        <tr class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                            <th class="pl-4 py-3" style="width: 80px;">BKG ID</th>
                            <th class="py-3">Booking Date</th>
                            <th class="py-3">Time slot</th>
                            <th class="py-3">Player Info</th>
                            <th class="py-3">Sport / Court</th>
                            <th class="py-3 text-center" style="width: 105px;">Payment Status</th>
                            <th class="py-3 text-right" style="width: 120px;">Price</th>
                            <th class="pr-4 py-3 text-center" style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($bookings as $booking)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="pl-4 py-3 font-mono font-bold text-slate-900 text-sm">#{{ $booking->id }}</td>
                                <td class="font-semibold text-slate-800">
                                    {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}
                                </td>
                                <td class="font-bold text-slate-700">
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                                </td>
                                <td>
                                    <div class="font-bold text-slate-800">{{ $booking->user_name }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">{{ $booking->user_number }}</div>
                                </td>
                                <td>
                                    <div class="flex items-center gap-1">
                                        <span class="inline-block bg-slate-100 text-slate-600 rounded-lg px-2 py-0.5 font-bold text-[10px] border border-slate-200">{{ $booking->game_name }}</span>
                                        <span class="text-[10px] text-slate-500 font-bold">Court {{ $booking->court_number }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $booking->payment_status === 'Paid' || $booking->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                        {{ ucfirst($booking->payment_status ?: 'unpaid') }}
                                    </span>
                                </td>
                                <td class="text-right font-black text-slate-950 text-sm">LKR {{ number_format($booking->price ?: 0, 2) }}</td>
                                <td class="pr-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button class="px-2.5 py-1 bg-brand-50 hover:bg-brand-100 text-brand-700 rounded-lg border border-brand-200 text-[11px] font-bold flex items-center gap-1 transition-colors shadow-sm" 
                                                wire:click="viewReceipt({{ $booking->id }})" 
                                                title="View Booking Receipt">
                                            <span class="material-symbols-outlined text-sm font-bold">print</span> Print
                                        </button>
                                        
                                        @if(strtolower($booking->payment_status) !== 'paid')
                                            <button class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-[11px] font-bold flex items-center gap-1 transition-colors shadow-sm"
                                                    wire:click="openPaymentModal({{ $booking->id }})"
                                                    title="Collect Payment">
                                                <span class="material-symbols-outlined text-sm font-bold">payments</span> Collect
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-20 text-slate-400">
                                    <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">find_in_page</span>
                                    <p class="font-bold text-xs text-slate-500 uppercase tracking-widest">No completed bookings found</p>
                                    <p class="text-[10px] text-slate-400 mt-1">Try adapting filters or datepicker date values.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $bookings->links() }}
            </div>
        </div>
    </div>

    <!-- Booking Receipt Modal -->
    @if($showReceiptModal && $selectedBooking)
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[2000] flex items-center justify-center" wire:click.self="closeReceiptModal()">
            <div class="bg-white border rounded-2xl shadow-2xl max-w-sm w-full mx-4 overflow-hidden animate-zoom-in">
                <div class="p-3 bg-slate-50 border-b border-slate-200/60 flex justify-between items-center">
                    <h5 class="font-extrabold text-sm text-slate-800 flex items-center gap-1.5"><span class="material-symbols-outlined text-brand-600 text-lg">receipt_long</span> Booking Receipt</h5>
                    <button type="button" class="text-slate-400 hover:text-slate-600" wire:click="closeReceiptModal()"><span class="material-symbols-outlined">close</span></button>
                </div>
                
                <div class="p-0 max-h-[70vh] overflow-y-auto custom-scrollbar">
                    <!-- Printed Receipt Mockup -->
                    <div id="printable-booking-receipt" class="bg-white p-6 text-slate-800 font-mono text-[11px] leading-relaxed">
                        <div class="text-center mb-4">
                            <h4 class="text-sm font-extrabold uppercase text-slate-900">{{ $selectedBooking->venue->name ?? 'Sport Complex Hub' }}</h4>
                            <div class="text-[10px] text-slate-500">Booking Management Department</div>
                            @if($selectedBooking->venue->address)
                                <div class="text-[9px] text-slate-400">{{ $selectedBooking->venue->address }}</div>
                            @endif
                        </div>
                        <div class="border-b border-dashed border-slate-300 my-2"></div>
                        
                        <div class="space-y-0.5 text-slate-600 mb-3">
                            <div><strong>Booking ID:</strong> #{{ $selectedBooking->id }}</div>
                            <div><strong>Issued Date:</strong> {{ now()->format('Y-m-d H:i:s') }}</div>
                            <div><strong>Status:</strong> COMPLETED</div>
                        </div>

                        <div class="border-b border-slate-200 my-2"></div>
                        <div class="space-y-2 text-slate-800 text-xs">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Player Name:</span>
                                <span class="font-bold">{{ $selectedBooking->user_name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Phone Number:</span>
                                <span class="font-bold">{{ $selectedBooking->user_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Sport Activity:</span>
                                <span class="font-bold">{{ $selectedBooking->game_name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Court / Slot:</span>
                                <span class="font-bold">Court {{ $selectedBooking->court_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Play Date:</span>
                                <span class="font-bold">{{ \Carbon\Carbon::parse($selectedBooking->booking_date)->format('l, M d, Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Play Time:</span>
                                <span class="font-bold">{{ \Carbon\Carbon::parse($selectedBooking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($selectedBooking->end_time)->format('h:i A') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Duration:</span>
                                <span class="font-bold">{{ $selectedBooking->duration ?: 1 }} hour(s)</span>
                            </div>
                        </div>
                        <div class="border-b border-slate-200 my-2"></div>
                        
                        <div class="space-y-1 mt-2">
                            <div class="flex justify-between text-slate-600">
                                <span>Payment Mode:</span>
                                <span class="font-semibold">{{ strtoupper($selectedBooking->payment_method ?: 'Cash') }}</span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>Payment Status:</span>
                                <span class="font-semibold">{{ strtoupper($selectedBooking->payment_status ?: 'Paid') }}</span>
                            </div>
                            <div class="flex justify-between font-bold text-slate-900 border-t border-slate-200 pt-1 text-xs mt-1">
                                <span>TOTAL PAID:</span>
                                <span>LKR {{ number_format($selectedBooking->price ?: 0, 2) }}</span>
                            </div>
                        </div>
                        
                        <div class="border-b border-dashed border-slate-300 my-2"></div>
                        <div class="text-center mt-5 text-[10px] text-slate-400">
                            <div>Thank you for playing with us!</div>
                            <div>Please bring this receipt for admission checks.</div>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-600" wire:click="closeReceiptModal()">Close</button>
                    <button type="button" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold flex items-center gap-1.5" onclick="printBookingReceipt()"><span class="material-symbols-outlined text-base">print</span> Print Receipt</button>
                </div>
            </div>
        </div>
        
        <script>
            function printBookingReceipt() {
                var printContents = document.getElementById('printable-booking-receipt').innerHTML;
                var popupWin = window.open('', '_blank', 'width=420,height=600');
                popupWin.document.open();
                popupWin.document.write('<html><head><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"><style>body{font-family: monospace; padding: 20px;} hr{border-top:1px dashed #000;}</style></head><body onload="window.print();window.close()">' + printContents + '</body></html>');
                popupWin.document.close();
            }
        </script>
    @endif

    <!-- Collect Payment Modal -->
    @if($showPaymentModal && $paymentBooking)
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[2000] flex items-center justify-center" wire:click.self="closePaymentModal()">
            <div class="bg-white border rounded-2xl shadow-2xl max-w-sm w-full mx-4 overflow-hidden animate-zoom-in">
                <div class="p-4 bg-slate-50 border-b border-slate-200/60 flex justify-between items-center">
                    <h5 class="font-extrabold text-sm text-slate-800 flex items-center gap-1.5"><span class="material-symbols-outlined text-brand-600 text-lg">payments</span> Collect Payment</h5>
                    <button type="button" class="text-slate-400 hover:text-slate-600" wire:click="closePaymentModal()"><span class="material-symbols-outlined">close</span></button>
                </div>
                
                <div class="p-4 space-y-4 text-xs">
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/50 space-y-1">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Booking ID:</span>
                            <span class="font-bold text-slate-800">#{{ $paymentBooking->id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Player:</span>
                            <span class="font-bold text-slate-800">{{ $paymentBooking->user_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Amount Due:</span>
                            <span class="font-extrabold text-brand-600">LKR {{ number_format($paymentBooking->price ?: 0, 2) }}</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Select Payment Method</label>
                        <div class="grid grid-cols-3 gap-2">
                            <div class="border border-slate-200 rounded-xl p-2 text-center cursor-pointer flex flex-col items-center gap-1 transition-all {{ $collectPaymentMethod === 'cash' ? 'border-brand-500 bg-brand-50 shadow-sm text-brand-900 font-bold' : 'hover:bg-slate-50 bg-white text-slate-600' }}"
                                 wire:click="$set('collectPaymentMethod', 'cash')">
                                <span class="material-symbols-outlined text-lg">payments</span>
                                <span class="text-[9px]">Cash</span>
                            </div>
                            <div class="border border-slate-200 rounded-xl p-2 text-center cursor-pointer flex flex-col items-center gap-1 transition-all {{ $collectPaymentMethod === 'card' ? 'border-brand-500 bg-brand-50 shadow-sm text-brand-900 font-bold' : 'hover:bg-slate-50 bg-white text-slate-600' }}"
                                 wire:click="$set('collectPaymentMethod', 'card')">
                                <span class="material-symbols-outlined text-lg">credit_card</span>
                                <span class="text-[9px]">Card</span>
                            </div>
                            <div class="border border-slate-200 rounded-xl p-2 text-center cursor-pointer flex flex-col items-center gap-1 transition-all {{ $collectPaymentMethod === 'upi' ? 'border-brand-500 bg-brand-50 shadow-sm text-brand-900 font-bold' : 'hover:bg-slate-50 bg-white text-slate-600' }}"
                                 wire:click="$set('collectPaymentMethod', 'upi')">
                                <span class="material-symbols-outlined text-lg">qr_code_2</span>
                                <span class="text-[9px]">UPI / QR</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-600" wire:click="closePaymentModal()">Cancel</button>
                    <button type="button" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold" wire:click="collectPayment()">Confirm Payment</button>
                </div>
            </div>
        </div>
    @endif
</div>
