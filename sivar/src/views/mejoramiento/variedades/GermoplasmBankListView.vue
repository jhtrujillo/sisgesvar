<template>
  <div class="space-y-4 pb-20">
    <div class="flex justify-start mb-4">
      <BaseButton variant="secondary" size="sm" :to="{ name: 'variedades.show' }">
        Volver a Variedades
      </BaseButton>
    </div>

    <!-- Header Card -->
    <div class="bg-white rounded-lg shadow-xl border border-gray-100 p-6 sm:px-10 mb-8">
      <div class="sm:flex sm:items-center sm:justify-between">
        <div class="sm:flex-auto">
          <h1 class="text-3xl font-black text-emerald-600">Banco de Germoplasma</h1>
          <p class="mt-2 text-sm text-gray-500">
            Explora la base de datos histórica de ensayos, fenotipos y métricas agroindustriales de todas las variedades.
          </p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none flex space-x-3 items-center">
          
          <!-- Column Selector Dropdown -->
          <div class="relative group">
            <button class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              Columnas ({{ columnsToShow.length }})
            </button>
            <div class="absolute right-0 z-50 mt-2 w-64 origin-top-right rounded-md bg-white shadow-2xl ring-1 ring-black ring-opacity-5 hidden group-hover:block max-h-96 overflow-y-auto">
              <div class="p-4 space-y-2">
                <label v-for="col in tableColumns" :key="'toggle-'+col.key" class="flex items-center space-x-3 cursor-pointer hover:bg-gray-50 p-1 rounded">
                  <input type="checkbox" :value="col.key" v-model="columnsToShow" class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" />
                  <span class="text-sm text-gray-700">{{ col.text }}</span>
                </label>
              </div>
            </div>
          </div>

          <button @click="downloadExcel" class="inline-flex items-center justify-center rounded-md border border-transparent bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Exportar Excel
          </button>
        </div>
      </div>

      <div class="mt-6 relative max-w-md">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
          <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
          </svg>
        </div>
        <input
          type="text"
          placeholder="Buscar variedad, ensayo o cruce..."
          class="block w-full rounded-md border-0 py-2.5 pl-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6"
          v-model="searchText"
          
        />
      </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-lg shadow-xl border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50 sticky top-0 z-20">
            <tr>
              <template v-for="column in tableColumns" :key="column.key">
                <th v-if="columnsToShow.includes(column.key)" 
                    :class="['px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap', 
                             column.key === 'variedad' ? 'sticky left-0 bg-gray-100 z-30 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]' : '']">
                  {{ column.text }}
                </th>
              </template>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            <tr v-for="germoplasma in GermoplasmBankStore.germplasm" :key="getGermoplasmaKey(germoplasma)" class="hover:bg-emerald-50/50 transition-colors group">
              <template v-for="column in tableColumns" :key="column.key">
                <td v-if="columnsToShow.includes(column.key)" 
                    :class="['px-4 py-3 text-sm whitespace-nowrap', 
                             column.key === 'variedad' ? 'sticky left-0 bg-white font-bold text-violet-700 z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] group-hover:bg-emerald-50/50' : 'text-gray-700']">
                  
                  <span v-if="column.key.includes('roya') && column.key.endsWith('_r') || column.key.includes('mosaico_r')" 
                        :class="{'px-2 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800': germoplasma[column.key], 'text-gray-700': !germoplasma[column.key]}">
                    {{ germoplasma[column.key] || '-' }}
                  </span>
                  <span v-else-if="column.key.includes('roya') && column.key.endsWith('_s') || column.key.includes('mosaico_s') || column.key.includes('mosaico_p')" 
                        :class="{'px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800': germoplasma[column.key], 'text-gray-700': !germoplasma[column.key]}">
                    {{ germoplasma[column.key] || '-' }}
                  </span>
                  <span v-else-if="column.key === 'madre' || column.key === 'padre'" class="text-emerald-700 font-medium">
                    {{ germoplasma[column.key] }}
                  </span>
                  <span v-else-if="column.key === 'sacarosa' || column.key === 'tch'" class="font-semibold text-gray-900">
                    {{ germoplasma[column.key] }}
                  </span>
                  <span v-else>
                    {{ germoplasma[column.key] }}
                  </span>
                </td>
              </template>
            </tr>
            <tr v-if="GermoplasmBankStore.germplasm.length === 0">
              <td :colspan="columnsToShow.length" class="px-4 py-10 text-center text-gray-500">
                No se encontraron registros en el banco de germoplasma.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="GermoplasmBankStore.totalPages > 1" class="flex items-center justify-between border-t border-gray-200 bg-gray-50 px-4 py-3 sm:px-6">
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
          <div>
            <p class="text-sm text-gray-700">
              Página <span class="font-semibold">{{ GermoplasmBankStore.currentPage }}</span> de <span class="font-semibold">{{ GermoplasmBankStore.totalPages }}</span>
            </p>
          </div>
          <div>
            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
              <button @click="firstPage()" :disabled="GermoplasmBankStore.currentPage === 1" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-100 focus:z-20 focus:outline-offset-0 disabled:opacity-50">
                <span class="sr-only">Primera</span>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M15.79 14.77a.75.75 0 01-1.06.02l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 111.04 1.08L11.832 10l3.938 3.71a.75.75 0 01.02 1.06zm-6 0a.75.75 0 01-1.06.02l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 111.04 1.08L5.832 10l3.938 3.71a.75.75 0 01.02 1.06z" clip-rule="evenodd" />
                </svg>
              </button>
              <button @click="previousPage()" :disabled="GermoplasmBankStore.currentPage === 1" class="relative inline-flex items-center px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-100 focus:z-20 focus:outline-offset-0 disabled:opacity-50">
                <span class="sr-only">Anterior</span>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                </svg>
              </button>
              <button @click="nextPage()" :disabled="GermoplasmBankStore.currentPage === GermoplasmBankStore.totalPages" class="relative inline-flex items-center px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-100 focus:z-20 focus:outline-offset-0 disabled:opacity-50">
                <span class="sr-only">Siguiente</span>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                </svg>
              </button>
              <button @click="lastPage()" :disabled="GermoplasmBankStore.currentPage === GermoplasmBankStore.totalPages" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-100 focus:z-20 focus:outline-offset-0 disabled:opacity-50">
                <span class="sr-only">Última</span>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M10.21 14.77a.75.75 0 01.02-1.06L14.168 10 10.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02zM4.21 14.77a.75.75 0 01.02-1.06L8.168 10 4.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                </svg>
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useGermoplasmBankStore } from "@/stores/germoplasm";
import * as XLSX from "xlsx";
import { ref, computed, onMounted, watch } from "vue";

