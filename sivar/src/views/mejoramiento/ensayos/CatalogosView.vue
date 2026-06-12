<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import debounce from 'lodash/debounce';
import { EnsayosService } from "@/services/ensayos.services";

// Local State
const catalogos = ref([]);
const stats = ref({});
const activeCategory = ref('ALL');
const search = ref('');
const isModalOpen = ref(false);
const isMergeModalOpen = ref(false);
const currentItem = ref(null);
const isLoading = ref(true);
const successMessage = ref('');
const errorMessage = ref('');

const form = ref({
    categoria: 'PROYECTO',
    valor: '',
    errors: {}
});

const mergeForm = ref({
    source_id: null,
    target_id: '',
    errors: {}
});

// Load catalog data from API
const loadCatalogos = async () => {
    isLoading.value = true;
    try {
        const catFilter = activeCategory.value === 'ALL' ? null : activeCategory.value;
        const response = await EnsayosService.getCatalogos({
            categoria: catFilter,
            search: search.value
        });
        catalogos.value = response.data.catalogos;
        stats.value = response.data.stats;
    } catch (error) {
        console.error("Error loading catalogs:", error);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    loadCatalogos();
});

// Watch activeCategory and search to reload
watch([activeCategory, search], debounce(() => {
    loadCatalogos();
}, 300));

// Computed filtered items
const filteredItems = computed(() => {
    return catalogos.value.filter(item => {
        const matchesCategory = activeCategory.value === 'ALL' || item.categoria === activeCategory.value;
        const matchesSearch = !search.value || item.valor.toLowerCase().includes(search.value.toLowerCase());
        return matchesCategory && matchesSearch;
    });
});

const openCreateModal = () => {
    currentItem.value = null;
    form.value = {
        categoria: activeCategory.value === 'ALL' ? 'PROYECTO' : activeCategory.value,
        valor: '',
        errors: {}
    };
    isModalOpen.value = true;
};

const openEditModal = (item) => {
    currentItem.value = item;
    form.value = {
        categoria: item.categoria,
        valor: item.valor,
        errors: {}
    };
    isModalOpen.value = true;
};

const submitForm = async () => {
    errorMessage.value = '';
    form.value.errors = {};
    
    try {
        if (currentItem.value) {
            const response = await EnsayosService.updateCatalogo(currentItem.value.id, {
                valor: form.value.valor
            });
            if (response.data.success) {
                successMessage.value = response.data.message || 'Catálogo modificado con éxito.';
                closeModal();
                loadCatalogos();
            }
        } else {
            const response = await EnsayosService.storeCatalogo({
                categoria: form.value.categoria,
                valor: form.value.valor
            });
            if (response.data.success) {
                successMessage.value = response.data.message || 'Elemento agregado con éxito.';
                closeModal();
                loadCatalogos();
            }
        }
    } catch (error) {
        if (error.response && error.response.status === 422) {
            form.value.errors = error.response.data.errors || { valor: error.response.data.message };
        } else {
            errorMessage.value = error.response?.data?.message || 'Error al guardar el elemento.';
        }
    }
};

const closeModal = () => {
    isModalOpen.value = false;
    currentItem.value = null;
};

const sourceItem = ref(null);

const allowedMergeTargets = computed(() => {
    if (!sourceItem.value) return [];
    return catalogos.value.filter(item => 
        item.categoria === sourceItem.value.categoria && 
        item.id !== sourceItem.value.id
    );
});

const openMergeModal = (item) => {
    sourceItem.value = item;
    mergeForm.value = {
        source_id: item.id,
        target_id: '',
        errors: {}
    };
    isMergeModalOpen.value = true;
};

const submitMerge = async () => {
    mergeForm.value.errors = {};
    try {
        const response = await EnsayosService.mergeCatalogos({
            source_id: mergeForm.value.source_id,
            target_id: mergeForm.value.target_id
        });
        if (response.data.success) {
            successMessage.value = response.data.message || 'Fusión completada con éxito.';
            closeMergeModal();
            loadCatalogos();
        }
    } catch (error) {
        if (error.response && error.response.status === 422) {
            mergeForm.value.errors = { target_id: error.response.data.message };
        } else {
            console.error("Error during merge:", error);
        }
    }
};

