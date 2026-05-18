<!-- Vista para mostrar en una lista los procesos que han sido completados -->
<template>
  <div class="w-full flex-col pt-5 grid place-content-center">
    <h1 class="text-center font-bold text-5xl mb-6 text-violet-800">Alineamientos de Secuencias</h1>
    <div>
      <button
        type="button"
        class="block mb-4 px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark-mode:bg-transparent dark-mode:hover:bg-gray-600 dark-mode:focus:bg-gray-600 dark-mode:focus:text-white dark-mode:hover:text-white dark-mode:text-gray-200 md:mt-0 focus:outline-none focus:shadow-outline"
        href="#"
      >
        <router-link
          class="text-violet-800 group border border-violet-800 flex items-center px-2 py-2 font-medium rounded-md pt-1 pb-1 pr-2 pl-2 hover:text-white hover:bg-violet-800"
          :to="{
            name: 'alignments.show'
          }"
        >
          Alinear Secuencia</router-link
        >
      </button>
    </div>
    <div class="w-full overflow-x-auto shadow-md sm:rounded-lg">
      <h2 class="text-center font-bold text-3xl mb-6 text-violet-800">Procesos completados</h2>
      <template v-if="alignmentsListsStore.alignmentsList.length === 0">
        <p class="text-center text-xl font-bold text-gray-500">No hay datos.</p>
      </template>
      <table v-else class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-center">
          <tr class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
            <th class="p-4">ID</th>
            <th class="p-4">OWNER</th>
            <th class="p-4">SUBMITTED</th>
            <th class="p-4">RUN_TIME</th>
            <th class="p-4">ST</th>
            <th class="p-4">COMPLETED</th>
            <th class="p-4">CMD</th>
          </tr>
        </thead>
        <tbody v-for="process in alignmentsListsStore.alignmentsList" :key="process.ID" class="text-center">
          <tr class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
            <td class="p-4">
              {{ process.ID }}
            </td>
            <td class="p-4">
              {{ process.OWNER }}
            </td>
            <td class="p-4">
              {{ process.SUBMITTED }}
            </td>
            <td class="p-4">
              {{ process.RUN_TIME }}
            </td>
            <td class="p-4">
              {{ process.ST }}
            </td>
            <td class="p-4">
              {{ process.COMPLETED }}
            </td>
            <td class="p-4">
              {{ process.CMD }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
<script setup lang="ts">
import { onMounted } from "vue";
import { useAlignmentsListStore } from "@/stores/alignmentsList";

const alignmentsListsStore = useAlignmentsListStore();

onMounted(async () => {
  await alignmentsListsStore.getAlignments();
});
</script>
