<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router, Link, usePage } from '@inertiajs/vue3';
import { ref, watch, nextTick, computed } from 'vue';
import { debounce } from 'lodash';
import axios from 'axios';

const props = defineProps({
    ensayos: Object,
    filters: Object,
    catalogos: Object, // Contains grouped lists by category
    users_list: Array
});

// Field mapping to Catalog categories for validation/autocomplete
const catalogMap = {
    proyecto: 'PROYECTO',
    ingenio: 'INGENIO',
    amb_seleccion: 'AMBIENTE',
    amb_evaluacion: 'AMBIENTE'
};

// Editing State Management
const editingCell = ref({ rowId: null, field: null });
const editValue = ref('');
const savingCell = ref({ rowId: null, field: null });

// State for Catalogo Overview Modal
const showCatalogoModal = ref(false);

const startEdit = (ensayo, col) => {
    editingCell.value = { rowId: ensayo.id, field: col.key };
    // For dates, ensure they work nicely with input type=date if we had it, 
    // but generic text covers everything flexible.
    editValue.value = ensayo[col.key] || '';
    
    nextTick(() => {
        const el = document.getElementById(`input-${ensayo.id}-${col.key}`);
        if (el) {
            el.focus();
            if (typeof el.select === 'function') el.select();
        }
    });
};

const cancelEdit = () => {
    editingCell.value = { rowId: null, field: null };
    editValue.value = '';
};

// --- Adjuntos / Attachments Slide-over Drawer ---
const drawerOpen = ref(false);
const activeEnsayo = ref(null);
const currentAdjuntos = ref([]);
const loadingAdjuntos = ref(false);
const uploadingFile = ref(false);
const adjuntoInputRef = ref(null);

const openAdjuntos = async (ensayo) => {
    activeEnsayo.value = ensayo;
    drawerOpen.value = true;
    currentAdjuntos.value = [];
    loadingAdjuntos.value = true;
    try {
        const res = await axios.get(route('adjuntos.index', ensayo.id));
        currentAdjuntos.value = res.data;
    } catch (e) {
        console.error(e);
    } finally {
        loadingAdjuntos.value = false;
    }
};

const closeDrawer = () => {
    drawerOpen.value = false;
    activeEnsayo.value = null;
    currentAdjuntos.value = [];
};

