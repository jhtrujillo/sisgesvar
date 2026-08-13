<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="close"></div>
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <div
        class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full"
      >
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
              <div class="flex justify-between items-center mb-4 border-b pb-3">
                <h3 class="text-xl leading-6 font-bold text-slate-800" id="modal-title">Asistente de Importación de Parcelas</h3>
                <BaseButton variant="ghost" size="xs" iconOnly @click="close" class="!text-slate-400 hover:!text-red-500">
                  <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </BaseButton>
              </div>

              <!-- Paso 1: Subir Archivo -->
              <div v-if="step === 1" class="py-4">
                <div
                  class="border-2 border-dashed border-slate-300 rounded-xl p-12 text-center hover:bg-slate-50 transition-colors cursor-pointer group"
                  @click="$refs.fileInput.click()"
                >
                  <svg
                    class="mx-auto h-16 w-16 text-slate-400 group-hover:text-cenicana transition-colors"
                    stroke="currentColor"
                    fill="none"
                    viewBox="0 0 48 48"
                    aria-hidden="true"
                  >
                    <path
                      d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                  <p class="mt-4 text-base font-medium text-slate-700">Haz clic para seleccionar el archivo Excel</p>
                  <p class="mt-1 text-sm text-slate-500">Formatos soportados: .xlsx, .csv, .xls</p>
                  <input ref="fileInput" type="file" class="hidden" accept=".xlsx, .xls, .csv" @change="handleFileUpload" />
                </div>
              </div>

              <!-- Paso 2: Seleccionar Hoja -->
              <div v-if="step === 2" class="py-4">
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase">1. Selecciona la hoja del archivo que contiene las parcelas</label>
                <select
                  v-model="selectedSheet"
                  class="mt-1 block w-full pl-3 pr-10 py-2.5 text-base border-slate-300 focus:outline-none focus:ring-1 focus:ring-cenicana focus:border-cenicana rounded-lg border shadow-sm"
                >
                  <option v-for="sheet in sheets" :key="sheet" :value="sheet">{{ sheet }}</option>
                </select>
                <div class="mt-8 flex justify-end">
                  <BaseButton variant="primary" size="md" @click="processSheet" :disabled="!selectedSheet">Siguiente</BaseButton>
                </div>
              </div>

              <!-- Paso 3: Mapeo de Columnas -->
              <div v-if="step === 3" class="py-4">
                <p class="text-sm text-slate-600 mb-6 bg-slate-50 p-3 rounded-lg border border-slate-200">
                  Por favor, relaciona las columnas requeridas por el sistema con los encabezados que hemos detectado en tu archivo Excel.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                  <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Columna Plot (Número)</label>
                    <select
                      v-model="mapping.plot"
                      class="w-full pl-3 pr-10 py-2 border-slate-300 rounded-md border text-sm focus:outline-none focus:ring-1 focus:ring-cenicana bg-slate-50"
                    >
                      <option value="">-- Seleccionar --</option>
                      <option v-for="col in headers" :key="col" :value="col">{{ col }}</option>
                    </select>
                  </div>
                  <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Columna Plot Origen</label>
                    <select
                      v-model="mapping.plot_origen"
                      class="w-full pl-3 pr-10 py-2 border-slate-300 rounded-md border text-sm focus:outline-none focus:ring-1 focus:ring-cenicana bg-slate-50"
                    >
                      <option value="">-- Opcional --</option>
                      <option v-for="col in headers" :key="col" :value="col">{{ col }}</option>
                    </select>
                    <p class="text-[10px] text-slate-500 mt-2 font-mono bg-slate-100 p-1 rounded">
                      El ID Plot se autocalculará como: {{ viveroIdentificador }}-XX
                    </p>
                  </div>
                  <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm border-l-4 border-l-cenicana">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Columna Variedad</label>
                    <select
                      v-model="mapping.variedad"
                      class="w-full pl-3 pr-10 py-2 border-slate-300 rounded-md border text-sm focus:outline-none focus:ring-1 focus:ring-cenicana bg-slate-50"
                    >
                      <option value="">-- Seleccionar --</option>
                      <option v-for="col in headers" :key="col" :value="col">{{ col }}</option>
                    </select>
                  </div>
                </div>
                <div class="mt-8 flex justify-between">
                  <BaseButton variant="secondary" size="md" @click="step = sheets.length > 1 ? 2 : 1">Volver</BaseButton>
                  <BaseButton
                    variant="primary"
                    size="md"
                    @click="validateData"
                    :disabled="!mapping.plot || !mapping.variedad || isAnalyzing"
                    :loading="isAnalyzing"
                  >
                    {{ isAnalyzing ? "Analizando 25,000+ registros..." : "Analizar Datos" }}
                  </BaseButton>
                </div>
              </div>

              <!-- Paso 4: Resolver Conflictos -->
              <div v-if="step === 4" class="py-2">
                <div v-if="conflicts.length === 0">
                  <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-6 mb-4 text-center">
                    <svg class="mx-auto h-12 w-12 text-emerald-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h4 class="text-lg font-bold text-emerald-800">¡Validación Exitosa!</h4>
                    <p class="text-sm text-emerald-600 mt-1">
                      Las {{ readyToImport.length }} variedades encontradas coinciden perfectamente con nuestra base de datos.
                    </p>
                  </div>
                </div>
                <div v-else>
                  <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
                    <div class="flex items-start">
                      <svg class="h-5 w-5 text-amber-500 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                        <path
                          fill-rule="evenodd"
                          d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      <div class="ml-3">
                        <h4 class="text-sm font-bold text-amber-800">Atención requerida</h4>
                        <p class="text-sm text-amber-700 mt-1">
                          Hay <strong>{{ unresolvedCount }}</strong> variedades (de {{ conflicts.length }} conflictos) del Excel que no encontramos en SIVAR.
                          Por favor, asígnales una correcta. <br /><strong>Nota:</strong> Las parcelas que dejes sin resolver (en rojo) serán omitidas y NO se
                          importarán.
                        </p>
                      </div>
                    </div>
                  </div>

                  <div class="mb-3 flex items-center justify-between bg-slate-100/50 p-2.5 rounded-lg border border-slate-200">
                    <label class="flex items-center gap-2 cursor-pointer">
                      <input type="checkbox" v-model="showOnlyUnresolved" class="rounded border-slate-300 text-cenicana focus:ring-cenicana w-4 h-4" />
                      <span class="text-sm font-medium text-slate-700">Mostrar solo filas sin resolver ({{ unresolvedCount }})</span>
                    </label>
                    <div class="text-xs text-slate-500 font-medium">
                      <span class="font-bold text-emerald-600">{{ conflicts.length - unresolvedCount }}</span> auto-resueltas
                    </div>
                  </div>

                  <div class="max-h-96 overflow-y-auto border border-slate-200 rounded-lg shadow-inner bg-slate-50 relative">
                    <div v-if="displayedConflicts.length === 0" class="p-8 text-center text-slate-500">¡No hay filas para mostrar con este filtro!</div>
                    <table v-else class="min-w-full divide-y divide-slate-200 text-sm">
                      <thead class="bg-slate-100 sticky top-0 z-10">
                        <tr>
                          <th class="px-4 py-3 text-left font-bold text-slate-600 uppercase text-xs">Fila / Plot</th>
                          <th class="px-4 py-3 text-left font-bold text-slate-600 uppercase text-xs">Variedad original (Excel)</th>
                          <th class="px-4 py-3 text-left font-bold text-slate-600 uppercase text-xs w-1/2">Asignar a variedad SIVAR</th>
                          <th class="px-4 py-3 text-center font-bold text-slate-600 uppercase text-xs w-16">Acción</th>
                        </tr>
                      </thead>
                      <tbody class="bg-white divide-y divide-slate-100">
                        <tr
                          v-for="(conflict, index) in displayedConflicts"
                          :key="index + '-' + conflict.excelVariedad"
                          :class="{ 'bg-emerald-50/50': conflict.resolvedId }"
                        >
                          <td class="px-4 py-3 font-mono text-slate-500">{{ conflict.row[mapping.plot] }}</td>
                          <td class="px-4 py-3 font-medium text-rose-600">{{ conflict.excelVariedad }}</td>
                          <td class="px-4 py-3 relative">
                            <div v-if="!conflict.resolvedId">
                              <input
                                type="text"
                                v-model="conflict.searchTerm"
                                @focus="conflict.showDropdown = true"
                                @blur="hideConflictDropdown(conflict)"
                                placeholder="Buscar en SIVAR..."
                                class="w-full border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:ring-1 focus:ring-cenicana outline-none shadow-sm"
                              />
                              <div
                                v-if="conflict.showDropdown"
                                class="absolute z-20 w-[90%] mt-1 bg-white shadow-xl max-h-48 rounded-lg py-1 text-xs overflow-auto border border-slate-200 left-4"
                              >
                                <div v-if="getFilteredVarieties(conflict.searchTerm).length === 0" class="px-3 py-2 text-slate-400">Sin resultados</div>
                                <div
                                  v-for="v in getFilteredVarieties(conflict.searchTerm)"
                                  :key="v.id_nm_vrdad"
                                  @mousedown="resolveConflict(conflict, v)"
                                  class="cursor-pointer px-3 py-2 hover:bg-cenicana hover:text-white border-b border-slate-50 transition-colors"
                                >
                                  <span class="font-bold">{{ v.nm_vrdad }}</span> <span class="opacity-75 text-[10px] ml-1">{{ v.pdgree }}</span>
                                </div>
                              </div>
                            </div>
                            <div v-else class="flex items-center justify-between bg-white border border-emerald-200 rounded-lg px-3 py-1.5">
                              <span class="text-sm text-emerald-700 font-bold flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ conflict.resolvedName }}
                              </span>
                              <BaseButton variant="link" size="xs" @click="conflict.resolvedId = null">Cambiar</BaseButton>
                            </div>
                          </td>
                          <td class="px-2 py-3 text-center">
                            <BaseButton
                              variant="ghost"
                              size="xs"
                              iconOnly
                              @click="removeConflict(conflict)"
                              title="Omitir fila"
                              class="!text-slate-400 hover:!text-red-500 hover:!bg-red-50"
                            >
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                ></path>
                              </svg>
                            </BaseButton>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <div class="mt-8 flex justify-between items-center">
                  <BaseButton variant="secondary" size="md" @click="step = 3">Volver</BaseButton>
                  <div class="flex items-center gap-3">
                    <span v-if="conflicts.length > 0" class="text-sm font-medium" :class="allConflictsResolved ? 'text-emerald-600' : 'text-amber-600'">
                      {{ resolvedCount }} de {{ conflicts.length }} resueltos
                    </span>
                    <BaseButton variant="primary" size="md" @click="submitImport" :disabled="!allConflictsResolved || isSubmitting" :loading="isSubmitting">
                      {{ isSubmitting ? "Guardando parcelas..." : "Finalizar e Importar" }}
                    </BaseButton>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, nextTick } from "vue";