const GermoplasmBankStore = useGermoplasmBankStore();
const searchText = ref("");

let searchTimeout: any;
watch(searchText, (newVal) => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    GermoplasmBankStore.currentPage = 1;
    GermoplasmBankStore.getGermoplasmBank(newVal);
  }, 300);
});
const firstPage = async () => {
  if (GermoplasmBankStore.currentPage > 1) {
    GermoplasmBankStore.currentPage = 1;
    await GermoplasmBankStore.setCurrentPage(GermoplasmBankStore.currentPage);
  }
};
const previousPage = async () => {
  if (GermoplasmBankStore.currentPage >= 1) {
    GermoplasmBankStore.currentPage--;
    await GermoplasmBankStore.setCurrentPage(GermoplasmBankStore.currentPage);
  }
};
const nextPage = async () => {
  if (GermoplasmBankStore.currentPage < GermoplasmBankStore.totalPages) {
    GermoplasmBankStore.currentPage = GermoplasmBankStore.currentPage + 1;
    await GermoplasmBankStore.setCurrentPage(GermoplasmBankStore.currentPage);
  }
};
const lastPage = async () => {
  if (GermoplasmBankStore.currentPage < GermoplasmBankStore.totalPages) {
    GermoplasmBankStore.currentPage = GermoplasmBankStore.totalPages;
    await GermoplasmBankStore.setCurrentPage(GermoplasmBankStore.currentPage);
  }
};