const handleAdjuntoUpload = async (e) => {
    const file = e.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('file', file);

    uploadingFile.value = true;
    try {
        const res = await axios.post(route('adjuntos.store', activeEnsayo.value.id), formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        currentAdjuntos.value.unshift(res.data.adjunto);
        // Increment count reactively in local array
        if (activeEnsayo.value) {
            activeEnsayo.value.adjuntos_count = (activeEnsayo.value.adjuntos_count || 0) + 1;
        }
    } catch (err) {
        alert('Falla al subir el archivo: ' + (err.response?.data?.message || err.message));
    } finally {
        uploadingFile.value = false;
        if (adjuntoInputRef.value) adjuntoInputRef.value.value = '';
    }
};

const deleteAdjunto = async (adjunto) => {
    if (!confirm(`¿Estás seguro de eliminar el archivo "${adjunto.nombre_archivo}"?`)) return;

    try {
        await axios.delete(route('adjuntos.destroy', adjunto.id));
        currentAdjuntos.value = currentAdjuntos.value.filter(a => a.id !== adjunto.id);
        // Decrement count reactively in local array
        if (activeEnsayo.value && activeEnsayo.value.adjuntos_count > 0) {
            activeEnsayo.value.adjuntos_count--;
        }
    } catch (err) {
        alert('Error al eliminar: ' + (err.response?.data?.error || err.message));
    }
};

const formatBytes = (bytes, decimals = 2) => {
    if (!+bytes) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
};

const getFileIcon = (mime) => {
    if (!mime) return '📄';
    if (mime.includes('pdf')) return '📕';
    if (mime.includes('image')) return '🖼️';
    if (mime.includes('spreadsheet') || mime.includes('excel') || mime.includes('csv')) return '📊';
    if (mime.includes('word') || mime.includes('officedocument')) return '📝';
    return '📄';
};

// --- 🛡️ SMART DATA QUALITY GUARD (State & Decisions) ---
const smartConfirm = ref({
    isOpen: false,
    dialogType: '', // 'FUZZY_MATCH' | 'NEW_TERM'
    valorPropuesto: '',
    valorSugerido: '',
    categoria: '',
    field: '',
    ensayo: null,
    col: null
});

const closeSmartConfirm = () => {
    smartConfirm.value = { isOpen: false, dialogType: '', valorPropuesto: '', valorSugerido: '', categoria: '', field: '', ensayo: null, col: null };
    savingCell.value = { rowId: null, field: null };
};

const executeSmartDecision = async (decision) => {
    const state = smartConfirm.value;
    if (!state.ensayo || !state.col) return;
    
    const targetEnsayo = state.ensayo;
    const targetCol = state.col;
    const payloadDecision = decision; // 'USE_SUGGESTED' or 'CREATE_NEW'
    const suggested = state.valorSugerido;
    
    // Reseteamos lock y cerramos vista instantáneamente para dar fluidez visual
    savingCell.value = { rowId: targetEnsayo.id, field: targetCol.key };
    smartConfirm.value.isOpen = false;

    try {
        await axios.post(route('ensayos.update', targetEnsayo.id), {
            _method: 'PATCH',
            field: targetCol.key,
            value: state.valorPropuesto,
            decision_catalogo: payloadDecision,
            suggested_value: suggested
        });

        // Todo bien! Actualizamos la cuadrícula limpiamente
        router.reload({ 
            only: ['ensayos', 'catalogos', 'flash'], 
            preserveScroll: true,
            preserveState: true
        });
    } catch (err) {
        console.error("Smart decision execution failure:", err);
        alert("Ocurrió un problema al procesar tu elección. Inténtalo de nuevo.");
    } finally {
        savingCell.value = { rowId: null, field: null };
        // Limpiamos el payload
        smartConfirm.value = { isOpen: false, dialogType: '', valorPropuesto: '', valorSugerido: '', categoria: '', field: '', ensayo: null, col: null };
    }
};

const saveEdit = async (ensayo, col) => {
    // Guard against concurrent saving triggers
    if (savingCell.value.rowId !== null) return;

    const oldValue = ensayo[col.key];
    const newValue = editValue.value;
    
    // Compare safely, avoid hit if no logical change
    if (String(oldValue ?? '') === String(newValue ?? '')) {
        editingCell.value = { rowId: null, field: null };
        return;
    }

    // Trigger saving animation
    savingCell.value = { rowId: ensayo.id, field: col.key };
    editingCell.value = { rowId: null, field: null };

    try {
        const response = await axios.post(route('ensayos.update', ensayo.id), {
            _method: 'PATCH',
            field: col.key,
            value: newValue
        });

        // --- 🕵️‍♂️ INTERCEPTOR DE INTELIGENCIA DE DATOS ---
        if (response.data && response.data.dialog_needed) {
            // Abrimos la interfaz flotante PREMIUM de toma de decisiones
            smartConfirm.value = {
                isOpen: true,
                dialogType: response.data.dialog_type,
                valorPropuesto: response.data.valor_propuesto,
                valorSugerido: response.data.valor_sugerido || '',
                categoria: response.data.categoria,
                field: response.data.field,
                ensayo: ensayo,
                col: col
            };
            // Soltamos la animación de carga para dar paso a la interacción del modal
            savingCell.value = { rowId: null, field: null };
            return;
        }

        // Todo salió bien y guardó sin obstáculos!
        router.reload({ 
            only: ['ensayos', 'catalogos', 'flash'], 
            preserveScroll: true,
            preserveState: true
        });

    } catch (error) {
        console.error("Falla crítica en saveEdit:", error);
        alert("FALLÓ EL GUARDADO. Por favor refresca la página.");
    } finally {
        // Solo quitamos el lock si no dejamos el modal abierto
        if (!smartConfirm.value.isOpen) {
            savingCell.value = { rowId: null, field: null };
        }
    }
};

// Full list of columns exactly mapping the Excel template
const columns = [
    { label: 'Nombre_Ensayo', key: 'nombre_ensayo', class: 'sticky-col', style: 'min-width: 200px;' },
    { label: 'Nombre_Env', key: 'nombre_env', style: 'min-width: 180px;' },
    { label: 'Proyecto', key: 'proyecto', style: 'min-width: 250px;' },
    { label: 'Estado de selección', key: 'estado_seleccion', style: 'min-width: 150px;' },
    { label: 'Serie', key: 'serie', style: 'min-width: 80px;' },
    { label: 'Amb_Seleccion', key: 'amb_seleccion', style: 'min-width: 140px;' },
    { label: 'Amb_Evaluación', key: 'amb_evaluacion', style: 'min-width: 140px;' },
    { label: 'Objetivo', key: 'objetivo', style: 'min-width: 120px;' },
    { label: 'Ingenio', key: 'ingenio', style: 'min-width: 80px;' },
    { label: 'Hacienda', key: 'hacienda', style: 'min-width: 140px;' },
    { label: 'Suerte', key: 'suerte', style: 'min-width: 80px;' },
    { label: 'ZA', key: 'zona_agroecologia', style: 'min-width: 70px;' },
    { label: 'CS', key: 'consociacion', style: 'min-width: 70px;' },
    { label: 'Corte', key: 'corte', style: 'min-width: 70px;' },
    { label: 'Entradas', key: 'entradas', style: 'min-width: 80px;' },
    { label: 'Checks', key: 'testigos', style: 'min-width: 80px;' },
    { label: 'NoClones', key: 'clones', style: 'min-width: 80px;' },
    { label: 'Plots', key: 'total_parcelas', style: 'min-width: 80px;' },
    { label: 'Diseño', key: 'diseno', style: 'min-width: 100px;' },
    { label: 'Nsurcos/Plot', key: 'surcos', style: 'min-width: 90px;' },
    { label: 'LonSurco', key: 'longitud_surco', style: 'min-width: 90px;' },
    { label: 'LonSurcoCallejon', key: 'longitud_callejon', style: 'min-width: 120px;' },
    { label: 'DistEntreSurco', key: 'distancia_surco', style: 'min-width: 120px;' },
    { label: 'AreaEnsayo', key: 'area_total', style: 'min-width: 100px;' },
    { label: 'RMA', key: 'red_meteorologica', style: 'min-width: 80px;' },
    { label: 'FS', key: 'fecha_siembra', isDate: true, style: 'min-width: 100px;' },
    { label: 'FC', key: 'fecha_cosecha_final', isDate: true, style: 'min-width: 100px;' },
    { label: 'Feval', key: 'fecha_evaluacion', isDate: true, style: 'min-width: 100px;' },
    { label: 'MDS', key: 'meses_evaluacion', style: 'min-width: 80px;' },
    { label: 'FC-Programada', key: 'fecha_cosecha_programada', isDate: true, style: 'min-width: 120px;' },
    { label: 'Edad Actual', key: 'estado_actual', style: 'min-width: 100px;' },
    { label: 'Año', key: 'ano_siembra', style: 'min-width: 80px;' },
    { label: 'Mes', key: 'mes_siembra', style: 'min-width: 80px;' },
    { label: 'TipoCosecha', key: 'tipo_cosecha', style: 'min-width: 140px;' },
    { label: 'Comentario', key: 'comentarios', style: 'min-width: 300px;' },
    { label: 'NombreArchivo', key: 'ubicacion_fisica', style: 'min-width: 200px;' },
    { label: 'Nombre_Ensayo2', key: 'nombre_reporte', style: 'min-width: 200px;' },
    { label: 'Investigador', key: 'investigador', style: 'min-width: 150px;' },
    { label: 'Cargado Por', key: 'user', isStatic: true, style: 'min-width: 160px;' }
];

// Search & Pagination Filters
const search = ref(props.filters.search || '');
const perPage = ref(props.filters.per_page || 10);
const sortBy = ref(props.filters.sort_by || 'id');
const sortDirection = ref(props.filters.sort_direction || 'asc');
const filterAmbiente = ref(props.filters.ambiente || '');
const filterUserId = ref(props.filters.user_id || '');

const updateFilters = debounce(() => {
    router.get(
        route('ensayos.index'),
        { 
            search: search.value, 
            ambiente: filterAmbiente.value,
            user_id: filterUserId.value,
            per_page: perPage.value,
            sort_by: sortBy.value,
            sort_direction: sortDirection.value
        },
        { preserveState: true, replace: true, preserveScroll: true }
    );
}, 300);

watch(search, () => updateFilters());
watch(filterAmbiente, () => updateFilters());
watch(filterUserId, () => updateFilters());
watch(perPage, () => updateFilters());

// --- 🧠 SMART DATA STANDARDIZATION ENGINE ---
const showStandardizerModal = ref(false);
const isScanningStandardizer = ref(false);
const isExecutingStandardizer = ref(false);
const standardizerAutoFixes = ref([]);
const standardizerManualFixes = ref([]);
const manualFixSelections = ref({});

const openStandardizer = async () => {
    showStandardizerModal.value = true;
    isScanningStandardizer.value = true;
    standardizerAutoFixes.value = [];
    standardizerManualFixes.value = [];
    manualFixSelections.value = {};
    
    try {
        const resp = await axios.get(route('ensayos.standardization.preview'));
        standardizerAutoFixes.value = resp.data.auto_fixes || [];
        standardizerManualFixes.value = resp.data.manual_fixes || [];
        
        // Map dynamic bindings for drop-down lists per orphan card
        standardizerManualFixes.value.forEach((fix, idx) => {
            manualFixSelections.value[`fix_${idx}`] = '';
        });
    } catch (err) {
        console.error('Standardizer scanner fail:', err);
    } finally {
        isScanningStandardizer.value = false;
    }
};

const applyAllAutoFixes = async () => {
    if (standardizerAutoFixes.value.length === 0) return;
    
    if (!confirm(`¿Seguro que deseas aplicar estas ${standardizerAutoFixes.value.length} correcciones automáticas en masa?`)) {
        return;
    }

    isExecutingStandardizer.value = true;
    const payload = standardizerAutoFixes.value.map(f => ({
        field: f.field,
        valor_origen: f.valor_origen,
        valor_destino: f.valor_destino
    }));

    try {
        const resp = await axios.post(route('ensayos.standardization.execute'), {
            correcciones: payload
        });
        // Instant visual feedback
        router.reload({ preserveState: true, preserveScroll: true });
        // Perform a fresh rescan to calculate remaining orphans
        await openStandardizer();
    } catch (err) {
        console.error(err);
        alert('No se pudieron procesar las correcciones.');
    } finally {
        isExecutingStandardizer.value = false;
    }
};

const applySingleManualFix = async (fix, index) => {
    const target = manualFixSelections.value[`fix_${index}`];
    if (!target) {
        alert('Por favor, selecciona un valor válido del catálogo oficial destino.');
        return;
    }

    isExecutingStandardizer.value = true;
    try {
        await axios.post(route('ensayos.standardization.execute'), {
            correcciones: [{
                field: fix.field,
                valor_origen: fix.valor_origen,
                valor_destino: target
            }]
        });
        
        router.reload({ preserveState: true, preserveScroll: true });
        await openStandardizer();
    } catch (err) {
        console.error(err);
        alert('Error al ejecutar corrección manual.');
    } finally {
        isExecutingStandardizer.value = false;
    }
};

const exportToExcel = () => {
    const params = new URLSearchParams();
    if (search.value) params.append('search', search.value);
    if (filterAmbiente.value) params.append('ambiente', filterAmbiente.value);
    if (filterUserId.value) params.append('user_id', filterUserId.value);
    
    window.location.href = `${route('ensayos.export')}?${params.toString()}`;
};

const toggleSort = (key) => {
    if (sortBy.value === key) {
        // Cycle: ASC -> DESC -> ASC
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortBy.value = key;
        sortDirection.value = 'asc';
    }
    updateFilters(); // Trigger instant visit
};

// Simple column resize handler
const initResize = (e) => {
    const th = e.target.parentElement;
    const startX = e.pageX;
    const startWidth = th.offsetWidth;

    document.body.style.cursor = 'col-resize';
    document.body.style.userSelect = 'none';

    const onMouseMove = (mouseMoveEvent) => {
        const newWidth = Math.max(50, startWidth + (mouseMoveEvent.pageX - startX));
        th.style.width = `${newWidth}px`;
        th.style.minWidth = `${newWidth}px`; // Force the minimum constraint
    };

    const onMouseUp = () => {
        document.removeEventListener('mousemove', onMouseMove);
        document.removeEventListener('mouseup', onMouseUp);
        document.body.style.cursor = '';
        document.body.style.userSelect = '';
    };

    document.addEventListener('mousemove', onMouseMove);
    document.addEventListener('mouseup', onMouseUp);
};

const page = usePage();
const authUser = computed(() => page.props.auth.user);

// Dynamically limit which Ambientes this user can assign to imports
const allowedUploadAmbientes = computed(() => {
    if (authUser.value?.role === 'JEFE') {
        return props.catalogos?.AMBIENTE || [];
    }
    // If Líder, strictly limit dropdown choices to their assigned area set
    const userAmb = authUser.value?.ambiente;
    return Array.isArray(userAmb) ? userAmb : (userAmb ? [userAmb] : []);
});

// Automatically resolve optimal starting default
const defaultUploadAmbiente = computed(() => {
    return allowedUploadAmbientes.value.length > 0 ? allowedUploadAmbientes.value[0] : '';
});

// Excel upload form handling
const uploadForm = useForm({
    file: null,
    ambiente: defaultUploadAmbiente.value,
});

const fileInputRef = ref(null);
const isDragging = ref(false);

const handleFileUpload = () => {
    if (uploadForm.file) {
        uploadForm.post(route('ensayos.import'), {
            onSuccess: () => {
                uploadForm.reset();
                if (fileInputRef.value) fileInputRef.value.value = null;
            },
        });
    }
};

const onFileChange = (e) => {
    uploadForm.file = e.target.files[0];
};

const onDrop = (e) => {
    isDragging.value = false;
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
        uploadForm.file = e.dataTransfer.files[0];
    }
};

