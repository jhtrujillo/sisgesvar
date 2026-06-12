<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const props = defineProps({
    conflicts: Object, // Contains 'proyectos': []
    catalogo: Object,  // Contains 'proyectos': []
    tempPath: String,
    ambiente: String,
});

// Setup mappings object dynamically
const form = useForm({
    tempPath: props.tempPath,
    ambiente: props.ambiente,
    mappings: {}, 
});

onMounted(() => {
    // Loop through all available dynamic conflict categories
    Object.keys(props.conflicts).forEach(category => {
        form.mappings[category] = {};
        props.conflicts[category].forEach(oldVal => {
            form.mappings[category][oldVal] = '__NEW__'; 
        });
    });
});

const submitMappings = () => {
    form.post(route('ensayos.confirm-import'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Homologación de Datos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-amber-100 text-amber-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-800 leading-tight">Validación de Catálogos</h2>
                    <p class="text-sm text-slate-500 font-medium mt-0.5">Se han detectado valores nuevos o diferentes en el archivo cargado.</p>
                </div>
            </div>
        </template>

        <div class="py-12 bg-[#f9fafb] min-h-screen">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                
                <form @submit.prevent="submitMappings" class="space-y-8">
                    <div 
                        v-for="(items, category, index) in conflicts" 
                        :key="category" 
                        class="bg-white shadow-2xl rounded-3xl border border-slate-200 overflow-hidden transition duration-300 hover:shadow-emerald-100/20"
                    >
                        <div class="p-8 border-b border-slate-100 bg-gradient-to-r from-amber-50/40 to-transparent">
                            <h3 class="text-xl font-bold text-slate-900 flex items-center gap-3">
                                <span class="w-8 h-8 flex items-center justify-center bg-amber-100 rounded-full text-amber-600 font-black text-sm">
                                    {{ Object.keys(conflicts).indexOf(category) + 1 }}
                                </span>
                                Homologación de la Columna: <span class="text-amber-700 font-black uppercase tracking-wide">{{ category }}</span>
                            </h3>
                            <p class="mt-3 text-slate-600 text-sm max-w-2xl leading-relaxed">
                                Los siguientes nombres detectados para <strong>{{ category }}</strong> no se encuentran en el Catálogo Maestro. 
                                Define la equivalencia oficial para que el sistema inserte los registros de forma coherente.
                            </p>
                        </div>

                        <div class="p-8 space-y-6 bg-slate-50/50">
                            <div 
                                v-for="(oldValue, itemIndex) in items" 
                                :key="itemIndex" 
                                class="group p-5 bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md hover:border-amber-200 transition duration-300"
                            >
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                                    
                                    <!-- Left: Excel value -->
                                    <div>
                                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-2">Encontrado en Excel</span>
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0 p-2.5 bg-amber-50/60 text-amber-600 rounded-xl border border-amber-100">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </div>
                                            <p class="text-slate-800 font-extrabold text-sm leading-snug break-all tracking-wide">
                                                "{{ oldValue }}"
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Right: Decision Selector -->
                                    <div v-if="form.mappings[category]">
                                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-2">Acción Correctora</span>
                                        <select 
                                            v-model="form.mappings[category][oldValue]"
                                            class="block w-full bg-white border border-slate-300 rounded-xl py-3.5 px-4 text-sm font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition shadow-sm hover:border-slate-400"
                                        >
                                            <option value="__NEW__" class="text-emerald-700 font-extrabold">
                                                ➕ Guardar como NUEVO/A en {{ category }}
                                            </option>
                                            <optgroup label="🔄 Homologar con Existente:">
                                                <option v-for="catItem in (catalogo[category] || [])" :key="catItem" :value="catItem">
                                                    Vincular a: {{ catItem }}
                                                </option>
                                            </optgroup>
                                        </select>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Global footer actions (placed outside the loop, unified execution) -->
                    <div class="bg-white shadow-xl rounded-2xl border border-slate-200 px-8 py-6 flex items-center justify-between">
                        <button 
                            type="button"
                            @click="$inertia.get(route('ensayos.index'))"
                            class="text-sm font-bold text-slate-500 hover:text-red-600 transition duration-300"
                        >
                            ❌ Cancelar Importación
                        </button>
                        
                        <button 
                            type="submit"
                            :disabled="form.processing"
                            class="px-10 py-3.5 bg-slate-900 text-white text-sm font-extrabold rounded-xl shadow-lg flex items-center gap-3 transition duration-300 hover:bg-emerald-700 hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-slate-900 disabled:hover:translate-y-0"
                        >
                            <span v-if="form.processing" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
                            <span v-if="form.processing">Procesando Mapeos...</span>
                            <template v-else>
                                <span>Homologar Todo e Insertar</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </template>
                        </button>
                    </div>
                </form>

                <!-- Help Info box -->
                <div class="mt-8 p-4 bg-blue-50 rounded-2xl flex gap-3 border border-blue-100 text-blue-800">
                    <svg class="w-6 h-6 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-sm leading-relaxed">
                        <strong>Consejo SIVAR:</strong> Si vinculas un valor a uno existente, el sistema guardará el dato corregido automáticamente en la base de datos. Si seleccionas "Registrar como Nuevo", el valor pasará a formar parte de tus Catálogos Maestros de forma permanente para futuras cargas.
                    </p>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
