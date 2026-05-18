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
          :to="{ name: 'mejoramiento.show' }"
        >
          Volver</router-link
        >
      </button>
    </div>
    <h1 class="text-center font-bold text-4xl mb-6 text-violet-800">Floración</h1>
    <div class="overflow-hidden">
      <TableComponent
        :rows="floweringListsStore.FloweringList"
        :have-search="true"
        :have-button-excel="true"
        :allow-hide-columns="true"
        name-excel="flowering"
        :columns="conlumnsInfo"
      ></TableComponent>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from "vue";
import { useFloweringStore } from "@/stores/flowering";
import TableComponent from "../../../components/app-table/TableComponent.vue";
import type { Column } from "../../../components/app-table/models";

const floweringListsStore = useFloweringStore();

onMounted(async () => {
  console.log("carga los flowering");
  await floweringListsStore.getFlowering();
  console.log(floweringListsStore.FloweringList);
});

const conlumnsInfo: Array<Column> = [
  {
    keyName: "id_flrcion",
    text: "Id"
  },
  {
    keyName: "hcnda",
    text: "Hacienda"
  },
  {
    keyName: "fcha",
    text: "Fecha"
  },
  {
    keyName: "hra",
    text: "Hora"
  },
  {
    keyName: "lte",
    text: "Lote"
  },
  {
    keyName: "prcla",
    text: "Parcela"
  },
  {
    keyName: "srco",
    text: "Surco"
  },
  {
    keyName: "vrdad",
    text: "Variedad"
  },
  {
    keyName: "flrcion",
    text: "Floración"
  },
  {
    keyName: "grpo",
    text: "Grupo"
  },
  {
    keyName: "polen",
    text: "Polen"
  },
  {
    keyName: "grnos_vbles1",
    text: "Granos Viables 1"
  },
  {
    keyName: "ttal_grnos1",
    text: "Total Granos 1"
  },
  {
    keyName: "grnos_vbles2",
    text: "Granos Viables 2"
  },
  {
    keyName: "ttal_grnos2",
    text: "Total Granos 2"
  },
  {
    keyName: "grnos_vbles3",
    text: "Granos Viables 3"
  },
  {
    keyName: "ttal_grnos3",
    text: "Total Granos 3"
  },
  {
    keyName: "grnos_vbles4",
    text: "Granos Viables 4"
  },
  {
    keyName: "ttal_grnos4",
    text: "Total Granos 4"
  },
  {
    keyName: "grnos_vbles5",
    text: "Granos Viables 5"
  },
  {
    keyName: "ttal_grnos5",
    text: "Total Granos 5"
  },
  {
    keyName: "sxo",
    text: "Sexo"
  },
  {
    keyName: "slcciondo",
    text: "Seleccionado"
  },
  {
    keyName: "cmbio_sxo",
    text: "Cambio de Sexo"
  },
  {
    keyName: "nm_prycto",
    text: "Nombre de proyecto"
  },
  {
    keyName: "nmbre_crcter",
    text: "Caracter"
  },
  {
    keyName: "vivero",
    text: "Vivero"
  },
  {
    keyName: "obsrvcn",
    text: "Observación"
  },
  {
    keyName: "usuario",
    text: "Usuario que Editó"
  },
  {
    keyName: "ingnio",
    text: "Ingenio"
  },
  {
    keyName: "id_smbra_cmpo",
    text: "Id Siembra de Campo"
  }
];
</script>
<!-- <template>
    <div class="w-full flex-col pt-2 grid place-content-center">
        <div>
            <button type="button"
                class="block mb-4 px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark-mode:bg-transparent dark-mode:hover:bg-gray-600 dark-mode:focus:bg-gray-600 dark-mode:focus:text-white dark-mode:hover:text-white dark-mode:text-gray-200 md:mt-0  focus:outline-none focus:shadow-outline"
                href="#">
                <router-link
                    class="text-violet-800 group border border-violet-800 flex items-center px-2 py-2 font-medium rounded-md pt-1 pb-1 pr-2 pl-2 hover:text-white hover:bg-violet-800"
                    :to="{
                        name: 'variedades.show'
                    }">Volver</router-link>
            </button>
        </div>
        <h1 class="text-center font-bold text-4xl mb-4 text-violet-800">Floración</h1>

        <div class=" w-full overflow-x-auto shadow-md sm:rounded-lg">
            <template v-if="filteredFloweringList.length === 0">
                <p class="text-center text-xl font-bold text-gray-500">No hay datos.</p>
            </template>
            <table v-else class="w-full  text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-center">
                    <tr class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
                        <th class="p-3">Id</th>
                        <th class="p-3">Hacienda</th>
                        <th class="p-3">Fecha</th>
                        <th class="p-3">Hora</th>
                        <th class="p-3">Lote</th>
                        <th class="p-3">Parcela</th>
                        <th class="p-3">Surco</th>
                        <th class="p-3">Variedad</th>
                        <th class="p-3">Floración</th>
                        <th class="p-3">Grupo</th>
                        <th class="p-3">Polen</th>
                        <th class="p-3">Granos Viables</th>
                        <th class="p-3">Total Granos 1</th>
                        <th class="p-3">Granos Viables 2</th>
                        <th class="p-3">Total Granos 2</th>
                        <th class="p-3">Granos Viables 3</th>
                        <th class="p-3">Total Granos 3</th>
                        <th class="p-3">Granos Viables 4</th>
                        <th class="p-3">Total Granos 4</th>
                        <th class="p-3">Granos Viables 5</th>
                        <th class="p-3">Total Granos 5</th>
                        <th class="p-3">Sexo</th>
                        <th class="p-3">Seleccionado</th>
                        <th class="p-3">Cambio de Sexo</th>
                        <th class="p-3">Nombre de proyecto</th>
                        <th class="p-3">Caracter</th>
                        <th class="p-3">Vivero</th>
                        <th class="p-3">Observación</th>
                        <th class="p-3">Usuario que Editó</th>
                        <th class="p-3">Ingenio</th>
                        <th class="p-3">Id Siembra de Campo</th>
                    </tr>
                </thead>
                <tbody v-for="flowering in filteredFloweringList" :key="flowering.id_flrcion" class="text-center">
                    <tr class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
                        <td class="p-3">
                            {{ flowering.id_flrcion }}
                        </td>
                        <td class="p-3">
                            {{ flowering.hcnda }}
                        </td>
                        <td class="p-3">
                            {{ flowering.fcha }}
                        </td>
                        <td class="p-3">
                            {{ flowering.hra }}
                        </td>
                        <td class="p-3">
                            {{ flowering.lte }}
                        </td>
                        <td class="p-3">
                            {{ flowering.prcla }}
                        </td>
                        <td class="p-3">
                            {{ flowering.srco }}
                        </td>
                        <td class="p-3">
                            {{ flowering.vrdad }}
                        </td>
                        <td class="p-3">
                            {{ flowering.flrcion }}
                        </td>
                        <td class="p-3">
                            {{ flowering.grpo }}
                        </td>
                        <td class="p-3">
                            {{ flowering.polen }}
                        </td>
                        <td class="p-3">
                            {{ flowering.grnos_vbles1 }}
                        </td>
                        <td class="p-3">
                            {{ flowering.ttal_grnos1 }}
                        </td>
                        <td class="p-3">
                            {{ flowering.grnos_vbles2 }}
                        </td>
                        <td class="p-3">
                            {{ flowering.ttal_grnos2 }}
                        </td>
                        <td class="p-3">
                            {{ flowering.grnos_vbles3 }}
                        </td>
                        <td class="p-3">
                            {{ flowering.ttal_grnos3 }}
                        </td>
                        <td class="p-3">
                            {{ flowering.grnos_vbles4 }}
                        </td>
                        <td class="p-3">
                            {{ flowering.ttal_grnos4 }}
                        </td>
                        <td class="p-3">
                            {{ flowering.grnos_vbles5 }}
                        </td>
                        <td class="p-3">
                            {{ flowering.ttal_grnos5 }}
                        </td>
                        <td class="p-3">
                            {{ flowering.sxo }}
                        </td>
                        <td class="p-3">
                            {{ flowering.slcciondo }}
                        </td>
                        <td class="p-3">
                            {{ flowering.cmbio_sxo }}
                        </td>
                        <td class="p-3">
                            {{ flowering.nm_prycto }}
                        </td>
                        <td class="p-3">
                            {{ flowering.nmbre_crcter }}
                        </td>
                        <td class="p-3">
                            {{ flowering.vivero }}
                        </td>
                        <td class="p-3">
                            {{ flowering.obsrvcn }}
                        </td>
                        <td class="p-3">
                            {{ flowering.usuario }}
                        </td>
                        <td class="p-3">
                            {{ flowering.ingnio }}
                        </td>
                        <td class="p-3">
                            {{ flowering.id_smbra_cmpo }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <v-pagination v-model="currentPage" :total-visible="7" :total-pages="totalPages"
                @input="paginate"></v-pagination>
        </div>
    </div>
</template>
<script setup lang="ts">
import { ref, watchEffect, onMounted } from "vue";
import type { FLowering } from "../services/types";
import { useFloweringStore } from "@/stores/flowering";
import { useUserStore } from "@/stores/user";

const currentPage = ref(1);
const itemsPerPage = 10;
const totalPages = ref(0);
const filteredFloweringList = ref<FLowering[]>([]);

const userStore = useUserStore();
const floweringListsStore = useFloweringStore();

onMounted(async () => {
    console.log("carga los flowering");
    await floweringListsStore.getFlowering();
    console.log(floweringListsStore.FloweringList);
});

// Lógica para paginar los datos
function paginate(newPage: number) {
    currentPage.value = newPage;
    const start = (newPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    filteredFloweringList.value = floweringListsStore.FloweringList.slice(start, end);
    totalPages.value = calculateTotalPages();
}

// Obtiene el número total de páginas
function calculateTotalPages() {
    return Math.ceil(floweringListsStore.FloweringList.length / itemsPerPage);
}

// Observa los cambios en la lista de floraciones para reflejarlos en la paginación
onMounted(() => {
    watchEffect(() => {
        filteredFloweringList.value = floweringListsStore.FloweringList.slice(0, itemsPerPage);
        totalPages.value = calculateTotalPages();
    });
});
</script> -->
../stores/varietys
