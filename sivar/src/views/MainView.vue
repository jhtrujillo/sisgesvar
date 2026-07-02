<!-- Vista para mostrar el menú principal al ingresar a la plataforma-->
<template>
  <div class="min-h-screen font-sans antialiased">
    <div class="flex flex-col min-h-screen">
      <TopNavComponent />
      
      <div class="flex-1 bg-gradient-to-b from-slate-50 via-white to-emerald-50/20 flex justify-center items-center relative overflow-hidden">
        <!-- Dynamic Background Accent -->
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-emerald-100/30 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-slate-100/50 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="w-full max-w-[98%] mx-auto px-2 sm:px-4 lg:px-6 relative z-10">
          <main class="flex flex-col justify-center items-center min-h-[calc(100vh-70px)] py-12">
            <router-view v-if="hasTools" class="w-full pt-6" />
            
            <div v-else class="flex flex-col items-center">
              <!-- Welcoming Title -->
              <div class="text-center mb-12 max-w-2xl">
                <h2 class="text-sm font-semibold text-cenicana uppercase tracking-wider mb-3">Panel Principal</h2>
                <h1 class="text-4xl font-extrabold text-slate-800 tracking-tight sm:text-5xl">
                  Bienvenido a <span class="text-cenicana-800 bg-gradient-to-r from-cenicana-800 to-emerald-600 bg-clip-text text-transparent">SIVAR</span>
                </h1>
                <p class="mt-4 text-lg text-slate-500">
                  Seleccione el módulo al que desea ingresar para comenzar a gestionar sus datos.
                </p>
              </div>

              <div class="flex flex-wrap justify-center items-stretch gap-8 w-full max-w-4xl px-4">
                <!-- Bioinformática Card (Ocultado Temporalmente) -->
                <router-link
                  v-if="false"
                  class="group relative flex-1 min-w-[280px] max-w-[380px] flex flex-col justify-between bg-white border border-slate-100 hover:border-emerald-100 rounded-3xl p-8 shadow-premium hover:shadow-premium-hover hover:-translate-y-2 transition-all duration-300 group overflow-hidden"
                  :to="{ name: 'bioinformatica.show' }"
                >
                  <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-emerald-400 to-teal-500 transform origin-left transition-transform duration-300"></div>
                  
                  <div class="flex flex-col h-full items-center text-center">
                    <div class="p-4 bg-emerald-50 rounded-2xl text-cenicana-800 group-hover:bg-cenicana-800 group-hover:text-white transition-colors duration-300 mb-6">
                      <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" class="transition-transform duration-500 group-hover:scale-110">
                        <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5">
                          <path d="M2.5 12c0-4.478 0-6.718 1.391-8.109S7.521 2.5 12 2.5c4.478 0 6.718 0 8.109 1.391S21.5 7.521 21.5 12c0 4.478 0 6.718-1.391 8.109S16.479 21.5 12 21.5c-4.478 0-6.718 0-8.109-1.391S2.5 16.479 2.5 12m0-3h19M7 6h.009M11 6h.009" />
                          <path d="m8 13l1.227 1.057c.515.445.773.667.773.943s-.258.498-.773.943L8 17m4 0h4" />
                        </g>
                      </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-3 group-hover:text-cenicana-800 transition-colors">Bioinformática</h3>
                    <p class="text-slate-500 text-sm leading-relaxed mb-6">Análisis de secuencias, herramientas JBrowse, servidores de secuencias y visualizadores genómicos.</p>
                    
                    <span class="inline-flex items-center text-sm font-semibold text-emerald-600 group-hover:text-emerald-700 mt-auto">
                      Ingresar Módulo
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1.5 transform transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                      </svg>
                    </span>
                  </div>
                </router-link>

                <!-- Mejoramiento Card -->
                <router-link
                  class="group relative flex-1 min-w-[280px] max-w-[380px] flex flex-col justify-between bg-white border border-slate-100 hover:border-emerald-100 rounded-3xl p-8 shadow-premium hover:shadow-premium-hover hover:-translate-y-2 transition-all duration-300 overflow-hidden"
                  :to="{ name: 'mejoramiento.show' }"
                >
                  <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-cenicana-800 to-emerald-600 transform origin-left transition-transform duration-300"></div>
                  
                  <div class="flex flex-col h-full items-center text-center">
                    <div class="p-4 bg-emerald-50 rounded-2xl text-cenicana-800 group-hover:bg-cenicana-800 group-hover:text-white transition-colors duration-300 mb-6">
                      <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 512 512" class="transition-transform duration-500 group-hover:rotate-12">
                        <path fill="currentColor" d="M350.8 22.28c-2.3 13.74-4.6 27.67-6.8 41.68h.6c1.3-.1 2.7 0 4.1.1c4.5.4 8.9 1.78 12.9 3.97c2.3-14.37 4.6-28.66 7-42.75zm-171.6.32l-17.8 2.52c1.2 8.41 2.4 16.86 3.6 25.34c3.9-1.63 8-2.62 12.2-2.89c1.8-.12 3.7-.1 5.5.1c-1.2-8.37-2.3-16.73-3.5-25.03zm.4 42.89h-1.2c-12.7.8-26.1 11.9-23.7 40.61l10.2 127.7c2.3 28.6 17.7 38.2 30.4 37.4s26.1-12 23.8-40.5l-10.3-127.8c-2.2-27.65-16.8-37.55-29.2-37.41m165.6 16.4c-4.5.13-9.2 2.17-13.6 7.1c-5.1 5.65-9.6 15.31-10.9 29.71l-11.3 127.5c-1.3 14.5 1.4 24.8 5.4 31.2c4 6.5 9 9.3 14 9.7c4.9.5 10.4-1.4 15.4-7c5.1-5.7 9.6-15.3 10.9-29.8l11.4-127.5c1.3-14.4-1.4-24.71-5.4-31.17s-9-9.23-14-9.68c-.6-.1-1.3-.1-1.9-.1zM25.99 223.3C78.79 299.9 126.7 397.8 125 489.7h35.3c3.1-20.8 7.5-39.3 13-56.2c-10.9-35.5-28.2-73.7-49.5-108.1c-28.14-45.5-63.44-83.9-97.81-102.1M419.3 270c-1.3 0-3.7.7-6.8 3.1c-3.6 2.7-7.8 7.3-11.9 13c-3.3 4.7-6.6 10.2-9.6 16c5-2.1 10-3.7 15-4.4c13.3-2 26.3 1 37.8 8.1c16.5 10.2 30.1 28.1 42.2 52.2c-.3-15.6-3.4-31.3-9.5-45c-10.4-23.3-28.3-40.3-56.7-43zm-210.9 16.3c-3.8 1.6-7.9 2.5-12 2.8c-2 .1-4 .1-5.9-.1c1.8 33.1 2.7 65.9 2.3 97.9c5.5-10.6 11.5-21 18.1-31.4c-.3-22.9-1.2-46-2.5-69.2m104.5 14.1c-5.5 56.5-9.3 112.6-10.6 166.7c5.9-20.8 12.7-41.5 20.6-62.1c1.8-32.8 4.5-66.2 7.7-99.8c-1.2 0-2.3 0-3.4-.1c-5-.5-9.9-2.1-14.3-4.7m88.3 16.9c-7 2.3-14.7 6.4-23 12.9c-30.1 52.2-49 105.1-63.4 159.5h80.5c-12.6-57.3-5-115 5.9-172.4m-146.7 5.6c-38.5 54.6-64.6 95.2-75.8 166H237c-13-31.1-12.1-62.8-4.3-96.6c5-21.6 12.9-44.6 21.8-69.4" />
                      </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-3 group-hover:text-cenicana-800 transition-colors">Mejoramiento</h3>
                    <p class="text-slate-500 text-sm leading-relaxed mb-6">Gestión de cruzamientos, floraciones, bancos de germoplasma, libros de campo y experimentos.</p>
                    
                    <span class="inline-flex items-center text-sm font-semibold text-emerald-600 group-hover:text-emerald-700 mt-auto">
                      Ingresar Módulo
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1.5 transform transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                      </svg>
                    </span>
                  </div>
                </router-link>

                <!-- Bitacora Card -->
                <a
                  href="http://192.168.153.119/bitacora/"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="group relative flex-1 min-w-[280px] max-w-[380px] flex flex-col justify-between bg-white border border-slate-100 hover:border-amber-100 rounded-3xl p-8 shadow-premium hover:shadow-premium-hover hover:-translate-y-2 transition-all duration-300 overflow-hidden"
                >
                  <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-amber-500 to-orange-400 transform origin-left transition-transform duration-300"></div>
                  
                  <div class="flex flex-col h-full items-center text-center">
                    <div class="p-4 bg-amber-50 rounded-2xl text-amber-600 group-hover:bg-amber-500 group-hover:text-white transition-colors duration-300 mb-6">
                      <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" class="transition-transform duration-500 group-hover:scale-110">
                        <path fill="currentColor" d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 14H7v-2h10v2zm0-4H7v-2h10v2zm0-4H7V7h10v2z" />
                      </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-3 group-hover:text-amber-600 transition-colors">Bitácora</h3>
                    <p class="text-slate-500 text-sm leading-relaxed mb-6">Acceda al sistema de bitácora para registrar y consultar las actividades diarias.</p>
                    
                    <span class="inline-flex items-center text-sm font-semibold text-amber-600 group-hover:text-amber-700 mt-auto">
                      Abrir Bitácora
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1.5 transform transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                      </svg>
                    </span>
                  </div>
                </a>

                <!-- Especificaciones Module Card -->
                <router-link
                  v-if="false"
                  class="group relative flex-1 min-w-[280px] max-w-[380px] flex flex-col justify-between bg-white border border-slate-100 hover:border-emerald-100 rounded-3xl p-8 shadow-premium hover:shadow-premium-hover hover:-translate-y-2 transition-all duration-300 overflow-hidden"
                  :to="{ name: 'about.show' }"
                >
                  <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-teal-500 to-blue-600 transform origin-left transition-transform duration-300"></div>
                  
                  <div class="flex flex-col h-full items-center text-center">
                    <div class="p-4 bg-teal-50 rounded-2xl text-teal-600 group-hover:bg-teal-600 group-hover:text-white transition-colors duration-300 mb-6">
                      <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="transition-transform duration-500 group-hover:rotate-45">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.43l-1.003.828c-.293.241-.438.613-.43.992a6.723 6.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.43l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.645-.869L9.594 3.94z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-3 group-hover:text-teal-600 transition-colors">System Specifications</h3>
                    <p class="text-slate-500 text-sm leading-relaxed mb-6">Technical breakdown of SIVAR (Sistema de Información de Variedades Cenicaña) development frameworks, libraries, databases, and servers.</p>
                    
                    <span class="inline-flex items-center text-sm font-semibold text-teal-600 group-hover:text-teal-700 mt-auto">
                      Enter Module
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1.5 transform transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                      </svg>
                    </span>
                  </div>
                </router-link>
              </div>
            </div>
          </main>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import TopNavComponent from "@/components/TopNavComponent.vue";
import { useRoute } from "vue-router";

// Computed property para verificar si se debe mostrar el contenido del router-view o el componente "Bienvenidos"
const hasTools = computed<boolean>(() => {
  const route = useRoute();

  return route.path !== "/";
});
</script>