const formatFecha = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString;
    return new Intl.DateTimeFormat('es-CO', { dateStyle: 'short' }).format(date);
};
</script>

<template>
    <Head title="Registro de Ensayos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col items-start justify-between md:flex-row md:items-center">
                <h2 class="text-2xl font-black leading-tight text-slate-800 flex items-center gap-2">
                    <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Base de Datos de Ensayos
                </h2>
                <p class="text-sm text-slate-500 font-medium mt-1 md:mt-0">
                    Visualización unificada estilo Hoja de Cálculo.
                </p>
            </div>
        </template>

        <div class="py-6 bg-slate-100 min-h-screen">
            <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                
                <!-- Alerts -->
                <div v-if="$page.props.flash?.success" class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded shadow-sm font-medium flex items-center gap-2 animate-pulse-once">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ $page.props.flash.success }}
                </div>

                <div v-if="$page.props.errors?.file" class="p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded shadow-sm font-bold flex items-center gap-2 animate-pulse-once">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    {{ $page.props.errors.file }}
                </div>

                <!-- Compact Top Form section -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden p-5">
                    <form @submit.prevent="handleFileUpload" class="flex flex-col md:flex-row items-center gap-4">
                        
                        <!-- Upload area (condensed) -->
                        <div class="flex-1 flex items-center gap-4">
                            <!-- File picker logic hidden behind button or input -->
                            <div 
                                class="relative flex-1 border-2 border-dashed rounded-lg px-4 py-3 text-center transition-all cursor-pointer flex items-center justify-center gap-3"
                                :class="[
                                    isDragging ? 'border-emerald-500 bg-emerald-50' : 'border-slate-300 hover:border-emerald-400 hover:bg-slate-50',
                                    uploadForm.errors.file ? 'border-red-300 bg-red-50' : ''
                                ]"
                                @dragover.prevent="isDragging = true"
                                @dragleave.prevent="isDragging = false"
                                @drop.prevent="onDrop"
                                @click="fileInputRef.click()"
                            >
                                <input type="file" ref="fileInputRef" class="hidden" accept=".xlsx,.xls,.csv" @change="onFileChange" />
                                <svg class="w-6 h-6 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <span class="text-sm font-semibold text-slate-700" v-if="!uploadForm.file">
                                    Cargar Nuevo Excel (.xlsx)
                                </span>
                                <span class="text-sm font-bold text-emerald-700 truncate" v-else>
                                    {{ uploadForm.file.name }}
                                </span>
                            </div>

                            <!-- Amb Selector -->
                            <div class="w-64">
                                <select 
                                    v-model="uploadForm.ambiente"
                                    class="block w-full bg-slate-50 border border-slate-300 rounded-lg py-2 px-3 text-sm shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition font-medium"
                                    required
                                >
                                    <option value="" disabled>Seleccionar Ambiente...</option>
                                    <option 
                                        v-for="amb in allowedUploadAmbientes" 
                                        :key="amb" 
                                        :value="amb"
                                    >
                                        {{ amb }}
                                    </option>
                                    <!-- Safety Fallback just in case list empty -->
                                    <option v-if="!allowedUploadAmbientes?.length" value="SIN ESPECIFICAR">Sin Ambientes Permitidos</option>
                                </select>
                            </div>
                        </div>

                        <!-- Action btn -->
                        <button 
                            type="submit"
                            class="px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-bold rounded-lg shadow transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 h-full min-h-[42px]"
                            :disabled="!uploadForm.file || !uploadForm.ambiente || uploadForm.processing"
                        >
                            <span v-if="uploadForm.processing">Procesando...</span>
                            <template v-else>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                <span>Procesar</span>
                            </template>
                        </button>
                    </form>
                    <p v-if="uploadForm.errors.file" class="mt-2 text-xs text-red-600 font-semibold">❌ {{ uploadForm.errors.file }}</p>
                    <p v-if="uploadForm.errors.ambiente" class="mt-2 text-xs text-red-600 font-semibold">❌ Selecciona un ambiente.</p>
                    <!-- Mini Progress -->
                    <div v-if="uploadForm.progress" class="mt-3 w-full bg-gray-100 rounded-full h-1 overflow-hidden">
                        <div class="bg-emerald-500 h-1" :style="{ width: uploadForm.progress.percentage + '%' }"></div>
                    </div>
                </div>

                <!-- Table Toolbar & Search (Fully Responsive & Pixel-Perfect Heights) -->
                <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap items-center gap-4 justify-between mb-6">
                    
                    <!-- Left Segment: Inputs & Filters -->
                    <div class="flex flex-wrap items-center gap-3 w-full xl:w-auto">
                        <!-- Search Field (Compact 220px/md) -->
                        <div class="relative w-full md:w-56 flex-shrink-0">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input 
                                v-model="search"
                                type="text" 
                                placeholder="Buscar..." 
                                class="block w-full pl-9 pr-3 h-10 border border-slate-300 rounded-xl bg-slate-50 focus:bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-sm transition font-medium"
                            />
                        </div>

                        <!-- Dynamic Ambiente Filter -->
                        <div class="relative w-full sm:w-48 flex-shrink-0">
                            <select 
                                v-model="filterAmbiente"
                                class="block w-full pl-3 pr-8 h-10 border border-slate-300 rounded-xl bg-slate-50 focus:bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-sm transition font-bold text-slate-700 cursor-pointer"
                            >
                                <option value="">🌎 Todos los Ambientes</option>
                                <option v-for="amb in catalogos.AMBIENTE" :key="amb" :value="amb">
                                    {{ amb }}
                                </option>
                            </select>
                        </div>

                        <!-- Dynamic User Filter -->
                        <div class="relative w-full sm:w-48 flex-shrink-0">
                            <select 
                                v-model="filterUserId"
                                class="block w-full pl-3 pr-8 h-10 border border-slate-300 rounded-xl bg-slate-50 focus:bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-sm transition font-bold text-slate-700 cursor-pointer"
                            >
                                <option value="">👤 Todos los Usuarios</option>
                                <option v-for="usr in users_list" :key="usr.id" :value="usr.id">
                                    {{ usr.name }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Right Segment: Action Buttons & Options (Exact Height Unification) -->
                    <div class="flex flex-wrap items-center gap-3 w-full xl:w-auto">
                        
                        <!-- 📈 Analytics Dashboard Link -->
                        <Link 
                            :href="route('ensayos.dashboard')"
                            class="inline-flex items-center justify-center px-4 h-10 border border-blue-200 bg-white hover:bg-blue-50/50 shadow-md text-xs md:text-sm font-black rounded-xl text-blue-700 hover:text-blue-800 transition-all duration-200 active:scale-95 gap-2 flex-grow sm:flex-grow-0"
                        >
                            <svg class="h-5 w-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            <span class="whitespace-nowrap">Estadísticas</span>
                        </Link>

                        <!-- 🧠 Smart Data Standardizer Wizard Trigger -->
                        <button 
                            v-if="$page.props.auth.user.role === 'JEFE'"
                            @click="openStandardizer"
                            type="button"
                            class="inline-flex items-center justify-center px-4 h-10 border border-amber-200 bg-white hover:bg-amber-50/50 shadow-md text-xs md:text-sm font-black rounded-xl text-amber-700 hover:text-amber-800 transition-all duration-200 active:scale-95 gap-2 flex-grow sm:flex-grow-0"
                        >
                            <svg class="h-5 w-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                            <span class="whitespace-nowrap">Estandarizar</span>
                        </button>

                        <!-- Dynamic Excel Exporter -->
                        <button 
                            @click="exportToExcel"
                            type="button"
                            class="inline-flex items-center justify-center px-4 h-10 border border-emerald-600 bg-emerald-700 hover:bg-emerald-800 shadow-md text-xs md:text-sm font-black rounded-xl text-white transition-all duration-200 active:scale-95 gap-2 flex-grow sm:flex-grow-0"
                            title="Descargar esta vista actual a Excel"
                        >
                            <svg class="h-5 w-5 text-emerald-100 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span class="whitespace-nowrap">Exportar Excel</span>
                        </button>

                        <!-- Master Data Quick Viewer -->
                        <button 
                            v-if="$page.props.auth.user.role === 'JEFE'"
                            @click="showCatalogoModal = true"
                            type="button"
                            class="inline-flex items-center justify-center px-4 h-10 border border-slate-300 bg-white hover:bg-slate-50 shadow-md text-xs md:text-sm font-extrabold rounded-xl text-slate-700 hover:text-slate-900 transition-all duration-200 active:scale-95 gap-2 flex-grow sm:flex-grow-0"
                        >
                            <svg class="h-5 w-5 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                            <span class="whitespace-nowrap">Ver Tablas Maestras</span>
                        </button>
                        
                        <!-- Rows per Page -->
                        <div class="flex items-center gap-2 whitespace-nowrap text-xs md:text-sm font-extrabold text-slate-500 ml-auto xl:ml-0">
                            <span>Mostrar</span>
                            <select 
                                v-model="perPage"
                                class="bg-white border border-slate-300 text-slate-700 h-10 pl-3 pr-8 rounded-xl text-xs md:text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-sm transition cursor-pointer font-black"
                            >
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <span>filas</span>
                        </div>

                        <!-- Row Count Badge (Pixel-perfect telemetry) -->
                        <div class="text-[11px] md:text-xs font-black bg-slate-100 border border-slate-200 text-slate-600 px-3 h-10 rounded-xl flex items-center gap-2 select-none">
                            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                            <span class="whitespace-nowrap uppercase tracking-wider">{{ ensayos.total }} Filas</span>
                        </div>
                    </div>
                </div>

                <!-- MAIN EXCEL-LIKE TABLE -->
                <div class="bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden">
                    <div class="overflow-auto max-h-[calc(100vh-320px)] scroll-custom">
                        <table class="table-excel w-full border-collapse border-spacing-0">
                            <thead>
                                <tr>
                                    <th class="index-col">#</th>
                                    <th 
                                        v-for="col in columns" 
                                        :key="col.key" 
                                        :class="[col.class, 'relative group pr-4 select-none']"
                                        :style="col.style"
                                    >
                                        <div class="flex items-center space-x-1 cursor-pointer" @click="toggleSort(col.key)">
                                            <span class="block truncate flex-grow">{{ col.label }}</span>
                                            <!-- Sort Icon -->
                                            <span class="text-slate-400 w-3 h-3 inline-flex items-center">
                                                <template v-if="sortBy === col.key">
                                                    <svg v-if="sortDirection === 'asc'" class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                                                    <svg v-else class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                                </template>
                                                <template v-else>
                                                    <!-- Subtle idle indicator on hover -->
                                                    <svg class="w-3 h-3 opacity-0 group-hover:opacity-40" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>
                                                </template>
                                            </span>
                                        </div>
                                        
                                        <!-- Drag Handle for resize (stop prop on mousedown prevented sort triggers) -->
                                        <div 
                                            class="resizer-bar opacity-0 group-hover:opacity-100"
                                            @mousedown.stop.prevent="initResize"
                                        ></div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="ensayos.data.length === 0">
                                    <td :colspan="columns.length + 1" class="text-center py-12 text-slate-400 italic">
                                        No se encontraron registros en la base de datos.
                                    </td>
                                </tr>
                                <tr v-for="(ensayo, rowIndex) in ensayos.data" :key="ensayo.id">
                                    <td class="index-col-body">
                                        {{ (ensayos.current_page - 1) * ensayos.per_page + rowIndex + 1 }}
                                    </td>
                                    <td 
                                        v-for="col in columns" 
                                        :key="col.key" 
                                        :class="[
                                            col.class, 
                                            col.isDate ? 'font-mono text-center' : '',
                                            (savingCell.rowId === ensayo.id && savingCell.field === col.key) ? 'bg-emerald-50 transition-all duration-500' : '',
                                            'relative group cursor-text hover:bg-slate-50 min-h-[36px]'
                                        ]"
                                        :style="col.style"
                                        class="cell-content"
                                        @dblclick="!col.isStatic && startEdit(ensayo, col)"
                                        :title="col.isStatic ? 'No editable' : 'Doble clic para editar'"
                                    >
                                        <!-- Saving Indicator -->
                                        <div v-if="savingCell.rowId === ensayo.id && savingCell.field === col.key" class="absolute inset-0 flex items-center justify-center bg-emerald-50 opacity-70 z-20">
                                            <svg class="animate-spin h-4 w-4 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </div>

                                        <!-- Active Input -->
                                        <template v-if="editingCell.rowId === ensayo.id && editingCell.field === col.key">
                                            <input 
                                                :id="`input-${ensayo.id}-${col.key}`"
                                                v-model="editValue"
                                                @keydown.enter.prevent="$event.target.blur()"
                                                @keydown.esc.prevent="cancelEdit"
                                                @blur="saveEdit(ensayo, col)"
                                                :list="catalogMap[col.key] ? 'datalist-' + catalogMap[col.key] : null"
                                                class="absolute inset-0 w-full h-full border-2 border-emerald-600 bg-white shadow-md z-30 px-2 text-[0.75rem] focus:ring-0 outline-none rounded-none"
                                                placeholder="Escribe o selecciona..."
                                            />
                                            
                                            <!-- Floating helper message for autocomplete fields -->
                                            <div v-if="catalogMap[col.key]" class="absolute bottom-full left-0 mb-1 bg-emerald-700 text-white text-[10px] px-2 py-1 rounded shadow z-40 whitespace-nowrap">
                                                Sugerencias disponibles. Si escribes algo nuevo, se agregará al catálogo.
                                            </div>
                                        </template>

                                        <!-- Static Display -->
                                        <template v-else>
                                            <!-- Little edit icon on hover -->
                                            <div v-if="!col.isStatic" class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 text-emerald-600 transition-opacity pointer-events-none bg-white/80 p-0.5 rounded">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path></svg>
                                            </div>
                                            
                                            <div class="py-0.5 break-words min-h-[1.25rem]">
                                                <template v-if="col.isDate">
                                                    {{ formatFecha(ensayo[col.key]) }}
                                                </template>
                                                <template v-else-if="col.key === 'nombre_ensayo'">
                                                    <div class="flex items-center justify-between group/name w-full pr-1">
                                                        <span class="font-bold text-slate-800 pr-1 truncate">{{ ensayo.nombre_ensayo }}</span>
                                                        <button 
                                                            @click.stop="openAdjuntos(ensayo)"
                                                            type="button"
                                                            class="flex-shrink-0 inline-flex items-center justify-center p-1 rounded-md bg-slate-100 hover:bg-emerald-100 text-slate-500 hover:text-emerald-700 shadow-sm transition transform active:scale-95 pointer-events-auto"
                                                            :class="ensayo.adjuntos_count > 0 ? 'bg-emerald-50 border border-emerald-200 !text-emerald-700 font-bold opacity-100' : 'opacity-25 group-hover/name:opacity-100'"
                                                            title="Gestionar mapas y archivos adjuntos"
                                                        >
                                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                            <span v-if="ensayo.adjuntos_count > 0" class="ml-0.5 text-[9px] leading-none font-black">{{ ensayo.adjuntos_count }}</span>
                                                        </button>
                                                    </div>
                                                </template>
                                                <template v-else-if="col.key === 'user'">
                                                    <span class="text-emerald-700 font-bold bg-emerald-50 px-1 rounded text-[10px] uppercase tracking-tight">
                                                        {{ ensayo.user?.name ?? 'Sistema' }}
                                                    </span>
                                                </template>
                                                <template v-else>
                                                    {{ ensayo[col.key] ?? '' }}
                                                </template>
                                            </div>
                                        </template>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Excel Style Pagination Footer -->
                    <div class="bg-[#f3f4f6] px-6 py-3 border-t border-slate-200 flex items-center justify-between text-sm">
                        <div class="text-slate-600">
                            Viendo {{ ensayos.from || 0 }} - {{ ensayos.to || 0 }} de {{ ensayos.total }}
                        </div>
                        <div class="flex items-center space-x-1">
                            <template v-for="(link, idx) in ensayos.links" :key="idx">
                                <Link 
                                    v-if="link.url" 
                                    :href="link.url" 
                                    v-html="link.label"
                                    class="px-3 py-1 border border-slate-300 rounded-md transition bg-white hover:bg-slate-50 text-slate-700"
                                    :class="{'!bg-emerald-600 !text-white border-emerald-600 font-bold': link.active}"
                                />
                                <span 
                                    v-else 
                                    v-html="link.label" 
                                    class="px-3 py-1 border border-transparent text-slate-400 select-none"
                                />
                            </template>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Global Native Autocomplete Catalogs -->
        <datalist v-for="(values, category) in catalogos" :key="category" :id="'datalist-' + category">
            <option v-for="v in values" :key="v" :value="v"></option>
        </datalist>

        <!-- 🧠 Modal del Motor de Estandarización Inteligente (Premium) -->
        <div v-if="showStandardizerModal" class="fixed inset-0 z-[9999] overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Glass Backdrop -->
                <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity" @click="!isExecutingStandardizer && (showStandardizerModal = false)"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-slate-50 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full border border-slate-200 animate-fade-in">
                    <!-- Header Banner -->
                    <div class="bg-gradient-to-r from-amber-600 to-orange-600 px-6 py-5 flex justify-between items-center text-white shadow-md">
                        <div class="flex items-center gap-3">
                            <div class="bg-white/20 p-2 rounded-xl shadow-inner animate-pulse-slow">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-lg leading-6 font-black tracking-wide">Estandarizador Inteligente de Datos</h3>
                                <p class="text-xs font-semibold text-amber-100">Corrige errores ortográficos, acentos y diferencias tipográficas en masa.</p>
                            </div>
                        </div>
                        <button @click="!isExecutingStandardizer && (showStandardizerModal = false)" class="rounded-full p-1.5 hover:bg-white/20 transition">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="max-h-[75vh] overflow-y-auto p-6 space-y-6 bg-slate-50">
                        <!-- Loading / Scanning State -->
                        <div v-if="isScanningStandardizer" class="py-16 flex flex-col items-center justify-center text-center space-y-4">
                            <svg class="animate-spin h-12 w-12 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <div>
                                <p class="text-base font-black text-slate-700">Escaneando tu base de datos...</p>
                                <p class="text-xs text-slate-500 font-medium">Buscando coincidencias de tildes, espacios y mayúsculas con los diccionarios.</p>
                            </div>
                        </div>

                        <template v-else>
                            <!-- Empty State: All clean -->
                            <div v-if="standardizerAutoFixes.length === 0 && standardizerManualFixes.length === 0" class="py-12 flex flex-col items-center justify-center text-center space-y-3">
                                <div class="text-6xl animate-bounce-slow">🎉</div>
                                <h4 class="text-lg font-black text-slate-800">¡Base de Datos Totalmente Limpia!</h4>
                                <p class="text-sm font-medium text-slate-500 max-w-md">No encontramos ninguna discrepancia de ortografía ni términos huérfanos. Tus datos cumplen perfectamente con los Diccionarios Maestros.</p>
                            </div>

                            <!-- Segment A: Auto-Fixes (The Super Button) -->
                            <div v-if="standardizerAutoFixes.length > 0" class="bg-white rounded-2xl p-5 border border-emerald-100 shadow-sm flex flex-col space-y-4">
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                                    <div class="flex gap-3">
                                        <span class="flex-shrink-0 w-10 h-10 bg-emerald-50 border border-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center text-lg shadow-inner">⚡</span>
                                        <div>
                                            <h4 class="font-black text-emerald-800 leading-tight">Correcciones Automáticas Obvias</h4>
                                            <p class="text-xs text-emerald-600 font-semibold">Encontrado exacto en el diccionario (difiere solo en acentos o letras minúsculas).</p>
                                        </div>
                                    </div>
                                    <button 
                                        @click="applyAllAutoFixes"
                                        :disabled="isExecutingStandardizer"
                                        class="inline-flex items-center px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white font-black text-sm rounded-xl shadow-lg shadow-emerald-100 hover:shadow-xl transition duration-200 transform active:scale-[0.98] gap-2"
                                    >
                                        <svg v-if="isExecutingStandardizer" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        ⚡ Aplicar {{ standardizerAutoFixes.length }} Correcciones
                                    </button>
                                </div>

                                <!-- List of obvious matches -->
                                <div class="border border-slate-100 rounded-xl max-h-48 overflow-y-auto bg-slate-50">
                                    <table class="w-full text-left border-collapse text-xs">
                                        <thead>
                                            <tr class="bg-slate-200/50 sticky top-0 font-black text-slate-500 border-b border-slate-200">
                                                <th class="px-3 py-2">Columna</th>
                                                <th class="px-3 py-2">Escrito en Ensayos</th>
                                                <th class="px-3 py-2 text-center">➡️</th>
                                                <th class="px-3 py-2">Valor Oficial Estándar</th>
                                                <th class="px-3 py-2 text-right">Registros</th>
                                            </tr>
                                        </thead>
                                        <tbody class="font-medium text-slate-700">
                                            <tr v-for="(f, idx) in standardizerAutoFixes" :key="idx" class="border-b border-slate-100 last:border-none hover:bg-emerald-50/30 transition-colors">
                                                <td class="px-3 py-2"><span class="font-bold text-[10px] bg-slate-200 text-slate-700 px-1.5 py-0.5 rounded uppercase">{{ f.field }}</span></td>
                                                <td class="px-3 py-2 text-red-600 line-through">"{{ f.valor_origen }}"</td>
                                                <td class="px-3 py-2 text-center font-bold text-emerald-600">➡️</td>
                                                <td class="px-3 py-2 font-black text-emerald-700 bg-emerald-50/40">"{{ f.valor_destino }}"</td>
                                                <td class="px-3 py-2 text-right font-bold text-slate-500">{{ f.total }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Segment B: Manual Orphans (System is not 100% sure) -->
                            <div v-if="standardizerManualFixes.length > 0" class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm space-y-4">
                                <div class="flex gap-3">
                                    <span class="flex-shrink-0 w-10 h-10 bg-amber-50 border border-amber-100 text-amber-600 rounded-xl flex items-center justify-center text-lg shadow-inner">🔍</span>
                                    <div>
                                        <h4 class="font-black text-slate-800 leading-tight">Casos Huérfanos o Dudosos</h4>
                                        <p class="text-xs text-slate-500 font-semibold">Términos no reconocidos por el sistema. Mapea manualmente a un destino del catálogo.</p>
                                    </div>
                                </div>

                                <!-- Manual Cards List -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div 
                                        v-for="(fix, idx) in standardizerManualFixes" 
                                        :key="idx" 
                                        class="bg-slate-50 border border-slate-200 rounded-xl p-4 flex flex-col space-y-3 relative transition hover:border-amber-300"
                                    >
                                        <div class="flex justify-between items-center">
                                            <span class="text-[9px] font-black bg-amber-100 text-amber-800 border border-amber-200 px-2 py-0.5 rounded uppercase tracking-widest">{{ fix.categoria }} / {{ fix.field }}</span>
                                            <span class="text-[10px] font-bold text-slate-400">{{ fix.total }} filas</span>
                                        </div>
                                        
                                        <div>
                                            <label class="text-[9px] font-black uppercase tracking-wider text-slate-400 block mb-1">Valor Actual No Estándar:</label>
                                            <div class="text-sm font-black text-red-600 bg-red-50 border border-red-100 rounded-lg px-2.5 py-1.5 select-all">
                                                "{{ fix.valor_origen }}"
                                            </div>
                                        </div>

                                        <div class="space-y-1.5">
                                            <label class="text-[9px] font-black uppercase tracking-wider text-slate-400 block">Estandarizar hacia:</label>
                                            <div class="flex gap-2">
                                                <select 
                                                    v-model="manualFixSelections[`fix_${idx}`]"
                                                    class="flex-1 min-w-0 bg-white border border-slate-300 rounded-lg px-2 py-1.5 text-xs font-bold text-slate-800 focus:ring-1 focus:ring-amber-500 outline-none cursor-pointer shadow-sm"
                                                >
                                                    <option value="" disabled>-- Elegir oficial --</option>
                                                    <option v-for="opt in fix.opciones" :key="opt.id" :value="opt.valor">
                                                        ✔️ {{ opt.valor }}
                                                    </option>
                                                </select>
                                                <button 
                                                    @click="applySingleManualFix(fix, idx)"
                                                    :disabled="isExecutingStandardizer || !manualFixSelections[`fix_${idx}`]"
                                                    class="bg-slate-800 hover:bg-slate-900 disabled:opacity-40 text-white px-3 rounded-lg text-[11px] font-black shadow-sm transition duration-200 flex items-center justify-center whitespace-nowrap"
                                                    title="Corregir este registro"
                                                >
                                                    Corregir
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🛡️ SMART DATA QUALITY SHIELD MODAL (Real-time Gate) -->
        <div v-if="smartConfirm.isOpen" class="fixed inset-0 z-[10000] overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                <!-- Dark Glass Overlay with Blur -->
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity" @click="closeSmartConfirm"></div>

                <!-- Modal Container -->
                <div class="relative inline-block bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full border border-slate-200 ring-1 ring-black ring-opacity-5 animate-fade-in">
                    
                    <!-- Banner Header Contextual -->
                    <div :class="[
                        'px-6 py-5 flex items-center gap-4 border-b',
                        smartConfirm.dialogType === 'FUZZY_MATCH' ? 'bg-amber-50 border-amber-100' : 'bg-slate-50 border-slate-100'
                    ]">
                        <div :class="[
                            'w-12 h-12 rounded-2xl flex items-center justify-center shadow-inner shrink-0',
                            smartConfirm.dialogType === 'FUZZY_MATCH' ? 'bg-amber-100 text-amber-600' : 'bg-slate-200 text-slate-600'
                        ]">
                            <!-- Alert / Question Icon -->
                            <svg v-if="smartConfirm.dialogType === 'FUZZY_MATCH'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-slate-900 leading-tight">Control de Calidad de Datos</h3>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-0.5">Catálogo: {{ smartConfirm.categoria }}</p>
                        </div>
                    </div>

                    <!-- Body Content -->
                    <div class="p-6">
                        <p class="text-sm text-slate-600 font-medium leading-relaxed">
                            Escribiste <strong class="text-slate-900 bg-slate-100 px-2 py-0.5 rounded-md font-black border border-slate-200 text-[0.95rem]">"{{ smartConfirm.valorPropuesto }}"</strong>, pero no se encuentra exactamente en el Diccionario Maestro.
                        </p>

                        <!-- CASE A: SUGGESTED VALUE REVEAL (Fuzzy match) -->
                        <div v-if="smartConfirm.dialogType === 'FUZZY_MATCH'" class="mt-4 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-start gap-3 shadow-sm">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-extrabold text-emerald-900">¡Encontramos una opción similar!</p>
                                <p class="text-xs text-emerald-700 font-medium mt-0.5">El término oficial registrado es:</p>
                                <div class="mt-2 inline-block text-lg font-black text-emerald-800 bg-emerald-100/80 px-3 py-1 rounded-xl border border-emerald-200">
                                    "{{ smartConfirm.valorSugerido }}"
                                </div>
                                <p class="text-[11px] text-emerald-600/90 font-bold mt-2 italic">Recomendamos usar el oficial para mantener tu base de datos limpia y uniforme.</p>
                            </div>
                        </div>

                        <!-- CASE B: NEW TERM ONLY -->
                        <div v-else class="mt-4 p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center shrink-0 mt-0.5 font-bold">?</div>
                            <div>
                                <p class="text-sm font-extrabold text-slate-800">Este es un término totalmente nuevo.</p>
                                <p class="text-xs text-slate-500 font-medium mt-1">No encontramos ninguna coincidencia o palabra parecida en el Diccionario.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Footer Grid -->
                    <div class="px-6 py-5 bg-slate-50 border-t border-slate-100 flex flex-col gap-3">
                        
                        <!-- OPTION 1: USE SUGGESTED (Primary Recommendation if exists) -->
                        <button 
                            v-if="smartConfirm.dialogType === 'FUZZY_MATCH'"
                            @click="executeSmartDecision('USE_SUGGESTED')"
                            type="button" 
                            class="w-full inline-flex items-center justify-center px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-black rounded-2xl shadow-lg hover:shadow-emerald-600/20 transition duration-200 active:scale-[0.98] gap-2"
                        >
                            <svg class="w-5 h-5 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Usar el término oficial "{{ smartConfirm.valorSugerido }}"
                        </button>

                        <!-- OPTION 2: FORCE NEW REGISTRATION -->
                        <button 
                            @click="executeSmartDecision('CREATE_NEW')"
                            type="button" 
                            :class="[
                                'w-full inline-flex items-center justify-center px-5 py-3 text-sm font-bold rounded-2xl border transition duration-200 active:scale-[0.98] gap-2',
                                smartConfirm.dialogType === 'FUZZY_MATCH' 
                                    ? 'bg-white border-slate-300 text-slate-700 hover:bg-slate-50' 
                                    : 'bg-slate-800 hover:bg-slate-900 border-slate-900 text-white shadow-lg'
                            ]"
                        >
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Registrar "{{ smartConfirm.valorPropuesto }}" como nuevo oficial
                        </button>

                        <!-- OPTION 3: ABORT / CANCEL -->
                        <button 
                            @click="closeSmartConfirm"
                            type="button" 
                            class="w-full inline-flex items-center justify-center px-5 py-2 bg-transparent border-transparent text-slate-500 hover:text-slate-700 text-sm font-extrabold rounded-xl transition duration-200"
                        >
                            Cancelar y descartar cambio
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Tablas Maestras -->
        <div v-if="showCatalogoModal" class="fixed inset-0 z-[9999] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" @click="showCatalogoModal = false" aria-hidden="true"></div>

                <!-- Modal content -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-slate-200">
                    <div class="bg-emerald-700 px-6 py-4 flex justify-between items-center text-white">
                        <div class="flex items-center gap-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                            <h3 class="text-lg leading-6 font-bold" id="modal-title">Diccionario de Tablas Maestras</h3>
                        </div>
                        <button @click="showCatalogoModal = false" class="text-white hover:text-emerald-100 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    
                    <div class="bg-slate-50 px-6 py-8 max-h-[70vh] overflow-y-auto">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div v-for="(values, category) in catalogos" :key="category" class="bg-white rounded-xl shadow-sm border border-slate-200 flex flex-col overflow-hidden">
                                <div class="bg-slate-100 px-4 py-3 border-b border-slate-200 font-bold text-slate-700 uppercase text-sm tracking-wide flex justify-between">
                                    {{ category }}
                                    <span class="bg-emerald-100 text-emerald-800 text-[10px] px-2 py-0.5 rounded-full">{{ values.length }}</span>
                                </div>
                                <div class="p-3 flex-grow overflow-y-auto max-h-64 bg-white">
                                    <ul class="space-y-1">
                                        <li v-for="val in values" :key="val" class="text-sm text-slate-600 py-1 px-2 rounded hover:bg-slate-50 border-b border-slate-50 last:border-0 break-words">
                                            {{ val }}
                                        </li>
                                    </ul>
                                    <div v-if="values.length === 0" class="text-xs italic text-slate-400 text-center py-4">
                                        Vacío
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-6 py-4 flex justify-end">
                        <button @click="showCatalogoModal = false" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 shadow focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- SLIDE-OVER DRAWER: MÓDULO DE MAPAS Y ADJUNTOS           -->
        <!-- ======================================================= -->
        <div 
            v-if="drawerOpen" 
            class="fixed inset-0 overflow-hidden z-50" 
            aria-labelledby="slide-over-title" 
            role="dialog" 
            aria-modal="true"
        >
            <div class="absolute inset-0 overflow-hidden">
                <div 
                    @click="closeDrawer"
                    class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity duration-500 animate-fade-in"
                ></div>

                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <div class="pointer-events-auto w-screen max-w-md transform transition-transform duration-500 ease-in-out translate-x-0 shadow-2xl">
                        <div class="flex h-full flex-col overflow-y-scroll bg-white">
                            
                            <!-- Header -->
                            <div class="bg-emerald-800 px-6 py-6 sm:px-6 shadow-lg relative">
                                <div class="flex items-start justify-between">
                                    <h2 class="text-lg font-black text-white flex items-center gap-2" id="slide-over-title">
                                        <span class="text-xl">📎</span> Adjuntos del Ensayo
                                    </h2>
                                    <div class="ml-3 flex h-7 items-center">
                                        <button 
                                            @click="closeDrawer" 
                                            type="button" 
                                            class="rounded-md bg-emerald-900 text-emerald-200 hover:text-white focus:outline-none p-1.5 transition transform active:scale-95"
                                        >
                                            <span class="sr-only">Cerrar</span>
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <p class="text-sm text-emerald-100 font-bold line-clamp-2 bg-emerald-900/30 px-3 py-2 rounded-xl">
                                        📁 {{ activeEnsayo?.nombre_ensayo }}
                                    </p>
                                </div>
                                <div class="absolute -bottom-4 -right-4 bg-emerald-500/30 w-24 h-24 rounded-full blur-2xl"></div>
                            </div>

                            <!-- Content Body -->
                            <div class="relative flex-1 px-6 py-6 bg-slate-50">
                                
                                <!-- Upload Box Selector -->
                                <div class="bg-white border-2 border-dashed border-slate-300 rounded-2xl p-6 text-center relative transition hover:border-emerald-500 hover:bg-emerald-50/10 group mb-6 shadow-sm">
                                    <input 
                                        type="file" 
                                        ref="adjuntoInputRef"
                                        @change="handleAdjuntoUpload" 
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" 
                                        accept=".pdf,.jpg,.jpeg,.png,.xlsx,.xls,.csv,.doc,.docx"
                                        :disabled="uploadingFile"
                                    />
                                    <div class="flex flex-col items-center z-0">
                                        <div 
                                            class="w-12 h-12 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-xl border-2 border-white shadow-md mb-3 group-hover:scale-110 transition duration-200"
                                            :class="uploadingFile ? 'animate-bounce bg-emerald-500 text-white' : ''"
                                        >
                                            {{ uploadingFile ? '⌛' : '☁️' }}
                                        </div>
                                        <h3 class="text-sm font-extrabold text-slate-700 group-hover:text-emerald-700">
                                            {{ uploadingFile ? 'Subiendo archivo físico...' : 'Arrastra o presiona para subir' }}
                                        </h3>
                                        <p class="text-xs text-slate-400 mt-1 font-semibold uppercase tracking-wider">PDF, Imágenes, Croquis, Mapas (Máx. 15MB)</p>
                                    </div>
                                </div>

                                <!-- Current Files Section Title -->
                                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3 border-b border-slate-200 pb-1.5 flex justify-between items-center">
                                    <span>Documentación Vinculada</span>
                                    <span class="text-[10px] bg-slate-200 text-slate-600 px-2 py-0.5 rounded-full font-black">{{ currentAdjuntos.length }}</span>
                                </h3>

                                <!-- Files Loading Skeleton -->
                                <div v-if="loadingAdjuntos" class="space-y-3">
                                    <div v-for="n in 2" :key="n" class="bg-slate-200 animate-pulse h-20 rounded-2xl w-full"></div>
                                </div>

                                <!-- Files Loop -->
                                <div v-else class="space-y-3">
                                    <div 
                                        v-for="adj in currentAdjuntos" 
                                        :key="adj.id" 
                                        class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex items-center gap-3 group/item hover:shadow-md hover:border-emerald-200 transition animate-fade-in"
                                    >
                                        <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-600 flex items-center justify-center text-xl border border-slate-100 group-hover/item:scale-105 group-hover/item:bg-emerald-50 transition-transform">
                                            {{ getFileIcon(adj.mime_type) }}
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-xs font-black text-slate-800 truncate group-hover/item:text-emerald-800" :title="adj.nombre_archivo">
                                                {{ adj.nombre_archivo }}
                                            </h4>
                                            <div class="flex items-center gap-2 mt-0.5 text-[10px] font-bold text-slate-400">
                                                <span class="bg-slate-100 px-1.5 py-0.5 rounded text-slate-500 font-black tracking-tighter">{{ formatBytes(adj.size) }}</span>
                                                <span>•</span>
                                                <span class="truncate">👤 {{ adj.user?.name ?? 'Sistema' }}</span>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-1">
                                            <a 
                                                :href="route('adjuntos.download', adj.id)" 
                                                class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition shadow-inner border border-transparent hover:border-emerald-100"
                                                title="Descargar archivo"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            </a>
                                            <button 
                                                v-if="$page.props.auth.user.role === 'JEFE' || adj.user_id === $page.props.auth.user.id"
                                                @click="deleteAdjunto(adj)"
                                                type="button"
                                                class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition shadow-inner border border-transparent hover:border-rose-100"
                                                title="Eliminar permanente"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Empty State -->
                                    <div v-if="currentAdjuntos.length === 0" class="py-12 text-center bg-slate-100 border border-slate-200 border-dashed rounded-2xl mt-2">
                                        <div class="text-4xl mb-2">🏜️</div>
                                        <h4 class="text-xs font-black text-slate-600">Sin archivos asociados</h4>
                                        <p class="text-[11px] text-slate-400 font-medium max-w-[200px] mx-auto mt-1 leading-snug">Aún no se han cargado planos o bitácoras de campo.</p>
                                    </div>
                                </div>

                            </div>
                            
                            <!-- Footer Status -->
                            <div class="bg-slate-100 border-t border-slate-200 px-6 py-4 flex justify-between items-center">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                    Almacenamiento Protegido
                                </span>
                                <button 
                                    @click="closeDrawer"
                                    type="button" 
                                    class="px-4 py-1.5 bg-slate-800 text-white text-xs font-black tracking-wide rounded-xl shadow transition hover:bg-slate-700"
                                >
                                    Cerrar
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Custom Excel Stylings for Table */
.table-excel {
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    font-size: 0.8125rem; /* 13px */
}

