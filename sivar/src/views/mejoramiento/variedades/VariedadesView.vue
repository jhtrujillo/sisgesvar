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
        :to="{ name: 'variety_list.show' }"
      >
        <div class="flex flex-col h-full sm:px-6 lg:px-8">
          <div class="sm:mx-auto sm:w-full sm:max-w-md mb-2 flex justify-center">
            <h1 class="text-emerald-500">Ver Variedades</h1>
          </div>
          <div class="flex justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 512 512">
              <path
                fill="#5b21b6"
                d="M350.8 22.28c-2.3 13.74-4.6 27.67-6.8 41.68h.6c1.3-.1 2.7 0 4.1.1c4.5.4 8.9 1.78 12.9 3.97c2.3-14.37 4.6-28.66 7-42.75zm-171.6.32l-17.8 2.52c1.2 8.41 2.4 16.86 3.6 25.34c3.9-1.63 8-2.62 12.2-2.89c1.8-.12 3.7-.1 5.5.1c-1.2-8.37-2.3-16.73-3.5-25.03zm.4 42.89h-1.2c-12.7.8-26.1 11.9-23.7 40.61l10.2 127.7c2.3 28.6 17.7 38.2 30.4 37.4c12.7-.8 26.1-12 23.8-40.5l-10.3-127.8c-2.2-27.65-16.8-37.55-29.2-37.41zm165.6 16.4c-4.5.13-9.2 2.17-13.6 7.1c-5.1 5.65-9.6 15.31-10.9 29.71l-11.3 127.5c-1.3 14.5 1.4 24.8 5.4 31.2c4 6.5 9 9.3 14 9.7c4.9.5 10.4-1.4 15.4-7c5.1-5.7 9.6-15.3 10.9-29.8l11.4-127.5c1.3-14.4-1.4-24.71-5.4-31.17c-4-6.46-9-9.23-14-9.68c-.6-.1-1.3-.1-1.9-.1zM25.99 223.3C78.79 299.9 126.7 397.8 125 489.7h35.3c3.1-20.8 7.5-39.3 13-56.2c-10.9-35.5-28.2-73.7-49.5-108.1c-28.14-45.5-63.44-83.9-97.81-102.1zM419.3 270c-1.3 0-3.7.7-6.8 3.1c-3.6 2.7-7.8 7.3-11.9 13c-3.3 4.7-6.6 10.2-9.6 16c5-2.1 10-3.7 15-4.4c13.3-2 26.3 1 37.8 8.1c16.5 10.2 30.1 28.1 42.2 52.2c-.3-15.6-3.4-31.3-9.5-45c-10.4-23.3-28.3-40.3-56.7-43zm-210.9 16.3c-3.8 1.6-7.9 2.5-12 2.8c-2 .1-4 .1-5.9-.1c1.8 33.1 2.7 65.9 2.3 97.9c5.5-10.6 11.5-21 18.1-31.4c-.3-22.9-1.2-46-2.5-69.2zm104.5 14.1c-5.5 56.5-9.3 112.6-10.6 166.7c5.9-20.8 12.7-41.5 20.6-62.1c1.8-32.8 4.5-66.2 7.7-99.8c-1.2 0-2.3 0-3.4-.1c-5-.5-9.9-2.1-14.3-4.7zm88.3 16.9c-7 2.3-14.7 6.4-23 12.9c-30.1 52.2-49 105.1-63.4 159.5h80.5c-12.6-57.3-5-115 5.9-172.4zm-146.7 5.6c-38.5 54.6-64.6 95.2-75.8 166H237c-13-31.1-12.1-62.8-4.3-96.6c5-21.6 12.9-44.6 21.8-69.4z"
              />
            </svg>
          </div>
        </div>
      </router-link>
      <router-link
        class="group grid font-medium rounded-md m-2 bg-white border-none shadow-xl py-8 px-4 sm:rounded-lg sm:px-10"
        :to="{ name: 'diagram_parents_level.show' }"
      >
        <div class="flex flex-col h-full sm:px-6 lg:px-8">
          <div class="sm:mx-auto sm:w-full sm:max-w-md mb-2 flex justify-center">
            <h1 class="text-emerald-500">Diagrama de parentales</h1>
          </div>
          <div class="flex justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 32 32">
              <path
                fill="#5b21b6"
                d="m6.812 17.202l7.396-3.665v-2.164h-.834c-.414 0-.808-.084-1.167-.237v1.16L4.81 15.96v2.912h2V17.2zm19.75 1.673v-2.913l-7.397-3.666v-1.158a2.98 2.98 0 0 1-1.166.236h-.833v2.164l7.395 3.666v1.672h2zm-9.874 0v-7.5h-2v7.5h2zm11.187 1H23.25a2 2 0 0 0-2 2V26.5a2 2 0 0 0 2 2h4.625a2 2 0 0 0 2-2v-4.625a2 2 0 0 0-2-2zm-19.75 0H3.5a2 2 0 0 0-2 2V26.5a2 2 0 0 0 2 2h4.625a2 2 0 0 0 2-2v-4.625a2 2 0 0 0-2-2zm5.25-9.5H18a2 2 0 0 0 2-2V3.75a2 2 0 0 0-2-2h-4.625a2 2 0 0 0-2 2v4.625a2 2 0 0 0 2 2zm4.625 9.5h-4.625a2 2 0 0 0-2 2V26.5a2 2 0 0 0 2 2H18a2 2 0 0 0 2-2v-4.625a2 2 0 0 0-2-2z"
              />
            </svg>
          </div>
        </div>
      </router-link>
      <router-link
        class="group grid font-medium rounded-md m-2 bg-white border-none shadow-xl py-8 px-4 sm:rounded-lg sm:px-10"
        :to="{ name: 'germoplasm_bank.show' }"
      >
        <div class="flex flex-col h-full sm:px-6 lg:px-8">
          <div class="sm:mx-auto sm:w-full sm:max-w-md mb-2 flex justify-center">
            <h1 class="text-emerald-500">Banco de germoplasma</h1>
          </div>
          <div class="flex justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 48 48">
              <g fill="none" stroke="#5b21b6" stroke-width="4">
                <path stroke-linecap="round" stroke-linejoin="round" d="m20 33l6 2s15-3 17-3s2 2 0 4s-9 8-15 8s-10-3-14-3H4" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 29c2-2 6-5 10-5s13.5 4 15 6s-3 5-3 5" />
                <circle cx="34" cy="22" r="3" />
                <circle cx="22" cy="15" r="3" />
                <circle cx="34" cy="7" r="3" />
              </g>
            </svg>
          </div>
        </div>
      </router-link>
      <router-link
        class="group grid font-medium rounded-md m-2 bg-white border-none shadow-xl py-8 px-4 sm:rounded-lg sm:px-10"
        :to="{ name: 'variety_history.show' }"
      >
        <div class="flex flex-col h-full sm:px-6 lg:px-8">
          <div class="sm:mx-auto sm:w-full sm:max-w-md mb-2 flex justify-center">
            <h1 class="text-emerald-500">Historial de variedad</h1>
          </div>
          <div class="flex justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 48 48">
              <g fill="none" stroke="#5b21b6" stroke-linecap="round" stroke-width="4">
                <path
                  stroke-linejoin="round"
                  d="M24 43.9998H10C8.89543 43.9998 8 43.1043 8 41.9998V5.99976C8 4.89519 8.89543 3.99976 10 3.99976H38C39.1046 3.99976 40 4.89519 40 5.99976V23.9998"
                />
                <path stroke-linejoin="round" d="M35.5 43.9998V30.9998" />
                <path stroke-linejoin="round" d="M31 34.4998L32.5 32.9998L35.5 29.9998L38.5 32.9998L40 34.4998" />
                <path d="M16 15.9998H32" />
                <path d="M16 23.9998H24" />
              </g>
            </svg>
          </div>
        </div>
      </router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { onMounted } from "vue";
import { useLinksStore } from "@/stores/links";

const linksStore = useLinksStore();

onMounted(async () => {
  console.log("carga los links");

  await linksStore.getLinks();
});

const data = ref({
  puedeAcceder: false,
  puedeMostrar: true
});
</script>
