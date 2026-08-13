<template>
  <div class="w-full flex-col grid place-content-center">
    <div class="mb-4">
      <BaseButton variant="violet" size="sm" :to="{ name: 'variedades.show' }"> Volver </BaseButton>
    </div>
    <h1 class="text-center font-bold text-4xl mb-6 text-violet-800">Historial Variedad</h1>
    <div class="flex flex-wrap justify-center items-center space-x-4 mb-4 mt-4">
      <div class="flex-none w-48">
        <!-- Etiqueta para la selección de variedad -->
        <label for="variety" class="block text-sm font-medium text-gray-700"> Busca una variedad </label>
        <ComboBoxMultiple
          :data-list="dataListVariedades"
          :column-value="columnValueVariedades"
          :column-to-show="columnToShowVariedades"
          v-model:selectedData="selectedVariety"
        />
      </div>

      <!-- Selector de estados -->
      <div class="flex-none w-48">
        <label for="variety" class="block text-sm font-medium text-gray-700"> Selecciona un Estado </label>
        <select
          id="state"
          name="Estado"
          class="mt-1 focus:ring-sky-500 focus:border-sky-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
          v-model="selectedState"
        >
          <option v-for="state in states" :key="state.id" :value="state.id">
            {{ state.name }}
          </option>
        </select>
      </div>
      <!-- Selector de tipos -->
      <div class="flex-none w-48">
        <label for="type" class="block text-sm font-medium text-gray-700"> Selecciona un Tipo </label>
        <select
          id="type"
          name="Tipo"
          class="mt-1 focus:ring-sky-500 focus:border-sky-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
          v-model="selectedType"
        >
          <option v-for="tipo in types" :key="tipo.id" :value="tipo.id">
            {{ tipo.name }}
          </option>
        </select>
      </div>
      <div class="flex-none w-48 pt-5">
        <BaseButton variant="violet" size="sm" @click="clearFields"> Limpiar Campos </BaseButton>
      </div>
      <div class="overflow-hidden mt-4">
        <TableComponent
          v-if="varietyHistoryStore.varietyHistory.length > 0"
          :rows="varietyHistoryStore.varietyHistory"
          :have-search="true"
          :have-button-excel="true"
          :allow-hide-columns="true"
          name-excel="variety_history"
          :columns="columnsInfo"
        ></TableComponent>
        <p v-else class="text-center font-bold text-xl mb-6 text-violet-800">No hay datos de esta consulta.</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, type Ref } from "vue";
import { useVarietyStore } from "@/stores/variety";
import { useVarietyHistoryStore } from "@/stores/varietyhistory";

import TableComponent from "@/components/app-table/TableComponent.vue";
import ComboBoxMultiple from "@/components/ComboBoxMultiple.vue";
import type { Column } from "@/components/app-table/models";

const varietyStore = useVarietyStore();
const varietyHistoryStore = useVarietyHistoryStore();

// Variables para el filtro
const selectedVariety: Ref<string | null> = ref(null);
const selectedState: Ref<number | null> = ref(null);
const selectedType: Ref<number | null> = ref(null);
const dataListVariedades = varietyStore.Variety;
const columnValueVariedades = "nm_vrdad";
const columnToShowVariedades = "nm_vrdad";
const states = [
  { id: 1, name: "Estado I" },
  { id: 2, name: "Estado II" },
  { id: 3, name: "Estado III" },
  { id: 4, name: "Estado IV" },
  { id: 5, name: "P.Regionales" }
];
const types = [
  { id: 1, name: "Como Madre" },
  { id: 2, name: "Como Padre" },
  { id: 3, name: "Cruzamientos Directos" }
];

// Call API to fetch varieties on component mount
onMounted(async (): Promise<void> => {
  await varietyStore.getVariety();
});

watch([selectedVariety, selectedState, selectedType], async ([selectedVariety, selectedState, selectedType]) => {
  if (selectedVariety && selectedState !== null && selectedType !== null) {
    const variety = selectedVariety;
    console.log(variety);
    const state = selectedState;
    console.log(state);
    const type = selectedType;
    console.log(type);
    await varietyHistoryStore.getVarietyHistory(variety, state, type);
    console.log(varietyHistoryStore.varietyHistory);
  }
});
const columnsInfo: Array<Column> = [
  {
    keyName: "vrdad_mdre",
    text: "Variedad Madre"
  },
  {
    keyName: "vrdad_pdre1",
    text: "Variedad Padre"
  },
  {
    keyName: "ano",
    text: "Año"
  },
  {
    keyName: "pdgree",
    text: "Pedigree"
  },
  {
    keyName: "nm_fmlias",
    text: "Cruzamiento"
  },
  {
    keyName: "plntlas_ttles",
    text: "# Plantulas"
  },
  {
    keyName: "id_stio_estcion",
    text: "Sitio"
  }
];
const clearFields = (): void => {
  selectedVariety.value = null; // Reiniciar la variedad seleccionada
  selectedState.value = null; // Reiniciar el estado seleccionado
  selectedType.value = null; // Reiniciar el tipo seleccionado
  varietyHistoryStore.varietyHistory = [];
};
</script>
