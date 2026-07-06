<script setup lang="ts">
import { ref } from 'vue';

const isLoading = ref(true);

</script>

<template>
  <div class="h-screen w-full flex flex-col bg-slate-50 p-6 overflow-hidden">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-3xl font-bold text-slate-800 tracking-tight flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
          </svg>
          Visor Genómico JBrowse 2
        </h1>
        <p class="text-slate-500 mt-1">Explorador de genomas y pistas de anotación integrado de alto rendimiento.</p>
      </div>
      <div class="flex space-x-3">
        <a 
          href="http://192.168.153.238:8080/" 
          target="_blank"
          class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 text-slate-700 font-medium rounded-lg hover:bg-slate-50 hover:shadow-sm transition-all"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
          </svg>
          Abrir en Pestaña Completa
        </a>
      </div>
    </div>

    <!-- Contenedor del Iframe Interno -->
    <div class="flex-1 bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm relative">
      <!-- Loading indicator -->
      <div v-if="isLoading" class="absolute inset-0 flex flex-col items-center justify-center bg-white z-10">
        <div class="w-12 h-12 border-4 border-emerald-100 border-t-emerald-600 rounded-full animate-spin mb-4"></div>
        <p class="text-slate-500 font-medium animate-pulse">Iniciando JBrowse 2...</p>
      </div>
      
      <!-- Iframe apuntando al jbrowse alojado en el servidor Linux -->
      <iframe 
        src="http://192.168.153.238:8080/" 
        class="w-full h-full border-none"
        @load="isLoading = false"
      ></iframe>
    </div>
  </div>
</template>

<style scoped>
/* Scoped styles */
</style>
