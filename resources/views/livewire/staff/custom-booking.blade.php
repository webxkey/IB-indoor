<div class="custom-booking-container py-2" wire:poll.15s>
    <!-- Tailwind CSS Play CDN & Google Fonts -->
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
        .glass-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(226, 232, 240, 0.8); }
    </style>

    <div class="min-h-screen bg-slate-50/50 p-2 font-sans text-slate-800 antialiased">
        <!-- Flash Messages -->
        @if (session('message'))
            <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200/80 text-emerald-800 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-xl text-emerald-600">check_circle</span>
                    <span>{{ session('message') }}</span>
                </div>
                <button type="button" class="text-emerald-500 hover:text-emerald-700" onclick="this.parentElement.remove()">
                    <span class="material-symbols-outlined text-lg">close</span>
                </button>
            </div>
        @endif

        <!-- Header area -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 border-b border-slate-200/60 pb-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                    <span class="p-2.5 bg-brand-100 text-brand-700 rounded-xl flex items-center justify-center shadow-sm">
                        <i class="fas fa-cubes text-xl"></i>
                    </span>
                    Custom Package Bookings Ledger
                </h1>
                <p class="text-xs text-slate-500 mt-1">Manage package-wise bookings, build multi-slot custom packages with private booking pricing, and perform full, day-wise, or slot-wise cancellations.</p>
            </div>
            <div class="flex items-center gap-2">
                <button wire:click="openCreateModal" class="px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-extrabold rounded-xl text-xs flex items-center gap-1.5 transition-all shadow-md">
                    <span class="material-symbols-outlined text-lg">add_circle</span> + Add Custom Booking
                </button>
                <a href="{{ route('staff.bookings') }}" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl text-xs flex items-center gap-1.5 transition-colors shadow-md">
                    <span class="material-symbols-outlined text-lg">calendar_month</span> Bookings Calendar
                </a>
            </div>
        </div>

        <!-- Filters Box -->
        <div class="glass-card rounded-2xl shadow-sm p-4 mb-6">
            <div class="flex flex-col md:flex-row gap-3 items-end justify-between">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 flex-grow">
                    <!-- Text Search -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Search Package Bookings</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                            <input type="text" 
                                   class="w-full pl-8 pr-3 py-2 bg-slate-50/50 border border-slate-200 rounded-xl outline-none text-xs font-semibold focus:ring-1 focus:ring-brand-500 focus:border-brand-500" 
                                   placeholder="Player name, phone, game, or Package Code..." 
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

        <!-- Main Package-Wise Bookings Ledger Table -->
        <div class="glass-card rounded-2xl shadow-sm overflow-hidden flex flex-col mb-6">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse text-xs align-middle">
                    <thead>
                        <tr class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                            <th class="pl-4 py-3" style="width: 130px;">PKG / BKG CODE</th>
                            <th class="py-3">Player Info</th>
                            <th class="py-3">Sports & Courts Included</th>
                            <th class="py-3">Booking Dates</th>
                            <th class="py-3 text-center" style="width: 90px;">Total Slots</th>
                            <th class="py-3 text-center" style="width: 110px;">Payment Status</th>
                            <th class="py-3 text-right" style="width: 110px;">Package Total</th>
                            <th class="pr-4 py-3 text-center" style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($groupedPackages as $pkg)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <!-- Code -->
                                <td class="pl-4 py-3 font-mono text-emerald-800 font-extrabold">
                                    {{ $pkg['package_code'] }}
                                    @if(!empty($pkg['is_private']))
                                        <span class="block text-[9px] font-bold text-amber-800 bg-amber-100 px-1.5 py-0.5 rounded max-w-max mt-0.5">
                                            🔒 Private
                                        </span>
                                    @endif
                                </td>

                                <!-- Player Info -->
                                <td class="py-3">
                                    <div class="font-bold text-slate-900">{{ $pkg['user_name'] ?: 'N/A' }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $pkg['user_number'] ?: 'N/A' }}</div>
                                </td>

                                <!-- Sports & Courts -->
                                <td class="py-3 font-semibold text-slate-800">
                                    {{ $pkg['sports_summary'] }}
                                </td>

                                <!-- Dates -->
                                <td class="py-3 text-slate-700 font-medium">
                                    {{ $pkg['date_range_summary'] }}
                                </td>

                                <!-- Total Slots -->
                                <td class="py-3 text-center font-bold text-slate-800">
                                    <span class="px-2 py-0.5 bg-slate-100 rounded-md">
                                        {{ $pkg['total_slots'] }} Slots
                                    </span>
                                </td>

                                <!-- Payment Status -->
                                <td class="py-3 text-center">
                                    @php
                                        $statusClass = match(strtolower($pkg['payment_status'] ?? 'pending')) {
                                            'paid' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                            'partial' => 'bg-amber-100 text-amber-800 border-amber-200',
                                            default => 'bg-slate-100 text-slate-700 border-slate-200',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 text-[10px] font-extrabold rounded-full border {{ $statusClass }}">
                                        {{ ucfirst($pkg['payment_status'] ?? 'Pending') }}
                                    </span>
                                </td>

                                <!-- Total Price -->
                                <td class="py-3 text-right font-extrabold text-slate-900">
                                    Rs. {{ number_format($pkg['total_price'], 2) }}
                                </td>

                                <!-- Actions -->
                                <td class="pr-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <button wire:click="viewPackageDetails('{{ $pkg['package_code'] }}')" 
                                                class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 rounded-lg text-[11px] font-bold transition-colors">
                                            <i class="fas fa-eye me-1"></i> View
                                        </button>

                                        @if(strtolower($pkg['status']) !== 'cancelled')
                                            <button wire:click="initiateCancelPackage('{{ $pkg['package_code'] }}')" 
                                                    class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-lg text-[11px] font-bold transition-colors">
                                                Cancel
                                            </button>
                                        @else
                                            <span class="text-[10px] font-bold text-rose-500 uppercase bg-rose-50 px-2 py-0.5 rounded border border-rose-200">
                                                Cancelled
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">event_busy</span>
                                        <p class="text-xs font-semibold text-slate-500">No package bookings found.</p>
                                        <p class="text-[11px] text-slate-400 mt-1">Click "+ Add Custom Booking" above to build a multi-slot package.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            @if($groupedPackages->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $groupedPackages->links() }}
                </div>
            @endif
        </div>

        <!-- Modal 1: Multi-Booking Package Builder Modal -->
        @if($showCreateModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fade-in">
                <div class="bg-white rounded-3xl shadow-2xl max-w-4xl w-full overflow-hidden border border-slate-100">
                    <!-- Solid Green Header -->
                    <div class="bg-emerald-700 text-white px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center font-extrabold text-base">+</span>
                            <h2 class="font-extrabold text-lg tracking-wide">Book Slot / Create Package</h2>
                        </div>
                        <button wire:click="closeCreateModal" type="button" class="text-white/80 hover:text-white p-1 transition-colors">
                            <span class="material-symbols-outlined text-xl">close</span>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 max-h-[85vh] overflow-y-auto custom-scrollbar space-y-5">
                        <!-- Top Section: Customer Details -->
                        <div class="p-2 bg-slate-50/90 border border-slate-200/80 rounded-2xl space-y-3">
                            <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base">person</span> Customer Information
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Player Name <span class="text-rose-500">*</span></label>
                                    <input type="text" placeholder="Enter player name" wire:model="playerName" class="w-full py-2 px-3 bg-white border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    @error('playerName') <span class="text-[10px] text-rose-500 font-semibold">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Phone Number <span class="text-rose-500">*</span></label>
                                    <input type="text" placeholder="Enter phone number" wire:model="phoneNumber" class="w-full py-2 px-3 bg-white border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    @error('phoneNumber') <span class="text-[10px] text-rose-500 font-semibold">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Middle Section: Slot Builder Controls -->
                        <div class="p-2 bg-emerald-50/40 border-2 border-emerald-500/60 rounded-2xl space-y-4">
                            <h3 class="text-xs font-extrabold text-emerald-800 uppercase tracking-wider flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-emerald-600">add_task</span> Add Slot Item to Package
                            </h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 items-end">
                                <!-- GAME -->
                                <div class="sm:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">GAME</label>
                                    <select wire:model.live="selectedGame" class="w-full py-2 px-3 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500">
                                        @forelse($sports as $sport)
                                            <option value="{{ $sport->name }}">{{ $sport->name }}</option>
                                        @empty
                                            <option value="">No Sports Available</option>
                                        @endforelse
                                    </select>
                                </div>

                                <!-- COURT -->
                                <div class="sm:col-span-1">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">COURT</label>
                                    <select wire:model="selectedCourt" class="w-full py-2 px-3 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500">
                                        @foreach($availableCourts as $court)
                                            <option value="{{ $court }}">{{ $court }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- DATE -->
                                <div class="sm:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                                        DATE @if($selectedDate) <span class="text-emerald-700">({{ \Carbon\Carbon::parse($selectedDate)->format('l') }})</span> @endif
                                    </label>
                                    <input type="date" wire:model.live="selectedDate" class="w-full py-2 px-3 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 items-end">
                                <!-- START TIME -->
                                <div class="sm:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">START TIME</label>
                                    <select wire:model.live="selectedTime" class="w-full py-2 px-3 bg-white border border-slate-200 rounded-xl text-xs font-bold text-emerald-700 focus:ring-2 focus:ring-emerald-500">
                                        @foreach($timeOptions as $t)
                                            <option value="{{ $t['value'] }}">{{ $t['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- END TIME -->
                                <div class="sm:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">END TIME</label>
                                    <select wire:model.live="selectedEndTime" class="w-full py-2 px-3 bg-white border border-slate-200 rounded-xl text-xs font-bold text-emerald-700 focus:ring-2 focus:ring-emerald-500">
                                        @foreach($endTimeOptions as $et)
                                            <option value="{{ $et['value'] }}">{{ $et['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Private Booking Checkbox -->
                                <div class="sm:col-span-1 pb-1">
                                    <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700">
                                        <input type="checkbox" wire:model.live="is_private" class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                                        <span class="flex items-center gap-1 text-slate-800">
                                            <span class="material-symbols-outlined text-sm text-amber-600">lock</span> Private
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <!-- Full Day Timeline & Availability Grid for Selected Date -->
                            @php
                                $dayTimeline = $this->getDaySlotsTimeline();
                                $rangeInfo = $this->getRangeAvailabilityInfo();
                            @endphp

                            @if(!empty($dayTimeline))
                                <div class="pt-3 border-t border-emerald-200/80">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[10px] font-extrabold text-slate-600 uppercase tracking-wider flex items-center gap-1">
                                            <span class="material-symbols-outlined text-xs text-emerald-600">view_timeline</span>
                                            Full Day Schedule for {{ $selectedCourt }} ({{ \Carbon\Carbon::parse($selectedDate)->format('D, M j') }})
                                        </span>
                                        <div class="flex items-center gap-3 text-[9.5px] font-bold">
                                            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span> Available</span>
                                            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block"></span> Booked</span>
                                            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-amber-500 inline-block"></span> Blocked</span>
                                        </div>
                                    </div>

                                    <!-- Hourly Slot Pills Grid (Expanded across max-w-4xl) -->
                                    <div class="grid grid-cols-6 sm:grid-cols-9 md:grid-cols-18 gap-1.5 p-2 bg-white border border-slate-200/90 rounded-2xl shadow-inner">
                                        @foreach($dayTimeline as $tSlot)
                                            @php
                                                $pillClass = match($tSlot['status']) {
                                                    'booked' => 'bg-rose-100 text-rose-800 border-rose-200 hover:bg-rose-200 cursor-not-allowed',
                                                    'blocked' => 'bg-amber-100 text-amber-800 border-amber-200 hover:bg-amber-200 cursor-not-allowed',
                                                    default => 'bg-emerald-50 hover:bg-emerald-200 text-emerald-800 border-emerald-300 cursor-pointer',
                                                };
                                                $isSelectedStart = ($tSlot['time_val'] === $selectedTime);
                                                if ($isSelectedStart) {
                                                    $pillClass .= ' ring-2 ring-emerald-600 font-black shadow-md';
                                                }
                                            @endphp
                                            <button type="button"
                                                    @if($tSlot['status'] === 'available') wire:click="selectTimeSlotFromPill('{{ $tSlot['time_val'] }}')" @endif
                                                    title="{{ $tSlot['slot_range'] }} - {{ ucfirst($tSlot['status']) }} {{ $tSlot['booked_by'] ? '(' . $tSlot['booked_by'] . ')' : '' }}"
                                                    class="py-1.5 px-1 rounded-xl border text-[9.5px] font-extrabold text-center transition-all flex flex-col items-center justify-center min-w-0 {{ $pillClass }}">
                                                <span class="truncate w-full">{{ $tSlot['label'] }}</span>
                                                <span class="text-[8px] font-semibold opacity-80">
                                                    @if($tSlot['status'] === 'available')
                                                        Avail
                                                    @elseif($tSlot['status'] === 'booked')
                                                        Taken
                                                    @else
                                                        Block
                                                    @endif
                                                </span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Selected Time Range Availability Live Preview & Add Button Row -->
                            <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 pt-2">
                                @if($rangeInfo)
                                    <div class="flex-grow p-3 rounded-xl border text-xs {{ $rangeInfo['is_fully_available'] ? 'bg-emerald-50 border-emerald-300 text-emerald-900' : 'bg-amber-50 border-amber-300 text-amber-900' }}">
                                        <div class="flex items-center justify-between font-bold">
                                            <span class="flex items-center gap-1.5">
                                                <span class="material-symbols-outlined text-base {{ $rangeInfo['is_fully_available'] ? 'text-emerald-600' : 'text-amber-600' }}">
                                                    {{ $rangeInfo['is_fully_available'] ? 'check_circle' : 'warning' }}
                                                </span>
                                                Range Status ({{ \Carbon\Carbon::parse($selectedTime)->format('g:i A') }} - {{ \Carbon\Carbon::parse($selectedEndTime)->format('g:i A') }}):
                                            </span>
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold {{ $rangeInfo['is_fully_available'] ? 'bg-emerald-200 text-emerald-800' : 'bg-amber-200 text-amber-800' }}">
                                                {{ $rangeInfo['avail_count'] }}/{{ $rangeInfo['total_slots'] }} Slots Free
                                            </span>
                                        </div>

                                        @if(!$rangeInfo['is_fully_available'] && !empty($rangeInfo['conflict_details']))
                                            <div class="mt-1.5 text-[11px] text-amber-800 space-y-0.5 border-t border-amber-200/80 pt-1.5">
                                                <span class="font-bold block text-[10px] text-amber-900 uppercase">Conflicts in selected range (will be skipped):</span>
                                                @foreach($rangeInfo['conflict_details'] as $conf)
                                                    <div class="flex items-center gap-1">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                        <span>{{ $conf }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <button type="button" wire:click="addDraftItem" class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-extrabold rounded-xl text-xs shadow-md transition-all flex items-center justify-center gap-1.5 shrink-0">
                                    <i class="fas fa-plus-circle text-sm me-1"></i> + Add to Package List
                                </button>
                            </div>

                            @if(session('draft_message'))
                                <div class="text-[11px] font-bold text-emerald-700 bg-emerald-100/80 px-3 py-1 rounded-lg">
                                    {{ session('draft_message') }}
                                </div>
                            @endif
                        </div>

                        <!-- In-Modal Staging Table (Draft Package Items) -->
                        <div class="space-y-2">
                            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center justify-between">
                                <span>Package Draft Items List ({{ count($draftPackageItems) }})</span>
                                @if(!empty($draftPackageItems))
                                    <span class="text-emerald-700 font-extrabold">Total: Rs. {{ number_format(array_sum(array_column($draftPackageItems, 'estimated_price')), 2) }}</span>
                                @endif
                            </h3>

                            @error('draft')
                                <div class="p-2 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="border border-slate-200 rounded-2xl overflow-hidden">
                                <table class="w-full text-left text-xs align-middle">
                                    <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase">
                                        <tr>
                                            <th class="p-2.5">Date & Day</th>
                                            <th class="p-2.5">Sport & Court</th>
                                            <th class="p-2.5">Time Slot</th>
                                            <th class="p-2.5 text-right">Est. Price</th>
                                            <th class="p-2.5 text-center">Status</th>
                                            <th class="p-2.5 text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($draftPackageItems as $item)
                                            <tr class="hover:bg-slate-50/80">
                                                <td class="p-2.5 font-bold text-slate-800">
                                                    {{ $item['formatted_date'] }}
                                                    @if(!empty($item['is_private']))
                                                        <span class="block text-[9px] font-extrabold text-amber-800 bg-amber-100 px-1 py-0.2 rounded max-w-max mt-0.5">
                                                            🔒 Private
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="p-2.5 font-semibold text-emerald-700">{{ $item['game_name'] }} (Court {{ $item['court_number'] }})</td>
                                                <td class="p-2.5 font-medium text-slate-700">{{ $item['formatted_time'] }}</td>
                                                <td class="p-2.5 text-right font-extrabold text-slate-900">Rs. {{ number_format($item['estimated_price'], 2) }}</td>
                                                <td class="p-2.5 text-center">
                                                    @if($item['blocked_count'] > 0)
                                                        <span class="px-1.5 py-0.5 bg-amber-100 text-amber-800 text-[9px] font-extrabold rounded">
                                                            Partial ({{ $item['avail_count'] }} Avail)
                                                        </span>
                                                    @else
                                                        <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-800 text-[9px] font-extrabold rounded">
                                                            Available
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="p-2.5 text-center">
                                                    <button type="button" wire:click="removeDraftItem('{{ $item['temp_id'] }}')" class="text-rose-500 hover:text-rose-700 font-bold p-1">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="p-4 text-center text-slate-400 text-xs">
                                                    No slots added yet. Select options above and click "+ Add to Package List".
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Financials / Advance Payment -->
                        <div class="pt-2">
                            <div class="max-w-xs">
                                <label class="block text-xs font-bold text-slate-700 mb-1">Advance Payment (Rs.)</label>
                                <input type="number" min="0" placeholder="0" wire:model="advance_amount" class="w-full py-2 px-3 bg-white border border-slate-200 rounded-xl text-xs font-semibold">
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                            <button type="button" wire:click="closeCreateModal" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs hover:bg-slate-200">
                                Cancel
                            </button>

                            <button type="button" wire:click="createCustomPackageBooking" wire:loading.attr="disabled" class="px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold rounded-xl text-xs shadow-md flex items-center gap-1.5">
                                <span wire:loading.remove><i class="fas fa-check-double me-1"></i> Confirm & Create Package Booking</span>
                                <span wire:loading><i class="fas fa-spinner fa-spin me-1"></i> Processing Package...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Modal 2: Package View Details Modal (Grouped Date & Sport Wise) -->
        @if($showViewPackageModal && $viewPackageGroup)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fade-in">
                <div class="bg-white rounded-3xl shadow-2xl max-w-3xl w-full overflow-hidden border border-slate-100">
                    <div class="bg-slate-900 text-white p-5 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-2xl text-emerald-400">receipt_long</span>
                            <div>
                                <h3 class="font-extrabold text-base">Package Details - {{ $viewPackageGroup['package_code'] }}</h3>
                                <p class="text-xs text-white/80">Player: <span class="font-bold text-white">{{ $viewPackageGroup['user_name'] }}</span> ({{ $viewPackageGroup['user_number'] }})</p>
                            </div>
                        </div>
                        <button wire:click="closeViewPackageModal" class="text-white/80 hover:text-white p-1">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <div class="p-6 max-h-[75vh] overflow-y-auto custom-scrollbar space-y-5">
                        <!-- Overview Header -->
                        <div class="grid grid-cols-3 gap-3 bg-slate-50 p-4 rounded-2xl text-xs border border-slate-200/80">
                            <div>
                                <span class="text-slate-400 font-bold uppercase text-[10px] block">Sessions / Total Slots</span>
                                <span class="font-extrabold text-slate-800 text-sm">{{ count($viewPackageGroup['grouped_by_date_sport'] ?? []) }} Sessions • {{ $viewPackageGroup['total_slots'] }} Slots</span>
                            </div>
                            <div>
                                <span class="text-slate-400 font-bold uppercase text-[10px] block">Payment Status</span>
                                <span class="font-extrabold text-emerald-700 text-sm">{{ ucfirst($viewPackageGroup['payment_status']) }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-slate-400 font-bold uppercase text-[10px] block">Total Package Price</span>
                                <span class="font-extrabold text-slate-900 text-sm">Rs. {{ number_format($viewPackageGroup['total_price'], 2) }}</span>
                            </div>
                        </div>

                        <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm text-emerald-600">calendar_view_day</span> Booked Sessions (Separated Date & Sport Wise)
                        </h4>

                        <div class="space-y-4">
                            @foreach($viewPackageGroup['grouped_by_date_sport'] ?? [] as $group)
                                <div class="border border-slate-200 rounded-2xl bg-white overflow-hidden shadow-sm">
                                    <!-- Date & Sport Section Header -->
                                    <div class="bg-slate-50/90 border-b border-slate-200/80 px-4 py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                        <div>
                                            <div class="font-extrabold text-slate-900 text-xs flex items-center gap-1.5">
                                                <i class="far fa-calendar-alt text-emerald-600"></i>
                                                {{ $group['formatted_date'] }}
                                                @if(!empty($group['is_private']))
                                                    <span class="px-1.5 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-extrabold rounded ms-1">
                                                        🔒 Private Session
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-[11px] font-bold text-emerald-700 mt-0.5">
                                                <i class="fas fa-running me-1"></i> {{ $group['game_name'] }} • Court {{ $group['court_number'] }}
                                            </div>
                                        </div>
                                        <div class="text-left sm:text-right">
                                            <span class="text-xs font-extrabold text-slate-900 block">Subtotal: Rs. {{ number_format($group['total_price'], 2) }}</span>
                                            <span class="text-[10px] font-semibold text-slate-500">({{ $group['slot_count'] }} Slots: {{ $group['time_range'] }})</span>
                                        </div>
                                    </div>

                                    <!-- Individual Slots Pills / Grid -->
                                    <div class="p-3 grid grid-cols-1 sm:grid-cols-2 gap-2 bg-slate-50/30">
                                        @foreach($group['slots'] as $sItem)
                                            <div class="p-2.5 bg-white border border-slate-200/80 rounded-xl flex items-center justify-between text-xs">
                                                <div class="flex items-center gap-2">
                                                    <span class="material-symbols-outlined text-sm text-slate-400">schedule</span>
                                                    <span class="font-bold text-slate-800">{{ $sItem['formatted_time'] }}</span>
                                                    @if(!empty($sItem['is_private']))
                                                        <span class="text-[9px] font-extrabold text-amber-700 me-1">🔒</span>
                                                    @endif
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="font-extrabold text-slate-700">Rs. {{ number_format($sItem['price'], 2) }}</span>
                                                    <span class="text-[9px] font-extrabold px-2 py-0.5 rounded {{ strtolower($sItem['status']) === 'cancelled' ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-800' }}">
                                                        {{ ucfirst($sItem['status']) }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
                        <button wire:click="closeViewPackageModal" class="px-5 py-2.5 bg-slate-800 text-white font-bold rounded-xl text-xs hover:bg-slate-900">
                            Close Details
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Modal 3: Three-Level Package Cancellation Modal (Full Package, Day-Wise, or Slot-Wise) -->
        @if($showCancelModal && $cancelPackageGroup)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fade-in">
                <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-100">
                    <!-- Modal Header -->
                    <div class="bg-rose-600 text-white p-5 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-2xl">cancel</span>
                            <div>
                                <h3 class="font-extrabold text-base">Cancel Package - {{ $cancelPackageGroup['package_code'] }}</h3>
                                <p class="text-xs text-white/80">
                                    {{ $cancelPackageGroup['user_name'] }} • {{ $cancelPackageGroup['total_slots'] }} Slot(s) Total
                                </p>
                            </div>
                        </div>
                        <button wire:click="closeCancelModal" class="text-white/80 hover:text-white p-1">
                            <span class="material-symbols-outlined text-xl">close</span>
                        </button>
                    </div>

                    <!-- Modal Content -->
                    <div class="p-6">
                        @if($cancelType === 'choose')
                            <!-- Step 1: 3-Level Choice Screen -->
                            <p class="text-xs font-semibold text-slate-700 mb-4">
                                This package contains <span class="font-extrabold text-rose-600">{{ $cancelPackageGroup['total_slots'] }} slots</span> across {{ count($cancelPackageGroup['dates_list']) }} date session(s). Select cancellation mode:
                            </p>

                            <div class="space-y-3">
                                <!-- Option 1: Cancel Entire Package -->
                                <div class="p-4 bg-slate-50 hover:bg-rose-50/50 border border-slate-200 hover:border-rose-300 rounded-2xl transition-all cursor-pointer group" wire:click="cancelEntirePackage">
                                    <div class="flex items-start gap-3">
                                        <div class="p-2 bg-rose-100 text-rose-700 rounded-xl group-hover:bg-rose-600 group-hover:text-white transition-colors">
                                            <span class="material-symbols-outlined text-xl">delete_sweep</span>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-xs text-slate-900 group-hover:text-rose-700">Option 1: Cancel Entire Package (All Days & Slots)</h4>
                                            <p class="text-[11px] text-slate-500 mt-0.5">Cancel all {{ $cancelPackageGroup['total_slots'] }} slots in this entire package.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Option 2: Cancel Day-Wise -->
                                <div class="p-4 bg-slate-50 hover:bg-amber-50/50 border border-slate-200 hover:border-amber-300 rounded-2xl transition-all cursor-pointer group" wire:click="setCancelType('day_wise')">
                                    <div class="flex items-start gap-3">
                                        <div class="p-2 bg-amber-100 text-amber-700 rounded-xl group-hover:bg-amber-600 group-hover:text-white transition-colors">
                                            <span class="material-symbols-outlined text-xl">calendar_month</span>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-xs text-slate-900 group-hover:text-amber-700">Option 2: Day-Wise Cancel (Select Dates)</h4>
                                            <p class="text-[11px] text-slate-500 mt-0.5">Choose specific dates to cancel while keeping other date sessions active.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Option 3: Cancel Slot-Wise -->
                                <div class="p-4 bg-slate-50 hover:bg-teal-50/50 border border-slate-200 hover:border-teal-300 rounded-2xl transition-all cursor-pointer group" wire:click="setCancelType('slot_wise')">
                                    <div class="flex items-start gap-3">
                                        <div class="p-2 bg-teal-100 text-teal-700 rounded-xl group-hover:bg-teal-600 group-hover:text-white transition-colors">
                                            <span class="material-symbols-outlined text-xl">checklist</span>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-xs text-slate-900 group-hover:text-teal-700">Option 3: Slot-Wise Cancel (Select Specific Time Slots)</h4>
                                            <p class="text-[11px] text-slate-500 mt-0.5">Choose individual time slots to cancel across any date in the package.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($cancelType === 'day_wise')
                            <!-- Step 2A: Day-Wise Cancellation Screen -->
                            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-3">Select Booking Dates to Cancel</h4>

                            @error('cancel_error')
                                <div class="mb-3 text-[11px] text-rose-600 font-semibold bg-rose-50 p-2 rounded-lg border border-rose-200">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="space-y-2 max-h-56 overflow-y-auto custom-scrollbar pr-1 mb-4">
                                @foreach($cancelPackageGroup['dates_list'] as $dItem)
                                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between text-xs">
                                        <label class="flex items-center gap-3 cursor-pointer flex-grow">
                                            <input type="checkbox" 
                                                   value="{{ $dItem['date'] }}" 
                                                   wire:model="selectedCancelDates"
                                                   class="w-4 h-4 text-rose-600 border-slate-300 rounded focus:ring-rose-500">
                                            <div>
                                                <span class="font-bold text-slate-800">{{ $dItem['formatted'] }}</span>
                                                <span class="text-slate-500 font-medium ms-1">({{ $dItem['slots_count'] }} slots)</span>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex items-center justify-between border-t border-slate-100 pt-4">
                                <button type="button" wire:click="setCancelType('choose')" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs hover:bg-slate-200">
                                    <i class="fas fa-arrow-left me-1"></i> Back to Options
                                </button>
                                <button type="button" wire:click="cancelSelectedDays" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-extrabold rounded-xl text-xs shadow-md">
                                    Confirm Cancel Selected Days
                                </button>
                            </div>
                        @elseif($cancelType === 'slot_wise')
                            <!-- Step 2B: Slot-Wise Cancellation Screen -->
                            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-3">Select Specific Time Slots to Cancel</h4>

                            @error('cancel_error')
                                <div class="mb-3 text-[11px] text-rose-600 font-semibold bg-rose-50 p-2 rounded-lg border border-rose-200">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="space-y-2 max-h-56 overflow-y-auto custom-scrollbar pr-1 mb-4">
                                @foreach($cancelPackageGroup['slot_details'] as $slot)
                                    @php $isAlreadyCancelled = strtolower($slot['status']) === 'cancelled'; @endphp
                                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between text-xs">
                                        <label class="flex items-center gap-3 cursor-pointer flex-grow">
                                            <input type="checkbox" 
                                                   value="{{ (string)$slot['id'] }}" 
                                                   wire:model="selectedCancelSlotIds"
                                                   @if($isAlreadyCancelled) disabled @endif
                                                   class="w-4 h-4 text-rose-600 border-slate-300 rounded focus:ring-rose-500">
                                            <div>
                                                <span class="font-bold text-slate-800">{{ $slot['formatted_date'] }}</span>
                                                <span class="text-slate-600 font-medium block">{{ $slot['formatted_time'] }} • {{ $slot['game_name'] }} (Court {{ $slot['court_number'] }})</span>
                                            </div>
                                        </label>
                                        <span class="text-[10px] font-extrabold px-2 py-0.5 rounded {{ $isAlreadyCancelled ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-800' }}">
                                            {{ ucfirst($slot['status']) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex items-center justify-between border-t border-slate-100 pt-4">
                                <button type="button" wire:click="setCancelType('choose')" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs hover:bg-slate-200">
                                    <i class="fas fa-arrow-left me-1"></i> Back to Options
                                </button>
                                <button type="button" wire:click="cancelSelectedSpecificSlots" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-extrabold rounded-xl text-xs shadow-md">
                                    Confirm Cancel Selected Slots
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Modal Footer for Choice view -->
                    @if($cancelType === 'choose')
                        <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
                            <button wire:click="closeCancelModal" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 font-bold rounded-xl text-xs hover:bg-slate-100">
                                Close
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Modal 4: Result / Feedback Modal -->
        @if($showResultModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fade-in">
                <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full overflow-hidden border border-slate-100">
                    <div class="bg-emerald-600 text-white p-5 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-2xl">check_circle</span>
                            <div>
                                <h3 class="font-extrabold text-base">Package Successfully Booked!</h3>
                                <p class="text-xs text-white/80">{{ $bookedCount }} slot(s) reserved for {{ $playerName }}</p>
                            </div>
                        </div>
                        <button wire:click="resetForm" class="text-white/80 hover:text-white p-1">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <div class="p-6 max-h-[70vh] overflow-y-auto custom-scrollbar space-y-4">
                        @if(!empty($bookedSlots))
                            <div>
                                <h4 class="text-xs font-extrabold text-emerald-700 uppercase tracking-wider mb-2">Confirmed Booked Slots ({{ count($bookedSlots) }})</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($bookedSlots as $bs)
                                        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-xs">
                                            <div class="font-bold text-slate-900">{{ $bs['date'] }}</div>
                                            <div class="text-slate-600 font-semibold">{{ $bs['slot'] }} • Court {{ $bs['court'] }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if(!empty($skippedSlots))
                            <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl">
                                <h4 class="text-xs font-extrabold text-amber-800 uppercase tracking-wider mb-1">Ignored / Already Booked Slots ({{ count($skippedSlots) }})</h4>
                                <p class="text-[11px] text-amber-700 mb-2">The following slots were skipped because they were already reserved or blocked:</p>
                                <div class="space-y-2">
                                    @foreach($skippedSlots as $ss)
                                        <div class="p-2.5 bg-white border border-amber-200 rounded-xl text-xs flex items-center justify-between">
                                            <div>
                                                <span class="font-bold text-slate-800">{{ $ss['date'] }}</span>
                                                <span class="text-slate-500">({{ $ss['slot'] }} - Court {{ $ss['court'] }})</span>
                                            </div>
                                            <span class="px-2 py-0.5 bg-rose-100 text-rose-700 text-[10px] font-extrabold rounded me-1">
                                                {{ $ss['reason'] }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
                        <button wire:click="resetForm" class="px-5 py-2.5 bg-slate-800 text-white font-bold rounded-xl text-xs hover:bg-slate-900">
                            Done
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
