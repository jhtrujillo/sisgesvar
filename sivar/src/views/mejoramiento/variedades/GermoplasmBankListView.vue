<!-- Vista para mostrar en una lista los procesos que han sido completados -->
<template>
  <div class="w-full flex-col pt-5 grid place-content-center">
    <div>
      <button
        type="button"
        class="block mb-4 px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark-mode:bg-transparent dark-mode:hover:bg-gray-600 dark-mode:focus:bg-gray-600 dark-mode:focus:text-white dark-mode:hover:text-white dark-mode:text-gray-200 md:mt-0 focus:outline-none focus:shadow-outline"
      >
        <router-link
          class="text-violet-800 group border border-violet-800 flex items-center px-2 py-2 font-medium rounded-md pt-1 pb-1 pr-2 pl-2 hover:text-white hover:bg-violet-800"
          :to="{ name: 'variedades.show' }"
        >
          Volver
        </router-link>
      </button>
    </div>
    <h1 class="text-center font-bold text-4xl mb-6 text-violet-800">Banco de Germoplasma</h1>
    <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
      <div class="flex flex-col sm:flex-row justify-between items-center w-full px-4 py-2">
        <div class="mb-4 sm:mb-0 sm:w-1/3">
          <input
            type="text"
            name="buscar"
            id="buscar"
            placeholder="Buscar"
            class="block w-full rounded-md border-0 px-2 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
            v-model="searchText"
            @input="updateFilteredGermoplasmaBank"
          />
        </div>
        <div class="flex space-x-2">
          <button
            type="button"
            class="inline-flex items-center rounded-md bg-indigo-600 px-3.5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            @click="downloadExcel"
          >
            <i class="bi bi-file-earmark-excel-fill"></i> Excel
          </button>
        </div>
      </div>
      <div class="mt-8 overflow-x-auto">
        <table class="table-auto w-full divide-y divide-gray-300">
          <!-- Encabezados de las columnas -->
          <thead class="bg-gray-50">
            <tr>
              <template v-for="column in tableColumns" :key="column.key">
                <th v-if="columnsToShow.includes(column.key)" class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                  {{ column.text }}
                </th>
              </template>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            <!-- Renderizar cada jornal en una fila de la tabla -->
            <tr v-for="germoplasma in GermoplasmBankStore.germplasm" :key="getGermoplasmaKey(germoplasma)">
              <!-- Renderizar los datos correspondientes a cada columna -->
              <template v-for="column in tableColumns" :key="column.key">
                <td v-if="columnsToShow.includes(column.key)" class="px-4 py-3 text-sm font-medium text-gray-800 whitespace-nowrap">
                  {{ germoplasma[column.key as keyof typeof germoplasma] }}
                </td>
              </template>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- Controles de paginación -->
      <div v-if="GermoplasmBankStore.totalPages > 1" class="flex items-center justify-between m-2 px-4 py-3 sm:px-6">
        <button
          class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
          @click="firstPage()"
          :disabled="GermoplasmBankStore.currentPage === 1"
        >
          Primera
        </button>
        <button
          class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
          @click="previousPage()"
          :disabled="GermoplasmBankStore.currentPage === 1"
        >
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5" />
          </svg>
          Anterior
        </button>
        <button>
          <span class="font-medium">{{ GermoplasmBankStore.currentPage }}</span> /
          <span class="font-medium">{{ GermoplasmBankStore.totalPages }}</span>
        </button>
        <button
          class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
          @click="nextPage()"
          :disabled="GermoplasmBankStore.currentPage === GermoplasmBankStore.totalPages"
        >
          Siguiente
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 4.5l7.5 7.5-7.5 7.5m-6-15l7.5 7.5-7.5 7.5" />
          </svg>
        </button>
        <button
          class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
          @click="lastPage()"
          :disabled="GermoplasmBankStore.currentPage === GermoplasmBankStore.totalPages"
        >
          Última
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useGermoplasmBankStore } from "@/stores/germoplasm";
import * as XLSX from "xlsx";
import { ref, computed, onMounted } from "vue";

const GermoplasmBankStore = useGermoplasmBankStore();
const searchText = ref("");
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
const filteredJornalesList = computed(() => {
  const normalizedSearchText = searchText.value.trim().toLowerCase();
  return GermoplasmBankStore.germplasm.filter((germoplasma) =>
    Object.values(germoplasma).some((value) => typeof value === "string" && value.toLowerCase().includes(normalizedSearchText))
  );
});
const updateFilteredGermoplasmaBank = () => {
  GermoplasmBankStore.germplasm = filteredJornalesList.value;
};
// Función para obtener la clave válida para el jornal en el v-for
const getGermoplasmaKey = (germoplasma: any) => germoplasma.ensayo.toString(); // Asegurar que la clave sea un string válido
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
    key: "variedad",
    text: "Variedad"
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
  }
];
const columnsToShow = ref([
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
  "variedad",
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
  "obsrvcnes"
]);

onMounted(async () => {
  await GermoplasmBankStore.getGermoplasmBank();
});
</script>