// Filtrar la lista de jornales según el texto de búsqueda
// Función para obtener la clave válida para el jornal en el v-for
const getGermoplasmaKey = (germoplasma: any) => germoplasma?.ensayo?.toString() || Math.random().toString(); // Asegurar que la clave sea un string válido
// Función para generar el archivo Excel
// Función para generar el archivo Excel con todos los datos
const downloadExcel = () => {
  const allData = GermoplasmBankStore.germplasm.map((germoplasma) =>
    Object.fromEntries(Object.entries(germoplasma).filter(([key]) => columnsToShow.value.includes(key)))
  );
  const worksheet = XLSX.utils.json_to_sheet(allData);
  const workbook = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(workbook, worksheet, "Histórico Banco de Germoplasma");
  XLSX.writeFile(workbook, "historico_banco_germoplasma.xlsx");
};

const tableColumns = [
  {
    key: "variedad",
    text: "Variedad"
  },
{
    key: "ensayo",
    text: "Ensayo"
  },
  {
    key: "sitio_seleccion",
    text: "Sitio de selección"
  },
  {
    key: "estado_seleccion",
    text: "Estado de selección"
  },
  {
    key: "serie",
    text: "Serie"
  },
  {
    key: "ingenio",
    text: "Ingenio"
  },
  {
    key: "hacienda",
    text: "Hacienda"
  },
  {
    key: "suerte",
    text: "Suerte"
  },
  {
    key: "za",
    text: "ZA"
  },
  {
    key: "GS",
    text: "gs"
  },
  {
    key: "gh",
    text: "GH"
  },
  {
    key: "origen",
    text: "Origen"
  },
  {
    key: "area",
    text: "Area"
  },
  
  {
    key: "madre",
    text: "Madre"
  },
  {
    key: "padre",
    text: "Padre"
  },
  {
    key: "grupo_snp",
    text: "Grupo SNP"
  },
  {
    key: "grupo_fenotipico",
    text: "Grupo fenotípico"
  },
  {
    key: "spp_hibrido",
    text: "SPP Hibrido"
  },
  {
    key: "procedencia",
    text: "Procedencia"
  },
  {
    key: "estacion",
    text: "Estacion"
  },
  {
    key: "especie",
    text: "Especie"
  },
  {
    key: "rep",
    text: "Rep"
  },
  {
    key: "block",
    text: "Block"
  },
  {
    key: "plot",
    text: "Plot"
  },
  {
    key: "entry",
    text: "Entry"
  },
  {
    key: "col",
    text: "Col"
  },
  {
    key: "corte",
    text: "Corte"
  },
  {
    key: "tllo",
    text: "Tallo"
  },
  {
    key: "fs",
    text: "Fecha siembra"
  },
  {
    key: "fc",
    text: "Fecha corte"
  },
  {
    key: "dds",
    text: "DDS"
  },
  {
    key: "fcha_eval_id",
    text: "Fecha Eval ID"
  },
  {
    key: "fcha_eval",
    text: "Fecha Eval"
  },
  {
    key: "tubos",
    text: "Tubos"
  },
  {
    key: "spad",
    text: "Spad"
  },
  {
    key: "raiz_nf",
    text: "Raiz nf"
  },
  {
    key: "altura_planta",
    text: "Altura planta"
  },
  {
    key: "numero_entrenudos",
    text: "Número entrenudos"
  },
  {
    key: "longitud_entrenudo",
    text: "Longitud entrenudo"
  },
  {
    key: "longitud_cogollo",
    text: "Longitud cogollo"
  },
  {
    key: "diametro_1_3",
    text: "Diametro 1_3"
  },
  {
    key: "diametro_2_3",
    text: "Diametro 2_3"
  },
  {
    key: "diametro_3_3",
    text: "Diametro 3_3"
  },
  {
    key: "diametro_tallo",
    text: "Diametro tallo"
  },
  {
    key: "longitud_hoja",
    text: "Longitud hoja"
  },
  {
    key: "ancho_hoja_1_3",
    text: "Ancho hoja 1_3"
  },
  {
    key: "ancho_hoja_2_3",
    text: "Ancho hoja 2_3"
  },
  {
    key: "ancho_hoja_3_3",
    text: "Ancho hoja 3_3"
  },
  {
    key: "poblacion_1m",
    text: "Poblacion 1m"
  },
  {
    key: "floracion_tllos",
    text: "Floracion tallos"
  },
  {
    key: "floracion_p",
    text: "Floracion p"
  },
  {
    key: "aspecto_planta",
    text: "Aspecto planta"
  },
  {
    key: "aspecto_seleccion",
    text: "Aspecto seleccion"
  },
  {
    key: "pelusa",
    text: "Pelusa"
  },
  {
    key: "volcamiento",
    text: "Volcamiento"
  },
  {
    key: "deshoje",
    text: "Deshoje"
  },
  {
    key: "materia_seca",
    text: "Materia seca"
  },
  {
    key: "humedad",
    text: "Humedad"
  },
  {
    key: "sacarosa",
    text: "sacarosa"
  },
  {
    key: "brix",
    text: "Brix"
  },
  {
    key: "fibra",
    text: "Fibra"
  },
  {
    key: "no_sacarosa",
    text: "No sacarosa"
  },
  {
    key: "pureza",
    text: "Pureza"
  },
  {
    key: "are",
    text: "Are"
  },
  {
    key: "reductores",
    text: "Reductores"
  },
  {
    key: "atr",
    text: "Atr"
  },
  {
    key: "peso",
    text: "Peso"
  },
  {
    key: "tch",
    text: "TCH"
  },
  {
    key: "tah",
    text: "TAH"
  },
  {
    key: "tsh",
    text: "TSH"
  },
  {
    key: "tchm",
    text: "TCHM"
  },
  {
    key: "tahm",
    text: "TACM"
  },
  {
    key: "tshm",
    text: "TSHM"
  },
  {
    key: "roya_cafe_r",
    text: "Roya cafe r"
  },
  {
    key: "roya_cafe_s",
    text: "Roya cafe s"
  },
  {
    key: "roya_naranja_r",
    text: "Roya naranja r"
  },
  {
    key: "roya_naranja_s",
    text: "Roya naranja s"
  },
  {
    key: "mosaico_r",
    text: "Mosaico r"
  },
  {
    key: "mosaico_e",
    text: "Mosaico e"
  },
  {
    key: "mosaico_t",
    text: "Mosaico t"
  },
  {
    key: "mosaico_p",
    text: "Mosaico p"
  },
  {
    key: "carbon_c",
    text: "Carbon c"
  },
  {
    key: "carbon_l",
    text: "Carbon l"
  },
  {
    key: "carbon_t",
    text: "Carbon t"
  },
  {
    key: "carbon_p",
    text: "Carbon p"
  },
  {
    key: "lsdte",
    text: "LSDTE"
  },
  {
    key: "lsdtt",
    text: "LSDTT"
  },
  {
    key: "lsdtv",
    text: "LSDTV"
  },
  {
    key: "lsdt",
    text: "LSDT"
  },
  {
    key: "rsd",
    text: "RSD"
  },
  {
    key: "sclyv",
    text: "SCLYV"
  },
  {
    key: "te",
    text: "TE"
  },
  {
    key: "ed",
    text: "ED"
  },
  {
    key: "eb",
    text: "EB"
  },
  {
    key: "id",
    text: "ID"
  },
  {
    key: "ib",
    text: "IB"
  },
  {
    key: "tallo_evaluados",
    text: "Tallo evaluados"
  },
  {
    key: "tallo_rajados",
    text: "Tallo rajados"
  },
  {
    key: "rajadura_inc",
    text: "Rajadura inc"
  },
  {
    key: "entrenudos_tallo",
    text: "Entrenudos tallo"
  },
  {
    key: "entrenudos_rajados",
    text: "Entrenudos rajados"
  },
  {
    key: "rajadura_sev",
    text: "Rajadura sev"
  },
  {
    key: "hojas_erectas",
    text: "Hojas erectas"
  },
  {
    key: "raices_tallos",
    text: "Raices tallos"
  },
  {
    key: "yemas_protuberantes",
    text: "Yemas protuberantes"
  },
  {
    key: "medula",
    text: "Medula"
  },
  {
    key: "habito_de_crecimiento",
    text: "Habito de crecimiento"
  },
  {
    key: "germinacion",
    text: "Germinación"
  },
  {
    key: "tolerancia_herbicida",
    text: "Tolerancia herbicida"
  },
  {
    key: "raices_adventicias",
    text: "Raices adventicias"
  },
  {
    key: "obsrvcnes",
    text: "Observaciones"
  }];