import * as XLSX from "xlsx";
import { useToast } from "vue-toastification";
import viverosServices from "@/services/viveros.services";

const props = defineProps<{
  show: boolean;
  variedades: any[];
  viveroId: string | number;
  viveroIdentificador: string;
  origenParcela?: string;
  consecutivoCorte?: number | null;
  caracterId?: string | number | null;
}>();

const emit = defineEmits(["close", "imported"]);
const toast = useToast();

const step = ref(1);
const fileInput = ref<HTMLInputElement | null>(null);
const workbook = ref<XLSX.WorkBook | null>(null);
const sheets = ref<string[]>([]);
const selectedSheet = ref("");
const headers = ref<string[]>([]);
const rawData = ref<any[]>([]);

const mapping = ref({
  plot: "",
  plot_origen: "",
  variedad: ""
});

const conflicts = ref<any[]>([]);
const readyToImport = ref<any[]>([]);
const isSubmitting = ref(false);
const isAnalyzing = ref(false);
const showOnlyUnresolved = ref(false);

const displayedConflicts = computed(() => {
  if (showOnlyUnresolved.value) {
    return conflicts.value.filter((c) => !c.resolvedId);
  }
  return conflicts.value;
});

const unresolvedCount = computed(() => {
  return conflicts.value.filter((c) => !c.resolvedId).length;
});

