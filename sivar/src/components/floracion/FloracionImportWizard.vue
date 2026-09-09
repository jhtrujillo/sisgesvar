<template>
  <div v-if="isOpen" class="fixed inset-0 flex items-center justify-center bg-slate-900/60 z-[100] transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl mx-4 overflow-hidden border border-slate-100 flex flex-col max-h-[90vh]">
      <!-- Header -->
      <div class="flex justify-between items-center border-b border-slate-100 p-5 bg-slate-50 shrink-0">
        <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wide flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-cenicana" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
          </svg>
          Importar Floración desde Excel
        </h4>
        <button @click="close" class="text-slate-400 hover:text-slate-600 text-2xl font-bold">&times;</button>
      </div>

      <!-- Stepper Header -->
      <div class="bg-white border-b border-slate-100 shrink-0">
        <div class="flex items-center px-6 py-4 justify-between">
          <div v-for="s in [1, 2, 3, 4]" :key="s" class="flex items-center gap-2" :class="{ 'opacity-50': step < s }">
            <div :class="[
              'w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold text-white',
              step === s ? 'bg-cenicana' : (step > s ? 'bg-emerald-500' : 'bg-slate-300')
            ]">
              <span v-if="step > s">✓</span>
              <span v-else>{{ s }}</span>
            </div>
            <span class="text-xs font-bold text-slate-600 uppercase" :class="{'text-cenicana': step === s}">
              {{ stepTitles[s-1] }}
            </span>
            <div v-if="s < 4" class="w-8 h-[1px] bg-slate-200 mx-2 hidden sm:block"></div>
          </div>
        </div>
      </div>

      <!-- Content -->
      <div class="p-6 overflow-y-auto flex-1 bg-slate-50/50">
        
        <!-- STEP 1: Select Vivero -->
        <div v-show="step === 1" class="space-y-6">
          <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
            <h5 class="text-sm font-bold text-blue-800 mb-2">Paso 1: Identificación del Vivero</h5>
            <p class="text-xs text-blue-600 leading-relaxed">
              Seleccione el vivero al cual pertenecen las flores del archivo Excel. El sistema utilizará este vivero para cruzar y validar que las variedades y parcelas coincidan.
            </p>
          </div>
          
          <div class="relative">
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Vivero de Origen <span class="text-red-500">*</span></label>
            <input
              v-model="searchQuery"
              @input="onSearchInput"
              @focus="onFocusInput"
              type="text"
              placeholder="Buscar por ID, nombre o hacienda..."
              class="w-full bg-white border border-slate-300 text-slate-800 text-sm rounded-xl px-4 py-3 focus:ring-2 focus:ring-cenicana focus:border-cenicana outline-none shadow-sm"
            />
            
            <div v-if="showViverosDropdown" class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl max-h-60 overflow-y-auto">
              <div v-if="isLoadingViveros" class="p-4 text-center text-xs text-slate-500">Buscando...</div>
              <div v-else-if="filteredViveros.length === 0" class="p-4 text-center text-xs text-slate-500">No se encontraron resultados</div>
              <ul v-else>
                <li
                  v-for="v in filteredViveros"
                  :key="v.id"
                  @mousedown="selectVivero(v)"
                  class="p-3 border-b border-slate-50 hover:bg-slate-50 cursor-pointer transition-colors"
                >
                  <div class="font-bold text-slate-800 text-sm">{{ v.identificador_unico }}</div>
                  <div class="text-[10px] text-slate-500 flex gap-2 mt-1">
                    <span class="bg-slate-100 px-2 py-0.5 rounded">{{ v.hacienda }}</span>
                    <span class="bg-slate-100 px-2 py-0.5 rounded">Lote: {{ v.suerte }}</span>
                  </div>
                </li>
              </ul>
            </div>
          </div>
          
          <div v-if="selectedVivero" class="bg-white p-4 rounded-xl border border-emerald-200 shadow-sm flex items-start gap-3">
            <div class="mt-0.5 text-emerald-500">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div>
              <h5 class="text-sm font-bold text-slate-800">Vivero Seleccionado: {{ selectedVivero.identificador_unico }}</h5>
              <p class="text-xs text-slate-500 mt-1">Ingenio: {{ selectedVivero.ingenio }} | Hacienda: {{ selectedVivero.hacienda }} | Lote: {{ selectedVivero.suerte }}</p>
            </div>
          </div>
        </div>

        <!-- STEP 2: Upload Excel -->
        <div v-show="step === 2" class="space-y-6">
          <div class="bg-white p-8 rounded-xl border-2 border-dashed border-slate-300 flex flex-col items-center justify-center text-center hover:bg-slate-50 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-slate-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h5 class="text-sm font-bold text-slate-700 mb-2">Seleccione o arrastre su archivo Excel</h5>
            <p class="text-xs text-slate-500 mb-6 max-w-md">El archivo debe contener una pestaña donde cada fila represente una flor individual con su viabilidad de polen y sexo.</p>
            
            <input type="file" ref="fileInput" accept=".xlsx,.xls" class="hidden" @change="onFileSelected" />
            <button @click="$refs.fileInput.click()" class="bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold uppercase tracking-wider py-2.5 px-6 rounded-lg transition-colors shadow-md">
              Examinar Archivo
            </button>
            <div v-if="file" class="mt-4 text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-100 flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
              {{ file.name }} ({{ (file.size / 1024).toFixed(1) }} KB)
            </div>
          </div>
          
          <div v-if="sheets.length > 0" class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">Seleccione la pestaña (Hoja) del archivo <span class="text-red-500">*</span></label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
              <button
                v-for="s in sheets"
                :key="s"
                @click="selectedSheet = s"
                :class="[
                  'py-2.5 px-4 rounded-lg text-xs font-bold border transition-all truncate',
                  selectedSheet === s ? 'bg-cenicana text-white border-cenicana shadow-md' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'
                ]"
                :title="s"
              >
                {{ s }}
              </button>
            </div>
          </div>
        </div>

        <!-- STEP 3: Mapeo -->
        <div v-show="step === 3" class="space-y-6">
          <div class="bg-amber-50 p-4 rounded-xl border border-amber-100">
            <h5 class="text-sm font-bold text-amber-800 mb-2">Paso 3: Mapeo de Columnas</h5>
            <p class="text-xs text-amber-700 leading-relaxed">
              El sistema ha detectado las columnas de su archivo. Por favor confirme cuál columna de su Excel corresponde a cada dato requerido por el sistema.
            </p>
          </div>
          
          <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-left text-sm">
              <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                  <th class="px-5 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider w-1/3">Dato Requerido</th>
                  <th class="px-5 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider w-2/3">Columna en el Excel</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="(label, key) in mappingConfig" :key="key" class="hover:bg-slate-50/50">
                  <td class="px-5 py-3 font-semibold text-slate-700 text-xs flex items-center gap-2">
                    <span v-if="label.required" class="text-red-500">*</span>
                    {{ label.title }}
                  </td>
                  <td class="px-5 py-3">
                    <select
                      v-model="mapping[key]"
                      class="w-full bg-white border border-slate-300 text-slate-800 text-xs rounded-lg px-3 py-2 focus:ring-2 focus:ring-cenicana focus:border-cenicana outline-none"
                    >
                      <option value="">-- Ignorar / No Mapear --</option>
                      <option v-for="col in excelColumns" :key="col" :value="col">{{ col }}</option>
                    </select>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- STEP 4: Validations & Finish -->
        <div v-show="step === 4" class="space-y-6">
          <div v-if="isValidating" class="flex flex-col items-center justify-center py-12">
            <svg class="animate-spin h-10 w-10 text-cenicana mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <h5 class="text-sm font-bold text-slate-700">Analizando y Validando Excel...</h5>
            <p class="text-xs text-slate-500 mt-2">Aplicando reglas de integridad a cada registro (Vivero, Lote, Parcela, Variedad, Proyecto)</p>
          </div>
          
          <div v-else-if="validationErrors.length > 0" class="space-y-4">
            <div class="bg-red-50 p-5 rounded-xl border border-red-200 flex items-start gap-4">
              <div class="text-red-600 mt-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
              </div>
              <div>
                <h5 class="text-base font-bold text-red-800">Se encontraron {{ validationErrors.length }} errores de integridad</h5>
                <p class="text-sm text-red-600 mt-1">La importación se ha detenido. Debe corregir los datos en su archivo Excel e intentarlo nuevamente, o registrar las parcelas/variedades faltantes en el sistema.</p>
              </div>
            </div>
            
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden max-h-80 overflow-y-auto">
              <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 sticky top-0">
                  <tr>
                    <th class="px-4 py-3 font-bold text-slate-500 uppercase w-16 text-center">Fila</th>
                    <th class="px-4 py-3 font-bold text-slate-500 uppercase">Descripción del Error</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr v-for="(err, i) in validationErrors" :key="i" class="hover:bg-red-50/30">
                    <td class="px-4 py-3 text-center font-bold text-slate-400 bg-slate-50/50">#{{ err.row }}</td>
                    <td class="px-4 py-3 text-slate-700 font-medium">{{ err.message }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          
          <div v-else-if="validationSuccess" class="bg-emerald-50 p-8 rounded-xl border border-emerald-200 text-center flex flex-col items-center">
            <div class="h-16 w-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            </div>
            <h5 class="text-lg font-bold text-emerald-800 mb-2">¡Validación Exitosa!</h5>
            <p class="text-sm text-emerald-700 max-w-md">
              Todos los datos de las flores coinciden perfectamente con el Vivero {{ selectedVivero?.identificador_unico }}, sus parcelas, variedades y proyecto.
            </p>
            <div class="mt-6 bg-white px-6 py-3 rounded-lg border border-emerald-100 shadow-sm font-mono font-bold text-slate-700 text-sm">
              {{ validRowsCount }} registros listos para importar
            </div>
          </div>
        </div>

      </div>

      <!-- Footer Buttons -->
      <div class="bg-slate-50 p-5 border-t border-slate-100 flex justify-between items-center shrink-0">
        <button
          v-if="step > 1 && !validationSuccess"
          @click="step--"
          class="px-5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-600 text-xs font-bold uppercase tracking-wider hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-200 transition-colors"
        >
          Anterior
        </button>
        <div v-else></div> <!-- Placeholder para justificar -->

        <div class="flex gap-3">
          <button
            @click="close"
            class="px-5 py-2.5 rounded-lg border border-transparent text-slate-500 text-xs font-bold uppercase tracking-wider hover:text-slate-700 transition-colors"
          >
            Cancelar
          </button>
          
          <!-- Botón Siguiente -->
          <button
            v-if="step < 4"
            @click="nextStep"
            :disabled="!canProceed"
            class="px-6 py-2.5 rounded-lg bg-cenicana text-white text-xs font-bold uppercase tracking-wider hover:bg-cenicana-800 focus:outline-none focus:ring-2 focus:ring-cenicana focus:ring-offset-2 transition-all shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Siguiente
          </button>
          
          <!-- Botón Importar -->
          <button
            v-if="step === 4 && validationSuccess"
            @click="submitImport"
            :disabled="isSubmitting"
            class="px-8 py-2.5 rounded-lg bg-emerald-600 text-white text-xs font-bold uppercase tracking-wider hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all shadow-md flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ isSubmitting ? 'Importando...' : 'Confirmar Importación' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import api from '@/services/api';
import urls from '@/services/urls';
import { useToast } from 'vue-toastification';
import * as ExcelJS from 'exceljs';
import _ from 'lodash';

const props = defineProps({
  isOpen: { type: Boolean, default: false }
});

const emit = defineEmits(['close', 'imported']);
const toast = useToast();

const step = ref(1);
const stepTitles = ["Vivero", "Archivo", "Mapeo", "Validación"];

// Step 1: Vivero Selection
const searchQuery = ref('');
const showViverosDropdown = ref(false);
const isLoadingViveros = ref(false);
const filteredViveros = ref<any[]>([]);
const selectedVivero = ref<any>(null);

const fetchViveros = async (q: string) => {
  isLoadingViveros.value = true;
  showViverosDropdown.value = true;
  try {
    const response = await api.get(`${urls.API_VIVEROS}/search-import?q=${q}`);
    filteredViveros.value = response.data;
  } catch (error) {
    console.error("Error searching viveros:", error);
  } finally {
    isLoadingViveros.value = false;
  }
};

const onSearchInput = _.debounce(() => {
  fetchViveros(searchQuery.value);
}, 300);

const onFocusInput = () => {
  showViverosDropdown.value = true;
  if (filteredViveros.value.length === 0) {
    fetchViveros(searchQuery.value);
  }
};

const selectVivero = (vivero: any) => {
  selectedVivero.value = vivero;
  searchQuery.value = vivero.identificador_unico;
  showViverosDropdown.value = false;
};

// Step 2: File Upload
const file = ref<File | null>(null);
const fileInput = ref<any>(null);
const workbook = ref<ExcelJS.Workbook | null>(null);
const sheets = ref<string[]>([]);
const selectedSheet = ref<string>("");

const onFileSelected = async (e: any) => {
  const selectedFile = e.target.files[0];
  if (!selectedFile) return;
  file.value = selectedFile;
  
  try {
    const arrayBuffer = await file.value.arrayBuffer();
    const wb = new ExcelJS.Workbook();
    await wb.xlsx.load(arrayBuffer);
    workbook.value = wb;
    
    sheets.value = [];
    wb.eachSheet((worksheet) => {
      sheets.value.push(worksheet.name);
    });
    
    // Auto-select "Floracion" tab if exists
    const floracionSheet = sheets.value.find(s => s.toLowerCase().includes('floracion') || s.toLowerCase().includes('floración'));
    if (floracionSheet) {
      selectedSheet.value = floracionSheet;
    } else if (sheets.value.length > 0) {
      selectedSheet.value = sheets.value[0];
    }
  } catch(error) {
    toast.error("Error leyendo archivo Excel.");
    console.error(error);
  }
};

// Step 3: Mapping
const excelColumns = ref<string[]>([]);
const mappingConfig = {
  vivero: { title: "Origen / ID Vivero", required: true, auto: ['origen', 'vivero', 'id vivero'] },
  parcela: { title: "Parcela / Lote", required: true, auto: ['parcela', 'numero parcela', 'no parcela'] },
  variedad: { title: "Variedad", required: true, auto: ['variedad', 'clon'] },
  proyecto: { title: "Proyecto", required: true, auto: ['proyecto'] },
  polen: { title: "Viabilidad Polen (%)", required: true, auto: ['polen', 'viabilidad', 'porcentaje polen'] },
  sexo: { title: "Sexo", required: true, auto: ['sexo'] },
  floracion: { title: "Tipo Floración (Nat/Ind)", required: false, auto: ['floracion', 'tipo'] },
  fecha: { title: "Fecha de Evaluación", required: true, auto: ['fecha'] }
};
const mapping = ref<Record<string, string>>({});

watch(selectedSheet, (newSheet) => {
  if (!newSheet || !workbook.value) return;
  const ws = workbook.value.getWorksheet(newSheet);
  if (!ws) return;
  
  // Assuming header is on row 1
  const headerRow = ws.getRow(1);
  excelColumns.value = [];
  headerRow.eachCell((cell, colNumber) => {
    if (cell.value) {
      excelColumns.value.push(cell.value.toString().trim());
    }
  });
  
  // Auto-map columns
  for (const [key, config] of Object.entries(mappingConfig)) {
    mapping.value[key] = "";
    for (const col of excelColumns.value) {
      if (config.auto.some(a => col.toLowerCase().includes(a))) {
        mapping.value[key] = col;
        break;
      }
    }
  }
});

// Step 4: Validations
const isValidating = ref(false);
const validationErrors = ref<any[]>([]);
const validationSuccess = ref(false);
const validRowsCount = ref(0);

const performValidation = async () => {
  isValidating.value = true;
  validationErrors.value = [];
  validationSuccess.value = false;
  
  try {
    const formData = new FormData();
    formData.append('file', file.value as File);
    formData.append('vivero_id', selectedVivero.value.id.toString());
    formData.append('sheet_name', selectedSheet.value);
    formData.append('mapping', JSON.stringify(mapping.value));
    
    // Validate only endpoint
    const response = await api.post(`/siembra-campo/floracion/validate-import`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    
    if (response.data.errors && response.data.errors.length > 0) {
      validationErrors.value = response.data.errors;
    } else {
      validationSuccess.value = true;
      validRowsCount.value = response.data.validCount;
    }
  } catch (error: any) {
    console.error("Validation error:", error);
    toast.error("Ocurrió un error en el servidor al intentar validar el archivo.");
  } finally {
    isValidating.value = false;
  }
};

const isSubmitting = ref(false);
const submitImport = async () => {
  isSubmitting.value = true;
  try {
    const formData = new FormData();
    formData.append('file', file.value as File);
    formData.append('vivero_id', selectedVivero.value.id.toString());
    formData.append('sheet_name', selectedSheet.value);
    formData.append('mapping', JSON.stringify(mapping.value));
    
    await api.post(`/siembra-campo/floracion/execute-import`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    
    toast.success(`${validRowsCount.value} flores importadas exitosamente.`);
    emit('imported');
    close();
  } catch (error) {
    console.error("Import error:", error);
    toast.error("Ocurrió un error guardando los datos.");
  } finally {
    isSubmitting.value = false;
  }
};

const canProceed = computed(() => {
  if (step.value === 1) return !!selectedVivero.value;
  if (step.value === 2) return !!file.value && !!selectedSheet.value;
  if (step.value === 3) {
    // Check required mappings
    for (const [key, config] of Object.entries(mappingConfig)) {
      if (config.required && !mapping.value[key]) return false;
    }
    return true;
  }
  return false;
});

const nextStep = () => {
  if (step.value < 4) {
    step.value++;
    if (step.value === 4) {
      performValidation();
    }
  }
};

const close = () => {
  step.value = 1;
  searchQuery.value = '';
  selectedVivero.value = null;
  file.value = null;
  selectedSheet.value = "";
  validationErrors.value = [];
  validationSuccess.value = false;
  emit('close');
};

watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    step.value = 1;
  }
});
</script>
