<template>
  <div v-if="show" class="w-full transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-sm w-full mx-auto overflow-hidden border border-slate-100 flex flex-col min-h-[60vh]">
      <!-- Header -->
      <div class="flex justify-between items-center border-b border-slate-100 p-5 bg-slate-50 shrink-0">
        <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wide flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-cenicana" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
          </svg>
          Importar Siembra Campo desde Excel
        </h4>
        <button @click="close" class="text-slate-500 hover:text-slate-700 text-sm font-bold flex items-center gap-1 bg-white border border-slate-200 px-3 py-1.5 rounded-md shadow-sm">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
          Volver
        </button>
      </div>

      <!-- ── Stepper ────────────────────────────────────────────────── -->
      <div class="bg-white border-b border-slate-100 shrink-0">
        <div class="flex items-center px-6 py-4 gap-2">
          <template v-for="s in [1, 2, 3]" :key="s">
            <div class="flex items-center gap-2" :class="{ 'opacity-40': step < s }">
              <div
                :class="[
                  'w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0',
                  step === s ? 'bg-cenicana' : step > s ? 'bg-emerald-500' : 'bg-slate-300'
                ]"
              >
                <span v-if="step > s">✓</span>
                <span v-else>{{ s }}</span>
              </div>
              <span class="text-xs font-bold text-slate-600 uppercase tracking-wide whitespace-nowrap" :class="{ 'text-cenicana': step === s }">
                {{ stepTitles[s - 1] }}
              </span>
            </div>
            <div v-if="s < 3" class="flex-1 h-px bg-slate-200 hidden sm:block"></div>
          </template>
        </div>
      </div>

      <!-- ── Content ────────────────────────────────────────────────── -->
      <div class="p-6 flex-1 overflow-y-auto bg-slate-50/50">
        <!-- ── PASO 1: Archivo y Hoja ─────────────────────────────── -->
        <div v-show="step === 1" class="space-y-6">
          <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
            <h5 class="text-sm font-bold text-blue-800 mb-1">Paso 1: Selección de Archivo y Hoja</h5>
            <p class="text-xs text-blue-600 leading-relaxed">
              Cargue un archivo Excel (.xlsx o .xls) con los registros de Siembra Campo. Si el archivo contiene varias hojas, seleccione la que contiene los
              datos.
            </p>
          </div>

          <!-- Zona de carga (drag & drop) -->
          <div
            class="bg-white border-2 border-dashed rounded-xl flex flex-col items-center justify-center text-center p-10 transition-colors"
            :class="isDragging ? 'border-cenicana bg-cenicana/5' : 'border-slate-300 hover:bg-slate-50'"
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="onDrop"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-14 w-14 mb-4 transition-colors"
              :class="isDragging ? 'text-cenicana' : 'text-slate-400'"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="1.5"
                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
              />
            </svg>
            <h5 class="text-sm font-bold text-slate-700 mb-1">
              {{ isDragging ? "Suelta el archivo aquí" : "Arrastra tu archivo Excel aquí" }}
            </h5>
            <p class="text-xs text-slate-500 mb-5">o haz clic para buscarlo en tu equipo</p>

            <input ref="fileInputRef" type="file" accept=".xlsx,.xls" class="hidden" @change="onFileSelected" />
            <button
              @click="fileInputRef?.click()"
              class="bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold uppercase tracking-wider py-2.5 px-6 rounded-lg transition-colors shadow-md"
            >
              Examinar Archivo
            </button>

            <div
              v-if="file"
              class="mt-5 text-xs font-bold text-emerald-700 bg-emerald-50 px-4 py-2 rounded-lg border border-emerald-200 flex items-center gap-2"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              {{ file.name }} <span class="text-emerald-500 font-normal">({{ (file.size / 1024).toFixed(1) }} KB)</span>
            </div>
          </div>

          <!-- Selector de hojas -->
          <div v-if="sheets.length > 0" class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">
              Seleccione la pestaña (hoja) que contiene los datos
              <span class="text-red-500">*</span>
            </label>
            <div class="flex flex-wrap gap-2">
              <button
                v-for="s in sheets"
                :key="s"
                @click="selectedSheet = s"
                :class="[
                  'py-2 px-4 rounded-lg text-xs font-bold border transition-all truncate max-w-[180px]',
                  selectedSheet === s ? 'bg-cenicana text-white border-cenicana shadow-md' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'
                ]"
                :title="s"
              >
                {{ s }}
              </button>
            </div>
          </div>
        </div>

        <!-- ── PASO 2: Mapeo de Columnas ──────────────────────────── -->
        <div v-show="step === 2" class="space-y-5">
          <div class="bg-amber-50 p-4 rounded-xl border border-amber-100">
            <h5 class="text-sm font-bold text-amber-800 mb-1">Paso 2: Mapeo de Columnas</h5>
            <p class="text-xs text-amber-700 leading-relaxed">
              Relaciona cada campo del sistema con la columna correspondiente de tu Excel. Los campos marcados con <strong>*</strong> son obligatorios. Cada
              columna solo puede asignarse a <strong>un único campo</strong> (regla de exclusión activa).
            </p>
          </div>

          <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-left text-sm">
              <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                  <th class="px-5 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider w-5/12">Campo del sistema</th>
                  <th class="px-5 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider w-7/12">Columna en el Excel</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="fieldDef in mappingConfig" :key="fieldDef.key" class="hover:bg-slate-50/50 transition-colors">
                  <td class="px-5 py-3">
                    <div class="flex items-center gap-1.5">
                      <span v-if="fieldDef.required" class="text-red-500 font-bold text-sm leading-none">*</span>
                      <div>
                        <div class="text-xs font-semibold text-slate-800">{{ fieldDef.label }}</div>
                        <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ fieldDef.key }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-5 py-3">
                    <select
                      v-model="mapping[fieldDef.key]"
                      class="w-full bg-white border border-slate-300 text-slate-800 text-xs rounded-lg px-3 py-2 focus:ring-2 focus:ring-cenicana focus:border-cenicana outline-none transition-all"
                    >
                      <option value="">{{ fieldDef.required ? "-- Seleccionar --" : "-- Ignorar / No Mapear --" }}</option>
                      <option v-for="col in availableColumnsFor(fieldDef.key)" :key="col" :value="col">
                        {{ col }}
                      </option>
                    </select>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Resumen de asignaciones -->
          <div class="flex items-center gap-3 text-xs text-slate-500 bg-white p-3 rounded-lg border border-slate-100">
            <div class="flex items-center gap-1.5 font-semibold text-emerald-700">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              {{ assignedCount }} columnas mapeadas
            </div>
            <div class="w-px h-4 bg-slate-200"></div>
            <div class="flex items-center gap-1.5 font-semibold text-rose-600" v-if="missingRequired.length > 0">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                />
              </svg>
              Faltan: {{ missingRequired.join(", ") }}
            </div>
            <div class="flex items-center gap-1.5 font-semibold text-emerald-600" v-else>Todos los campos obligatorios asignados ✓</div>
          </div>
        </div>

        <!-- ── PASO 3: Confirmación ────────────────────────────────── -->
        <div v-show="step === 3" class="space-y-5">
          <div class="bg-emerald-50 p-4 rounded-xl border border-emerald-100">
            <h5 class="text-sm font-bold text-emerald-800 mb-1">Paso 3: Confirmar e Importar</h5>
            <p class="text-xs text-emerald-700 leading-relaxed">
              Revisa el resumen del mapeo configurado. Al confirmar, los datos del Excel se cargarán directamente en la tabla <strong>siembra_campo</strong> de
              la base de datos.
            </p>
          </div>

          <!-- Resumen: Archivo y Hoja -->
          <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm grid grid-cols-2 gap-4">
            <div>
              <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Archivo</div>
              <div class="text-sm font-semibold text-slate-800 truncate">{{ file?.name ?? "-" }}</div>
            </div>
            <div>
              <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Hoja seleccionada</div>
              <div class="text-sm font-semibold text-cenicana">{{ selectedSheet }}</div>
            </div>
          </div>

          <!-- Tabla de mapeo final -->
          <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b border-slate-200">
              <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Resumen del Mapeo</span>
            </div>
            <div class="divide-y divide-slate-100 max-h-64 overflow-y-auto">
              <div
                v-for="fieldDef in mappingConfig"
                :key="fieldDef.key"
                class="flex items-center px-5 py-2.5 gap-4 text-xs"
                :class="{ 'opacity-40': !mapping[fieldDef.key] }"
              >
                <div class="w-5/12 flex items-center gap-1.5">
                  <span v-if="fieldDef.required" class="text-red-500 font-bold">*</span>
                  <span class="font-semibold text-slate-700">{{ fieldDef.label }}</span>
                </div>
                <div class="text-slate-400 shrink-0">→</div>
                <div class="flex-1">
                  <span v-if="mapping[fieldDef.key]" class="font-mono font-bold text-cenicana bg-cenicana/10 px-2 py-0.5 rounded">
                    {{ mapping[fieldDef.key] }}
                  </span>
                  <span v-else class="italic text-slate-400">No mapeado / Se omitirá</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Bloqueo si faltan campos requeridos -->
          <div v-if="missingRequired.length > 0" class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-start gap-3">
            <svg class="h-5 w-5 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
              />
            </svg>
            <div>
              <p class="text-sm font-bold text-red-800">No se puede importar</p>
              <p class="text-xs text-red-700 mt-1">
                Los siguientes campos obligatorios no están mapeados:
                <strong>{{ missingRequired.join(", ") }}</strong
                >. Vuelve al paso anterior para asignarlos.
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- ── Footer (Botones de navegación) ────────────────────────── -->
      <div class="bg-slate-50 p-5 border-t border-slate-100 flex justify-between items-center shrink-0">
        <!-- Botón Anterior -->
        <button
          v-if="step > 1"
          @click="step--"
          class="px-5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-600 text-xs font-bold uppercase tracking-wider hover:bg-slate-50 focus:outline-none transition-colors"
        >
          Anterior
        </button>
        <div v-else></div>

        <div class="flex gap-3">
          <button
            @click="close"
            class="px-5 py-2.5 rounded-lg text-slate-500 text-xs font-bold uppercase tracking-wider hover:text-slate-700 transition-colors"
          >
            Cancelar
          </button>

          <!-- Botón Siguiente (pasos 1 y 2) -->
          <button
            v-if="step < 3"
            @click="nextStep"
            :disabled="!canProceed"
            class="px-6 py-2.5 rounded-lg bg-cenicana text-white text-xs font-bold uppercase tracking-wider hover:bg-cenicana-800 focus:outline-none focus:ring-2 focus:ring-cenicana focus:ring-offset-2 transition-all shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Siguiente
          </button>

          <!-- Botón Importar (paso 3) -->
          <button
            v-if="step === 3"
            @click="submitImport"
            :disabled="missingRequired.length > 0 || isSubmitting"
            class="px-8 py-2.5 rounded-lg bg-emerald-600 text-white text-xs font-bold uppercase tracking-wider hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all shadow-md flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg v-if="isSubmitting" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
            </svg>
            <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
            {{ isSubmitting ? "Importando..." : "Cargar a Siembra Campo" }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from "vue";
import * as XLSX from "xlsx";
import { useToast } from "vue-toastification";
import siembracampoServices from "@/services/siembracampo.services";

// ── Props / Emits ──────────────────────────────────────────────────────────
const props = defineProps<{ show: boolean }>();
const emit = defineEmits<{ (e: "close"): void; (e: "imported", count: number): void }>();

const toast = useToast();

// ── Estado General ─────────────────────────────────────────────────────────
const step = ref(1);
const stepTitles = ["Archivo & Hoja", "Mapeo de Columnas", "Confirmación"];

// ── Paso 1: Archivo ────────────────────────────────────────────────────────
const fileInputRef = ref<HTMLInputElement | null>(null);
const file = ref<File | null>(null);
const isDragging = ref(false);
const workbook = ref<XLSX.WorkBook | null>(null);
const sheets = ref<string[]>([]);
const selectedSheet = ref("");
const excelHeaders = ref<string[]>([]);

const onDrop = (e: DragEvent) => {
  isDragging.value = false;
  const dropped = e.dataTransfer?.files?.[0];
  if (dropped) loadFile(dropped);
};

const onFileSelected = (e: Event) => {
  const target = e.target as HTMLInputElement;
  const selected = target.files?.[0];
  if (selected) loadFile(selected);
};

const loadFile = (f: File) => {
  const validTypes = ["application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", "application/vnd.ms-excel"];
  const validExt = /\.(xlsx|xls)$/i.test(f.name);
  if (!validTypes.includes(f.type) && !validExt) {
    toast.error("Solo se aceptan archivos Excel (.xlsx, .xls).");
    return;
  }

  file.value = f;
  const reader = new FileReader();
  reader.onload = (ev) => {
    try {
      const data = new Uint8Array(ev.target?.result as ArrayBuffer);
      workbook.value = XLSX.read(data, { type: "array" });
      sheets.value = workbook.value.SheetNames;

      if (sheets.value.length === 1) {
        selectedSheet.value = sheets.value[0];
      } else {
        // Auto-seleccionar hoja con nombre relevante si existe
        const auto = sheets.value.find((s) => /siembra|campo|data|datos/i.test(s));
        selectedSheet.value = auto ?? sheets.value[0];
      }
    } catch {
      toast.error("No se pudo leer el archivo. Verifique que sea un Excel válido.");
      resetState();
    }
  };
  reader.readAsArrayBuffer(f);
};

// Cuando cambia la hoja seleccionada → extraer headers y hacer auto-mapeo
watch(selectedSheet, (sheet) => {
  if (!sheet || !workbook.value) return;
  const ws = workbook.value.Sheets[sheet];
  if (!ws) return;

  const rows = XLSX.utils.sheet_to_json<string[]>(ws, { header: 1 });
  if (!rows.length) {
    excelHeaders.value = [];
    return;
  }
  excelHeaders.value = (rows[0] as any[]).map((h: any) => String(h ?? "").trim()).filter(Boolean);

  applyAutoMapping();
});

// ── Paso 2: Mapeo ──────────────────────────────────────────────────────────
interface FieldDef {
  key: string;
  label: string;
  required: boolean;
  keywords: string[];
}

const mappingConfig: FieldDef[] = [
  { key: "vrdad", label: "Nombre de la Variedad", required: true, keywords: ["variedad", "vrdad", "clon", "variety", "clone"] },
  { key: "id_pr", label: "ID Proyecto", required: false, keywords: ["id_pr", "id proyecto", "proyecto", "project", "id proy"] },
  { key: "id_crcter", label: "ID Carácter", required: true, keywords: ["id_crcter", "caracter", "carácter", "character", "crcter", "id car"] },
  { key: "ingnio", label: "Ingenio", required: false, keywords: ["ingenio", "ingnio", "mill", "ing"] },
  { key: "hcnda", label: "Hacienda", required: false, keywords: ["hacienda", "hcnda", "farm", "hda"] },
  { key: "lte", label: "Lote / Suerte", required: false, keywords: ["lote", "lte", "suerte", "lot", "lte"] },
  { key: "plot", label: "Parcela / Plot", required: false, keywords: ["plot", "parcela", "prcla", "parcel", "numero parcela"] },
  { key: "fcha_smbra", label: "Fecha de Siembra", required: true, keywords: ["siembra", "fcha_smbra", "fecha siembra", "planting", "smbra", "fecha_siembra"] },
  { key: "fcha_crte", label: "Fecha de Corte", required: false, keywords: ["corte", "fcha_crte", "fecha corte", "cutting", "fecha_corte"] },
  { key: "edad_actual", label: "Edad Actual", required: false, keywords: ["edad", "age", "edad_actual"] },
  { key: "crte", label: "Número de Corte", required: false, keywords: ["num corte", "n corte", "crte", "numero corte", "nro corte", "cut"] },
  {
    key: "orgen_bnco_grmplsma",
    label: "Origen Banco Germoplasma",
    required: false,
    keywords: ["origen", "banco", "germoplasma", "origin", "orgen", "grmplsma"]
  },
  { key: "tpo_flrcion", label: "Tipo Floración", required: false, keywords: ["floracion", "floración", "tpo_flrcion", "tipo flor", "tipo floracion"] },
  { key: "vivero", label: "Vivero", required: false, keywords: ["vivero", "nursery", "id vivero"] },
  { key: "grpo", label: "Grupo / Carácter (apoyo visual)", required: false, keywords: ["grupo", "group", "grpo"] },
  { key: "vivero_en_campo", label: "Vivero en Campo (Booleano)", required: false, keywords: ["campo", "en campo", "sembrado", "in field", "vivero en campo"] },
  { key: "observacuines", label: "Observaciones", required: false, keywords: ["observacion", "observacuines", "nota", "note", "comment", "obs"] },
  { key: "id_carga", label: "ID Carga", required: false, keywords: ["id carga", "id_carga", "carga", "load id", "idcarga"] },
  { key: "temporada", label: "Temporada / Año", required: false, keywords: ["temporada", "año", "season", "year", "anio"] }
];

// mapping reactivo: { fieldKey: excelColumnName }
const mapping = ref<Record<string, string>>(Object.fromEntries(mappingConfig.map((f) => [f.key, ""])));

/**
 * Normaliza texto: minúsculas + elimina tildes para comparación flexible.
 */
const normalize = (s: string) =>
  s
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .trim();

/**
 * Auto-mapeo inteligente: recorre cada campo y busca el primer header
 * que contenga alguno de los keywords del campo.
 */
const applyAutoMapping = () => {
  const headers = excelHeaders.value;
  const newMapping: Record<string, string> = Object.fromEntries(mappingConfig.map((f) => [f.key, ""]));
  const usedHeaders = new Set<string>();

  for (const fieldDef of mappingConfig) {
    for (const header of headers) {
      if (usedHeaders.has(header)) continue;
      const normHeader = normalize(header);
      const matched = fieldDef.keywords.some((kw) => normHeader.includes(normalize(kw)));
      if (matched) {
        newMapping[fieldDef.key] = header;
        usedHeaders.add(header);
        break;
      }
    }
  }

  mapping.value = newMapping;
};

/**
 * Devuelve las columnas disponibles para un campo dado,
 * excluyendo las ya asignadas en OTROS campos (regla de exclusión).
 */
const availableColumnsFor = (fieldKey: string): string[] => {
  const assignedElsewhere = new Set(
    Object.entries(mapping.value)
      .filter(([k, v]) => k !== fieldKey && v !== "")
      .map(([, v]) => v)
  );
  return excelHeaders.value.filter((h) => !assignedElsewhere.has(h));
};

// Campos requeridos que aún no están mapeados
const missingRequired = computed(() => mappingConfig.filter((f) => f.required && !mapping.value[f.key]).map((f) => f.label));

// Cantidad de campos mapeados
const assignedCount = computed(() => Object.values(mapping.value).filter((v) => v !== "").length);

// ── Validación por paso para habilitar "Siguiente" ─────────────────────────
const canProceed = computed(() => {
  if (step.value === 1) return !!file.value && !!selectedSheet.value;
  if (step.value === 2) return missingRequired.value.length === 0;
  return false;
});

const nextStep = () => {
  if (canProceed.value && step.value < 3) step.value++;
};

// ── Paso 3: Importación ────────────────────────────────────────────────────
const isSubmitting = ref(false);

const submitImport = async () => {
  if (!file.value || !selectedSheet.value || missingRequired.value.length > 0) return;

  isSubmitting.value = true;

  const formData = new FormData();
  formData.append("file", file.value);
  formData.append("sheet_name", selectedSheet.value);
  formData.append("mapping", JSON.stringify(mapping.value));

  const [response, error] = await siembracampoServices.importarSiembraCampo(formData);

  isSubmitting.value = false;

  if (error) {
    const msg = (error as any)?.response?.data?.message || "Ocurrió un error al importar los datos. Revise el archivo e intente de nuevo.";
    toast.error(msg);
    return;
  }

  const count: number = response?.data?.count ?? 0;
  toast.success(`✅ ${count} registros importados correctamente a Siembra Campo.`);
  emit("imported", count);
  close();
};

// ── Reset & Close ──────────────────────────────────────────────────────────
const resetState = () => {
  step.value = 1;
  file.value = null;
  workbook.value = null;
  sheets.value = [];
  selectedSheet.value = "";
  excelHeaders.value = [];
  mapping.value = Object.fromEntries(mappingConfig.map((f) => [f.key, ""]));
  isSubmitting.value = false;
  if (fileInputRef.value) fileInputRef.value.value = "";
};

const close = () => {
  resetState();
  emit("close");
};

// Reset cuando el modal se vuelve a abrir
watch(
  () => props.show,
  (val) => {
    if (val) resetState();
  }
);
</script>

