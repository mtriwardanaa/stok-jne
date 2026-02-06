@props([
    'options' => [],
    'value' => '',
    'placeholder' => 'Pilih...',
    'name' => '',
    'wireModel' => null,
])

<div 
    x-data="{
        open: false,
        search: '',
        value: @entangle($wireModel ?? 'null'),
        options: {{ json_encode($options) }},
        placeholder: '{{ $placeholder }}',
        
        get filteredOptions() {
            if (!this.search) return this.options;
            return this.options.filter(opt => 
                opt.label.toLowerCase().includes(this.search.toLowerCase())
            );
        },
        
        get selectedLabel() {
            const selected = this.options.find(opt => opt.value == this.value);
            return selected ? selected.label : '';
        },
        
        selectOption(option) {
            this.value = option.value;
            this.search = '';
            this.open = false;
        },
        
        clear() {
            this.value = '';
            this.search = '';
        }
    }"
    @click.away="open = false; search = ''"
    class="relative w-full"
>
    {{-- Hidden input for form submission --}}
    <input type="hidden" name="{{ $name }}" :value="value">
    
    {{-- Trigger button --}}
    <button
        type="button"
        @click="open = !open; $nextTick(() => open && $refs.searchInput.focus())"
        class="w-full flex items-center justify-between px-4 py-3 text-left bg-white border-2 rounded-xl transition-all duration-200"
        :class="open ? 'border-blue-500 ring-2 ring-blue-100' : 'border-gray-200 hover:border-gray-300'"
    >
        <span :class="value ? 'text-gray-900' : 'text-gray-400'" x-text="selectedLabel || placeholder"></span>
        <div class="flex items-center gap-2">
            <button 
                type="button" 
                x-show="value" 
                @click.stop="clear()" 
                class="text-gray-400 hover:text-red-500 transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <svg class="w-5 h-5 text-gray-400 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </button>
    
    {{-- Dropdown --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 w-full mt-2 bg-white border-2 border-gray-200 rounded-xl shadow-xl overflow-hidden"
        style="display: none;"
    >
        {{-- Search input --}}
        <div class="p-3 border-b border-gray-100 bg-gray-50">
            <input
                type="text"
                x-ref="searchInput"
                x-model="search"
                @keydown.escape="open = false"
                placeholder="Cari..."
                class="w-full px-3 py-2 text-sm border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all"
            >
        </div>
        
        {{-- Options list --}}
        <ul class="max-h-60 overflow-y-auto">
            <template x-for="option in filteredOptions" :key="option.value">
                <li
                    @click="selectOption(option)"
                    class="px-4 py-3 cursor-pointer transition-colors"
                    :class="value == option.value ? 'bg-blue-50 text-blue-700 font-medium' : 'hover:bg-gray-50 text-gray-700'"
                >
                    <span x-text="option.label"></span>
                </li>
            </template>
            <li x-show="filteredOptions.length === 0" class="px-4 py-8 text-center text-gray-400">
                Tidak ditemukan
            </li>
        </ul>
    </div>
</div>