.table-excel thead th {
    position: sticky;
    top: 0;
    background: #f8fafc;
    color: #475569;
    font-weight: 600;
    padding: 8px 12px;
    text-align: left;
    border-right: 1px solid #e2e8f0;
    border-bottom: 2px solid #cbd5e1;
    white-space: nowrap;
    z-index: 20;
    user-select: none;
}

.table-excel tbody td {
    border-right: 1px solid #f1f5f9;
    border-bottom: 1px solid #f1f5f9;
    padding: 8px 12px;
    color: #334155;
    white-space: normal; /* Wrap content to show it fully */
    word-break: break-word;
    vertical-align: top; /* Top align for wrapped text looks cleaner */
    font-size: 0.75rem;
    line-height: 1.3;
}

/* Resizer handle styling */
.resizer-bar {
    position: absolute;
    top: 0;
    right: 0;
    width: 4px;
    height: 100%;
    cursor: col-resize;
    background-color: #10b981; /* Tailwind emerald-500 */
    z-index: 30;
    transition: opacity 0.1s;
}
.resizer-bar:hover {
    width: 6px;
}

.table-excel tbody tr:nth-child(even) {
    background-color: #fafafa;
}

.table-excel tbody tr:hover {
    background-color: #f0fdf4; /* Very light green */
}