const closeMergeModal = () => {
    isMergeModalOpen.value = false;
    sourceItem.value = null;
};

const deleteItem = async (item) => {
    if (confirm(`¿Estás seguro de eliminar "${item.valor}" del catálogo? Esto no borrará tus ensayos, pero dejará el campo desvinculado.`)) {
        try {
            const response = await EnsayosService.deleteCatalogo(item.id);
            if (response.data.success) {
                successMessage.value = response.data.message || 'Elemento eliminado.';
                loadCatalogos();
            }
        } catch (error) {
            console.error("Error deleting catalog item:", error);
        }
    }
};

const getUsageCount = (item) => {
    if (!item) return 0;
    const cat = item.categoria;
    const val = item.valor;
    if (cat === 'PROYECTO') return stats.value?.PROYECTO?.[val] || 0;
    if (cat === 'INGENIO') return stats.value?.INGENIO?.[val] || 0;
    if (cat === 'AMBIENTE') {
        const s = stats.value?.AMBIENTE_SEL?.[val] || 0;
        const e = stats.value?.AMBIENTE_EVAL?.[val] || 0;
        return s + e;
    }
    return 0;
};

const formatCatName = (cat) => {
    if (cat === 'PROYECTO') return '📁 Proyecto';
    if (cat === 'INGENIO') return '🏭 Ingenio';
    if (cat === 'AMBIENTE') return '🌱 Ambiente';
    return cat;
};
</script>

