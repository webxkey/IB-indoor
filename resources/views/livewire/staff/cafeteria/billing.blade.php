<div class="pos-billing-terminal py-2" wire:poll.15s>
    <!-- Tailwind CSS Play CDN to ensure dynamic compilation of premium utility styles -->
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
        .payment-card { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
        .payment-card.active { border-color: #16a34a; background-color: #f0fdf4; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.08); }
    </style>

    <div class="min-h-screen bg-slate-50/50 p-1 font-sans text-slate-800 antialiased">
 
        <!-- Alert notifications -->
        @if (session()->has('success'))
            <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-2 shadow-sm animate-fade-in" role="alert">
                <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                <span class="text-sm font-semibold">{{ session('success') }}</span>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-4 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl flex items-center gap-2 shadow-sm animate-fade-in" role="alert">
                <span class="material-symbols-outlined text-rose-600">error</span>
                <span class="text-sm font-semibold">{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
            <!-- LEFT SECTION: POS CART & BILL DETAILS (7 Cores) -->
            <div class="lg:col-span-7 flex flex-col gap-6">
                <!-- Search & Cart Container -->
                <div class="glass-card rounded-2xl shadow-sm flex flex-col overflow-hidden">
                    <div class="p-4 bg-slate-50/50 border-b border-slate-200/60">
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xl pointer-events-none">search</span>
                            <input type="text" 
                                class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none text-sm transition-all" 
                                placeholder="Scan item barcode or search products by name/code..." 
                                wire:model.live.debounce.300ms="search">
                            
                            @if($search)
                                <button class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" wire:click="$set('search', '')">
                                    <span class="material-symbols-outlined text-lg">cancel</span>
                                </button>
                            @endif
                        </div>

                        <!-- Dropdown Search matches -->
                        @if($search && count($searchResults) > 0)
                            <div class="absolute left-4 right-4 mt-2 bg-white border border-slate-200 rounded-xl shadow-xl z-50 max-h-72 overflow-y-auto custom-scrollbar divide-y divide-slate-100">
                                @foreach($searchResults as $res)
                                    <div class="flex items-center justify-between p-3 hover:bg-slate-50 cursor-pointer transition-colors"
                                         wire:click="addToCart({{ json_encode($res) }})">
                                        <div class="flex items-center gap-3">
                                            @if($res['image'])
                                                <img src="{{ $this->getImageUrl($res['image']) }}" class="w-9 h-9 rounded-lg object-cover border border-slate-100" onerror="this.onerror=null;this.src='https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSrn_80I-lMAa0pVBNmFmQ7VI6l4rr74JW-eQ&s';">
                                            @else
                                                <div class="w-9 h-9 bg-slate-100 rounded-lg flex items-center justify-center border border-slate-200"><span class="material-symbols-outlined text-slate-400 text-lg">lunch_dining</span></div>
                                            @endif
                                            <div>
                                                <h4 class="font-bold text-xs text-slate-800">{{ $res['name'] }}</h4>
                                                <p class="text-[10px] text-slate-500 font-mono">{{ $res['code'] }} <span class="mx-1">|</span> Stock: {{ $res['stock'] }}</p>
                                            </div>
                                        </div>
                                        <span class="text-xs font-black text-brand-600">LKR {{ number_format($res['price'], 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    <!-- Walking Customer Direct Form -->
                    @if($customerId === '')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50 p-3 rounded-xl border border-slate-200/50">
                            <div>
                                <input type="text" class="w-full text-xs font-semibold bg-white border border-slate-200 rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-brand-500" placeholder="Walking Customer Name" wire:model="walkingName">
                            </div>
                            <div>
                                <input type="text" class="w-full text-xs font-semibold bg-white border border-slate-200 rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-brand-500" placeholder="Phone Number (Optional)" wire:model="walkingPhone">
                            </div>
                        </div>
                    @endif
                    </div>

                    <!-- Cart Table items -->
                    <div class="overflow-y-auto custom-scrollbar p-0 flex-grow-1" style="min-height: 320px; max-height: 420px;">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                    <th class="pl-4 py-3">Item details</th>
                                    <th class="py-3 text-center" style="width: 130px;">Qty</th>
                                    <th class="py-3 text-right" style="width: 110px;">Price</th>
                                    <th class="py-3 text-center" style="width: 80px;">Disc %</th>
                                    <th class="py-3 text-right" style="width: 100px;">Subtotal</th>
                                    <th class="pr-4 py-3 text-center" style="width: 40px;">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($cart as $key => $item)
                                    <tr class="group hover:bg-slate-50/50 transition-colors" wire:key="cart-{{ $key }}">
                                        <td class="pl-4 py-3">
                                            @if($item['is_custom'])
                                                <div class="flex flex-col gap-1.5 max-w-[170px]">
                                                    <input type="text" class="w-full text-xs font-bold text-slate-800 bg-amber-50 border border-amber-200 rounded-lg px-2 py-1 focus:ring-1 focus:ring-brand-500 focus:border-brand-500" value="{{ $item['name'] }}" wire:change="updateCustomName('{{ $key }}', $event.target.value)" placeholder="Item name...">
                                                    <input type="text" class="w-full text-[9px] text-slate-500 font-mono bg-slate-50 border border-slate-200 rounded px-2 py-0.5" value="{{ $item['code'] }}" wire:change="updateCustomCode('{{ $key }}', $event.target.value)" placeholder="Code...">
                                                </div>
                                            @else
                                                <div class="flex items-center gap-3">
                                                    @if($item['image'])
                                                        <img src="{{ $this->getImageUrl($item['image']) }}" class="w-9 h-9 rounded-lg object-cover border border-slate-100" onerror="this.onerror=null;this.src='https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSrn_80I-lMAa0pVBNmFmQ7VI6l4rr74JW-eQ&s';">
                                                    @else
                                                        <div class="w-9 h-9 bg-slate-100 rounded-lg flex items-center justify-center border border-slate-200"><span class="material-symbols-outlined text-slate-400 text-lg">lunch_dining</span></div>
                                                    @endif
                                                    <div class="min-w-0">
                                                        <h4 class="font-bold text-xs text-slate-800 truncate max-w-[150px]">{{ $item['name'] }}</h4>
                                                        <p class="text-[10px] text-slate-500 font-mono mt-0.5">{{ $item['code'] }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            <div class="flex items-center justify-center border border-slate-200 rounded-lg bg-white overflow-hidden max-w-[100px] mx-auto">
                                                <button class="w-7 py-1 text-slate-500 hover:bg-slate-100 transition-colors font-bold text-xs" type="button" wire:click="decrementQuantity('{{ $key }}')">-</button>
                                                <input type="number" 
                                                    class="w-8 text-center text-xs font-black border-0 p-0 focus:ring-0" 
                                                    value="{{ $item['quantity'] }}" 
                                                    min="1"
                                                    max="{{ $item['stock'] }}"
                                                    wire:change="updateQuantity('{{ $key }}', $event.target.value)">
                                                <button class="w-7 py-1 text-slate-500 hover:bg-slate-100 transition-colors font-bold text-xs" type="button" wire:click="incrementQuantity('{{ $key }}')">+</button>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <div class="flex items-center justify-end gap-1">
                                                <span class="text-[9px] text-slate-400 font-bold">LKR</span>
                                                <input type="number" 
                                                    step="0.01"
                                                    class="w-20 text-right text-xs font-semibold bg-slate-50/50 border border-slate-200 rounded-lg px-1.5 py-1 focus:ring-1 focus:ring-brand-500" 
                                                    value="{{ $item['price'] }}"
                                                    wire:change="updatePrice('{{ $key }}', $event.target.value)">
                                            </div>
                                        </td>
                                        <td class="py-3 text-center">
                                            <input type="number" 
                                                min="0"
                                                max="100"
                                                class="w-12 text-center text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg px-1 py-1 focus:ring-1 focus:ring-brand-500" 
                                                value="{{ $item['discount_percentage'] }}"
                                                placeholder="0"
                                                wire:change="updateDiscount('{{ $key }}', $event.target.value + '%')">
                                        </td>
                                        <td class="py-3 text-right font-bold text-xs text-slate-900 pr-1">
                                            LKR {{ number_format($item['total'], 2) }}
                                        </td>
                                        <td class="pr-4 py-3 text-center">
                                            <button class="text-rose-500 hover:text-rose-700 hover:bg-rose-50 p-1 rounded-full transition-colors flex items-center justify-center" wire:click="removeFromCart('{{ $key }}')">
                                                <span class="material-symbols-outlined text-lg">delete_sweep</span>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-20 text-slate-400">
                                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-200/50">
                                                <span class="material-symbols-outlined text-3xl text-slate-300">shopping_bag</span>
                                            </div>
                                            <p class="font-bold text-xs text-slate-500 uppercase tracking-widest">Cart is empty</p>
                                            <p class="text-[11px] text-slate-400 mt-1">Tap products from the right panel to begin.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Customer Selection & Pay Panel -->
                <div class="glass-card rounded-2xl shadow-sm p-4 flex flex-col gap-4">
                

             

                    <!-- Premium Card-Based Payment Mode Select -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Payment mode</label>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="payment-card border border-slate-200 rounded-xl p-2.5 text-center cursor-pointer flex flex-col items-center gap-1 {{ $paymentMethod === 'cash' ? 'active' : 'hover:bg-slate-50 bg-white' }}"
                                 wire:click="$set('paymentMethod', 'cash')">
                                <span class="material-symbols-outlined text-brand-600">payments</span>
                                <span class="text-[10px] font-bold">Cash</span>
                            </div>
                            <div class="payment-card border border-slate-200 rounded-xl p-2.5 text-center cursor-pointer flex flex-col items-center gap-1 {{ $paymentMethod === 'card' ? 'active' : 'hover:bg-slate-50 bg-white' }}"
                                 wire:click="$set('paymentMethod', 'card')">
                                <span class="material-symbols-outlined text-brand-600">credit_card</span>
                                <span class="text-[10px] font-bold">Card</span>
                            </div>
                            <div class="payment-card border border-slate-200 rounded-xl p-2.5 text-center cursor-pointer flex flex-col items-center gap-1 {{ $paymentMethod === 'upi' ? 'active' : 'hover:bg-slate-50 bg-white' }}"
                                 wire:click="$set('paymentMethod', 'upi')">
                                <span class="material-symbols-outlined text-brand-600">qr_code_2</span>
                                <span class="text-[10px] font-bold">UPI / QR</span>
                            </div>
                        </div>
                    </div>

                    <hr class="border-slate-200/60 my-1">

                    <!-- Totals breakdown and Checkout Button -->
                    <div class="flex flex-col md:flex-row justify-between items-stretch md:items-center gap-4">
                        <div class="flex-grow space-y-1 bg-slate-50/80 p-3 rounded-xl border border-slate-100">
                            @php
                                $originalSubtotal = collect($cart)->sum(function ($item) {
                                    return ($item['price'] ?? 0) * ($item['quantity'] ?? 0);
                                });
                                $unitDiscountRs = collect($cart)->sum(function ($item) {
                                    return ($item['discount'] ?? 0) * ($item['quantity'] ?? 0);
                                });
                                $globalDiscountAmount = $this->additionalDiscountAmount;
                                $totalDiscountRs = $unitDiscountRs + $globalDiscountAmount;
                            @endphp
                            <div class="flex justify-between text-xs text-slate-500">
                                <span>Subtotal:</span>
                                <span>LKR {{ number_format($originalSubtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-xs text-rose-500">
                                <span>Discounts:</span>
                                <span class="font-semibold">- LKR {{ number_format($totalDiscountRs, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-baseline pt-1 border-t border-slate-200/50">
                                <span class="text-xs font-bold text-slate-800">Net Total:</span>
                                <span class="text-xl font-extrabold text-brand-600 font-mono">LKR {{ number_format($this->grandTotal, 2) }}</span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2 md:w-52">
                            <button class="py-2.5 bg-white border border-slate-200 hover:border-brand-500 hover:text-brand-700 text-slate-600 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-colors shadow-sm" wire:click="openSaleDiscountModal()">
                                <span class="material-symbols-outlined text-base">percent</span> Global discount
                            </button>
                            <button class="py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-extrabold rounded-xl text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-lg shadow-emerald-600/10 transition-all transform hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-40 disabled:pointer-events-none" 
                                    wire:click="validateAndCreateSale" 
                                    {{ count($cart) === 0 ? 'disabled' : '' }}>
                                <span class="material-symbols-outlined text-base">payments</span> Pay & Print
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT SECTION: INVENTORY PRODUCT GRID (5 Cores) -->
            <div class="lg:col-span-5 flex flex-col gap-6">
                <div class="glass-card rounded-2xl shadow-sm p-4 flex flex-col gap-4 overflow-hidden h-full">
                    <!-- Grid Header -->
                    <div class="flex justify-between items-center pb-2 border-b border-slate-200/60">
                        <h3 class="font-extrabold text-sm text-slate-800 flex items-center gap-1.5"><span class="material-symbols-outlined text-brand-600 text-lg">grid_view</span> Menu list</h3>
                        <button class="px-3 py-1 bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded-lg text-[10px] font-black text-amber-800 uppercase tracking-wider shadow-sm transition-colors" wire:click="addCustomProduct">
                            <i class="fas fa-plus-circle me-1"></i> Custom item
                        </button>
                    </div>

                    <!-- Category Pills -->
                    <div class="overflow-x-auto custom-scrollbar flex gap-1.5 pb-2 whitespace-nowrap">
                        <button class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all shadow-sm {{ is_null($selectedCategory) ? 'bg-brand-600 text-white shadow-brand-500/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}" 
                                wire:click="$set('selectedCategory', null)">
                            All Items
                        </button>
                        @foreach($categories as $cat)
                            <button class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all shadow-sm {{ $selectedCategory == $cat->id ? 'bg-brand-600 text-white shadow-brand-500/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}" 
                                    wire:click="selectCategory({{ $cat->id }})">
                                {{ $cat->name }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Product Items Grid -->
                    <div class="overflow-y-auto custom-scrollbar flex-grow-1 pr-1" style="max-height: 480px;">
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 gap-3 pb-4">
                            @forelse($gridProducts as $gp)
                                <div class="product-card bg-white border border-slate-200/60 rounded-xl p-2.5 text-center flex flex-col items-center justify-between" 
                                     wire:click="addToCart({{ json_encode($gp) }})">
                                    <div class="mb-2 w-16 h-16 rounded-xl overflow-hidden bg-slate-50 border border-slate-100 flex items-center justify-center">
                                        @if($gp['image'])
                                            <img src="{{ $this->getImageUrl($gp['image']) }}" 
                                                 class="w-full h-full object-cover transition-transform duration-300 hover:scale-110" 
                                                 onerror="this.onerror=null;this.src='https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSrn_80I-lMAa0pVBNmFmQ7VI6l4rr74JW-eQ&s';">
                                        @else
                                            <span class="material-symbols-outlined text-slate-300 text-3xl">lunch_dining</span>
                                        @endif
                                    </div>
                                    
                                    <div class="w-full">
                                        <div class="font-extrabold text-xs text-slate-800 truncate" title="{{ $gp['name'] }}">{{ $gp['name'] }}</div>
                                        <div class="text-brand-600 font-extrabold font-mono text-[11px] mt-0.5">LKR {{ number_format($gp['price'], 0) }}</div>
                                    </div>

                                    <div class="mt-2 w-full">
                                        @if($gp['stock'] <= 0)
                                            <span class="inline-block w-full py-0.5 bg-rose-50 text-rose-600 rounded text-[9px] font-bold border border-rose-100">Out of Stock</span>
                                        @elseif($gp['stock'] <= 5)
                                            <span class="inline-block w-full py-0.5 bg-amber-50 text-amber-700 rounded text-[9px] font-bold border border-amber-100">Only {{ $gp['stock'] }} left</span>
                                        @else
                                            <span class="inline-block w-full py-0.5 bg-slate-50 text-slate-500 rounded text-[9px] font-bold border border-slate-100">Stock: {{ $gp['stock'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full text-center py-20 text-slate-400">
                                    <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">inventory_2</span>
                                    <p class="font-bold text-xs text-slate-500 uppercase tracking-widest">No active products</p>
                                    <p class="text-[10px] text-slate-400 mt-1">Configure cafeteria menu list in the Products section.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Discount Modal -->
    @if($showDiscountModal)
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[2000] flex items-center justify-center" wire:click.self="$set('showDiscountModal', false)">
            <div class="bg-white border rounded-2xl shadow-2xl max-w-sm w-full mx-4 overflow-hidden animate-zoom-in">
                <div class="p-4 bg-slate-50 border-b border-slate-200/60 flex justify-between items-center">
                    <h5 class="font-extrabold text-sm text-slate-800 flex items-center gap-1.5"><span class="material-symbols-outlined text-brand-600 text-lg">percent</span> Apply global discount</h5>
                    <button type="button" class="text-slate-400 hover:text-slate-600" wire:click="$set('showDiscountModal', false)"><span class="material-symbols-outlined">close</span></button>
                </div>
                <div class="p-4 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Discount Type</label>
                        <select class="w-full text-xs font-semibold bg-slate-50 border border-slate-200 rounded-xl px-3 py-2" wire:model.live="additionalDiscountType">
                            <option value="fixed">Fixed LKR Discount</option>
                            <option value="percentage">Percentage (%) Discount</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Value</label>
                        <input type="number" step="any" min="0" class="w-full text-xs font-semibold bg-white border border-slate-200 rounded-xl px-3 py-2" wire:model="additionalDiscount">
                    </div>
                </div>
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-600" wire:click="$set('showDiscountModal', false)">Cancel</button>
                    <button type="button" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold" wire:click="applyGlobalDiscount()">Apply</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Add Customer Modal -->
    @if($showCustomerModal)
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[2000] flex items-center justify-center" wire:click.self="closeCustomerModal()">
            <div class="bg-white border rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden animate-zoom-in">
                <div class="p-4 bg-slate-50 border-b border-slate-200/60 flex justify-between items-center">
                    <h5 class="font-extrabold text-sm text-slate-800 flex items-center gap-1.5"><span class="material-symbols-outlined text-brand-600 text-lg">person_add</span> Register customer</h5>
                    <button type="button" class="text-slate-400 hover:text-slate-600" wire:click="closeCustomerModal()"><span class="material-symbols-outlined">close</span></button>
                </div>
                <form wire:submit.prevent="saveCustomer">
                    <div class="p-4 space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">First Name <span class="text-rose-500">*</span></label>
                                <input type="text" class="w-full text-xs font-semibold border border-slate-200 rounded-xl px-3 py-2 @error('newCustomerFirstName') border-rose-300 @enderror" wire:model="newCustomerFirstName" placeholder="John">
                                @error('newCustomerFirstName') <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Last Name <span class="text-rose-500">*</span></label>
                                <input type="text" class="w-full text-xs font-semibold border border-slate-200 rounded-xl px-3 py-2 @error('newCustomerLastName') border-rose-300 @enderror" wire:model="newCustomerLastName" placeholder="Doe">
                                @error('newCustomerLastName') <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Phone Number <span class="text-rose-500">*</span></label>
                            <input type="text" class="w-full text-xs font-semibold border border-slate-200 rounded-xl px-3 py-2 @error('newCustomerPhone') border-rose-300 @enderror" wire:model="newCustomerPhone" placeholder="e.g. +94 77 123 4567">
                            @error('newCustomerPhone') <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Email Address</label>
                            <input type="email" class="w-full text-xs font-semibold border border-slate-200 rounded-xl px-3 py-2 @error('newCustomerEmail') border-rose-300 @enderror" wire:model="newCustomerEmail" placeholder="john.doe@example.com">
                            @error('newCustomerEmail') <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-600" wire:click="closeCustomerModal()">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold">Register</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

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
        
    @endif

    <script>
        function printInvoice() {
            var receiptEl = document.getElementById('printable-receipt');
            if (!receiptEl) {
                alert('Receipt not ready. Please try again.');
                return;
            }
            var printContents = receiptEl.innerHTML;
            var popupWin = window.open('', '_blank', 'width=420,height=600');
            if (!popupWin || popupWin.closed || typeof popupWin.closed == 'undefined') {
                alert('Popup blocked. Please allow popups for this site.');
                return;
            }
            popupWin.document.open();
        popupWin.document.write('<html><head><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"><style>body{font-family: monospace; padding: 20px;} hr{border-top:1px dashed #000;}</style></head><body>' + printContents + '</body></html>');
        popupWin.document.close();
        popupWin.focus();
        popupWin.print();
        }
    </script>

    <script>
        setInterval(function() {
            var clockEl = document.getElementById('pos-clock');
            if (clockEl) {
                var now = new Date();
                var timeStr = now.toTimeString().split(' ')[0];
                clockEl.textContent = timeStr;
            }
        }, 1000);
    </script>
</div>
