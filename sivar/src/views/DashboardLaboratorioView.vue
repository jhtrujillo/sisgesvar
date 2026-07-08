<template>
  <div class="space-y-6 w-full max-w-[98%] mx-auto px-4 pt-6">
    
    <!-- Encabezado y Navegación -->
    <div class="flex justify-between items-center bg-white border border-slate-100 rounded-xl p-4 shadow-sm">
      <div class="flex items-center space-x-3">
        <router-link
          :to="{ name: 'main.show' }"
          class="flex items-center text-slate-500 hover:text-fuchsia-700 transition-colors group"
        >
          <div class="p-2 bg-slate-50 group-hover:bg-fuchsia-50 rounded-lg mr-2 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
          </div>
          <span class="font-semibold text-sm">Volver al Panel</span>
        </router-link>
      </div>
      <div>
        <h1 class="text-xl font-bold text-slate-800 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-fuchsia-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
          </svg>
          Módulo de Laboratorio
        </h1>
      </div>
    </div>

    <!-- Grid de Herramientas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      
      <!-- Inventario Card -->
      <router-link
        class="group relative flex flex-col bg-white border border-slate-100 hover:border-fuchsia-200 rounded-2xl p-6 shadow-premium hover:shadow-premium-hover hover:-translate-y-1.5 transition-all duration-300 overflow-hidden"
        :to="{ name: 'laboratorio.inventario' }"
      >
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-fuchsia-400 to-pink-500 transform origin-left transition-transform duration-300"></div>
        
        <div class="flex flex-col h-full text-left">
          <div class="w-12 h-12 flex items-center justify-center bg-fuchsia-50 rounded-xl text-fuchsia-600 group-hover:bg-fuchsia-600 group-hover:text-white transition-all duration-300 mb-4 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="transition-transform duration-500 group-hover:scale-110">
              <path fill="currentColor" d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-9 14H6v-2h4v2zm0-4H6v-2h4v2zm0-4H6V7h4v2zm8 8h-4v-2h4v2zm0-4h-4v-2h4v2zm0-4h-4V7h4v2z" />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-800 mb-2 group-hover:text-fuchsia-700 transition-colors">Inventario</h3>
          <p class="text-slate-500 text-xs leading-relaxed mb-4 flex-1">Gestión de reactivos, muestras, equipos y material de laboratorio.</p>
          
          <span class="inline-flex items-center text-xs font-semibold text-fuchsia-600 mt-auto group-hover:text-fuchsia-800">
            Abrir Inventario
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
// Dashboard de Laboratorio
</script>

<style scoped>
</style>
