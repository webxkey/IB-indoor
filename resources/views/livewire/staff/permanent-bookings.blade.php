<div class="permanent-bookings-ledger py-2" wire:poll.15s>
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
                    <span class="p-2 bg-brand-100 text-brand-700 rounded-xl flex items-center justify-center shadow-sm"><span class="material-symbols-outlined text-lg">calendar_month</span></span>
                    Permanent Bookings
                </h1>
                <p class="text-xs text-slate-500 mt-1">Manage and view all recurring permanent bookings.</p>
            </div>
            <div>
                <a href="{{ route('staff.bookings') }}" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl text-xs flex items-center gap-1.5 transition-colors shadow-md">
                    <span class="material-symbols-outlined text-lg">calendar_month</span> Manage Live Scheduler
                </a>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session('message'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200/80 text-emerald-800 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-xl text-emerald-600">check_circle</span>
                    <span>{{ session('message') }}</span>
                </div>
                <button type="button" class="text-emerald-500 hover:text-emerald-700" onclick="this.parentElement.remove()">
                    <span class="material-symbols-outlined text-lg">close</span>
                </button>
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200/80 text-rose-800 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-xl text-rose-600">error</span>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" class="text-rose-500 hover:text-rose-700" onclick="this.parentElement.remove()">
                    <span class="material-symbols-outlined text-lg">close</span>
                </button>
            </div>
        @endif

        <!-- Filters Box -->
        <div class="glass-card rounded-2xl shadow-sm p-4 mb-6">
            <div class="flex flex-col md:flex-row gap-3 items-end justify-between">
                <div class="w-full md:w-1/3">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Search Bookings</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                        <input type="text" 
                               class="w-full pl-8 pr-3 py-2 bg-slate-50/50 border border-slate-200 rounded-xl outline-none text-xs font-semibold focus:ring-1 focus:ring-brand-500 focus:border-brand-500" 
                               placeholder="Search by ID..." 
                               wire:model.live.debounce.300ms="search">
                    </div>
                </div>

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
                            <th class="pl-4 py-3" style="width: 80px;">ID</th>
                            <th class="py-3">Player Info</th>
                            <th class="py-3">Time & Duration</th>
                            <th class="py-3">Price</th>
                            <th class="py-3">Schedule</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-center">Created</th>
                            <th class="pr-4 py-3 text-right" style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($bookings as $booking)
                            @php
                                $userName = 'Unknown';
                                $userNumber = 'Unknown';
                                $isMobile = true;

                                if ($booking->user) {
                                    $userName = trim(($booking->user->first_name ?? '') . ' ' . ($booking->user->last_name ?? ''));
                                    $userName = $userName ?: 'Unknown';
                                    $userNumber = $booking->user->phone_number ?? 'Unknown';
                                }

                                $firstChild = $booking->bookings->first();
                                if ($firstChild) {
                                    if ($firstChild->admin_comments === 'web_book') {
                                        $isMobile = false;
                                        $userName = !empty($firstChild->user_name) ? $firstChild->user_name : $userName;
                                        $userNumber = !empty($firstChild->user_number) ? $firstChild->user_number : $userNumber;
                                    } else {
                                        $userName = ($userName === 'Unknown' && !empty($firstChild->user_name)) ? $firstChild->user_name : $userName;
                                        $userNumber = ($userNumber === 'Unknown' && !empty($firstChild->user_number)) ? $firstChild->user_number : $userNumber;
                                    }
                                }

                                $activeChildCount = $booking->bookings->where('status', '!=', 'Cancelled')->count();
                                $totalChildCount = $booking->bookings->count();
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="pl-4 py-3 font-mono font-bold text-slate-900 text-sm">#{{ $booking->id }}</td>
                                <td class="py-3">
                                    <div class="font-bold text-slate-800 flex items-center gap-1">
                                        {{ $userName }}
                                        @if($isMobile)
                                            <span class="material-symbols-outlined text-[14px] text-brand-500" title="Booked via Mobile App">smartphone</span>
                                        @endif
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">{{ $userNumber }}</div>
                                </td>
                                <td class="py-3">
                                    <div class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">{{ $booking->duration }} mins</div>
                                </td>
                                <td class="py-3 font-black text-slate-950 text-sm">
                                    LKR {{ number_format($booking->price, 2) }}
                                </td>
                                <td class="py-3">
                                    @if(is_array($booking->recurring_config) && isset($booking->recurring_config['selected_days']) && is_array($booking->recurring_config['selected_days']))
                                        <div class="flex flex-wrap gap-1">
                                            @php
                                                $dayMap = [0 => 'SUN', 1 => 'MON', 2 => 'TUE', 3 => 'WED', 4 => 'THU', 5 => 'FRI', 6 => 'SAT', 7 => 'SUN'];
                                            @endphp
                                            @foreach($booking->recurring_config['selected_days'] as $dayNum)
                                                <span class="inline-block bg-slate-100 text-slate-600 rounded px-1.5 py-0.5 text-[9px] font-bold uppercase">{{ $dayMap[(int)$dayNum] ?? 'DAY '.$dayNum }}</span>
                                            @endforeach
                                        </div>
                                        @if(isset($booking->recurring_config['months']))
                                            <div class="text-[9px] text-slate-400 mt-1 font-bold uppercase">{{ $booking->recurring_config['months'] }} Month(s)</div>
                                        @endif
                                    @else
                                        <span class="text-[10px] text-slate-400">Not set</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    @if($booking->is_active && $activeChildCount > 0)
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold border bg-emerald-50 text-emerald-700 border-emerald-200">Active</span>
                                        <div class="text-[9px] text-slate-400 mt-0.5 font-medium">{{ $activeChildCount }}/{{ $totalChildCount }} slots active</div>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold border bg-rose-50 text-rose-700 border-rose-200">Inactive</span>
                                    @endif
                                </td>
                                <td class="py-3 text-center text-[10px] font-semibold text-slate-500">
                                    {{ \Carbon\Carbon::parse($booking->created_at)->format('M d, Y') }}
                                </td>
                                <td class="pr-4 py-3 text-right">
                                    @if($booking->is_active && $activeChildCount > 0)
                                        <button wire:click="openCancelModal({{ $booking->id }})" 
                                                class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold rounded-xl text-xs transition-colors inline-flex items-center gap-1 shadow-sm border border-rose-200/80">
                                            <span class="material-symbols-outlined text-sm">cancel</span>
                                            Cancel
                                        </button>
                                    @else
                                        <button wire:click="openCancelModal({{ $booking->id }})" 
                                                class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl text-xs transition-colors inline-flex items-center gap-1 shadow-sm border border-slate-200">
                                            <span class="material-symbols-outlined text-sm">info</span>
                                            Details
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-20 text-slate-400">
                                    <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">find_in_page</span>
                                    <p class="font-bold text-xs text-slate-500 uppercase tracking-widest">No permanent bookings found</p>
                                    <p class="text-[10px] text-slate-400 mt-1">Try adapting your search filter.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($bookings->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $bookings->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Cancellation Modal -->
    @if($showCancelModal && $selectedPermanentBooking)
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="glass-card bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] flex flex-col overflow-hidden border border-slate-200 transition-all">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center gap-2.5">
                        <div class="p-2 bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-xl">event_busy</span>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Cancel Booking #{{ $selectedPermanentBooking->id }}</h3>
                            <p class="text-xs text-slate-500">Choose fully booking cancel or selection to slot cancel</p>
                        </div>
                    </div>
                    <button wire:click="closeCancelModal" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-xl">close</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 overflow-y-auto custom-scrollbar space-y-5">
                    @if($cancelType === 'choose')
                        <div class="text-xs font-semibold text-slate-600 mb-2">
                            Select how you would like to handle this booking cancellation:
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Card 1: Fully Booking Cancel -->
                            <div wire:click="setCancelType('full')" class="cursor-pointer group p-5 border-2 border-slate-200 hover:border-rose-500 rounded-2xl bg-white hover:bg-rose-50/30 transition-all shadow-sm flex flex-col justify-between">
                                <div>
                                    <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                        <span class="material-symbols-outlined text-2xl">delete_sweep</span>
                                    </div>
                                    <h4 class="text-sm font-extrabold text-slate-900 mb-1 group-hover:text-rose-600 transition-colors">Fully Booking Cancel</h4>
                                    <p class="text-xs text-slate-500 leading-relaxed">
                                        Cancel the entire permanent booking series and deactivate all remaining slot occurrences.
                                    </p>
                                </div>
                                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-rose-600">
                                    <span>Cancel Entire Series</span>
                                    <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform">arrow_forward</span>
                                </div>
                            </div>

                            <!-- Card 2: Selection to Slot Cancel -->
                            <div wire:click="setCancelType('slots')" class="cursor-pointer group p-5 border-2 border-slate-200 hover:border-amber-500 rounded-2xl bg-white hover:bg-amber-50/30 transition-all shadow-sm flex flex-col justify-between">
                                <div>
                                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                        <span class="material-symbols-outlined text-2xl">checklist</span>
                                    </div>
                                    <h4 class="text-sm font-extrabold text-slate-900 mb-1 group-hover:text-amber-600 transition-colors">Selection to Slot Cancel</h4>
                                    <p class="text-xs text-slate-500 leading-relaxed">
                                        Select specific slot occurrences/dates to cancel while keeping other scheduled slots active.
                                    </p>
                                </div>
                                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-amber-600">
                                    <span>Select Specific Slots</span>
                                    <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform">arrow_forward</span>
                                </div>
                            </div>
                        </div>

                    @elseif($cancelType === 'full')
                        <div class="space-y-4">
                            <div class="flex items-center gap-2">
                                <button wire:click="setCancelType('choose')" class="text-xs text-slate-500 hover:text-slate-800 font-bold flex items-center gap-1 transition-colors">
                                    <span class="material-symbols-outlined text-base">arrow_back</span> Back to options
                                </button>
                            </div>

                            <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 space-y-2">
                                <div class="font-bold flex items-center gap-1.5 text-sm">
                                    <span class="material-symbols-outlined text-lg text-rose-600">warning</span>
                                    Confirm Fully Booking Cancel
                                </div>
                                <p>
                                    Are you sure you want to fully cancel Permanent Booking <strong>#{{ $selectedPermanentBooking->id }}</strong>?
                                    This will set the permanent booking status to inactive and cancel all associated slot occurrences.
                                </p>
                            </div>

                            @php
                                $modalUserName = 'Unknown';
                                $modalUserNumber = 'Unknown';
                                if ($selectedPermanentBooking->user) {
                                    $modalUserName = trim(($selectedPermanentBooking->user->first_name ?? '') . ' ' . ($selectedPermanentBooking->user->last_name ?? ''));
                                    $modalUserName = $modalUserName ?: 'Unknown';
                                    $modalUserNumber = $selectedPermanentBooking->user->phone_number ?? 'Unknown';
                                }
                                $modalFirstChild = $selectedPermanentBooking->bookings->first();
                                if ($modalFirstChild) {
                                    $modalUserName = !empty($modalFirstChild->user_name) ? $modalFirstChild->user_name : $modalUserName;
                                    $modalUserNumber = !empty($modalFirstChild->user_number) ? $modalFirstChild->user_number : $modalUserNumber;
                                }
                                $activeSlotsCount = $selectedPermanentBooking->bookings->where('status', '!=', 'Cancelled')->count();
                                $totalSlotsCount = $selectedPermanentBooking->bookings->count();
                            @endphp

                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/80 space-y-2 text-xs">
                                <div class="flex justify-between border-b border-slate-200/60 pb-2">
                                    <span class="text-slate-400 font-semibold">Player:</span>
                                    <span class="font-bold text-slate-800">{{ $modalUserName }} ({{ $modalUserNumber }})</span>
                                </div>
                                <div class="flex justify-between border-b border-slate-200/60 pb-2">
                                    <span class="text-slate-400 font-semibold">Time & Duration:</span>
                                    <span class="font-bold text-slate-800">
                                        {{ \Carbon\Carbon::parse($selectedPermanentBooking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($selectedPermanentBooking->end_time)->format('h:i A') }} ({{ $selectedPermanentBooking->duration }} mins)
                                    </span>
                                </div>
                                <div class="flex justify-between border-b border-slate-200/60 pb-2">
                                    <span class="text-slate-400 font-semibold">Price per slot:</span>
                                    <span class="font-bold text-slate-900">LKR {{ number_format($selectedPermanentBooking->price, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-400 font-semibold">Active Slots Count:</span>
                                    <span class="font-extrabold text-rose-600">{{ $activeSlotsCount }} / {{ $totalSlotsCount }} active slots</span>
                                </div>
                            </div>
                        </div>

                    @elseif($cancelType === 'slots')
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <button wire:click="setCancelType('choose')" class="text-xs text-slate-500 hover:text-slate-800 font-bold flex items-center gap-1 transition-colors">
                                    <span class="material-symbols-outlined text-base">arrow_back</span> Back to options
                                </button>
                                <span class="text-xs font-semibold text-slate-500">
                                    Selected: <strong class="text-slate-900">{{ count($selectedSlots) }}</strong> slot(s)
                                </span>
                            </div>

                            <p class="text-xs text-slate-500">
                                Select the specific slot occurrences you wish to cancel:
                            </p>

                            <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                                <table class="w-full text-left text-xs align-middle">
                                    <thead>
                                        <tr class="bg-slate-100 text-[10px] font-bold text-slate-500 uppercase border-b border-slate-200">
                                            <th class="p-3 w-10 text-center">
                                                <input type="checkbox" wire:model.live="selectAllSlots" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                            </th>
                                            <th class="py-3 px-2">Date</th>
                                            <th class="py-3 px-2">Time</th>
                                            <th class="py-3 px-2">Court</th>
                                            <th class="py-3 px-2 text-center">Payment</th>
                                            <th class="py-3 px-2 text-right pr-3">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($selectedPermanentBooking->bookings as $child)
                                            <tr class="hover:bg-slate-50/80 transition-colors {{ $child->status === 'Cancelled' ? 'bg-slate-50/50 opacity-60' : '' }}">
                                                <td class="p-3 text-center">
                                                    @if($child->status !== 'Cancelled')
                                                        <input type="checkbox" value="{{ $child->id }}" wire:model.live="selectedSlots" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                                    @else
                                                        <span class="material-symbols-outlined text-slate-300 text-base">block</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-2 font-bold text-slate-800">
                                                    {{ \Carbon\Carbon::parse($child->booking_date ?? $child->date)->format('M d, Y (D)') }}
                                                </td>
                                                <td class="py-3 px-2 text-slate-600 font-medium">
                                                    {{ \Carbon\Carbon::parse($child->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($child->end_time)->format('h:i A') }}
                                                </td>
                                                <td class="py-3 px-2 font-semibold text-slate-700">
                                                    Court {{ $child->court_number ?? '1' }}
                                                </td>
                                                <td class="py-3 px-2 text-center">
                                                    <span class="inline-block px-2 py-0.5 rounded text-[9px] font-bold uppercase {{ $child->payment_status === 'Paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                                        {{ $child->payment_status ?? 'Pending' }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-2 text-right pr-3">
                                                    @if($child->status === 'Cancelled')
                                                        <span class="inline-block px-2 py-0.5 rounded text-[9px] font-bold bg-slate-200 text-slate-500 uppercase">Cancelled</span>
                                                    @else
                                                        <span class="inline-block px-2 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-700 uppercase">{{ $child->status }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-8 text-slate-400">
                                                    No slot occurrences found for this permanent booking.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-3">
                    <button wire:click="closeCancelModal" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl text-xs hover:bg-slate-100 transition-colors shadow-sm">
                        Close
                    </button>

                    @if($cancelType === 'full')
                        <button wire:click="cancelFullBooking" wire:loading.attr="disabled" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 disabled:opacity-50 text-white font-bold rounded-xl text-xs flex items-center gap-1.5 transition-colors shadow-md">
                            <span wire:loading.remove wire:target="cancelFullBooking" class="material-symbols-outlined text-base">delete_forever</span>
                            <span wire:loading wire:target="cancelFullBooking" class="animate-spin material-symbols-outlined text-base">refresh</span>
                            Confirm Fully Booking Cancel
                        </button>
                    @elseif($cancelType === 'slots')
                        <button wire:click="cancelSelectedSlots" wire:loading.attr="disabled" @if(empty($selectedSlots)) disabled @endif class="px-5 py-2 bg-amber-600 hover:bg-amber-700 disabled:opacity-50 text-white font-bold rounded-xl text-xs flex items-center gap-1.5 transition-colors shadow-md">
                            <span wire:loading.remove wire:target="cancelSelectedSlots" class="material-symbols-outlined text-base">event_busy</span>
                            <span wire:loading wire:target="cancelSelectedSlots" class="animate-spin material-symbols-outlined text-base">refresh</span>
                            Cancel Selected Slots ({{ count($selectedSlots) }})
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
