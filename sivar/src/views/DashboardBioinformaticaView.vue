<template>
  <div class="space-y-6 w-full max-w-[98%] mx-auto px-4 pt-6">
    
    <!-- Encabezado y Navegación -->
    <div class="flex justify-between items-center bg-white border border-slate-100 rounded-xl p-4 shadow-sm">
      <div class="flex items-center space-x-3">
        <router-link
          :to="{ name: 'main.show' }"
          class="flex items-center text-slate-500 hover:text-blue-700 transition-colors group"
        >
          <div class="p-2 bg-slate-50 group-hover:bg-blue-50 rounded-lg mr-2 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
          </div>
          <span class="font-semibold text-sm">Volver al Panel</span>
        </router-link>
      </div>
      <div>
        <h1 class="text-xl font-bold text-slate-800 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
          </svg>
          Módulo de Bioinformática
        </h1>
      </div>
    </div>

    <!-- Grid de Herramientas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      
      <!-- SequenceServer Card -->
      <router-link
        class="group relative flex flex-col bg-white border border-slate-100 hover:border-blue-200 rounded-2xl p-6 shadow-premium hover:shadow-premium-hover hover:-translate-y-1.5 transition-all duration-300 overflow-hidden"
        :to="{ name: 'sequence_server.show' }"
      >
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-400 to-indigo-500 transform origin-left transition-transform duration-300"></div>
        
        <div class="flex flex-col h-full text-left">
          <div class="w-12 h-12 flex items-center justify-center bg-blue-50 rounded-xl text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300 mb-4 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="transition-transform duration-500 group-hover:scale-110">
              <path fill="currentColor" d="M2.5 12c0-4.478 0-6.718 1.391-8.109S7.521 2.5 12 2.5c4.478 0 6.718 0 8.109 1.391S21.5 7.521 21.5 12c0 4.478 0 6.718-1.391 8.109S16.479 21.5 12 21.5c-4.478 0-6.718 0-8.109-1.391S2.5 16.479 2.5 12m0-3h19M7 6h.009M11 6h.009" />
              <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m8 13l1.227 1.057c.515.445.773.667.773.943s-.258.498-.773.943L8 17m4 0h4" />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-800 mb-2 group-hover:text-blue-700 transition-colors">SequenceServer</h3>
          <p class="text-slate-500 text-xs leading-relaxed mb-4 flex-1">Herramienta BLAST interactiva para la búsqueda local de secuencias genéticas usando genomas de referencia.</p>
          
          <span class="inline-flex items-center text-xs font-semibold text-blue-600 mt-auto group-hover:text-blue-800">
            Abrir Herramienta
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-1 transform transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </span>
        </div>
      </router-link>

      <!-- Servidor BLAST Nativo Card -->
      <router-link
        class="group relative flex flex-col bg-white border border-slate-100 hover:border-purple-200 rounded-2xl p-6 shadow-premium hover:shadow-premium-hover hover:-translate-y-1.5 transition-all duration-300 overflow-hidden"
        :to="{ name: 'blast_nativo.show' }"
      >
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-purple-400 to-fuchsia-500 transform origin-left transition-transform duration-300"></div>
        
        <div class="flex flex-col h-full text-left">
          <div class="w-12 h-12 flex items-center justify-center bg-purple-50 rounded-xl text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-all duration-300 mb-4 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="transition-transform duration-500 group-hover:scale-110">
              <path fill="currentColor" d="M14.5 13a1.5 1.5 0 1 0 0-3a1.5 1.5 0 0 0 0 3zm-5 0a1.5 1.5 0 1 0 0-3a1.5 1.5 0 0 0 0 3z"/>
              <path fill="currentColor" fill-rule="evenodd" d="M21 11.5a9 9 0 1 1-18 0a9 9 0 0 1 18 0zM12 4a7.5 7.5 0 0 0-7.5 7.5v.5h15v-.5A7.5 7.5 0 0 0 12 4z" clip-rule="evenodd"/>
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-800 mb-2 group-hover:text-purple-700 transition-colors">BLAST Nativo (Nuevo)</h3>
          <p class="text-slate-500 text-xs leading-relaxed mb-4 flex-1">Herramienta de alineamiento de secuencias totalmente integrada a SIVAR (Sin dependencias externas).</p>
          
          <span class="inline-flex items-center text-xs font-semibold text-purple-600 mt-auto group-hover:text-purple-800">
            Abrir Servidor
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-1 transform transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </span>
        </div>
      </router-link>

      <!-- BioJava Suite Card -->
      <router-link
        class="group relative flex flex-col bg-white border border-slate-100 hover:border-teal-200 rounded-2xl p-6 shadow-premium hover:shadow-premium-hover hover:-translate-y-1.5 transition-all duration-300 overflow-hidden"
        :to="{ name: 'biojava.show' }"
      >
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-teal-400 to-teal-600 transform origin-left transition-transform duration-300"></div>
        
        <div class="flex flex-col h-full text-left">
          <div class="w-12 h-12 flex items-center justify-center bg-teal-50 rounded-xl text-teal-600 group-hover:bg-teal-600 group-hover:text-white transition-all duration-300 mb-4 shadow-sm">
            <!-- Icono representativo de BioJava -->
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="transition-transform duration-500 group-hover:scale-110">
              <path fill="currentColor" d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10s-4.477 10-10 10zm-1.012-4.664a4.004 4.004 0 0 0 4.298-2.673l2.842 1.344a6.007 6.007 0 0 1-6.738 4.223zm-3.69-2.008a4.002 4.002 0 0 0 1.947 4.544l-1.344 2.842a6.006 6.006 0 0 1-3.057-6.915zm7.398-3.033A2.002 2.002 0 1 1 12 10a2.002 2.002 0 0 1 2.696 2.295zM7.298 9.328a4.004 4.004 0 0 0-1.895 4.577l-2.84 1.346a6.008 6.008 0 0 1 2.924-7.054zM16.702 14.672a4.004 4.004 0 0 0 1.895-4.577l2.84-1.346a6.008 6.008 0 0 1-2.924 7.054zM10.988 6.664a4.004 4.004 0 0 0-4.298 2.673L3.848 8.002A6.007 6.007 0 0 1 10.585 3.77zM14.69 8.672a4.002 4.002 0 0 0-1.947-4.544l1.344-2.842a6.006 6.006 0 0 1 3.057 6.915z"/>
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-800 mb-2 group-hover:text-teal-700 transition-colors">Suite BioJava</h3>
          <p class="text-slate-500 text-xs leading-relaxed mb-4 flex-1">Herramientas nativas para manipulación de secuencias, genómica comparativa y análisis poblacionales.</p>
          
          <span class="inline-flex items-center text-xs font-semibold text-teal-600 mt-auto group-hover:text-teal-800">
            Abrir Suite
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-1 transform transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </span>
        </div>
      </router-link>

      <!-- JBrowse 2 Card -->
      <router-link
        class="group relative flex flex-col bg-white border border-slate-100 hover:border-emerald-200 rounded-2xl p-6 shadow-premium hover:shadow-premium-hover hover:-translate-y-1.5 transition-all duration-300 overflow-hidden"
        :to="{ name: 'jbrowse.show' }"
      >
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-400 to-green-500 transform origin-left transition-transform duration-300"></div>
        
        <div class="flex flex-col h-full text-left">
          <div class="w-12 h-12 flex items-center justify-center bg-emerald-50 rounded-xl text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300 mb-4 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="transition-transform duration-500 group-hover:scale-110">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" fill="none" stroke="currentColor" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-800 mb-2 group-hover:text-emerald-700 transition-colors">Visor JBrowse 2</h3>
          <p class="text-slate-500 text-xs leading-relaxed mb-4 flex-1">Explorador de genomas interactivo de nueva generación para visualizar anotaciones, variantes y alineamientos.</p>
          
          <span class="inline-flex items-center text-xs font-semibold text-emerald-600 mt-auto group-hover:text-emerald-800">
            Abrir Visor
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-1 transform transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </span>
        </div>
      </router-link>

    </div>

  </div>
</template>

<script setup lang="ts">
// Dashboard de Bioinformática - Plantilla limpia
</script>

<style scoped>
/* Estilos específicos si son necesarios */
</style>
