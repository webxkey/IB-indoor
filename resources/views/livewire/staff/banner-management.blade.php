<div class="banner-management-ledger py-2">
    <!-- Tailwind CSS Play CDN -->
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

        /* Grid layouts */
        .banner-grid { display: grid; }
        .banner-grid.layout_1 { grid-template-columns: 1fr; grid-template-rows: 1fr; }
        .banner-grid.layout_2 { grid-template-columns: 1fr 1fr; grid-template-rows: 1fr; }
        .banner-grid.layout_3 { grid-template-columns: 2fr 1fr; grid-template-rows: 1fr 1fr; }
        .banner-grid.layout_3 .section-1 { grid-row: 1 / 3; }
        .banner-grid.layout_4 { grid-template-columns: 1fr 1fr; grid-template-rows: 1fr 1fr; }
        .banner-grid.layout_5 { grid-template-columns: repeat(6, 1fr); grid-template-rows: 1fr 1fr; }
        .banner-grid.layout_5 .section-1 { grid-column: 1 / 4; }
        .banner-grid.layout_5 .section-2 { grid-column: 4 / 7; }
        .banner-grid.layout_5 .section-3 { grid-column: 1 / 3; }
        .banner-grid.layout_5 .section-4 { grid-column: 3 / 5; }
        .banner-grid.layout_5 .section-5 { grid-column: 5 / 7; }
        .banner-grid.layout_6 { grid-template-columns: 1fr 1fr 1fr; grid-template-rows: 1fr 1fr; }
    </style>

    <div class="min-h-screen bg-slate-50/50 p-1 font-sans text-slate-800 antialiased">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 border-b border-slate-200/60 pb-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                    <span class="p-2 bg-brand-100 text-brand-700 rounded-xl flex items-center justify-center shadow-sm"><span class="material-symbols-outlined text-lg">imagesmode</span></span>
                    Banner Management
                </h1>
                <p class="text-xs text-slate-500 mt-1">Configure layout styles and upload media for your frontend display.</p>
            </div>
            @if(!$isEditing)
            <div class="flex items-center gap-2">
                <button wire:click="openCarouselModal" class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl text-xs flex items-center gap-1.5 transition-colors shadow-md">
                    <span class="material-symbols-outlined text-lg">slideshow</span> Live Display
                </button>
                <button wire:click="createNew" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl text-xs flex items-center gap-1.5 transition-colors shadow-md">
                    <span class="material-symbols-outlined text-lg">add_circle</span> Create New Banner
                </button>
            </div>
            @endif
        </div>

        @if(session()->has('success'))
            <div class="mb-6 p-4 bg-brand-50 border border-brand-200 text-brand-700 rounded-xl text-xs font-bold flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined">check_circle</span>
                    {{ session('success') }}
                </div>
                <button type="button" class="text-brand-700 hover:text-brand-900" onclick="this.parentElement.remove()"><span class="material-symbols-outlined text-lg">close</span></button>
            </div>
        @endif

        @if(!$isEditing)
            <!-- List View -->
            <div class="glass-card rounded-2xl shadow-sm overflow-hidden flex flex-col">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse text-xs align-middle">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                <th class="pl-4 py-3">Name</th>
                                <th class="py-3">Layout Type</th>
                                <th class="py-3 text-center">Order</th>
                                <th class="py-3 text-center">Duration</th>
                                <th class="py-3">Status</th>
                                <th class="pr-4 py-3 text-right" style="width: 250px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($banners as $banner)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="pl-4 py-3 font-bold text-slate-800">{{ $banner->name }}</td>
                                    <td>
                                        <span class="inline-block bg-slate-100 text-slate-600 rounded-lg px-2 py-0.5 font-bold text-[10px] border border-slate-200">
                                            {{ str_replace('_', ' ', ucfirst($banner->layout_type)) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="inline-block bg-slate-100 text-slate-600 rounded-lg px-2 py-0.5 font-bold text-[10px] border border-slate-200">{{ $banner->sort_order }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="inline-block bg-sky-50 text-sky-700 rounded-lg px-2 py-0.5 font-bold text-[10px] border border-sky-200">{{ $banner->display_duration }} sec</span>
                                    </td>
                                    <td>
                                        <button wire:click="toggleActiveStatus({{ $banner->id }})" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border transition-colors flex items-center w-max gap-1 {{ $banner->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-200 hover:bg-rose-100' }}">
                                            @if($banner->is_active)
                                                <span class="material-symbols-outlined" style="font-size:14px;">toggle_on</span> Active
                                            @else
                                                <span class="material-symbols-outlined" style="font-size:14px;">toggle_off</span> Inactive
                                            @endif
                                        </button>
                                    </td>
                                    <td class="pr-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button class="px-2.5 py-1 bg-sky-50 hover:bg-sky-100 text-sky-700 rounded-lg border border-sky-200 text-[11px] font-bold flex items-center gap-1 transition-colors shadow-sm" wire:click="viewBanner({{ $banner->id }})">
                                                <span class="material-symbols-outlined text-sm font-bold">visibility</span> View
                                            </button>
                                            <button class="px-2.5 py-1 bg-brand-50 hover:bg-brand-100 text-brand-700 rounded-lg border border-brand-200 text-[11px] font-bold flex items-center gap-1 transition-colors shadow-sm" wire:click="editBanner({{ $banner->id }})">
                                                <span class="material-symbols-outlined text-sm font-bold">edit</span> Edit
                                            </button>
                                            <button class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg border border-rose-200 text-[11px] font-bold flex items-center gap-1 transition-colors shadow-sm" onclick="confirm('Are you sure you want to delete this banner?') || event.stopImmediatePropagation()" wire:click="deleteBanner({{ $banner->id }})">
                                                <span class="material-symbols-outlined text-sm font-bold">delete</span> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-20 text-slate-400">
                                        <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">imagesmode</span>
                                        <p class="font-bold text-xs text-slate-500 uppercase tracking-widest">No banners found</p>
                                        <p class="text-[10px] text-slate-400 mt-1">Create your first banner layout to get started.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <!-- Edit/Create View -->
            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Editor Sidebar -->
                <div class="lg:w-1/3">
                    <div class="glass-card rounded-2xl shadow-sm sticky top-5 overflow-hidden">
                        <div class="bg-slate-50 border-b border-slate-200/60 p-4">
                            <h5 class="font-extrabold text-sm text-slate-800 flex items-center gap-1.5"><span class="material-symbols-outlined text-brand-600 text-lg">settings</span> Configuration</h5>
                        </div>
                        <div class="p-5 space-y-5">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Banner Name</label>
                                <input type="text" class="w-full py-2 px-3 bg-slate-50/50 border border-slate-200 rounded-xl outline-none text-xs font-semibold focus:ring-1 focus:ring-brand-500 focus:border-brand-500" wire:model="name" placeholder="e.g. Summer Promo">
                                @error('name') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Layout Style</label>
                                <select class="w-full py-2 px-3 bg-slate-50/50 border border-slate-200 rounded-xl outline-none text-xs font-semibold focus:ring-1 focus:ring-brand-500 focus:border-brand-500" wire:model.live="layout_type">
                                    <option value="layout_1">Layout 1 (1 Section - Full)</option>
                                    <option value="layout_2">Layout 2 (2 Sections - Split)</option>
                                    <option value="layout_3">Layout 3 (3 Sections - 1 Large, 2 Small)</option>
                                    <option value="layout_4">Layout 4 (4 Sections - 2x2 Grid)</option>
                                    <option value="layout_5">Layout 5 (5 Sections - Top 2, Bottom 3)</option>
                                    <option value="layout_6">Layout 6 (6 Sections - 3x2 Grid)</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Section Gap (px)</label>
                                    <input type="number" class="w-full py-2 px-3 bg-slate-50/50 border border-slate-200 rounded-xl outline-none text-xs font-semibold focus:ring-1 focus:ring-brand-500 focus:border-brand-500" wire:model.live="gap_size" min="0" max="100">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Edge Radius (px)</label>
                                    <input type="number" class="w-full py-2 px-3 bg-slate-50/50 border border-slate-200 rounded-xl outline-none text-xs font-semibold focus:ring-1 focus:ring-brand-500 focus:border-brand-500" wire:model.live="border_radius" min="0" max="100">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Display Time (sec)</label>
                                    <input type="number" class="w-full py-2 px-3 bg-slate-50/50 border border-slate-200 rounded-xl outline-none text-xs font-semibold focus:ring-1 focus:ring-brand-500 focus:border-brand-500" wire:model="display_duration" min="1" max="3600">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Sort Order</label>
                                    <input type="number" class="w-full py-2 px-3 bg-slate-50/50 border border-slate-200 rounded-xl outline-none text-xs font-semibold focus:ring-1 focus:ring-brand-500 focus:border-brand-500" wire:model="sort_order" min="0">
                                </div>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" id="activeSwitch" wire:model="is_active" class="rounded border-slate-300 text-brand-600 shadow-sm focus:ring-brand-500 focus:ring-offset-0 mr-2">
                                <label for="activeSwitch" class="text-xs font-bold text-slate-700">Set as Active Banner</label>
                            </div>

                            <hr class="border-slate-200">

                            <div class="flex flex-col gap-2">
                                <button class="w-full px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-colors shadow-sm" wire:click="saveBanner" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="saveBanner" class="flex items-center gap-1.5"><span class="material-symbols-outlined text-lg">save</span> Save Banner</span>
                                    <span wire:loading wire:target="saveBanner" class="flex items-center gap-1.5"><span class="material-symbols-outlined text-lg animate-spin">refresh</span> Saving...</span>
                                </button>
                                <button class="w-full px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-xl text-xs font-bold transition-colors shadow-sm" wire:click="resetForm">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preview Area -->
                <div class="lg:w-2/3">
                    <div class="glass-card rounded-2xl shadow-sm overflow-hidden">
                        <div class="bg-slate-50 border-b border-slate-200/60 p-4 flex justify-between items-center">
                            <h5 class="font-extrabold text-sm text-slate-800 flex items-center gap-1.5"><span class="material-symbols-outlined text-brand-600 text-lg">preview</span> Live Preview</h5>
                            <span class="inline-block bg-sky-50 text-sky-700 rounded-lg px-2 py-0.5 font-bold text-[10px] border border-sky-200 flex items-center gap-1">
                                <span class="material-symbols-outlined" style="font-size:12px;">desktop_windows</span> Desktop View
                            </span>
                        </div>
                        <div class="p-4 bg-slate-100">
                            <!-- Banner Container -->
                            <div class="bg-white rounded-xl shadow-sm overflow-hidden relative" style="aspect-ratio: 21/9;">
                                @php
                                    $sectionsCount = (int) str_replace('layout_', '', $layout_type);
                                @endphp

                                <div class="banner-grid {{ $layout_type }} h-full w-full p-2" style="gap: {{ is_numeric($gap_size) ? $gap_size : 8 }}px;">
                                    @for($i = 0; $i < $sectionsCount; $i++)
                                        <div class="section-{{ $i + 1 }} relative bg-slate-50 overflow-hidden border-2 border-dashed border-slate-300 flex items-center justify-center group transition-all hover:border-brand-500" style="border-radius: {{ is_numeric($border_radius) ? $border_radius : 8 }}px;">
                                            
                                            <!-- Hidden File Input -->
                                            <input type="file" wire:model="mediaUploads.{{ $i }}" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="image/*,video/*">
                                            
                                            <!-- Media Display -->
                                            @if(isset($mediaUploads[$i]) || isset($mediaItems[$i]))
                                                @php
                                                    $isTemp = isset($mediaUploads[$i]);
                                                    $mediaType = $isTemp ? (str_starts_with($mediaUploads[$i]->getMimeType(), 'video') ? 'video' : 'image') : ($mediaItems[$i]['type'] ?? 'image');
                                                    $mediaUrl = $isTemp ? $mediaUploads[$i]->temporaryUrl() : ($mediaItems[$i]['url'] ?? '');
                                                @endphp
                                                
                                                @if($mediaType === 'video')
                                                    <video src="{{ $mediaUrl }}" class="absolute inset-0 w-full h-full object-contain bg-black" autoplay muted loop></video>
                                                @else
                                                    <img src="{{ $mediaUrl }}" class="absolute inset-0 w-full h-full object-contain bg-slate-100" alt="Section {{ $i+1 }}">
                                                @endif
                                                
                                                <!-- Overlay Controls -->
                                                <div class="absolute inset-0 bg-slate-900/50 z-20 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center">
                                                    <div class="text-white mb-2 font-bold text-xs drop-shadow-md">Section {{ $i + 1 }}</div>
                                                    <button type="button" class="bg-rose-500 text-white rounded-full p-1.5 shadow hover:bg-rose-600 transition-colors z-30" wire:click.stop="removeMedia({{ $i }})">
                                                        <span class="material-symbols-outlined text-sm">close</span>
                                                    </button>
                                                </div>
                                            @else
                                                @php
                                                    $recommendedSize = '1920 x 822';
                                                    if ($layout_type === 'layout_2') $recommendedSize = '960 x 822';
                                                    elseif ($layout_type === 'layout_3') $recommendedSize = $i === 0 ? '1280 x 822' : '640 x 411';
                                                    elseif ($layout_type === 'layout_4') $recommendedSize = '960 x 411';
                                                    elseif ($layout_type === 'layout_5') $recommendedSize = $i < 2 ? '960 x 411' : '640 x 411';
                                                    elseif ($layout_type === 'layout_6') $recommendedSize = '640 x 411';
                                                @endphp
                                                <!-- Empty State -->
                                                <div class="text-center pointer-events-none p-2 flex flex-col items-center">
                                                    <span class="material-symbols-outlined text-3xl text-brand-500 mb-1 opacity-80">cloud_upload</span>
                                                    <div class="font-bold text-[10px] text-slate-500 uppercase tracking-widest">Section {{ $i + 1 }}</div>
                                                    <div class="text-[9px] text-slate-400 mt-0.5 hidden md:block">Click to upload</div>
                                                    <div class="inline-block bg-brand-50 text-brand-700 rounded-md px-1.5 py-0.5 font-bold text-[9px] border border-brand-200 mt-2">{{ $recommendedSize }}</div>
                                                    
                                                    <div wire:loading wire:target="mediaUploads.{{ $i }}" class="mt-2 text-brand-600 text-[10px] font-bold flex items-center gap-1">
                                                        <span class="material-symbols-outlined text-[12px] animate-spin">refresh</span> Uploading...
                                                    </div>
                                                    @error('mediaUploads.'.$i)
                                                        <div class="mt-2 bg-rose-50 text-rose-600 border border-rose-200 rounded px-2 py-1 text-[9px] font-bold shadow-sm flex items-center gap-1">
                                                            <span class="material-symbols-outlined text-[10px]">error</span> Required
                                                        </div>
                                                    @enderror
                                                </div>
                                            @endif
                                        </div>
                                    @endfor
                                </div>
                            </div>
                            
                            <div class="text-center mt-3 text-slate-500 text-[10px] font-semibold flex items-center justify-center gap-1">
                                <span class="material-symbols-outlined text-[12px]">info</span> Media will fit within the section without cropping.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Preview Modal -->
    @if($showPreviewModal && $previewBanner)
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[2000] flex items-center justify-center p-4" wire:click.self="closePreviewModal">
            <div class="bg-white border rounded-2xl shadow-2xl w-full max-w-6xl overflow-hidden">
                <div class="p-4 bg-slate-800 border-b border-slate-700 flex justify-between items-center text-white">
                    <h5 class="font-extrabold text-sm flex items-center gap-1.5"><span class="material-symbols-outlined text-lg">visibility</span> Preview: {{ $previewBanner->name }}</h5>
                    <button type="button" class="text-slate-400 hover:text-white transition-colors" wire:click="closePreviewModal"><span class="material-symbols-outlined">close</span></button>
                </div>
                
                <div class="p-0 bg-slate-100">
                    <div class="bg-white relative w-full" style="aspect-ratio: 21/9;">
                        @php
                            $sectionsCount = (int) str_replace('layout_', '', $previewBanner->layout_type);
                        @endphp
                        <div class="banner-grid {{ $previewBanner->layout_type }} h-full w-full p-2" style="gap: {{ is_numeric($previewBanner->gap_size) ? $previewBanner->gap_size : 8 }}px;">
                            @for($i = 0; $i < $sectionsCount; $i++)
                                <div class="section-{{ $i + 1 }} relative bg-slate-50 overflow-hidden border-2 border-slate-200 flex items-center justify-center" style="border-radius: {{ is_numeric($previewBanner->border_radius) ? $previewBanner->border_radius : 8 }}px;">
                                    @if(isset($previewBanner->media_items[$i]))
                                        @php
                                            $mediaItem = $previewBanner->media_items[$i];
                                            $mediaType = $mediaItem['type'] ?? 'image';
                                            $mediaUrl = $mediaItem['url'] ?? '';
                                        @endphp
                                        @if($mediaType === 'video')
                                            <video src="{{ $mediaUrl }}" class="absolute inset-0 w-full h-full object-contain bg-black" autoplay muted loop playsinline></video>
                                        @else
                                            <img src="{{ $mediaUrl }}" class="absolute inset-0 w-full h-full object-contain bg-slate-100" alt="Section {{ $i+1 }}">
                                        @endif
                                    @else
                                        <div class="text-center text-slate-400 flex flex-col items-center">
                                            <span class="material-symbols-outlined text-3xl opacity-50 mb-1">image</span>
                                            <div class="text-[10px] font-bold">Empty</div>
                                        </div>
                                    @endif
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Fullscreen Carousel Modal -->
    @if($showCarouselModal)
        <div class="fixed inset-0 bg-black z-[3000] flex items-center justify-center overflow-hidden" 
             x-data="{ 
                 activeIndex: 0, 
                 total: {{ count($activeBanners) }},
                 durations: {{ json_encode($activeBanners->pluck('display_duration')->map(fn($d) => ($d ?? 10) * 1000)) }},
                 timer: null,
                 init() {
                     if (this.total > 1) {
                         this.startTimer();
                     }
                 },
                 startTimer() {
                     let duration = this.durations[this.activeIndex] || 10000;
                     this.timer = setTimeout(() => {
                         this.next();
                     }, duration);
                 },
                 stopTimer() {
                     clearTimeout(this.timer);
                 },
                 next() {
                     this.stopTimer();
                     this.activeIndex = (this.activeIndex + 1) % this.total;
                     this.startTimer();
                 }
             }">
             
            <!-- Close Button -->
            <button type="button" class="absolute top-6 right-6 z-50 bg-black/50 hover:bg-white/20 text-white rounded-full p-2 backdrop-blur transition-colors" wire:click="closeCarouselModal" @click="stopTimer()">
                <span class="material-symbols-outlined text-3xl">close</span>
            </button>
            
            @if(count($activeBanners) === 0)
                <div class="text-white text-center">
                    <span class="material-symbols-outlined text-6xl mb-4 opacity-50">visibility_off</span>
                    <h2 class="text-2xl font-bold">No Active Banners</h2>
                    <p class="text-gray-400 mt-2">Activate some banners to see them in the slideshow.</p>
                </div>
            @else
                <!-- Slides Container -->
                <div class="relative w-full h-full flex items-center justify-center">
                    @foreach($activeBanners as $index => $banner)
                        <div class="absolute inset-0 w-full h-full transition-opacity duration-1000 ease-in-out"
                             x-show="activeIndex === {{ $index }}"
                             x-transition:enter="transition-opacity ease-linear duration-1000"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition-opacity ease-linear duration-1000"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             style="display: none;">
                             
                            <!-- Fullscreen Banner Layout -->
                            <div class="w-full h-full bg-black flex items-center justify-center p-8">
                                <div class="w-full h-full relative">
                                    @php
                                        $sectionsCount = (int) str_replace('layout_', '', $banner->layout_type);
                                    @endphp
                                    <div class="banner-grid {{ $banner->layout_type }} h-full w-full" style="gap: {{ is_numeric($banner->gap_size) ? $banner->gap_size : 8 }}px;">
                                        @for($i = 0; $i < $sectionsCount; $i++)
                                            <div class="section-{{ $i + 1 }} relative bg-slate-900 overflow-hidden flex items-center justify-center" style="border-radius: {{ is_numeric($banner->border_radius) ? $banner->border_radius : 8 }}px;">
                                                @if(isset($banner->media_items[$i]))
                                                    @php
                                                        $mediaItem = $banner->media_items[$i];
                                                        $mediaType = $mediaItem['type'] ?? 'image';
                                                        $mediaUrl = $mediaItem['url'] ?? '';
                                                    @endphp
                                                    @if($mediaType === 'video')
                                                        <video src="{{ $mediaUrl }}" class="absolute inset-0 w-full h-full object-contain bg-black" autoplay muted loop playsinline></video>
                                                    @else
                                                        <img src="{{ $mediaUrl }}" class="absolute inset-0 w-full h-full object-contain bg-black" alt="Section {{ $i+1 }}">
                                                    @endif
                                                @endif
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Progress Bar / Indicator -->
                <div class="absolute bottom-6 left-0 right-0 flex justify-center gap-3 z-40">
                    @foreach($activeBanners as $index => $banner)
                        <button class="w-12 h-1.5 rounded-full transition-all duration-300"
                                :class="activeIndex === {{ $index }} ? 'bg-white' : 'bg-white/30'"
                                @click="stopTimer(); activeIndex = {{ $index }}; startTimer();"></button>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>
