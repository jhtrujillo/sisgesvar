<template>
  <div class="space-y-4 w-full h-[85vh] flex flex-col">
    <!-- Encabezado y Navegación -->
    <div class="flex justify-between items-center bg-white border border-slate-100 rounded-xl p-4 shadow-sm shrink-0">
      <div class="flex items-center space-x-3">
        <router-link
          :to="{ name: 'bioinformatica.show' }"
          class="flex items-center text-slate-500 hover:text-blue-700 transition-colors group"
        >
          <div class="p-2 bg-slate-50 group-hover:bg-blue-50 rounded-lg mr-2 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
          </div>
          <span class="font-semibold text-sm">Volver a Bioinformática</span>
        </router-link>
      </div>
      <div>
        <h1 class="text-xl font-bold text-slate-800 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="text-blue-600 mr-2">
            <path fill="currentColor" d="M2.5 12c0-4.478 0-6.718 1.391-8.109S7.521 2.5 12 2.5c4.478 0 6.718 0 8.109 1.391S21.5 7.521 21.5 12c0 4.478 0 6.718-1.391 8.109S16.479 21.5 12 21.5c-4.478 0-6.718 0-8.109-1.391S2.5 16.479 2.5 12m0-3h19M7 6h.009M11 6h.009" />
          </svg>
          SequenceServer (BLAST)
        </h1>
      </div>
    </div>

    <!-- Contenedor del Iframe -->
    <div class="flex-1 bg-white border border-slate-100 rounded-xl overflow-hidden shadow-sm relative">
      <!-- Loading indicator -->
      <div v-if="isLoading" class="absolute inset-0 flex flex-col items-center justify-center bg-white z-10">
        <div class="w-12 h-12 border-4 border-blue-100 border-t-blue-600 rounded-full animate-spin mb-4"></div>
        <p class="text-slate-500 font-medium animate-pulse">Conectando con SequenceServer...</p>
      </div>
      
      <!-- Iframe apuntando al servidor Linux donde corre SequenceServer en Docker -->
      <iframe 
        src="http://192.168.153.238:4567" 
        class="w-full h-full border-none"
        @load="isLoading = false"
        sandbox="allow-same-origin allow-scripts allow-forms"
      ></iframe>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const isLoading = ref(true);
</script>

<style scoped>
/* Estilos adicionales si se requieren */
</style>
