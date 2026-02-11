<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: ''
    },
    options: {
        type: Array,
        default: () => []
    },
    placeholder: {
        type: String,
        default: 'Pilih...'
    },
    searchPlaceholder: {
        type: String,
        default: 'Cari...'
    },
    compact: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update:modelValue'])

const isOpen = ref(false)
const search = ref('')
const inputRef = ref(null)
const containerRef = ref(null)

const filteredOptions = computed(() => {
    if (!search.value) return props.options
    const query = search.value.toLowerCase()
    return props.options.filter(opt => 
        opt.label.toLowerCase().includes(query)
    )
})

const selectedLabel = computed(() => {
    const selected = props.options.find(opt => String(opt.value) === String(props.modelValue))
    return selected ? selected.label : ''
})

const selectOption = (option) => {
    emit('update:modelValue', option.value)
    isOpen.value = false
    search.value = ''
}

const clear = () => {
    emit('update:modelValue', '')
    search.value = ''
}

const toggleDropdown = () => {
    isOpen.value = !isOpen.value
    if (isOpen.value) {
        setTimeout(() => inputRef.value?.focus(), 50)
    }
}

// Close on click outside
const handleClickOutside = (e) => {
    if (containerRef.value && !containerRef.value.contains(e.target)) {
        isOpen.value = false
        search.value = ''
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
    <div ref="containerRef" class="relative w-full">
        <!-- Trigger button -->
        <button
            type="button"
            @click="toggleDropdown"
            class="w-full flex items-center justify-between text-left bg-white rounded-xl transition-all duration-200 text-sm"
            :class="[
                compact 
                    ? 'px-2.5 py-1.5 border border-slate-200 hover:border-slate-300' 
                    : 'px-4 py-2.5 border border-slate-200 hover:border-slate-300',
                isOpen ? 'border-indigo-500 ring-2 ring-indigo-500/10' : ''
            ]"
        >
            <span class="truncate" :class="modelValue ? 'text-slate-900' : 'text-slate-400'">
                {{ selectedLabel || placeholder }}
            </span>
            <div class="flex items-center gap-1 ml-1 flex-shrink-0">
                <button 
                    v-if="modelValue"
                    type="button"
                    @click.stop="clear" 
                    class="text-slate-400 hover:text-red-500 transition-colors"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <svg 
                    class="w-4 h-4 text-slate-400 transition-transform" 
                    :class="isOpen && 'rotate-180'" 
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </button>
        
        <!-- Dropdown -->
        <Transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="opacity-0 -translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-1"
        >
            <div
                v-if="isOpen"
                class="absolute right-0 top-full z-[9999] mt-1 bg-white border border-slate-200 rounded-xl shadow-2xl overflow-hidden"
                style="min-width: 220px;"
            >
                <!-- Search input -->
                <div class="p-2.5 border-b border-slate-100 bg-slate-50">
                    <input
                        ref="inputRef"
                        v-model="search"
                        type="text"
                        :placeholder="searchPlaceholder"
                        @keydown.escape="isOpen = false"
                        class="w-full px-3 py-1.5 text-sm border border-slate-200 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 outline-none transition-all"
                    >
                </div>
                
                <!-- Options list -->
                <ul class="max-h-60 overflow-y-auto">
                    <li
                        v-for="option in filteredOptions"
                        :key="option.value"
                        @click="selectOption(option)"
                        class="px-3 py-2.5 cursor-pointer transition-colors flex items-center justify-between text-sm"
                        :class="String(modelValue) === String(option.value) 
                            ? 'bg-indigo-50 text-indigo-700 font-medium' 
                            : 'hover:bg-slate-50 text-slate-700'"
                    >
                        <span>{{ option.label }}</span>
                        <svg 
                            v-if="String(modelValue) === String(option.value)"
                            class="w-4 h-4 text-indigo-600" 
                            fill="none" 
                            viewBox="0 0 24 24" 
                            stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </li>
                    <li v-if="filteredOptions.length === 0" class="px-4 py-6 text-center text-slate-400 text-sm">
                        Tidak ditemukan
                    </li>
                </ul>
            </div>
        </Transition>
    </div>
</template>
