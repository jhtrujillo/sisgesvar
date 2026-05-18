<!-- Vista para mostrar la herramienta embebida WikiCenicaña-->
<template>
  <div class="flex flex-grow flex-col pt-5">
    <div>
      <button
        type="button"
        class="block mb-4 px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark-mode:bg-transparent dark-mode:hover:bg-gray-600 dark-mode:focus:bg-gray-600 dark-mode:focus:text-white dark-mode:hover:text-white dark-mode:text-gray-200 md:mt-0 focus:outline-none focus:shadow-outline"
        href="#"
      >
        <router-link
          class="text-violet-800 group border border-violet-800 flex items-center px-2 py-2 font-medium rounded-md pt-1 pb-1 pr-2 pl-2 hover:text-white hover:bg-violet-800"
          :to="{
            name: 'bioinformatica.show'
          }"
        >
          Volver</router-link
        >
      </button>
    </div>
    <h1 class="text-center font-bold text-5xl mb-6 text-violet-800">{{ linksStore.linkList[1]?.name }}</h1>
    <iframe class="justify-center text-center" :src="linksStore.linkList[1]?.url" frameborder="0" width="100%" height="1000"></iframe>
  </div>
</template>
<script setup lang="ts">
import { onMounted } from "vue";
import { useLinksStore } from "@/stores/links";

const linksStore = useLinksStore();

onMounted(async () => {
  console.log("carga los links");

  await linksStore.getLinks();
});
onMounted(async () => {});
</script>
