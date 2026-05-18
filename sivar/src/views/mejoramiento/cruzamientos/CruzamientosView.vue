<template>
  <div class="space-y-4">
    <!-- Botón Volver -->
    <div class="flex justify-start mb-4">
      <button
        type="button"
        class="px-4 py-2 text-sm font-semibold bg-transparent rounded-lg dark-mode:bg-transparent dark-mode:hover:bg-gray-600 dark-mode:focus:bg-gray-600 dark-mode:focus:text-white dark-mode:hover:text-white dark-mode:text-gray-200 focus:outline-none focus:shadow-outline"
      >
        <router-link
          class="text-violet-800 group border border-violet-800 flex items-center px-2 py-2 font-medium rounded-md pt-1 pb-1 pr-2 pl-2 hover:text-white hover:bg-violet-800"
          :to="{ name: 'mejoramiento.show' }"
        >
          Volver
        </router-link>
      </button>
    </div>
    <div class="grid grid-cols-1 text-center sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-3 gap-4">
      <router-link
        class="group grid font-medium rounded-md m-2 bg-white border-none shadow-xl py-8 px-4 sm:rounded-lg sm:px-10"
        :to="{ name: 'crossing_list.show' }"
      >
        <div class="flex flex-col h-full sm:px-6 lg:px-8">
          <div class="sm:mx-auto sm:w-full sm:max-w-md mb-2 flex justify-center">
            <h1 class="text-emerald-500">Ver Cruzamientos</h1>
          </div>
          <div class="flex justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 640 512">
              <path
                fill="#5b21b6"
                d="M176 288a112 112 0 1 0 0-224a112 112 0 1 0 0 224zm176-112c0 86.3-62.1 158.1-144 173.1V384h32c17.7 0 32 14.3 32 32s-14.3 32-32 32h-32v32c0 17.7-14.3 32-32 32s-32-14.3-32-32v-32h-32c-17.7 0-32-14.3-32-32s14.3-32 32-32h32v-34.9C62.1 334.1 0 262.3 0 176C0 78.8 78.8 0 176 0s176 78.8 176 176zm-80.1 184.6c19.3-10.1 36.9-23.1 52.1-38.4c20 18.5 46.7 29.8 76.1 29.8c61.9 0 112-50.1 112-112s-50.1-112-112-112c-7.2 0-14.3.7-21.1 2c-4.9-21.5-13-41.7-24-60.2c14.3-3.8 29.4-5.8 45-5.8c37 0 71.4 11.4 99.8 31l20.6-20.6L487 41c-6.9-6.9-8.9-17.2-5.2-26.2S494.3 0 504 0h112c13.3 0 24 10.7 24 24v112c0 9.7-5.8 18.5-14.8 22.2s-19.3 1.7-26.2-5.2l-33.4-33.4l-20.6 20.6c19.5 28.4 31 62.7 31 99.8c0 97.2-78.8 176-176 176c-50.5 0-96-21.3-128.1-55.4z"
              />
            </svg>
          </div>
        </div>
      </router-link>
      <router-link
        class="group grid font-medium rounded-md m-2 bg-white border-none shadow-xl py-8 px-4 sm:rounded-lg sm:px-10"
        :to="{ name: 'crossing_initial_data.show' }"
      >
        <div class="flex flex-col h-full sm:px-6 lg:px-8">
          <div class="sm:mx-auto sm:w-full sm:max-w-md mb-2 flex justify-center">
            <h1 class="text-emerald-500">Programación de Cruzamientos</h1>
          </div>
          <div class="flex justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24">
              <path
                fill="#5b21b6"
                d="M7 3V1h2v2h6V1h2v2h4a1 1 0 0 1 1 1v5h-2V5h-3v2h-2V5H9v2H7V5H4v14h6v2H3a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1zm10 9a4 4 0 1 0 0 8a4 4 0 0 0 0-8m-6 4a6 6 0 1 1 12 0a6 6 0 0 1-12 0m5-3v3.414l2.293 2.293l1.414-1.414L18 15.586V13z"
              />
            </svg>
          </div>
        </div>
      </router-link>
      <router-link
        class="group grid font-medium rounded-md m-2 bg-white border-none shadow-xl py-8 px-4 sm:rounded-lg sm:px-10"
        :to="{ name: 'alignments.show' }"
      >
        <div class="flex flex-col h-full sm:px-6 lg:px-8">
          <div class="sm:mx-auto sm:w-full sm:max-w-md mb-2 flex justify-center">
            <h1 class="text-emerald-500">Consolidado de Cruzamientos</h1>
          </div>
          <div class="flex justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 16 16">
              <path
                fill="#5b21b6"
                d="M2.5 1.75v11.5c0 .138.112.25.25.25h3.17a.75.75 0 0 1 0 1.5H2.75A1.75 1.75 0 0 1 1 13.25V1.75C1 .784 1.784 0 2.75 0h8.5C12.216 0 13 .784 13 1.75v7.736a.75.75 0 0 1-1.5 0V1.75a.25.25 0 0 0-.25-.25h-8.5a.25.25 0 0 0-.25.25m13.274 9.537l-4.557 4.45a.75.75 0 0 1-1.055-.008l-1.943-1.95a.75.75 0 0 1 1.062-1.058l1.419 1.425l4.026-3.932a.75.75 0 1 1 1.048 1.074M4.75 4h4.5a.75.75 0 0 1 0 1.5h-4.5a.75.75 0 0 1 0-1.5M4 7.75A.75.75 0 0 1 4.75 7h2a.75.75 0 0 1 0 1.5h-2A.75.75 0 0 1 4 7.75"
              />
            </svg>
          </div>
        </div>
      </router-link>
      <!-- <router-link
            class="group grid font-medium rounded-md m-2 bg-white border-none shadow-xl py-8 px-4 sm:rounded-lg sm:px-10"
            :to="{ name: 'alignments.show' }" :disabled="!data.puedeAcceder" v-if="data.puedeMostrar">
            <div class="flex flex-col h-full sm:px-6 lg:px-8" :class="{ 'opacity-50': !data.puedeAcceder }">
                <div class="sm:mx-auto sm:w-full sm:max-w-md mb-2 flex justify-center">
                    <h1 class=" text-emerald-500">Experimentos</h1>
                </div>
                <div class="flex justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24">
                        <path fill="#5b21b6"
                            d="M12 22q-2.075 0-3.538-1.463T7 17V8q-.825 0-1.413-.588T5 6V4q0-.825.588-1.413T7 2h10q.825 0 1.413.588T19 4v2q0 .825-.588 1.413T17 8v9q0 2.075-1.463 3.538T12 22Zm0-2q.975 0 1.75-.563T14.825 18H13q-.425 0-.713-.288T12 17q0-.425.288-.713T13 16h2v-1h-2q-.425 0-.713-.288T12 14q0-.425.288-.713T13 13h2v-1h-2q-.425 0-.713-.288T12 11q0-.425.288-.713T13 10h2V8H9v9q0 1.25.875 2.125T12 20Z" />
                    </svg>
                </div>
            </div>
        </router-link> -->
    </div>
  </div>
</template>

<script setup lang="ts">
/* import { ref } from "vue"; */
import { onMounted } from "vue";
import { useLinksStore } from "@/stores/links";

const linksStore = useLinksStore();

onMounted(async () => {
  await linksStore.getLinks();
});

/* const data = ref({
  puedeAcceder: false,
  puedeMostrar: true
}); */
</script>