const columnsToShow = ref([
  "variedad",
"ensayo",
  "sitio_seleccion",
  "estado_seleccion",
  "serie",
  "ingenio",
  "hacienda",
  "suerte",
  "za",
  "GS",
  "gh",
  "origen",
  "area",
  
  "madre",
  "padre",
  "grupo_snp",
  "grupo_fenotipico",
  "spp_hibrido",
  "procedencia",
  "estacion",
  "especie",
  "rep",
  "block",
  "plot",
  "entry",
  "col",
  "corte",
  "tllo",
  "fs",
  "fc",
  "dds",
  "fcha_eval_id",
  "fcha_eval",
  "tubos",
  "spad",
  "raiz_nf",
  "altura_planta",
  "numero_entrenudos",
  "longitud_entrenudo",
  "longitud_cogollo",
  "diametro_1_3",
  "diametro_2_3",
  "diametro_3_3",
  "diametro_tallo",
  "longitud_hoja",
  "ancho_hoja_1_3",
  "ancho_hoja_2_3",
  "ancho_hoja_3_3",
  "poblacion_1m",
  "floracion_tllos",
  "floracion_p",
  "aspecto_planta",
  "aspecto_seleccion",
  "pelusa",
  "volcamiento",
  "deshoje",
  "materia_seca",
  "humedad",
  "sacarosa",
  "brix",
  "fibra",
  "no_sacarosa",
  "pureza",
  "are",
  "reductores",
  "atr",
  "peso",
  "tch",
  "tah",
  "tsh",
  "tchm",
  "tahm",
  "tshm",
  "roya_cafe_r",
  "roya_cafe_s",
  "roya_naranja_r",
  "roya_naranja_s",
  "mosaico_r",
  "mosaico_e",
  "mosaico_t",
  "mosaico_p",
  "carbon_c",
  "carbon_l",
  "carbon_t",
  "carbon_p",
  "lsdte",
  "lsdtt",
  "lsdtv",
  "lsdt",
  "rsd",
  "sclyv",
  "te",
  "ed",
  "eb",
  "id",
  "ib",
  "tallo_evaluados",
  "tallo_rajados",
  "rajadura_inc",
  "entrenudos_tallo",
  "entrenudos_rajados",
  "rajadura_sev",
  "hojas_erectas",
  "raices_tallos",
  "yemas_protuberantes",
  "medula",
  "habito_de_crecimiento",
  "germinacion",
  "tolerancia_herbicida",
  "raices_adventicias",
  "obsrvcnes"]);

onMounted(async () => {
  await GermoplasmBankStore.getGermoplasmBank(searchText.value);
});
</script>
