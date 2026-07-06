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

    <!-- Contenedor Informativo y Botón -->
    <div class="flex-1 bg-slate-50 border border-slate-100 rounded-xl flex flex-col items-center justify-center p-8 shadow-sm">
      <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 max-w-lg text-center flex flex-col items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-blue-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
        </svg>
        <h2 class="text-2xl font-bold text-slate-800 mb-2">Servidor BLAST Activo</h2>
        <p class="text-slate-600 mb-6 leading-relaxed">
          Por razones de seguridad del navegador y para garantizar el máximo rendimiento durante el procesamiento de los alineamientos, SequenceServer debe ejecutarse en una ventana independiente.
        </p>
        <a 
          href="http://192.168.153.238:4567" 
          target="_blank"
          rel="noopener noreferrer"
          class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 hover:shadow-lg transition-all transform hover:-translate-y-0.5"
        >
          Abrir SequenceServer (Nueva Pestaña)
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
          </svg>
        </a>
      </div>
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