.table-excel tbody tr:hover td {
    color: #064e3b;
}

/* Row Index Styling */
.index-col {
    min-width: 50px;
    background: #f1f5f9 !important;
    text-align: center !important;
    color: #94a3b8 !important;
    font-size: 0.75rem;
    z-index: 30 !important;
}

.index-col-body {
    background: #f8fafc;
    text-align: center;
    font-weight: 500;
    color: #64748b;
    border-right: 1px solid #cbd5e1 !important;
    position: sticky;
    left: 0;
    z-index: 10;
    vertical-align: middle !important; /* Keep index vertically centered */
}

/* Sticky First Column Styling (Nombre_Ensayo) */
.sticky-col {
    position: sticky;
    left: 50px; /* After index col */
    background: #ffffff;
    z-index: 10;
    border-right: 2px solid #cbd5e1 !important;
    box-shadow: 2px 0 5px rgba(0,0,0,0.02);
}

/* Correct specificity for sticky header overlapping */
thead th.sticky-col {
    background: #f8fafc !important;
    z-index: 40 !important;
}

tbody tr:hover td.sticky-col {
    background-color: #f0fdf4 !important;
}

.cell-content {
    /* Remove max-width restrictions that force truncation */
}

/* Custom scrollbar to make it look smoother */
.scroll-custom::-webkit-scrollbar {
    width: 10px;
    height: 10px;
}
.scroll-custom::-webkit-scrollbar-track {
    background: #f1f5f9;
}
.scroll-custom::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 5px;
}
.scroll-custom::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

@keyframes pulse-once {
    0% { transform: scale(1); opacity: 0.8; }
    50% { transform: scale(1.01); opacity: 1; }
    100% { transform: scale(1); opacity: 1; }
}
.animate-pulse-once {
    animation: pulse-once 0.3s ease-out;
}
</style>