const close = () => {
  resetState();
  emit("close");
};

const removeConflict = (conflictToRemove: any) => {
  const index = conflicts.value.indexOf(conflictToRemove);
  if (index !== -1) {
    conflicts.value.splice(index, 1);
  }
};

const resetState = () => {
  step.value = 1;
  workbook.value = null;
  sheets.value = [];
  selectedSheet.value = "";
  headers.value = [];
  rawData.value = [];
  mapping.value = { plot: "", plot_origen: "", variedad: "" };
  conflicts.value = [];
  readyToImport.value = [];
  isSubmitting.value = false;
  isAnalyzing.value = false;
  if (fileInput.value) fileInput.value.value = "";
};

const handleFileUpload = (e: Event) => {
  const target = e.target as HTMLInputElement;
  const file = target.files?.[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = (e) => {
    try {
      const data = new Uint8Array(e.target?.result as ArrayBuffer);
      workbook.value = XLSX.read(data, { type: "array" });
      sheets.value = workbook.value.SheetNames;

      if (sheets.value.length === 1) {
        selectedSheet.value = sheets.value[0];
        processSheet();
      } else {
        step.value = 2;
      }
    } catch (err) {
      toast.error("El archivo no tiene un formato válido de Excel.");
      resetState();
    }
  };
  reader.readAsArrayBuffer(file);
};

const processSheet = () => {
  if (!workbook.value || !selectedSheet.value) return;
  const worksheet = workbook.value.Sheets[selectedSheet.value];
  const data = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

  if (data.length === 0) {
    toast.error("La hoja está vacía.");
    return;
  }

  headers.value = (data[0] as string[]).map((h) => String(h).trim()).filter((h) => h);

  // Auto-guess columns
  const plotCol = headers.value.find((h) => h.toLowerCase().includes("plot") && !h.toLowerCase().includes("origen"));
  if (plotCol) mapping.value.plot = plotCol;

  const plotOrigenCol = headers.value.find((h) => h.toLowerCase().includes("origen") || h.toLowerCase().includes("iorigen"));
  if (plotOrigenCol) mapping.value.plot_origen = plotOrigenCol;

  const varCol = headers.value.find((h) => h.toLowerCase().includes("variedad"));
  if (varCol) mapping.value.variedad = varCol;

  const raw = XLSX.utils.sheet_to_json(worksheet);
  rawData.value = raw.map((row: any) => {
    const newRow: any = {};
    for (const key in row) {
      const val = row[key];
      newRow[String(key).trim()] = typeof val === "string" ? val.trim() : val;
    }
    return newRow;
  });
  step.value = 3;
};

const levenshteinDistance = (a: string, b: string) => {
  if (a.length === 0) return b.length;
  if (b.length === 0) return a.length;
  const matrix = [];
  for (let i = 0; i <= b.length; i++) matrix[i] = [i];
  for (let j = 0; j <= a.length; j++) matrix[0][j] = j;
  for (let i = 1; i <= b.length; i++) {
    for (let j = 1; j <= a.length; j++) {
      if (b.charAt(i - 1) === a.charAt(j - 1)) {
        matrix[i][j] = matrix[i - 1][j - 1];
      } else {
        matrix[i][j] = Math.min(matrix[i - 1][j - 1] + 1, Math.min(matrix[i][j - 1] + 1, matrix[i - 1][j] + 1));
      }
    }
  }
  return matrix[b.length][a.length];
};

const findBestMatch = (term: string) => {
  if (!term) return null;
  const cleanTerm = term.toLowerCase().replace(/[^a-z0-9]/g, "");
  if (!cleanTerm) return null;

  let bestMatch = null;
  let minDistance = Infinity;

  for (const v of props.variedades) {
    const cleanV = v.nm_vrdad.toLowerCase().replace(/[^a-z0-9]/g, "");
    if (cleanV === cleanTerm) return v; // Exact match ignoring punctuation/spaces

    if (Math.abs(cleanV.length - cleanTerm.length) <= 4) {
      const dist = levenshteinDistance(cleanTerm, cleanV);
      if (dist < minDistance) {
        minDistance = dist;
        bestMatch = v;
      }
    }
  }

  // Auto-assign if distance is 3 or less (minor typo)
  if (minDistance <= 3 && bestMatch) {
    return bestMatch;
  }
  return null;
};

const validateData = async () => {
  isAnalyzing.value = true;
  await nextTick();
  await new Promise((resolve) => setTimeout(resolve, 50));

  conflicts.value = [];
  readyToImport.value = [];

  // Create a fast lookup map for exact match
  const varMap = new Map();
  props.variedades.forEach((v) => {
    varMap.set(v.nm_vrdad.toLowerCase().trim(), v);
  });

  rawData.value.forEach((row) => {
    const plotVal = row[mapping.value.plot];
    if (plotVal === undefined || plotVal === null || plotVal === "") return; // Skip empty rows

    const varVal = row[mapping.value.variedad] ? String(row[mapping.value.variedad]).trim() : "";
    const plotOrigenVal = mapping.value.plot_origen ? row[mapping.value.plot_origen] : null;

    const exactMatch = varMap.get(varVal.toLowerCase());

    const getIdPlotOrigen = (pOrigenVal: any) => {
      if (props.origenParcela && props.consecutivoCorte) {
        return `${props.origenParcela}-${props.consecutivoCorte}`;
      }
      return pOrigenVal ? `${props.viveroIdentificador}-${pOrigenVal}` : null;
    };

    if (exactMatch) {
      readyToImport.value.push({
        numero_parcela: plotVal,
        variedad_id: exactMatch.id_nm_vrdad,
        numero_parcela_origen: plotOrigenVal,
        id_plot_origen: getIdPlotOrigen(plotOrigenVal),
        caracter_id: props.caracterId || null
      });
    } else {
      // Try to find a fuzzy best match
      const bestMatch = findBestMatch(varVal);

      conflicts.value.push({
        row: row,
        excelVariedad: varVal || "(Vacío)",
        searchTerm: bestMatch ? bestMatch.nm_vrdad : varVal,
        showDropdown: false,
        resolvedId: bestMatch ? bestMatch.id_nm_vrdad : null,
        resolvedName: bestMatch ? bestMatch.nm_vrdad : ""
      });
    }
  });

  step.value = 4;
  isAnalyzing.value = false;
};

const getFilteredVarieties = (term: string) => {
  if (!term) return props.variedades.slice(0, 50);
  const q = term.toLowerCase();
  return props.variedades.filter((v) => v.nm_vrdad.toLowerCase().includes(q) || (v.pdgree && v.pdgree.toLowerCase().includes(q))).slice(0, 50);
};

const hideConflictDropdown = (conflict: any) => {
  setTimeout(() => {
    conflict.showDropdown = false;
  }, 200);
};

const resolveConflict = (conflict: any, variety: any) => {
  conflict.resolvedId = variety.id_nm_vrdad;
  conflict.resolvedName = variety.nm_vrdad;
  conflict.searchTerm = variety.nm_vrdad;
  conflict.showDropdown = false;
};

const resolvedCount = computed(() => {
  return conflicts.value.filter((c) => c.resolvedId !== null).length;
});

const allConflictsResolved = computed(() => {
  return conflicts.value.every((c) => c.resolvedId !== null);
});

const submitImport = async () => {
  isSubmitting.value = true;

  // Combine ready and resolved
  const payload = [
    ...readyToImport.value,
    ...conflicts.value.map((c) => {
      const plotOrigenVal = mapping.value.plot_origen ? c.row[mapping.value.plot_origen] : null;
      const getIdPlotOrigen = (pOrigenVal: any) => {
        if (props.origenParcela && props.consecutivoCorte) {
          return `${props.origenParcela}-${props.consecutivoCorte}`;
        }
        return pOrigenVal ? `${props.viveroIdentificador}-${pOrigenVal}` : null;
      };
      return {
        numero_parcela: c.row[mapping.value.plot],
        variedad_id: c.resolvedId,
        numero_parcela_origen: plotOrigenVal,
        id_plot_origen: getIdPlotOrigen(plotOrigenVal),
        caracter_id: props.caracterId || null
      };
    })
  ];

  try {
    const res = await viverosServices.importBatchParcelas(props.viveroId, payload);
    toast.success(res.data.message || `Se importaron las parcelas correctamente.`);
    emit("imported");
    close();
  } catch (error: any) {
    console.error("Error in batch import:", error);
    toast.error(error.response?.data?.message || "Hubo un error al guardar las parcelas en la base de datos.");
  } finally {
    isSubmitting.value = false;
  }
};
</script>
