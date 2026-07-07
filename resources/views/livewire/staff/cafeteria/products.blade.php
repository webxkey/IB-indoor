<div class="pos-products-management py-2">
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
        .modal-backdrop { background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px); }
    </style>

    <div class="min-h-screen bg-slate-50/50 p-1 font-sans text-slate-800 antialiased">
        <!-- Header area -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 border-b border-slate-200/60 pb-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                    <span class="p-2 bg-brand-100 text-brand-700 rounded-xl flex items-center justify-center shadow-sm"><i class="fas fa-utensils text-lg"></i></span>
                    Products & Categories
                </h1>
                <p class="text-xs text-slate-500 mt-1">Manage cafeteria items, set pricing levels, modify available stocks, and define menu categories.</p>
            </div>
            <div class="flex items-center gap-2">
                <button class="px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold rounded-xl text-xs flex items-center gap-1.5 transition-colors shadow-sm" wire:click="openCategoryModal()">
                    <span class="material-symbols-outlined text-lg text-brand-600">create_new_folder</span> Add Category
                </button>
                <button class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl text-xs flex items-center gap-1.5 transition-colors shadow-lg shadow-brand-600/10" wire:click="openProductModal()">
                    <span class="material-symbols-outlined text-lg">add_box</span> Add Product
                </button>
            </div>
        </div>

        <!-- Alert messages -->
        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-2 shadow-sm animate-fade-in" role="alert">
                <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                <span class="text-sm font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
            <!-- Categories Card (Left 4 Cores) -->
            <div class="lg:col-span-4">
                <div class="glass-card rounded-2xl shadow-sm flex flex-col overflow-hidden h-full">
                    <div class="p-4 border-b border-slate-200/60 bg-slate-50/50 flex items-center justify-between">
                        <h3 class="font-extrabold text-sm text-slate-800 flex items-center gap-1.5"><span class="material-symbols-outlined text-brand-600 text-lg">sell</span> Categories</h3>
                        <span class="badge bg-slate-200/80 text-slate-700 font-bold text-[10px] px-2 py-0.5 rounded-full">{{ count($categories) }}</span>
                    </div>

                    <div class="p-0 overflow-y-auto custom-scrollbar flex-grow">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                    <th class="pl-4 py-3">Category Details</th>
                                    <th class="pr-4 py-3 text-right" style="width: 90px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($categories as $category)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="pl-4 py-3">
                                            <div class="font-bold text-slate-800 text-sm">{{ $category->name }}</div>
                                            <div class="text-[11px] text-slate-400 mt-0.5 truncate max-w-[190px]" title="{{ $category->description }}">{{ $category->description ?: 'No description' }}</div>
                                        </td>
                                        <td class="pr-4 py-3 text-right">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <button class="p-1.5 text-blue-600 hover:bg-blue-50 hover:text-blue-800 rounded-lg transition-colors flex items-center justify-center border border-slate-100 bg-white" wire:click="openCategoryModal({{ $category->id }})" title="Edit">
                                                    <span class="material-symbols-outlined text-[15px]">edit</span>
                                                </button>
                                                <button class="p-1.5 text-rose-600 hover:bg-rose-50 hover:text-rose-800 rounded-lg transition-colors flex items-center justify-center border border-slate-100 bg-white" onclick="confirm('Are you sure you want to delete this category? This will delete all products in this category.') || event.stopImmediatePropagation()" wire:click="deleteCategory({{ $category->id }})" title="Delete">
                                                    <span class="material-symbols-outlined text-[15px]">delete</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center py-10 text-slate-400">No categories created yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Products Card (Right 8 Cores) -->
            <div class="lg:col-span-8">
                <div class="glass-card rounded-2xl shadow-sm flex flex-col overflow-hidden h-full">
                    <!-- Filters & Search -->
                    <div class="p-4 border-b border-slate-200/60 bg-slate-50/50">
                        <div class="flex flex-col md:flex-row gap-3 items-stretch md:items-center justify-between">
                            <h3 class="font-extrabold text-sm text-slate-800 flex items-center gap-1.5"><span class="material-symbols-outlined text-brand-600 text-lg">inventory_2</span> Products Ledger</h3>
                            
                            <div class="flex flex-col sm:flex-row gap-2 items-stretch">
                                <div class="relative w-full sm:w-48">
                                    <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg">filter_alt</span>
                                    <select class="w-full pl-8 pr-7 py-1.5 bg-white border border-slate-200 rounded-xl outline-none text-xs font-semibold appearance-none focus:ring-1 focus:ring-brand-500 focus:border-brand-500" wire:model.live="categoryFilter">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-lg">expand_more</span>
                                </div>

                                <div class="relative w-full sm:w-56">
                                    <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                                    <input type="text" class="w-full pl-8 pr-3 py-1.5 bg-white border border-slate-200 rounded-xl outline-none text-xs font-semibold focus:ring-1 focus:ring-brand-500 focus:border-brand-500" placeholder="Search product..." wire:model.live.debounce.300ms="search">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products Table -->
                    <div class="p-0 overflow-x-auto custom-scrollbar flex-grow">
                        <table class="w-full text-left border-collapse text-xs align-middle">
                            <thead>
                                <tr class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                    <th class="pl-4 py-3" style="width: 70px;">Image</th>
                                    <th class="py-3">Item details</th>
                                    <th class="py-3">Category</th>
                                    <th class="py-3 text-right" style="width: 100px;">Price</th>
                                    <th class="py-3 text-center" style="width: 90px;">Stock</th>
                                    <th class="py-3 text-center" style="width: 95px;">Status</th>
                                    <th class="pr-4 py-3 text-right" style="width: 90px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($products as $product)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="pl-4 py-3">
                                            @if($product->image)
                                                <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-10 h-10 rounded-xl object-cover border border-slate-100" onerror="this.onerror=null;this.src='https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSrn_80I-lMAa0pVBNmFmQ7VI6l4rr74JW-eQ&s';">
                                            @else
                                                <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center border border-slate-200"><span class="material-symbols-outlined text-slate-300 text-xl">lunch_dining</span></div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="font-bold text-slate-800 text-sm">{{ $product->name }}</div>
                                            <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $product->code }}</div>
                                        </td>
                                        <td>
                                            <span class="inline-block bg-slate-100 text-slate-600 rounded-lg px-2.5 py-1 font-bold text-[10px] border border-slate-200">{{ $product->category->name }}</span>
                                        </td>
                                        <td class="text-right font-extrabold text-slate-950 text-xs">
                                            LKR {{ number_format($product->price, 2) }}
                                        </td>
                                        <td class="text-center">
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $product->stock <= 5 ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                                {{ $product->stock }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold border cursor-pointer {{ $product->is_active ? 'bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-700' : 'bg-amber-100 text-amber-700 border-amber-200 hover:bg-amber-200' }}" wire:click="toggleProductStatus({{ $product->id }})">
                                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                                            </button>
                                        </td>
                                        <td class="pr-4 py-3 text-right">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <button class="p-1.5 text-blue-600 hover:bg-blue-50 hover:text-blue-800 rounded-lg transition-colors flex items-center justify-center border border-slate-100 bg-white" wire:click="openProductModal({{ $product->id }})" title="Edit">
                                                    <span class="material-symbols-outlined text-[15px]">edit</span>
                                                </button>
                                                <button class="p-1.5 text-rose-600 hover:bg-rose-50 hover:text-rose-800 rounded-lg transition-colors flex items-center justify-center border border-slate-100 bg-white" onclick="confirm('Are you sure you want to delete this product?') || event.stopImmediatePropagation()" wire:click="deleteProduct({{ $product->id }})" title="Delete">
                                                    <span class="material-symbols-outlined text-[15px]">delete</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-20 text-slate-400">
                                            <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">inventory_2</span>
                                            <p class="font-bold text-xs text-slate-500 uppercase tracking-widest">No products found</p>
                                            <p class="text-[10px] text-slate-400 mt-1">Try resetting filters or adding a new cafeteria product.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 border-t border-slate-100">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Modal -->
    @if($showCategoryModal)
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[2000] flex items-center justify-center" wire:click.self="closeCategoryModal()">
            <div class="bg-white border rounded-2xl shadow-2xl max-w-sm w-full mx-4 overflow-hidden animate-zoom-in">
                <div class="p-4 bg-slate-50 border-b border-slate-200/60 flex justify-between items-center">
                    <h5 class="font-extrabold text-sm text-slate-800 flex items-center gap-1.5"><span class="material-symbols-outlined text-brand-600 text-lg">folder_open</span> {{ $selectedCategoryId ? 'Edit Category' : 'Add Category' }}</h5>
                    <button type="button" class="text-slate-400 hover:text-slate-600" wire:click="closeCategoryModal()"><span class="material-symbols-outlined">close</span></button>
                </div>
                <form wire:submit.prevent="saveCategory">
                    <div class="p-4 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Category Name <span class="text-rose-500">*</span></label>
                            <input type="text" class="w-full text-xs font-semibold border border-slate-200 rounded-xl px-3 py-2 focus:ring-1 focus:ring-brand-500 @error('catName') border-rose-300 @enderror" wire:model="catName" placeholder="e.g. Beverages, Snacks">
                            @error('catName') <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Description</label>
                            <textarea class="w-full text-xs font-semibold border border-slate-200 rounded-xl px-3 py-2 focus:ring-1 focus:ring-brand-500" rows="3" wire:model="catDescription" placeholder="Optional category description..."></textarea>
                        </div>
                    </div>
                    <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-600" wire:click="closeCategoryModal()">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold">{{ $selectedCategoryId ? 'Update' : 'Save' }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Product Modal -->
    @if($showProductModal)
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[2000] flex items-center justify-center" wire:click.self="closeProductModal()">
            <div class="bg-white border rounded-2xl shadow-2xl max-w-lg w-full mx-4 overflow-hidden animate-zoom-in">
                <div class="p-4 bg-slate-50 border-b border-slate-200/60 flex justify-between items-center">
                    <h5 class="font-extrabold text-sm text-slate-800 flex items-center gap-1.5"><span class="material-symbols-outlined text-brand-600 text-lg">box</span> {{ $selectedProductId ? 'Edit Product' : 'Add Product' }}</h5>
                    <button type="button" class="text-slate-400 hover:text-slate-600" wire:click="closeProductModal()"><span class="material-symbols-outlined">close</span></button>
                </div>
                <form wire:submit.prevent="saveProduct">
                    <div class="p-4 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Product Name <span class="text-rose-500">*</span></label>
                                <input type="text" class="w-full text-xs font-semibold border border-slate-200 rounded-xl px-3 py-2 focus:ring-1 focus:ring-brand-500 @error('prodName') border-rose-300 @enderror" wire:model="prodName" placeholder="e.g. Mineral Water">
                                @error('prodName') <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Barcode / Code <span class="text-rose-500">*</span></label>
                                <input type="text" class="w-full text-xs font-semibold border border-slate-200 rounded-xl px-3 py-2 focus:ring-1 focus:ring-brand-500 @error('prodCode') border-rose-300 @enderror" wire:model="prodCode" placeholder="e.g. WTR500">
                                @error('prodCode') <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Category <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <select class="w-full pl-3 pr-8 py-2 bg-white border border-slate-200 rounded-xl outline-none text-xs font-semibold appearance-none focus:ring-1 focus:ring-brand-500 @error('prodCategoryId') border-rose-300 @enderror" wire:model="prodCategoryId">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-lg">expand_more</span>
                                </div>
                                @error('prodCategoryId') <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Stock Quantity <span class="text-rose-500">*</span></label>
                                <input type="number" class="w-full text-xs font-semibold border border-slate-200 rounded-xl px-3 py-2 focus:ring-1 focus:ring-brand-500 @error('prodStock') border-rose-300 @enderror" wire:model="prodStock" placeholder="e.g. 50">
                                @error('prodStock') <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Retail Price (LKR) <span class="text-rose-500">*</span></label>
                                <input type="number" step="0.01" class="w-full text-xs font-semibold border border-slate-200 rounded-xl px-3 py-2 focus:ring-1 focus:ring-brand-500 @error('prodPrice') border-rose-300 @enderror" wire:model="prodPrice" placeholder="e.g. 150">
                                @error('prodPrice') <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Wholesale (LKR)</label>
                                <input type="number" step="0.01" class="w-full text-xs font-semibold border border-slate-200 rounded-xl px-3 py-2 focus:ring-1 focus:ring-brand-500" wire:model="prodWholesalePrice" placeholder="Optional">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Distribute (LKR)</label>
                                <input type="number" step="0.01" class="w-full text-xs font-semibold border border-slate-200 rounded-xl px-3 py-2 focus:ring-1 focus:ring-brand-500" wire:model="prodDistributePrice" placeholder="Optional">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Image URL</label>
                            <input type="text" class="w-full text-xs font-semibold border border-slate-200 rounded-xl px-3 py-2 focus:ring-1 focus:ring-brand-500" wire:model="prodImage" placeholder="https://example.com/image.png">
                            <small class="text-[10px] text-slate-400 mt-1 block">Link to a direct public image address.</small>
                        </div>

                        <div class="flex items-center gap-2 pt-2">
                            <input class="rounded text-brand-600 focus:ring-brand-500 w-4 h-4 border-slate-300 cursor-pointer" type="checkbox" id="prodActive" wire:model="prodIsActive">
                            <label class="text-xs font-bold text-slate-700 cursor-pointer" for="prodActive">Product Available for Sale</label>
                        </div>
                    </div>
                    <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-600" wire:click="closeProductModal()">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold">{{ $selectedProductId ? 'Update' : 'Save' }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
