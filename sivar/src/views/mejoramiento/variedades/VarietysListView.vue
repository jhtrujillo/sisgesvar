<!-- Vista para mostrar en una lista los procesos que han sido completados -->
<template>
  <div class="w-full flex-col pt-5 grid place-content-center">
    <div class="mb-4">
      <BaseButton variant="violet" size="sm" :to="{ name: 'variedades.show' }"> Volver </BaseButton>
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
