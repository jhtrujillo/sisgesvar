<template>
  <div class="space-y-6 w-full max-w-[98%] mx-auto px-4 pt-6">
    
    <!-- Encabezado y Navegación -->
    <div class="flex justify-between items-center bg-white border border-slate-100 rounded-xl p-4 shadow-sm">
      <div class="flex items-center space-x-3">
        <router-link
          :to="{ name: 'bioinformatica.show' }"
          class="flex items-center text-slate-500 hover:text-teal-700 transition-colors group"
        >
          <div class="p-2 bg-slate-50 group-hover:bg-teal-50 rounded-lg mr-2 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
          </div>
          <span class="font-semibold text-sm">Volver a Bioinformática</span>
        </router-link>
      </div>
      <div>
        <h1 class="text-xl font-bold text-slate-800 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="text-teal-600 mr-2">
            <path fill="currentColor" d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10s-4.477 10-10 10zm-1.012-4.664a4.004 4.004 0 0 0 4.298-2.673l2.842 1.344a6.007 6.007 0 0 1-6.738 4.223zm-3.69-2.008a4.002 4.002 0 0 0 1.947 4.544l-1.344 2.842a6.006 6.006 0 0 1-3.057-6.915zm7.398-3.033A2.002 2.002 0 1 1 12 10a2.002 2.002 0 0 1 2.696 2.295zM7.298 9.328a4.004 4.004 0 0 0-1.895 4.577l-2.84 1.346a6.008 6.008 0 0 1 2.924-7.054zM16.702 14.672a4.004 4.004 0 0 0 1.895-4.577l2.84-1.346a6.008 6.008 0 0 1-2.924 7.054zM10.988 6.664a4.004 4.004 0 0 0-4.298 2.673L3.848 8.002A6.007 6.007 0 0 1 10.585 3.77zM14.69 8.672a4.002 4.002 0 0 0-1.947-4.544l1.344-2.842a6.006 6.006 0 0 1 3.057 6.915z"/>
          </svg>
          Módulo BioJava
        </h1>
      </div>
    </div>

    <p class="text-slate-500 text-sm mb-6 max-w-4xl px-2">
      Seleccione una de las herramientas de la suite BioJava para ejecutar análisis genómicos interactivos.
    </p>

    <!-- Grid de Herramientas BioJava -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      
      <!-- Tarjeta Genómica Comparativa -->
      <router-link
        :to="{ name: 'comp_gen.show' }"
        class="group relative flex flex-col bg-white rounded-2xl border border-slate-200 hover:border-teal-400 p-6 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden h-full"
      >
        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-teal-50 to-teal-100 rounded-bl-full -mr-16 -mt-16 transition-transform group-hover:scale-110"></div>
        
        <div class="flex flex-col h-full text-left relative z-10">
          <div class="flex items-start justify-between mb-4">
            <div class="p-3 bg-teal-50 text-teal-600 rounded-xl group-hover:bg-teal-600 group-hover:text-white transition-colors duration-300">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
            <span class="text-xs font-bold text-slate-400 group-hover:text-teal-500 transition-colors">HTML/D3.js</span>
          </div>
          
          <h3 class="text-lg font-bold text-slate-800 mb-2 group-hover:text-teal-600 transition-colors">comp-gen</h3>
          <p class="text-slate-500 text-xs mb-6 flex-grow">
            Genómica Comparativa. Integra bloques de sintenia y ortología (McScanX) con anotaciones funcionales para análisis evolutivos.
          </p>
          
          <div class="mt-auto flex items-center text-teal-600 text-sm font-semibold group-hover:text-teal-700">
            Iniciar Herramienta
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </div>
        </div>
      </router-link>

      <!-- Tarjetas Placeholders (Otras herramientas de BioJava) -->
      
      <!-- Pop Structure -->
      <div class="group relative flex flex-col bg-slate-50 border border-dashed border-slate-200 rounded-2xl p-6 h-full opacity-60 hover:opacity-100 transition-opacity duration-300">
        <div class="flex flex-col h-full text-left relative z-10 grayscale group-hover:grayscale-0 transition-all">
          <div class="flex items-start justify-between mb-4">
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
              </svg>
            </div>
            <span class="text-[10px] font-bold text-amber-600 bg-amber-100 px-2 py-1 rounded-full uppercase tracking-wider">Próximamente</span>
          </div>
          <h3 class="text-lg font-bold text-slate-800 mb-2">pop-structure</h3>
          <p class="text-slate-500 text-xs mb-6 flex-grow">
            Estructura Poblacional. PCA, Kinship interactivo y decaimiento LD a partir de archivos VCF poblacionales.
          </p>
        </div>
      </div>

      <!-- SNP Explorer -->
      <div class="group relative flex flex-col bg-slate-50 border border-dashed border-slate-200 rounded-2xl p-6 h-full opacity-60 hover:opacity-100 transition-opacity duration-300">
        <div class="flex flex-col h-full text-left relative z-10 grayscale group-hover:grayscale-0 transition-all">
          <div class="flex items-start justify-between mb-4">
            <div class="p-3 bg-rose-50 text-rose-600 rounded-xl">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
              </svg>
            </div>
            <span class="text-[10px] font-bold text-amber-600 bg-amber-100 px-2 py-1 rounded-full uppercase tracking-wider">Próximamente</span>
          </div>
          <h3 class="text-lg font-bold text-slate-800 mb-2">snp-explorer</h3>
          <p class="text-slate-500 text-xs mb-6 flex-grow">
            Explorador visual interactivo de calidad de SNPs y alelos. Ideal para auditar variantes de VCF masivos.
          </p>
        </div>
      </div>

      <!-- GWAS -->
      <div class="group relative flex flex-col bg-slate-50 border border-dashed border-slate-200 rounded-2xl p-6 h-full opacity-60 hover:opacity-100 transition-opacity duration-300">
        <div class="flex flex-col h-full text-left relative z-10 grayscale group-hover:grayscale-0 transition-all">
          <div class="flex items-start justify-between mb-4">
            <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
              </svg>
            </div>
            <span class="text-[10px] font-bold text-amber-600 bg-amber-100 px-2 py-1 rounded-full uppercase tracking-wider">Próximamente</span>
          </div>
          <h3 class="text-lg font-bold text-slate-800 mb-2">gwas / genetic-map</h3>
          <p class="text-slate-500 text-xs mb-6 flex-grow">
            Suite Polyploid GWAS y utilidades para creación de mapas genéticos. Exporta a GWASpoly y JoinMap.
          </p>
        </div>
      </div>

    </div>

  </div>
</template>

<script setup lang="ts">
// Dashboard interno de herramientas de BioJava
</script>
