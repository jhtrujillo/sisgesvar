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
          Volver</router-link
        >
      </button>
    </div>
    <h1 class="text-center font-bold text-4xl mb-6 text-violet-800">Variedades</h1>
    <TableComponent
      :rows="varietysListsStore.VarietysList"
      :have-search="true"
      :have-button-excel="true"
      :allow-hide-columns="true"
      name-excel="varietys"
      :columns="conlumnsInfo"
    ></TableComponent>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from "vue";
import { useVarietysStore } from "@/stores/varietys";
import TableComponent from "../../../components/app-table/TableComponent.vue";
import type { Column } from "../../../components/app-table/models";

const varietysListsStore = useVarietysStore();

onMounted(async () => {
  await varietysListsStore.getVarietys();
});

const conlumnsInfo: Array<Column> = [
  {
    keyName: "nm_vrdad",
    text: "Nombre Variedad"
  },
  {
    keyName: "vrdad_madre",
    text: "Variedad Madre"
  },
  {
    keyName: "vrdad_pdre",
    text: "Variedad Padre"
  },
  {
    keyName: "tpo",
    text: "Tipo"
  }
];
</script>