<template>
    <div class="min-h-screen bg-slate-50/50 p-4 sm:p-8 font-sans">
        <!-- Overlay Loading State -->
        <div v-if="isLoading" class="absolute inset-0 z-50 bg-white/70 backdrop-blur-sm flex flex-col items-center justify-center transition-all duration-300">
            <div class="p-4 bg-white rounded-2xl shadow-xl flex flex-col items-center gap-3 border border-indigo-100">
                <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-indigo-800 font-bold tracking-wide animate-pulse">Sincronizando catálogos...</span>
            </div>
        </div>

        <div class="max-w-7xl mx-auto space-y-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/60 shadow-sm">
                <div class="flex items-center space-x-3">
                    <div class="p-2.5 bg-indigo-100 text-indigo-600 rounded-xl shadow-inner">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black leading-tight text-slate-800">Diccionarios Maestros</h2>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">Gestión de nomenclaturas estandarizadas y fusión de históricos en cascada.</p>
                    </div>
                </div>
                <button 
                    @click="openCreateModal"
                    class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-200 hover:shadow-xl transition duration-300 group"
                >
                    <svg class="w-4 h-4 mr-2 group-hover:rotate-90 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Agregar Elemento
                </button>
            </div>

            <!-- Flash success alerts -->
            <div v-if="successMessage" class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-xl shadow-sm font-semibold flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ successMessage }}</span>
                </div>
                <button @click="successMessage = ''" class="text-slate-400 hover:text-slate-700">✕</button>
            </div>

            <div v-if="errorMessage" class="p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded-xl shadow-sm font-semibold flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ errorMessage }}</span>
                </div>
                <button @click="errorMessage = ''" class="text-slate-400 hover:text-slate-700">✕</button>
            </div>

            <!-- Toolbar Filters -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex flex-col lg:flex-row gap-5 items-center justify-between">
                <!-- Category Tabs -->
                <div class="flex p-1 bg-slate-100 rounded-xl w-full lg:w-auto overflow-x-auto">
                    <button 
                        v-for="cat in ['ALL', 'PROYECTO', 'INGENIO', 'AMBIENTE']" 
                        :key="cat"
                        @click="activeCategory = cat"
                        class="px-5 py-2 text-xs sm:text-sm font-bold rounded-lg whitespace-nowrap transition duration-200"
                        :class="activeCategory === cat ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                    >
                        {{ cat === 'ALL' ? '📚 Todos' : formatCatName(cat) }}
                    </button>
                </div>

                <!-- Live Search -->
                <div class="relative w-full lg:w-80">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input 
                        type="text" 
                        v-model="search" 
                        placeholder="Buscar por nombre o código..."
                        class="block w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 placeholder-slate-400 font-medium"
                    />
                </div>
            </div>

            <!-- Results Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div v-for="cat in ['PROYECTO', 'INGENIO', 'AMBIENTE']" :key="cat" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center justify-between transition duration-300 hover:shadow-md">
                    <div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ cat }}S</span>
                        <h4 class="text-2xl font-black text-slate-800 leading-tight mt-1">
                            {{ catalogos.filter(i => i.categoria === cat).length }}
                        </h4>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-2xl">
                        {{ cat === 'PROYECTO' ? '📁' : (cat === 'INGENIO' ? '🏭' : '🌱') }}
                    </div>
                </div>
            </div>

            <!-- Main Table / Data Matrix -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Categoría</th>
                                <th class="px-6 py-4 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nombre / Código</th>
                                <th class="px-6 py-4 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Impacto / Uso Activo</th>
                                <th class="px-6 py-4 text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider">Operaciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            <tr v-for="item in filteredItems" :key="item.id" class="group hover:bg-indigo-50/20 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span 
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider border shadow-sm"
                                        :class="{
                                            'bg-emerald-50 border-emerald-200 text-emerald-700': item.categoria === 'PROYECTO',
                                            'bg-amber-50 border-amber-200 text-amber-700': item.categoria === 'INGENIO',
                                            'bg-blue-50 border-blue-200 text-blue-700': item.categoria === 'AMBIENTE'
                                        }"
                                    >
                                        {{ item.categoria }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-extrabold text-slate-800 leading-snug">{{ item.valor }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <div 
                                            class="h-2.5 w-2.5 rounded-full animate-pulse-slow"
                                            :class="getUsageCount(item) > 0 ? 'bg-emerald-500' : 'bg-slate-300'"
                                        ></div>
                                        <span class="text-xs font-bold text-slate-600">
                                            {{ getUsageCount(item) }} ensayos vinculados
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right space-x-1 text-sm font-medium">
                                    <!-- Merge Utility Trigger -->
                                    <button 
                                        @click="openMergeModal(item)" 
                                        title="Fusionar Duplicados"
                                        class="inline-flex items-center p-2 text-amber-600 hover:bg-amber-50 border border-transparent hover:border-amber-200 rounded-lg transition duration-200"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                    </button>
                                    <!-- Edit Trigger -->
                                    <button 
                                        @click="openEditModal(item)" 
                                        class="inline-flex items-center p-2 text-indigo-600 hover:bg-indigo-50 border border-transparent hover:border-indigo-200 rounded-lg transition duration-200"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                    <!-- Delete Trigger -->
                                    <button 
                                        @click="deleteItem(item)" 
                                        class="inline-flex items-center p-2 text-red-600 hover:bg-red-50 border border-transparent hover:border-red-200 rounded-lg transition duration-200"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="filteredItems.length === 0">
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400 font-medium text-sm bg-slate-50/30">
                                    🚫 No se encontraron elementos coincidentes en este catálogo.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal: CREAR / EDITAR ELEMENTO -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm transition-all duration-300 animate-fade-in">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all scale-100 border border-slate-100">
                <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-indigo-50/40 to-transparent flex items-center justify-between">
                    <h3 class="text-lg font-black text-slate-900">
                        {{ currentItem ? 'Editar Elemento del Catálogo' : 'Agregar Nuevo Elemento' }}
                    </h3>
                    <button @click="closeModal" class="p-1 rounded-full hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="p-6 space-y-5">
                    <div v-if="!currentItem">
                        <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-2">Categoría Destino</label>
                        <select 
                            v-model="form.categoria"
                            class="block w-full bg-slate-50 border border-slate-300 rounded-xl py-3 px-4 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition font-bold text-slate-700"
                            required
                        >
                            <option value="PROYECTO">📁 PROYECTO</option>
                            <option value="INGENIO">🏭 INGENIO</option>
                            <option value="AMBIENTE">🌱 AMBIENTE</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-2">
                            Valor Estandarizado (Nombre / Código)
                        </label>
                        <input 
                            type="text" 
                            v-model="form.valor" 
                            required
                            placeholder="Escribe el valor oficial exactamente..."
                            class="block w-full bg-slate-50 border border-slate-300 rounded-xl py-3 px-4 text-sm shadow-sm focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition font-bold text-slate-800"
                        />
                        <p v-if="form.errors.valor" class="mt-1.5 text-xs text-red-600 font-bold animate-pulse-once">❌ {{ form.errors.valor[0] || form.errors.valor }}</p>
                    </div>

                    <div v-if="currentItem && getUsageCount(currentItem) > 0" class="p-4 bg-amber-50 rounded-xl border border-amber-100 flex gap-3">
                        <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <p class="text-[11px] leading-relaxed font-bold text-amber-800">
                            ¡ADVERTENCIA DE CASCADA! Modificar este valor actualizará automáticamente los {{ getUsageCount(currentItem) }} registros históricos vinculados en la base de datos.
                        </p>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3">
                        <button 
                            type="button" 
                            @click="closeModal"
                            class="px-4 py-2 text-sm font-bold text-slate-500 hover:text-slate-700 transition"
                        >
                            Cancelar
                        </button>
                        <button 
                            type="submit" 
                            class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-md transition duration-200"
                        >
                            {{ currentItem ? 'Actualizar Todo' : 'Registrar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal: FUSIÓN DE CATÁLOGOS (Merge Tool) -->
        <div v-if="isMergeModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-md transition-all duration-300 animate-fade-in">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all scale-100 border border-red-100">
                <div class="p-6 border-b border-red-100 bg-gradient-to-r from-red-50/50 to-transparent flex items-center justify-between">
                    <div class="flex items-center gap-2 text-red-700">
                        <svg class="w-6 h-6 animate-bounce-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        <h3 class="text-lg font-black">Fusionar Entidades de Catálogo</h3>
                    </div>
                    <button @click="closeMergeModal" class="p-1 rounded-full hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form @submit.prevent="submitMerge" class="p-6 space-y-5">
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl relative overflow-hidden flex flex-col gap-2">
                        <span class="text-[9px] font-black uppercase text-slate-400 tracking-widest">Origen (A Eliminar)</span>
                        <span class="text-base font-black text-red-600 line-through">"{{ sourceItem?.valor }}"</span>
                        <p class="text-xs font-bold text-slate-500 italic mt-1">
                            Este elemento redundante desaparecerá de la lista del catálogo definitivamente.
                        </p>
                    </div>

                    <div class="flex justify-center -my-2 text-slate-400 relative z-10">
                        <div class="p-2 bg-white rounded-full border shadow-sm">
                            <svg class="w-6 h-6 animate-pulse-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-2">
                            Destino Maestro (Mantener y Unificar en)
                        </label>
                        <select 
                            v-model="mergeForm.target_id"
                            required
                            class="block w-full bg-slate-50 border border-slate-300 rounded-xl py-3 px-4 text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition font-black text-slate-800"
                        >
                            <option value="" disabled>Seleccionar entidad oficial de destino...</option>
                            <option v-for="target in allowedMergeTargets" :key="target.id" :value="target.id">
                                ✔️ {{ target.valor }}
                            </option>
                        </select>
                        <p v-if="mergeForm.errors.target_id" class="mt-1 text-xs text-red-600 font-bold">❌ Elige un destino válido.</p>
                    </div>

                    <div class="p-5 bg-red-50 rounded-2xl border border-red-200 space-y-3">
                        <h5 class="text-xs font-black text-red-800 uppercase flex items-center gap-2">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Operación Crítica E Irreversible
                        </h5>
                        <p class="text-xs leading-relaxed text-red-700 font-medium">
                            Al proceder, el motor de SIVAR re-mapeará **los {{ getUsageCount(sourceItem) }} ensayos históricos** que utilizaban la llave eliminada, asignándoles la nueva llave oficial elegida arriba en una transacción segura.
                        </p>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3">
                        <button 
                            type="button" 
                            @click="closeMergeModal"
                            class="px-4 py-2 text-sm font-bold text-slate-500 hover:text-slate-700 transition"
                        >
                            Abortar
                        </button>
                        <button 
                            type="submit" 
                            :disabled="!mergeForm.target_id"
                            class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white text-sm font-black rounded-xl shadow-lg shadow-red-200 disabled:opacity-50 disabled:cursor-not-allowed transition duration-300 transform active:scale-95"
                        >
                            Ejecutar Fusión de Datos
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.animate-pulse-slow {
    animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.animate-bounce-slow {
    animation: bounce 2s infinite;
}

@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.97); }
    to { opacity: 1; transform: scale(1); }
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: .4; }
}
</style>
