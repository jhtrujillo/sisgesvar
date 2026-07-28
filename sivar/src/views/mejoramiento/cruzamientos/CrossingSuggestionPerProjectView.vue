<template>
  <div class="space-y-6 w-full max-w-[98%] mx-auto px-2 sm:px-4 pt-4">
    <!-- Encabezado exclusivo de impresión (solo visible en PDF/Impresora) -->
    <div class="hidden print:block mb-6 border-b-2 border-emerald-600 pb-4">
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-xl font-extrabold text-emerald-800 tracking-tight">CENICAÑA - PROGRAMACIÓN DE CRUZAMIENTOS</h1>
          <p class="text-xs text-slate-500 font-semibold mt-1">Módulo Científico de Hibridación Avanzada SIVARCC</p>
        </div>
        <div class="text-right text-xs text-slate-500 font-semibold">
          <div><strong>Fecha:</strong> {{ new Date().toLocaleDateString("es-ES") }}</div>
          <div><strong>Proyecto:</strong> {{ selectedCdCntble }} | <strong>Testigo:</strong> {{ selectedVariety }}</div>
        </div>
      </div>
      <div class="mt-4 text-[10px] text-slate-600 bg-slate-50 p-2.5 border border-slate-200 rounded-lg flex justify-between items-center">
        <div><strong>Mega Ambiente:</strong> {{ selectedMegaAmbiente }}</div>
        <div><strong>Responsable:</strong> ___________________________</div>
        <div><strong>Firma de Aprobación:</strong> ___________________________</div>
      </div>
    </div>
    <!-- Wrapper de la Vista de Planeación -->
    <div v-if="!isFinished" class="space-y-6">
      <!-- Encabezado con Indicador de Progreso -->
      <div class="border-b border-slate-100 pb-4">
        <div class="flex items-center justify-between mb-3">
          <h1 class="text-2xl font-extrabold text-slate-800 flex items-center">
            <div class="p-1.5 bg-emerald-50 text-cenicana rounded-lg mr-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"
                />
              </svg>
            </div>
            Sugerencia de Cruzamientos
          </h1>
          <span class="text-[11px] font-bold uppercase tracking-wider px-2.5 py-0.5 bg-emerald-100 text-emerald-800 rounded-full"> Proyecto Final </span>
        </div>
        <div class="flex flex-wrap items-center justify-between ml-9 text-xs text-slate-500">
          <span>Confirme las sugerencias de cruzamientos viables generadas por proyecto.</span>
          <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100 mt-1 sm:mt-0">
            Proyecto: {{ selectedCdCntble }}
          </span>
        </div>
      </div>

      <!-- Contenedor Principal de la Matriz -->
      <div class="bg-white border border-slate-100 rounded-xl p-3 sm:p-4 shadow-premium relative min-h-[250px]">
        <!-- Estado de Procesamiento / Cargando -->
        <div
          v-if="isLoading || isOptimizing"
          class="absolute inset-0 bg-white/95 rounded-xl z-30 flex flex-col items-center justify-center space-y-4 transition-all duration-300"
        >
          <div class="relative w-14 h-14">
            <!-- Círculo de base -->
            <div class="absolute inset-0 rounded-full border-4 border-emerald-50"></div>
            <!-- Círculo giratorio -->
            <div class="absolute inset-0 rounded-full border-4 border-t-cenicana animate-spin"></div>
          </div>
          <div class="flex flex-col items-center text-center px-4">
            <span v-if="isLoading" class="text-sm font-bold text-slate-700 animate-pulse">Calculando las mejores sugerencias de cruzamientos...</span>
            <span v-else class="text-sm font-bold text-slate-700 animate-pulse">
              Aplicando algoritmos de optimización avanzada
              <span
                v-if="optimizandoMadre"
                class="text-emerald-700 block mt-1 text-xs font-black bg-emerald-50 py-1 px-3 rounded-full border border-emerald-100"
                >Evaluando madre: {{ optimizandoMadre }}</span
              >
              <span v-else>...</span>
            </span>
          </div>
        </div>

        <!-- Estado de Guardado / Persistiendo en Base de Datos -->
        <div
          v-if="isSaving"
          class="absolute inset-0 bg-white/95 rounded-xl z-30 flex flex-col items-center justify-center space-y-4 transition-all duration-300"
        >
          <div class="relative w-14 h-14">
            <!-- Círculo de base -->
            <div class="absolute inset-0 rounded-full border-4 border-emerald-50"></div>
            <!-- Círculo giratorio -->
            <div class="absolute inset-0 rounded-full border-4 border-t-cenicana animate-spin"></div>
          </div>
          <div class="flex flex-col items-center text-center px-4 max-w-md">
            <span class="text-sm font-bold text-slate-750 animate-pulse leading-relaxed">
              Guardando programación de cruzamientos en la base de datos de Cenicaña...
            </span>
          </div>
        </div>

        <!-- Ayuda del Índice Combinado -->
        <div
          v-if="tipoMapaCalor === 'ic'"
          class="bg-indigo-50 border border-indigo-100 rounded-lg p-3 mb-4 mt-2 flex items-start space-x-3 text-xs text-indigo-900 shadow-sm transition-all animate-fade-in-up"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div>
            <strong class="font-bold block text-sm mb-1 text-indigo-800">¿Cómo se calcula el Índice Combinado (IC)?</strong>
            <p class="mb-1">
              El IC es un puntaje de <strong>0 a 100</strong> que balancea un <strong>60% del Valor de Mérito (VM)</strong> y un
              <strong>40% de la Distancia Genética (DG)</strong>.
            </p>
            <ul class="list-disc pl-4 space-y-1 mb-2 text-indigo-800/80">
              <li>
                <strong>VM (Valor de Mérito):</strong> El sistema usa una escala invertida de 1 a 9 (1 es excelente, 9 es malo). Se "voltea" a puntaje usando la
                fórmula: <code>((9.0 - VM) / 8.0) * 100</code>.
                <em>(Nota: Se divide sobre 8 porque es la amplitud o tamaño total de la escala, es decir: 9 - 1 = 8)</em>.
              </li>
              <li><strong>DG (Distancia Genética):</strong> Se asume que 0.70 o superior es el techo ideal. Su puntaje es: <code>(DG / 0.70) * 100</code></li>
            </ul>
            <p class="font-mono text-[10px] bg-white/60 p-1.5 rounded text-indigo-700">
              <strong>Ejemplo:</strong> Si el VM es 3.0 (75 pts) y la DG es 0.50 (71.4 pts). El IC será = (75 x 0.6) + (71.4 x 0.4) = 45 + 28.5 =
              <strong>73.5 pts</strong>.
            </p>
          </div>
        </div>

        <!-- Leyenda informativa y Botón de Filtro -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mb-3 border-b border-slate-100 pb-3 no-print">
          <div class="flex flex-wrap items-center gap-3 text-[11px] font-semibold text-slate-500">
            <span class="flex items-center">
              <span class="w-3 h-3 bg-rose-100 border border-rose-200 rounded mr-1"></span>
              Hembra (Polen &le; 20)
            </span>
            <span class="flex items-center">
              <span class="w-3 h-3 bg-sky-100 border border-sky-200 rounded mr-1"></span>
              Macho (Polen &gt; 20)
            </span>
            <span v-if="tipoMapaCalor === 'none'" class="flex items-center">
              <span class="w-3 h-3 bg-emerald-100 border border-emerald-200 rounded mr-1"></span>
              Recomendado
            </span>
            <span class="flex items-center">
              <span class="font-bold text-slate-700 mr-0.5">VM:</span>
              Valor de Mérito
            </span>
            <span class="flex items-center">
              <span class="font-bold text-slate-700 mr-0.5">DG:</span>
              Distancia Genética
            </span>

            <!-- Leyenda del Mapa de Calor (DG) -->
            <span v-if="tipoMapaCalor === 'dg'" class="flex flex-wrap items-center gap-2 border-l border-slate-200 pl-3">
              <span class="text-[9px] text-slate-400 font-extrabold uppercase mr-1">Mapa de Calor (DG):</span>
              <span class="flex items-center">
                <span class="w-3.5 h-3.5 bg-blue-600 rounded mr-1"></span>
                <span class="text-[9px] font-bold text-blue-800 mr-2">Exc (&ge;0.65)</span>
              </span>
              <span class="flex items-center">
                <span class="w-3.5 h-3.5 bg-sky-400/80 rounded mr-1"></span>
                <span class="text-[9px] font-bold text-sky-700 mr-2">Bueno (&ge;0.55)</span>
              </span>
              <span class="flex items-center">
                <span class="w-3.5 h-3.5 bg-slate-300 rounded mr-1"></span>
                <span class="text-[9px] font-bold text-slate-600 mr-2">Acept (&ge;0.45)</span>
              </span>
              <span class="flex items-center">
                <span class="w-3.5 h-3.5 bg-amber-400 rounded mr-1"></span>
                <span class="text-[9px] font-bold text-amber-800 mr-2">Cerca (&ge;0.35)</span>
              </span>
              <span class="flex items-center">
                <span class="w-3.5 h-3.5 bg-orange-600 rounded mr-1"></span>
                <span class="text-[9px] font-bold text-orange-800">Riesgo (&lt;0.35)</span>
              </span>
            </span>
            <!-- Leyenda del Mapa de Calor (IC) -->
            <span v-if="tipoMapaCalor === 'ic'" class="flex flex-wrap items-center gap-2 border-l border-slate-200 pl-3">
              <span class="text-[9px] text-slate-400 font-extrabold uppercase mr-1">Índice Combinado (IC):</span>
              <span class="flex items-center">
                <span class="w-3.5 h-3.5 bg-indigo-600 rounded mr-1"></span>
                <span class="text-[9px] font-bold text-indigo-800 mr-2">Exc (&ge;80)</span>
              </span>
              <span class="flex items-center">
                <span class="w-3.5 h-3.5 bg-sky-400 rounded mr-1"></span>
                <span class="text-[9px] font-bold text-sky-700 mr-2">Bueno (&ge;65)</span>
              </span>
              <span class="flex items-center">
                <span class="w-3.5 h-3.5 bg-slate-200 rounded mr-1"></span>
                <span class="text-[9px] font-bold text-slate-600 mr-2">Reg (&ge;50)</span>
              </span>
              <span class="flex items-center">
                <span class="w-3.5 h-3.5 bg-rose-500 rounded mr-1"></span>
                <span class="text-[9px] font-bold text-rose-800">Bajo (&lt;50)</span>
              </span>
            </span>
          </div>

          <!-- Botones de Control Interactivos -->
          <div class="flex justify-end items-center space-x-2">
            <!-- Selector de Mapa de Calor (3 opciones) -->
            <div class="flex items-center bg-slate-100 rounded-lg p-0.5 border border-slate-200 shadow-inner">
              <button
                @click="tipoMapaCalor = 'none'"
                class="px-2.5 py-1 text-[10px] font-bold rounded-md transition-all duration-200 whitespace-nowrap"
                :class="
                  tipoMapaCalor === 'none'
                    ? 'bg-white text-slate-700 shadow-sm border border-slate-200/50'
                    : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'
                "
              >
                Sin Color
              </button>
              <button
                @click="tipoMapaCalor = 'dg'"
                class="px-2.5 py-1 text-[10px] font-bold rounded-md transition-all duration-200 flex items-center"
                :class="
                  tipoMapaCalor === 'dg' ? 'bg-amber-500 text-white shadow-sm border border-amber-600' : 'text-slate-500 hover:text-amber-600 hover:bg-amber-50'
                "
              >
                DG
              </button>
              <button
                @click="tipoMapaCalor = 'ic'"
                class="px-2.5 py-1 text-[10px] font-bold rounded-md transition-all duration-200 flex items-center"
                :class="
                  tipoMapaCalor === 'ic'
                    ? 'bg-indigo-600 text-white shadow-sm border border-indigo-700'
                    : 'text-slate-500 hover:text-indigo-600 hover:bg-indigo-50'
                "
              >
                Índice
              </button>
            </div>

            <button
              type="button"
              @click="autoOptimizarFlores"
              class="flex items-center px-3 py-1.5 mr-2 text-[11px] font-bold rounded-lg transition-all duration-200 border bg-white border-slate-200 text-slate-600 hover:bg-slate-50 whitespace-nowrap"
              title="Optimizar inventario (Reducir excesos y maximizar capacidad usando los mejores cruces)"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143z" />
              </svg>
              Optimizar
            </button>

            <!-- Toggle vistas -->
            <div class="flex items-center bg-slate-100 p-1 rounded-lg mr-2 border border-slate-200">
              <button 
                @click="vistaActual = 'cuadricula'"
                :class="['px-3 py-1 text-[11px] font-bold rounded-md transition-all', vistaActual === 'cuadricula' ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-500 hover:text-slate-700']"
              >
                Cuadrícula
              </button>
              <button 
                @click="vistaActual = 'asistente'"
                :class="['px-3 py-1 text-[11px] font-bold rounded-md transition-all', vistaActual === 'asistente' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-700']"
              >
                Asistente
              </button>
              <button 
                @click="vistaActual = 'asistente-pro'"
                :class="['px-3 py-1 text-[11px] font-bold rounded-md transition-all', vistaActual === 'asistente-pro' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-700']"
              >
                Asist. Pro
              </button>
            </div>

            <!-- Toggle de Vistas Oculto temporalmente
          <button 
            @click="isDragDropView = !isDragDropView"
            class="flex items-center px-3 py-1.5 text-[11px] font-bold rounded-lg transition-all duration-200 border whitespace-nowrap"
            :class="isDragDropView ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm hover:bg-indigo-700' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50'"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path v-if="!isDragDropView" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5" />
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
            </svg>
            {{ isDragDropView ? 'Ver Matriz Clásica' : 'Vista Drag & Drop' }}
          </button>
          -->

            <!-- Botón de Filtro Ocultar/Ver Inviables -->
            <button
              @click="ocultarInviables = !ocultarInviables"
              class="flex items-center px-3 py-1.5 text-[11px] font-bold rounded-lg transition-all duration-200 border whitespace-nowrap"
              :class="
                !ocultarInviables
                  ? 'bg-emerald-600 border-emerald-600 text-white shadow-sm hover:bg-emerald-700'
                  : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50'
              "
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2.5"
                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                />
              </svg>
              {{ ocultarInviables ? "Ver Inviables" : "Ocultar Inviables" }}
            </button>

            <!-- Botón Expandir a Pantalla Completa -->
            <button
              @click="isExpanded = !isExpanded"
              class="flex items-center px-3 py-1.5 text-[11px] font-bold rounded-lg transition-all duration-200 border whitespace-nowrap"
              :class="
                isExpanded
                  ? 'bg-slate-700 border-slate-700 text-white shadow-sm hover:bg-slate-800'
                  : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50'
              "
              title="Ampliar/Reducir tabla de cruzamientos"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path
                  v-if="!isExpanded"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2.5"
                  d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"
                />
                <path
                  v-else
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2.5"
                  d="M4 14h4v4m0-4l-5 5m11-5h-4v4m0-4l5 5M4 10h4V6m0 4L3 5m11 5h-4V6m0 4l5-5"
                />
              </svg>
              {{ isExpanded ? "Reducir" : "Ampliar" }}
            </button>
          </div>
        </div>

        <!-- Alerta si no hay ningún cruce viable en modo limpio -->
        <div
          v-if="ocultarInviables && viabilidadesMatriz.length > 0 && !hasAnyViableCrossing"
          class="flex flex-col items-center justify-center py-10 text-center text-slate-400 space-y-2 bg-slate-50/50 rounded-xl border border-slate-100"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-350" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="1.5"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
            />
          </svg>
          <span class="text-xs font-semibold text-slate-500">No se encontraron cruzamientos sugeridos en este proyecto.</span>
          <span class="text-[11px] text-slate-400">Haga clic en "Ver Inviables" para ver todas las opciones o modifique los pesos en el paso anterior.</span>
        </div>

        <!-- Contenedor General para Ambas Vistas -->
        <div v-else class="w-full">
          <!-- VISTA MATRIZ CLÁSICA -->
          <div
            v-show="!isDragDropView"
            :class="
              isExpanded
                ? 'fixed inset-0 z-[9999] bg-white shadow-[0_0_80px_rgba(0,0,0,0.3)] flex flex-col border border-slate-200 overflow-hidden w-full h-full'
                : 'overflow-hidden border border-slate-100 rounded-xl shadow-sm relative'
            "
          >
            <!-- Header de Modo Expandido -->
            <div v-if="isExpanded" class="flex justify-between items-center px-4 py-3 border-b border-slate-100 bg-slate-50/80 backdrop-blur">
              <div class="flex items-center space-x-3">
                <span class="p-1.5 bg-emerald-100 text-emerald-700 rounded-lg">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"
                    />
                  </svg>
                </span>
                <div>
                  <h3 class="text-sm font-bold text-slate-800 leading-tight">Vista Ampliada de Cruzamientos</h3>
                  <p class="text-[10px] text-slate-500 font-semibold">{{ selectedCdCntble }} | {{ selectedMegaAmbiente }}</p>
                </div>
              </div>
              <button
                @click="isExpanded = false"
                class="px-3 py-1.5 flex items-center text-[11px] font-bold text-slate-500 hover:text-slate-800 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg shadow-sm transition-colors"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Cerrar
              </button>
            </div>

            <div
              :class="
                vistaActual === 'cuadricula'
                  ? (isExpanded ? 'flex-1 overflow-x-auto overflow-y-auto scrollbar-custom p-1 bg-slate-50/30' : 'max-h-[500px] overflow-x-auto overflow-y-auto scrollbar-custom')
                  : (isExpanded ? 'flex-1 p-1 bg-slate-50/30 flex flex-col' : 'w-full flex flex-col')
              "
            >
              <!-- VISTA CUADRÍCULA -->
              <div v-show="vistaActual === 'cuadricula'">
              <table v-if="!isOptimizing" ref="matrizTable" class="table-auto w-full divide-y divide-slate-150 bg-white rounded-lg">
                <thead class="bg-slate-50">
                  <tr>
                    <th
                      class="px-2 py-2 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500 bg-slate-50 border-r border-slate-100 sticky top-0 left-0 z-20 shadow-[2px_0_5px_rgba(0,0,0,0.02)] min-w-[140px]"
                    >
                      Hembra / Machos
                    </th>
                    <!-- Envoltura <template> para evaluar v-if en el scope correcto de Vue 3 -->
                    <template v-for="(flor, indexCol) in floresSeleccionadas || []" :key="indexCol">
                      <th
                        v-if="(!ocultarInviables || isColumnViable(indexCol)) && Number(flor.polen) > 20"
                        class="px-2 py-2 text-center text-[11px] font-bold uppercase tracking-wider text-slate-650 bg-slate-50 border-r border-slate-100 sticky top-0 z-10 min-w-[75px]"
                      >
                        <span
                          class="block text-slate-800 font-extrabold leading-tight cursor-pointer hover:underline hover:text-emerald-700 transition-colors"
                          @click="openVarietyProfile(flor.variedad)"
                        >
                          {{ flor.variedad }}
                        </span>
                        <div class="flex flex-col items-center justify-center text-[9px] font-semibold text-slate-500 mt-1 space-y-0.5 mb-1">
                          <span v-if="viabilidadesMatriz?.[0]?.[indexCol]?.vm2 !== undefined">VM: {{ viabilidadesMatriz[0][indexCol].vm2 }}</span>
                          <span>Disp: {{ flor.cantidad }}</span>
                          <span>Polen: {{ flor.polen ? flor.polen + "%" : "N/A" }}</span>
                          <span class="font-bold mt-0.5" :class="getFloresUsadas(flor.variedad, false) > flor.cantidad ? 'text-rose-600' : 'text-emerald-700'">
                            Usadas: {{ getFloresUsadas(flor.variedad, false) }} / {{ flor.cantidad }}
                          </span>
                        </div>
                      </th>
                    </template>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                  <!-- Envoltura <template> para evaluar v-if en el scope correcto de Vue 3 -->
                  <template v-for="(viabilidadRow, indexRow) in viabilidadesMatriz || []" :key="indexRow">
                    <tr
                      v-if="(!ocultarInviables || isRowViable(viabilidadRow)) && Number(viabilidadRow[0]?.polen) <= 20"
                      class="hover:bg-slate-50/40 transition-colors"
                    >
                      <!-- Celda Madre Fija a la izquierda -->
                      <td
                        :class="[
                          +viabilidadRow[0]?.polen <= 20
                            ? 'bg-rose-50/60 hover:bg-rose-100/50 border-r border-rose-100 text-rose-800'
                            : 'bg-sky-50/60 hover:bg-sky-100/50 border-r border-sky-100 text-sky-850'
                        ]"
                        class="whitespace-nowrap px-2 py-2 text-center text-[11px] font-bold sticky left-0 z-10 shadow-[2px_0_5px_rgba(0,0,0,0.02)] min-w-[140px]"
                      >
                        <div class="flex items-center justify-center space-x-1.5 mb-0.5">
                          <i :class="getIcon(viabilidadRow[0]?.polen)"></i>
                          <span
                            class="font-extrabold text-slate-800 leading-tight cursor-pointer hover:underline hover:text-emerald-700 transition-colors"
                            @click="openVarietyProfile(viabilidadRow[0]?.varA)"
                            >{{ viabilidadRow[0]?.varA || "N/A" }}</span
                          >
                        </div>
                        <div class="flex flex-col items-center justify-center text-[9px] font-semibold text-slate-500 space-y-0.5">
                          <span>VM: {{ getRowVm(viabilidadRow) }}</span>
                          <span>Disp: {{ getCantidadFlores(viabilidadRow[0]?.varA) }}</span>
                          <span>Polen: {{ viabilidadRow[0]?.polen ? viabilidadRow[0].polen + "%" : "0%" }}</span>
                          <span
                            class="font-bold mt-0.5"
                            :class="
                              getFloresUsadas(viabilidadRow[0]?.varA, true) > getCantidadFlores(viabilidadRow[0]?.varA) ? 'text-rose-600' : 'text-emerald-700'
                            "
                          >
                            Usadas: {{ getFloresUsadas(viabilidadRow[0]?.varA, true) }} / {{ getCantidadFlores(viabilidadRow[0]?.varA) }}
                          </span>
                        </div>

                        <!-- Autofecundación Estilizada -->
                        <div
                          class="mt-1.5 flex items-center justify-center space-x-1 bg-white/70 py-0.5 px-1.5 rounded-lg border border-slate-200/50 shadow-sm max-w-[120px] mx-auto"
                        >
                          <input
                            type="checkbox"
                            :checked="autofecundacionesSeleccionadas.has(viabilidadRow[0]?.varA)"
                            @click="toggleAutofecundar(viabilidadRow)"
                            class="h-3 w-3 rounded border-slate-350 text-emerald-600 focus:ring-emerald-100 cursor-pointer"
                          />
                          <span class="text-[8px] font-extrabold uppercase text-slate-600">Autofecundar</span>
                        </div>
                      </td>

                      <!-- Celdas de la matriz filtradas por columna -->
                      <template v-for="(car, indexCol) in viabilidadRow" :key="indexCol">
                        <td
                          v-if="(!ocultarInviables || isColumnViable(indexCol)) && Number(car?.polen2) > 20"
                          :class="[getHeatmapClass(car?.varA, car?.varB, !!car?.viabilidad, car?.vm2)]"
                          class="p-2 text-center border-b border-slate-100 transition-all duration-200 min-w-[75px]"
                        >
                          <div class="flex flex-col items-center justify-center space-y-1">
                            <input
                              type="checkbox"
                              :checked="!!car?.viabilidad"
                              @click="toggleCruzamiento(car)"
                              class="h-3.5 w-3.5 rounded border-slate-350 text-emerald-600 focus:ring-emerald-100 transition cursor-pointer"
                            />
                            <!-- Selector numérico cuando es viable -->
                            <div v-if="car?.viabilidad" class="mt-0.5 mb-1">
                              <button
                                @click.stop="openFlowerAdjustmentModal(car)"
                                class="flex items-center justify-center space-x-1 bg-white/90 hover:bg-white rounded border border-slate-200 px-1.5 py-0.5 shadow-sm text-[9px] text-slate-700 transition-colors cursor-pointer"
                                title="Ajustar consumo de flores"
                              >
                                <span>⚙️</span>
                                <span class="font-bold">{{ car.flores_madre ?? 1 }} / {{ car.flores_padre ?? 1 }}</span>
                              </button>
                            </div>
                            <div
                              class="text-[9px] font-extrabold leading-tight"
                              :class="[tipoMapaCalor !== 'none' && isDarkBackground(car.varA, car.varB, car.vm2) ? 'text-white' : 'text-slate-900']"
                            >
                              {{ car.varB }}
                            </div>
                            <div
                              class="text-[8px] font-semibold leading-tight"
                              :class="[tipoMapaCalor !== 'none' && isDarkBackground(car.varA, car.varB, car.vm2) ? 'text-emerald-100' : 'text-slate-700']"
                            >
                              Polen: {{ car.polen2 }} | VM: {{ car.vm2 }}
                            </div>
                            <div class="flex flex-col items-center justify-center w-full border-t border-slate-100/50 pt-1 mt-1 space-y-1">
                              <span
                                class="text-[9px] font-extrabold tracking-tight leading-none text-center"
                                :class="[tipoMapaCalor !== 'none' && isDarkBackground(car.varA, car.varB, car.vm2) ? 'text-white' : 'text-slate-900']"
                              >
                                <template v-if="tipoMapaCalor === 'ic'">
                                  IC:
                                  {{
                                    isNaN(getIndiceCombinado(car.varA, car.varB, car.vm2)) ? "NA" : Math.round(getIndiceCombinado(car.varA, car.varB, car.vm2))
                                  }}
                                </template>
                                <template v-else> DG: {{ getDistancia(car.varA, car.varB) || "NA" }} </template>
                              </span>
                              <!-- Botón Comparador Lado a Lado -->
                              <button
                                @click.stop="openParentComparator(car?.varA, car?.varB, car?.viabilidad)"
                                class="text-[8px] font-bold px-1.5 py-0.5 rounded transition-all duration-150 flex items-center justify-center space-x-0.5 border mx-auto"
                                :class="[
                                  tipoMapaCalor !== 'none' && isDarkBackground(car.varA, car.varB, car.vm2)
                                    ? 'bg-white/20 hover:bg-white/30 text-white border-white/25'
                                    : 'bg-slate-100 hover:bg-emerald-50 text-slate-900 hover:text-emerald-700 border-slate-300 hover:border-emerald-300'
                                ]"
                                title="Comparar Progenitores Lado a Lado"
                              >
                                <i class="fa fa-balance-scale"></i>
                                <span>VS</span>
                              </button>
                            </div>
                          </div>
                        </td>
                      </template>
                    </tr>
                  </template>
                </tbody>
              </table>
              </div>

              <!-- VISTA ASISTENTE (MAESTRO-DETALLE) -->
              <div v-if="vistaActual === 'asistente' && !isOptimizing">
                <SuggestionMasterDetail
                  :females="assistantFemales"
                  :selectedFemaleRow="selectedFemaleRow"
                  :sortedMales="assistantSortedMales"
                  :ocultarRiesgos="assistantOcultarRiesgos"
                  :tipoMapaCalor="tipoMapaCalor"
                  @select-female="row => selectedFemaleRow = row"
                  @toggle-cross="toggleCruzamiento"
                  @open-flower-modal="openFlowerAdjustmentModal"
                  @toggle-riesgos="assistantOcultarRiesgos = !assistantOcultarRiesgos"
                  :getDisp="varName => cantidadesMap[varName] || 0"
                  :getDistanciaLocal="getDistancia"
                  :getIndiceCombinadoLocal="getIndiceCombinado"
                />
              </div>

              <!-- VISTA ASISTENTE PRO (TABLA COMPACTA) -->
              <div v-if="vistaActual === 'asistente-pro' && !isOptimizing">
                <SuggestionTableDetail
                  :females="assistantFemales"
                  :selectedFemaleRow="selectedFemaleRow"
                  :sortedMales="assistantSortedMales"
                  :ocultarRiesgos="assistantOcultarRiesgos"
                  :tipoMapaCalor="tipoMapaCalor"
                  @select-female="row => selectedFemaleRow = row"
                  @toggle-cross="toggleCruzamiento"
                  @open-flower-modal="openFlowerAdjustmentModal"
                  @toggle-riesgos="assistantOcultarRiesgos = !assistantOcultarRiesgos"
                  :getDisp="varName => cantidadesMap[varName] || 0"
                  :getDistanciaLocal="getDistancia"
                  :getIndiceCombinadoLocal="getIndiceCombinado"
                />
              </div>
            </div>
          </div>

          <!-- VISTA DRAG & DROP -->
          <div
            v-show="isDragDropView"
            class="w-full bg-white border border-slate-200 rounded-xl flex shadow-sm min-h-[600px] max-h-[80vh] overflow-hidden select-none"
          >
            <!-- PANEL IZQUIERDO: MADRES -->
            <div class="w-72 border-r border-slate-100 flex flex-col bg-slate-50">
              <div class="p-3 border-b border-slate-100 bg-emerald-700 text-white shadow-sm z-10 flex justify-between items-center">
                <h3 class="font-bold text-sm flex items-center">
                  <span class="w-2 h-2 rounded-full bg-white mr-2"></span>
                  Madres (Hembras)
                </h3>
                <span class="text-xs bg-emerald-800 px-2 py-0.5 rounded-full font-medium">{{ madresUnicas.length }}</span>
              </div>
              <div class="flex-1 overflow-y-auto p-3 space-y-3 custom-scrollbar">
                <div
                  v-for="madre in madresUnicas"
                  :key="madre.variedad"
                  draggable="true"
                  @dragstart="handleDragStart($event, madre, 'madre')"
                  class="bg-white border rounded-lg shadow-sm cursor-grab active:cursor-grabbing hover:shadow-md transition-all duration-200 overflow-hidden flex flex-col group"
                  :class="
                    getCantidadFlores(madre.variedad) - getFloresUsadas(madre.variedad, true) <= 0
                      ? 'opacity-50 border-rose-200'
                      : 'border-slate-200 hover:border-emerald-300'
                  "
                >
                  <div class="px-3 py-2 border-b border-slate-50 flex justify-between items-center bg-gradient-to-r from-emerald-50/30 to-white">
                    <span class="font-bold text-sm text-slate-800">{{ madre.variedad }}</span>
                    <span class="text-[10px] text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded font-medium">Polen: {{ Number(madre.polen).toFixed(0) }}</span>
                  </div>
                  <div class="px-3 py-1.5 bg-slate-50/50 flex justify-between items-center text-xs">
                    <span class="text-slate-500 font-medium">Disponibles</span>
                    <span
                      class="font-bold"
                      :class="getCantidadFlores(madre.variedad) - getFloresUsadas(madre.variedad, true) <= 0 ? 'text-rose-600' : 'text-emerald-600'"
                    >
                      {{ getCantidadFlores(madre.variedad) - getFloresUsadas(madre.variedad, true) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- PANEL CENTRAL: ZONA DE CRUCES -->
            <div class="flex-1 flex flex-col relative bg-[#f8fafc]">
              <!-- PANEL SUPERIOR: PADRES -->
              <div class="h-28 border-b border-slate-200 bg-white shadow-sm z-10 flex flex-col">
                <div class="px-4 py-1.5 bg-indigo-600 text-white text-xs font-bold flex justify-between items-center">
                  <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-white mr-2"></span> Padres (Machos)</span>
                  <span class="text-[10px] bg-indigo-700 px-1.5 py-0.5 rounded-full">{{ floresSeleccionadas.length }}</span>
                </div>
                <div class="flex-1 overflow-x-auto flex items-center px-3 space-x-3 custom-scrollbar py-2">
                  <div
                    v-for="padre in floresSeleccionadas"
                    :key="padre.variedad"
                    draggable="true"
                    @dragstart="handleDragStart($event, padre, 'padre')"
                    class="flex-shrink-0 w-44 bg-white border rounded-lg shadow-sm cursor-grab active:cursor-grabbing hover:shadow-md transition-all duration-200 flex flex-col h-full"
                    :class="
                      getCantidadFlores(padre.variedad) - getFloresUsadas(padre.variedad, false) <= 0
                        ? 'opacity-50 border-rose-200'
                        : 'border-slate-200 hover:border-indigo-300'
                    "
                  >
                    <div class="px-2 py-1.5 border-b border-slate-50 flex justify-between items-center bg-gradient-to-r from-indigo-50/30 to-white">
                      <span class="font-bold text-xs text-slate-800 truncate">{{ padre.variedad }}</span>
                      <span class="text-[9px] text-slate-500 bg-slate-100 px-1 py-0.5 rounded font-medium">Pol: {{ Number(padre.polen).toFixed(0) }}</span>
                    </div>
                    <div class="px-2 py-1 bg-slate-50/50 flex justify-between items-center text-[11px] flex-1">
                      <span class="text-slate-500">Disp.</span>
                      <span
                        class="font-bold"
                        :class="getCantidadFlores(padre.variedad) - getFloresUsadas(padre.variedad, false) <= 0 ? 'text-rose-600' : 'text-indigo-600'"
                      >
                        {{ getCantidadFlores(padre.variedad) - getFloresUsadas(padre.variedad, false) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- DROP ZONE -->
              <div class="flex-1 relative overflow-y-auto p-6" @dragover="handleDragOver" @drop="handleDrop($event, 'zone')">
                <!-- Hint Background -->
                <div v-if="manualCrosses.length === 0" class="absolute inset-0 pointer-events-none flex items-center justify-center opacity-40 flex-col">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-slate-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M13 10V3L4 14h7v7l9-11h-7z" />
                  </svg>
                  <h2 class="text-2xl font-bold text-slate-500">Arrastra Madres y Padres aquí</h2>
                  <p class="text-slate-500 mt-2">Une las tarjetas para formar cruces interactivos</p>
                </div>

                <!-- Pending Match Indicators -->
                <div
                  v-if="pendingMother || pendingFather"
                  class="absolute top-4 left-1/2 -translate-x-1/2 bg-white px-6 py-3 rounded-2xl shadow-lg border border-emerald-200 flex items-center space-x-4 z-20 animate-pulse"
                >
                  <div class="flex flex-col items-center">
                    <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider mb-1">Madre</span>
                    <div class="px-3 py-1.5 bg-slate-100 rounded border border-slate-200 min-w-[100px] text-center font-bold text-slate-700">
                      {{ pendingMother ? pendingMother.variedad : "Esperando..." }}
                    </div>
                  </div>
                  <div class="text-slate-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                  </div>
                  <div class="flex flex-col items-center">
                    <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider mb-1">Padre</span>
                    <div class="px-3 py-1.5 bg-slate-100 rounded border border-slate-200 min-w-[100px] text-center font-bold text-slate-700">
                      {{ pendingFather ? pendingFather.variedad : "Esperando..." }}
                    </div>
                  </div>
                </div>

                <!-- Manual Crosses Grid -->
                <div class="relative z-10 grid grid-cols-1 xl:grid-cols-2 gap-4">
                  <div
                    v-for="(cross, i) in manualCrosses"
                    :key="i"
                    class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 hover:shadow-md transition-all flex flex-col"
                  >
                    <div class="flex justify-between items-start mb-3">
                      <div class="flex items-center space-x-2">
                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded">Exitosa</span>
                        <span class="text-[10px] text-slate-500 font-medium bg-slate-100 px-2 py-0.5 rounded">VM: {{ cross.valor_merito }}</span>
                        <span
                          v-if="cross.causa_inviabilidad !== 'Ninguna'"
                          class="px-2 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-bold rounded max-w-[120px] truncate"
                          :title="cross.causa_inviabilidad"
                        >
                          {{ cross.causa_inviabilidad }}
                        </span>
                      </div>
                      <button @click="removeManualCross(cross)" class="text-slate-300 hover:text-rose-500 hover:bg-rose-50 rounded-full p-1 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                      </button>
                    </div>

                    <div class="flex items-center justify-between px-2 py-3 bg-slate-50/50 rounded-lg border border-slate-100 mb-3">
                      <div class="flex flex-col items-center flex-1 w-0">
                        <span class="text-[10px] text-emerald-700 font-bold uppercase tracking-wider mb-1">Madre</span>
                        <span class="font-bold text-slate-800 text-sm truncate w-full text-center" :title="cross.varA">{{ cross.varA }}</span>
                      </div>
                      <div class="px-2 text-slate-300 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                      </div>
                      <div class="flex flex-col items-center flex-1 w-0">
                        <span class="text-[10px] text-indigo-700 font-bold uppercase tracking-wider mb-1">Padre</span>
                        <span class="font-bold text-slate-800 text-sm truncate w-full text-center" :title="cross.varB">{{ cross.varB }}</span>
                      </div>
                    </div>

                    <div class="flex justify-between items-center pt-3 border-t border-slate-100 mt-auto">
                      <div class="flex flex-col">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Flores Asignadas</span>
                      </div>
                      <div class="flex items-center space-x-3 bg-slate-100 rounded-lg p-1 border border-slate-200">
                        <button
                          @click="
                            if (cross.flores_madre > 1) {
                              cross.flores_madre--;
                              cross.flores_padre--;
                            }
                          "
                          class="w-6 h-6 rounded bg-white shadow-sm flex items-center justify-center text-slate-600 hover:text-rose-600 hover:border-rose-200 border border-transparent transition-all"
                        >
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4" />
                          </svg>
                        </button>
                        <span class="font-bold text-sm text-slate-700 min-w-[20px] text-center">{{ cross.flores_madre }}</span>
                        <button
                          @click="
                            () => {
                              const limitM = getCantidadFlores(cross.varA) - getFloresUsadas(cross.varA, true);
                              const limitP = getCantidadFlores(cross.varB) - getFloresUsadas(cross.varB, false);
                              if (limitM > 0 && limitP > 0) {
                                cross.flores_madre++;
                                cross.flores_padre++;
                              } else {
                                toast.warning('No hay más flores disponibles para aumentar este cruce.');
                              }
                            }
                          "
                          class="w-6 h-6 rounded bg-emerald-600 shadow-sm flex items-center justify-center text-white hover:bg-emerald-700 transition-all"
                        >
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                          </svg>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Botones de Navegación -->
        <div class="flex justify-between items-center pt-2 flex-wrap gap-2 no-print">
          <div class="flex items-center">
            <router-link :to="{ name: 'crossing_matrix.show' }">
              <button
                type="button"
                class="flex items-center px-4 py-2 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-xl shadow-sm hover:bg-slate-50 transition-all duration-200"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Atrás
              </button>
            </router-link>
          </div>

          <!-- Botones de Acción y Exportación -->
          <div class="flex items-center space-x-2 flex-wrap gap-2">
            <button
              type="button"
              @click="exportarDesempenoIndividual"
              class="flex items-center px-4 py-2 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl shadow-sm hover:bg-emerald-100 transition-all duration-200"
              title="Descargar Memoria de Desempeño Individual"
            >
              <span class="mr-1.5">📊</span>
              Descargar Desempeño
            </button>

            <button
              type="button"
              @click="imprimirHojaCampo"
              class="flex items-center px-4 py-2 text-xs font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-xl shadow-sm hover:bg-indigo-100 transition-all duration-200"
              title="Imprimir Hoja de Campo en PDF"
            >
              <span class="mr-1.5">🖨️</span>
              Imprimir PDF
            </button>

            <button
              type="button"
              @click="descargarPNG"
              class="flex items-center px-4 py-2 text-xs font-bold text-teal-700 bg-teal-50 border border-teal-200 rounded-xl shadow-sm hover:bg-teal-100 transition-all duration-200"
              title="Descargar matriz como imagen (PNG)"
              :disabled="isDownloadingImage"
            >
              <span class="mr-1.5" v-if="!isDownloadingImage">🖼️</span>
              <svg v-else class="animate-spin h-3.5 w-3.5 mr-1.5 text-teal-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
              </svg>
              {{ isDownloadingImage ? "Generando..." : "Descargar PNG" }}
            </button>

            <div class="flex flex-col items-end">
              <div v-if="hasOverusedFlowers" class="text-[10px] text-rose-600 font-bold mb-1">⚠️ Excedes las flores disponibles</div>
              <button
                type="button"
                @click="finalizarProceso"
                :disabled="hasOverusedFlowers"
                class="flex items-center px-5 py-2 text-xs font-bold text-white bg-cenicana hover:bg-cenicana-800 disabled:opacity-50 disabled:cursor-not-allowed rounded-xl shadow-md transition-all duration-200"
              >
                Finalizar
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-else class="bg-white border border-emerald-100 rounded-2xl shadow-xl p-8 max-w-4xl mx-auto my-8 animate-fade-in-up">
      <div class="flex flex-col items-center text-center">
        <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mb-6">
          <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
          </svg>
        </div>
        <h2 class="text-3xl font-extrabold text-slate-800 mb-2">¡Cruzamientos Programados Exitosamente!</h2>
        <p class="text-slate-500 mb-8 max-w-lg">
          Se han guardado y oficializado <strong class="text-emerald-700">{{ resumenCrucesGuardados.length }} cruzamientos</strong> para el proyecto
          <strong class="text-emerald-700">{{ selectedCdCntble }}</strong
          >. Ahora forman parte del historial de Cenicaña.
        </p>

        <!-- Tabla Resumen -->
        <div class="w-full bg-slate-50 border border-slate-200 rounded-xl overflow-hidden mb-8">
          <div class="max-h-[300px] overflow-y-auto scrollbar-custom">
            <table class="min-w-full divide-y divide-slate-200">
              <thead class="bg-slate-100 sticky top-0 z-10">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Madre (Hembra)</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Padre (Macho)</th>
                  <th class="px-6 py-3 text-center text-xs font-bold text-slate-600 uppercase tracking-wider">Autofecundado</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-slate-100">
                <tr v-for="(cruce, index) in resumenCrucesGuardados" :key="index" class="hover:bg-slate-50 transition-colors">
                  <td class="px-6 py-3 whitespace-nowrap text-sm font-bold text-slate-800">
                    <span
                      class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-100"
                    >
                      {{ cruce.varA }}
                    </span>
                  </td>
                  <td class="px-6 py-3 whitespace-nowrap text-sm font-bold text-slate-800">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-sky-50 text-sky-800 border border-sky-100">
                      {{ cruce.varB }}
                    </span>
                  </td>
                  <td class="px-6 py-3 whitespace-nowrap text-center text-sm text-slate-500 font-semibold">
                    <span v-if="cruce.autofecundado" class="px-2 py-1 rounded bg-amber-100 text-amber-800 text-xs font-bold">SÍ</span>
                    <span v-else class="text-slate-300">—</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Botones de Acción -->
        <div class="flex flex-col sm:flex-row items-center justify-center space-y-3 sm:space-y-0 sm:space-x-4">
          <button
            @click="router.push({ name: 'crossing_list.show' })"
            class="w-full sm:w-auto px-6 py-3 text-sm font-bold text-white bg-cenicana hover:bg-cenicana-800 rounded-xl shadow-md transition-all duration-200 flex items-center justify-center"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
              ></path>
            </svg>
            Ver Historial Completo
          </button>
          <button
            @click="router.push({ name: 'crossing_initial_data.show' })"
            class="w-full sm:w-auto px-6 py-3 text-sm font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 hover:text-slate-900 rounded-xl shadow-sm transition-all duration-200 flex items-center justify-center"
          >
            <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Programar Nuevo Proyecto
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Ajuste de Flores -->
    <div
      v-if="flowerModalData"
      class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center"
      @click.self="closeFlowerAdjustmentModal"
    >
      <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-6 max-w-sm w-full mx-4">
        <h3 class="text-lg font-bold text-slate-800 mb-1">Ajustar Consumo de Flores</h3>
        <p class="text-xs text-slate-500 mb-4">
          Cruce: <strong>{{ flowerModalData.varA }}</strong> × <strong>{{ flowerModalData.varB }}</strong>
        </p>

        <div class="space-y-4">
          <div class="flex items-center justify-between bg-slate-50 p-3 rounded-lg border border-slate-100">
            <div>
              <div class="text-xs font-bold text-slate-700">Madre ({{ flowerModalData.varA }})</div>
              <div class="text-[10px] text-slate-500">Flores a consumir</div>
            </div>
            <div class="flex items-center space-x-3 bg-white rounded border border-slate-200 px-2 py-1 shadow-sm">
              <button
                @click="flowerModalData.flores_madre = Math.max(1, (flowerModalData.flores_madre ?? 1) - 1)"
                class="text-slate-500 hover:text-rose-600 font-bold transition-colors w-5 h-5 flex items-center justify-center"
              >
                -
              </button>
              <span class="text-xs font-black text-slate-800 w-4 text-center">{{ flowerModalData.flores_madre ?? 1 }}</span>
              <button
                @click="flowerModalData.flores_madre = (flowerModalData.flores_madre ?? 1) + 1"
                class="text-slate-500 hover:text-emerald-600 font-bold transition-colors w-5 h-5 flex items-center justify-center"
              >
                +
              </button>
            </div>
          </div>

          <div class="flex items-center justify-between bg-slate-50 p-3 rounded-lg border border-slate-100">
            <div>
              <div class="text-xs font-bold text-slate-700">Padre ({{ flowerModalData.varB }})</div>
              <div class="text-[10px] text-slate-500">Flores a consumir</div>
            </div>
            <div class="flex items-center space-x-3 bg-white rounded border border-slate-200 px-2 py-1 shadow-sm">
              <button
                @click="flowerModalData.flores_padre = Math.max(1, (flowerModalData.flores_padre ?? 1) - 1)"
                class="text-slate-500 hover:text-rose-600 font-bold transition-colors w-5 h-5 flex items-center justify-center"
              >
                -
              </button>
              <span class="text-xs font-black text-slate-800 w-4 text-center">{{ flowerModalData.flores_padre ?? 1 }}</span>
              <button
                @click="flowerModalData.flores_padre = (flowerModalData.flores_padre ?? 1) + 1"
                class="text-slate-500 hover:text-emerald-600 font-bold transition-colors w-5 h-5 flex items-center justify-center"
              >
                +
              </button>
            </div>
          </div>
        </div>

        <div class="mt-6 flex justify-end">
          <button
            @click="closeFlowerAdjustmentModal"
            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition-colors"
          >
            Confirmar
          </button>
        </div>
      </div>
    </div>

    <!-- Drawer de Hoja de Vida de la Variedad (Quick Drawer) -->
    <VarietyProfileDrawer v-model:isOpen="isDrawerOpen" :varietyName="selectedVarietyForDrawer" />

    <!-- Modal de Comparación Lado a Lado -->
    <ParentComparatorModal
      v-model:isOpen="isComparatorOpen"
      :motherName="comparatorMother"
      :fatherName="comparatorFather"
      :initiallyViable="comparatorInitiallyViable"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed, watch } from "vue";
import { useRouter } from "vue-router";
import { useToast } from "vue-toastification";
import { useSuggestionCrossingPerProjectStore } from "@/stores/crossingsuggestionperproject";
import { useParametizeWeightedCrossingStore } from "@/stores/crossignparametizeweighted";
import VarietyProfileDrawer from "@/components/VarietyProfileDrawer.vue";
import ParentComparatorModal from "@/components/ParentComparatorModal.vue";
import SuggestionMasterDetail from "@/components/SuggestionMasterDetail.vue";
import SuggestionTableDetail from "@/components/SuggestionTableDetail.vue";
import * as XLSX from "xlsx";
import ExcelJS from "exceljs";
import html2canvas from "html2canvas";
import CrossingsService from "@/services/crossings.services";

const SuggestionCrossingPerProjectStore = useSuggestionCrossingPerProjectStore();
const ParametizeWeightedStore = useParametizeWeightedCrossingStore();
const router = useRouter();
const toast = useToast();
const selectedCdCntble = ref(localStorage.getItem("selectedCdCntble") || "");
const selectedMegaAmbiente = ref(localStorage.getItem("selectedMegaAmbiente") || "");

const autofecundacionesSeleccionadas = ref<Set<string>>(new Set());
const selectedVariety = ref(localStorage.getItem("selectedVariety") || "");
const selectedIdProject = ref(localStorage.getItem("selectedIdProject") || "");
const draftKey = computed(() => `sivarcc_draft_crossings_${selectedCdCntble.value}_${selectedMegaAmbiente.value}`);
const isSaving = ref(false);
const isFinished = ref(false);
const resumenCrucesGuardados = ref<any[]>([]);

const ocultarInviables = ref(true); // Vista compacta limpia por defecto
const isLoading = ref(false); // Ref para spinner de carga
const isOptimizing = ref(false); // Ref para spinner de optimización
const optimizandoMadre = ref(""); // Para mostrar en el loading qué variedad se procesa
const isExpanded = ref(false); // Ref para modo pantalla completa
const isDragDropView = ref(false); // Ref para alternar a Drag & Drop

// --- NUEVA VISTA MAESTRO-DETALLE ---
const vistaActual = ref<'cuadricula' | 'asistente' | 'asistente-pro'>('cuadricula');
const selectedFemaleRow = ref<any>(null);
const assistantOcultarRiesgos = ref(true);

const assistantFemales = computed(() => {
  return (viabilidadesMatriz.value || []).filter((row: any) => {
    return row && row.length > 0 && Number(row[0]?.polen) <= 20 && row[0]?.varA !== selectedVariety.value;
  });
});

const assistantSortedMales = computed(() => {
  if (!selectedFemaleRow.value) return [];
  const cells = [...selectedFemaleRow.value];
  
  let males = cells.filter((cell: any) => {
    return cell && Number(cell.polen2) > 20 && cell.varB !== selectedVariety.value;
  });
  
  if (assistantOcultarRiesgos.value) {
    males = males.filter((cell: any) => {
      const dg = Number(getDistancia(cell.varA, cell.varB));
      return isNaN(dg) || dg >= 0.35;
    });
  }
  
  males.sort((a: any, b: any) => {
    if (tipoMapaCalor.value === 'ic') {
      const icA = getIndiceCombinado(a.varA, a.varB, a.vm2);
      const icB = getIndiceCombinado(b.varA, b.varB, b.vm2);
      return (isNaN(icB) ? 0 : icB) - (isNaN(icA) ? 0 : icA);
    } else {
      // Sort by DG (Distancia Genética) by default
      const dgA = Number(getDistancia(a.varA, a.varB));
      const dgB = Number(getDistancia(b.varA, b.varB));
      return (isNaN(dgB) ? 0 : dgB) - (isNaN(dgA) ? 0 : dgA);
    }
  });
  
  return males;
});
// -----------------------------------

// Variables de Estado para Drag & Drop
const activeDragType = ref<"madre" | "padre" | null>(null);
const activeDragItem = ref<any>(null);
const pendingMother = ref<any>(null);
const pendingFather = ref<any>(null);
const tipoMapaCalor = ref("dg"); // Vista con mapa de calor por defecto

function getIndiceCombinado(varA: string, varB: string, vm: number | string) {
  const dgVal = Number(getDistancia(varA, varB));
  const vmVal = Number(vm);

  if (isNaN(dgVal) || isNaN(vmVal)) return NaN;

  // Normalizar VM: 1 es excelente (100 pts), 9 es malo (0 pts). Rango de 8.
  let puntajeVM = ((9.0 - vmVal) / 8.0) * 100;
  if (puntajeVM < 0) puntajeVM = 0;
  if (puntajeVM > 100) puntajeVM = 100;

  // Normalizar DG: Topado en 0.70 (100 pts)
  let puntajeDG = (dgVal / 0.7) * 100;
  if (puntajeDG > 100) puntajeDG = 100;
  if (puntajeDG < 0) puntajeDG = 0;

  // Índice Combinado (60% VM, 40% DG)
  return puntajeVM * 0.6 + puntajeDG * 0.4;
}

function isDarkBackground(varA: string, varB: string, vm: number | string) {
  if (tipoMapaCalor.value === "ic") {
    const ic = getIndiceCombinado(varA, varB, vm);
    return !isNaN(ic) && (ic >= 80 || ic < 50); // Indigo y Rose son oscuros
  } else if (tipoMapaCalor.value === "dg") {
    const dgVal = getDistancia(varA, varB);
    const val = Number(dgVal);
    // Es oscuro si es azul (>= 0.65) o naranja (< 0.35)
    return !isNaN(val) && (val >= 0.65 || val < 0.35);
  }
  return false;
}

function getHeatmapClass(varA: string, varB: string, viabilidad: boolean, vm: number | string) {
  // Sin opacidad para personas daltónicas, el contraste completo ayuda a diferenciar
  const unselectedClasses = !viabilidad ? "" : "";

  if (tipoMapaCalor.value === "none") {
    if (!viabilidad) return "bg-slate-50/50 hover:bg-slate-100/50 border-r border-slate-100 text-slate-400 opacity-60";
    return "bg-emerald-50/50 hover:bg-emerald-100/50 border-r border-emerald-100/50 text-emerald-800";
  }

  if (tipoMapaCalor.value === "ic") {
    const ic = getIndiceCombinado(varA, varB, vm);
    if (isNaN(ic)) return "bg-emerald-50/50 border-r border-emerald-100/50 text-emerald-800 hover:bg-emerald-100/50" + unselectedClasses;

    if (ic >= 80) return "bg-indigo-600/90 text-white font-black border-r border-indigo-700/50 shadow-inner hover:bg-indigo-700" + unselectedClasses;
    if (ic >= 65) return "bg-sky-400/80 text-slate-900 font-extrabold border-r border-sky-500/30 hover:bg-sky-500/90" + unselectedClasses;
    if (ic >= 50) return "bg-slate-200/60 text-slate-800 font-bold border-r border-slate-300/30 hover:bg-slate-300/80" + unselectedClasses;

    return "bg-rose-500/90 text-white font-semibold border-r border-rose-600/30 hover:bg-rose-600/80" + unselectedClasses;
  }

  // Fallback a DG
  const dgVal = getDistancia(varA, varB);
  const val = Number(dgVal);
  if (isNaN(val)) {
    return "bg-emerald-50/50 border-r border-emerald-100/50 text-emerald-800 hover:bg-emerald-100/50" + unselectedClasses;
  }

  if (val >= 0.65) {
    return "bg-blue-600/90 text-white font-black border-r border-blue-700/50 shadow-inner hover:bg-blue-700" + unselectedClasses;
  } else if (val >= 0.55) {
    return "bg-sky-400/60 text-slate-900 font-extrabold border-r border-sky-500/30 hover:bg-sky-500/50" + unselectedClasses;
  } else if (val >= 0.45) {
    return "bg-slate-200/60 text-slate-800 font-bold border-r border-slate-300/30 hover:bg-slate-300/40" + unselectedClasses;
  } else if (val >= 0.35) {
    return "bg-amber-300/80 text-amber-900 font-semibold border-r border-amber-400/30 hover:bg-amber-400/50" + unselectedClasses;
  } else {
    return "bg-orange-500/90 text-white font-semibold border-r border-orange-600/30 hover:bg-orange-600/80" + unselectedClasses;
  }
}

const getRowVm = (row: any[]) => {
  if (!row || row.length === 0) return "0";
  const validCell = row.find((cell) => cell && cell.vm !== 1 && cell.vm !== "1" && cell.vm !== 0 && cell.vm !== "0");
  return validCell ? validCell.vm : row[0]?.vm || "0";
};

// Estados para el Drawer de variedades
const isDrawerOpen = ref(false);
const selectedVarietyForDrawer = ref("");

const openVarietyProfile = (name: string) => {
  if (name && name !== "null" && name !== "?") {
    selectedVarietyForDrawer.value = name;
    isDrawerOpen.value = true;
  }
};

// Estados para el Comparador Lado a Lado
const isComparatorOpen = ref(false);
const comparatorMother = ref("");
const comparatorFather = ref("");
const comparatorInitiallyViable = ref(true);

const openParentComparator = (mother: string, father: string, viable: boolean) => {
  if (mother && father) {
    comparatorMother.value = mother;
    comparatorFather.value = father;
    comparatorInitiallyViable.value = viable;
    isComparatorOpen.value = true;
  }
};

// Manejador de teclado para la tecla Escape
const handleKeydown = (e: KeyboardEvent) => {
  if (e.key === "Escape" && isExpanded.value) {
    isExpanded.value = false;
  }
};

// Cargar datos iniciales
onMounted(() => {
  loadSuggestionCrossings();
  window.addEventListener("keydown", handleKeydown);
});

onUnmounted(() => {
  window.removeEventListener("keydown", handleKeydown);
});

watch([selectedIdProject, selectedMegaAmbiente, selectedCdCntble, selectedVariety], loadSuggestionCrossings);

async function loadSuggestionCrossings() {
  if (selectedIdProject.value && selectedMegaAmbiente.value && selectedCdCntble.value && selectedVariety.value) {
    isLoading.value = true;
    try {
      await SuggestionCrossingPerProjectStore.getSuggestionCrossingPerProjectList(
        selectedIdProject.value,
        selectedCdCntble.value,
        selectedVariety.value,
        selectedMegaAmbiente.value
      );

      // Restaurar borrador de cruzamientos deshabilitados si existe
      const storedDraft = localStorage.getItem(draftKey.value);
      if (storedDraft) {
        const disabledCrosses = JSON.parse(storedDraft);
        const rows = viabilidadesMatriz.value || [];
        rows.forEach((row: any) => {
          row.forEach((car: any) => {
            if (car) {
              const matches = disabledCrosses.some((d: any) => d.varA === car.varA && d.varB === car.varB);
              if (matches) {
                car.viabilidad = false;
              }
            }
          });
        });
      } else {
        // Si no hay borrador, optimizar automáticamente por defecto para no desbordar el inventario
        await autoOptimizarFlores(true);
      }
    } catch (error) {
      console.error("Error al cargar cruzamientos por proyecto:", error);
    } finally {
      isLoading.value = false;
    }
  }
}

const cantidadesMap = computed(() => {
  const rawFlores = SuggestionCrossingPerProjectStore.suggestionCrossingsPerProjectFilter?.flores || [];
  const map: Record<string, number> = {};
  rawFlores.forEach((f: any) => {
    map[f.vrdad] = f.numero;
  });
  return map;
});

const floresSeleccionadas = computed(() => {
  const rows = viabilidadesMatriz.value || [];
  if (rows.length > 0 && rows[0]) {
    return rows[0].map((cell: any) => ({
      variedad: cell.varB,
      cantidad: cantidadesMap.value[cell.varB] || 0,
      polen: cell.polen2
    }));
  }

  // Fallback
  const rawFlores = SuggestionCrossingPerProjectStore.suggestionCrossingsPerProjectFilter?.flores || [];
  return rawFlores.map((flor: any) => ({
    variedad: flor.vrdad,
    cantidad: flor.numero,
    polen: flor.polen || 0
  }));
});

/**
 * Modificación realizada por: Jhon Henry Trujillo PhD
 * Fecha: 2026-05-16
 * Propósito: Reestructurar la obtención de la matriz de viabilidades eliminando la
 * lógica defectuosa de anidamiento 3D que rompía la vista de la tabla y causaba problemas de interfaz.
 */
const viabilidadesMatriz = computed(() => {
  return SuggestionCrossingPerProjectStore.suggestionCrossingsPerProjectFilter?.viabilidades || [];
});

// Helper para verificar si un índice de columna (Padre) tiene al menos un cruce viable
const isColumnViable = (indexCol: number) => {
  const rows = viabilidadesMatriz.value || [];
  return rows.some((row: any) => row[indexCol]?.viabilidad);
};

// Helper para verificar si una fila (Madre) tiene al menos un cruce viable
const isRowViable = (viabilidadRow: any[]) => {
  return viabilidadRow.some((car: any) => car?.viabilidad);
};

// Helper reactivo para verificar si hay al menos UN cruce viable en toda la matriz
const hasAnyViableCrossing = computed(() => {
  const rows = viabilidadesMatriz.value || [];
  return rows.some((row: any) => row.some((car: any) => car?.viabilidad));
});

// Métodos auxiliares
function getCantidadFlores(vrdad: string) {
  return Number(cantidadesMap.value[vrdad] || 0);
}

function getFloresUsadas(vrdad: string, isMadre: boolean) {
  let count = 0;
  const rows = viabilidadesMatriz.value || [];

  rows.forEach((row: any) => {
    row.forEach((car: any) => {
      if (car?.viabilidad) {
        if (isMadre && car.varA === vrdad) {
          count += Number(car.flores_madre ?? 1);
        } else if (!isMadre && car.varB === vrdad) {
          count += Number(car.flores_padre ?? 1);
        }
      }
    });
  });

  if (isMadre && autofecundacionesSeleccionadas.value.has(vrdad)) {
    count += 1;
  }
  return count;
}

const flowerModalData = ref<any>(null);

function openFlowerAdjustmentModal(car: any) {
  car.flores_madre = car.flores_madre ?? 1;
  car.flores_padre = car.flores_padre ?? 1;
  flowerModalData.value = car;
}

function closeFlowerAdjustmentModal() {
  flowerModalData.value = null;
}

function getIcon(polen: string | number) {
  return +polen <= 20 ? "fa fa-venus text-rose-500 font-bold" : "fa fa-mars text-sky-500 font-bold";
}

const hasOverusedFlowers = computed(() => {
  // Check Mothers
  const rows = viabilidadesMatriz.value || [];
  for (const row of rows) {
    if (row && row.length > 0) {
      const madre = row[0]?.varA;
      if (madre && getFloresUsadas(madre, true) > getCantidadFlores(madre)) {
        return true;
      }
    }
  }
  // Check Fathers
  const padres = floresSeleccionadas.value || [];
  for (const padre of padres) {
    if (padre.variedad && getFloresUsadas(padre.variedad, false) > Number(padre.cantidad)) {
      return true;
    }
  }
  return false;
});

function toggleCruzamiento(car: any) {
  if (!car.viabilidad) {
    car.viabilidad = true;
    car.flores_madre = 1;
    car.flores_padre = 1;
  } else {
    car.viabilidad = false;
    car.flores_madre = 0;
    car.flores_padre = 0;
  }

  // Guardar estado actual de cruzamientos deshabilitados en localStorage (Auto-save)
  const rows = viabilidadesMatriz.value || [];
  const disabledCrosses: Array<{ varA: string; varB: string }> = [];
  rows.forEach((row: any) => {
    row.forEach((c: any) => {
      if (c && c.viabilidad === false) {
        disabledCrosses.push({ varA: c.varA, varB: c.varB });
      }
    });
  });

  localStorage.setItem(draftKey.value, JSON.stringify(disabledCrosses));
}

function toggleAutofecundar(viabilidadRow: any) {
  const varA = viabilidadRow[0]?.varA;
  if (!varA) return;

  if (autofecundacionesSeleccionadas.value.has(varA)) {
    autofecundacionesSeleccionadas.value.delete(varA);
  } else {
    autofecundacionesSeleccionadas.value.add(varA);
  }
}

// Caché para getDistancia
let distanciasCache: Map<string, Map<string, any>> | null = null;

function buildDistanciasCache() {
  const distancias = SuggestionCrossingPerProjectStore.suggestionCrossingsPerProjectFilter?.distancias || {};
  const cache = new Map<string, Map<string, any>>();

  for (const [keyA, subDist] of Object.entries(distancias)) {
    const cleanA = keyA.trim().toUpperCase();
    const subCache = new Map<string, any>();

    if (subDist && typeof subDist === "object") {
      for (const [keyB, val] of Object.entries(subDist)) {
        subCache.set(keyB.trim().toUpperCase(), val);
      }
    }
    cache.set(cleanA, subCache);
  }
  distanciasCache = cache;
}

function getDistancia(varA: string, varB: string) {
  if (!varA || !varB) return "NA";

  if (!distanciasCache) {
    buildDistanciasCache();
  }

  const cleanA = varA.trim().toUpperCase();
  const cleanB = varB.trim().toUpperCase();

  const subDist = distanciasCache?.get(cleanA);
  if (!subDist) return "NA";

  const val = subDist.get(cleanB);
  return val !== null && val !== undefined ? String(val) : "NA";
}

function getCausaInviabilidad(cell: any): string {
  if (cell.viabilidad) return "-";

  const filterData = SuggestionCrossingPerProjectStore.suggestionCrossingsPerProjectFilter || {};
  const ponderados = ParametizeWeightedStore.parametizeWeightedCrossingFilter?.ponderados || [];
  const floresBG = filterData.flores_bg || [];
  const floresPR = filterData.flores_pr || [];
  const floresEIII = filterData.flores_eiii || [];
  const testigoLimpio = filterData.testigo_limpio || {};
  // Caché estática para findVarietyData para evitar millones de operaciones .find()
  if (!(window as any).__varietyDataCache) {
    const map = new Map<string, any>();
    [...floresBG, ...floresPR, ...floresEIII].forEach((f: any) => {
      if (f && f.vrdad) map.set(f.vrdad.trim().toUpperCase(), f);
    });
    (window as any).__varietyDataCache = map;
  }

  const findVarietyData = (varName: string) => {
    if (!varName) return undefined;
    return (window as any).__varietyDataCache.get(varName.trim().toUpperCase());
  };

  const florAData = findVarietyData(cell.varA);
  const florBData = findVarietyData(cell.varB);

  const motivos: string[] = [];

  // Verificar autofecundación
  if (cell.varA && cell.varB && cell.varA.trim() === cell.varB.trim()) {
    motivos.push("Restricción de Autogamia");
  }

  // Verificar incompatibilidad de sexo
  if (florAData && florBData) {
    const sxoA = florAData.sxo || "";
    const sxoB = florBData.sxo || "";
    const isA_Female = ["Hembra", "HD", "HF"].includes(sxoA);
    const isB_Female = ["Hembra", "HD", "HF"].includes(sxoB);
    if (isA_Female && isB_Female) {
      motivos.push("Incompatibilidad de sexo (Hembra x Hembra)");
    } else if (isB_Female) {
      motivos.push("Incompatibilidad de sexo (Macho es Hembra)");
    }
  }

  if (florAData && florBData) {
    ponderados.forEach((p: any) => {
      const caracteristica = p.equivalente ? p.equivalente.toLowerCase() : "";
      const limiteMax = p.nivel;
      if (limiteMax !== null && limiteMax !== undefined && caracteristica) {
        const valorRealA = florAData[caracteristica] ?? "-";
        const valorRealB = florBData[caracteristica] ?? "-";

        if (valorRealA !== "-" && valorRealB !== "-" && valorRealA !== null && valorRealB !== null) {
          let lvlA = 999;
          let lvlB = 999;

          if (caracteristica === "msco_r" || caracteristica === "carbon") {
            [
              { val: Number(valorRealA), setLvl: (l: number) => (lvlA = l) },
              { val: Number(valorRealB), setLvl: (l: number) => (lvlB = l) }
            ].forEach(({ val, setLvl }) => {
              if (val <= 2) setLvl(1);
              else if (val > 2 && val <= 3) setLvl(2);
              else if (val > 3 && val <= 5) setLvl(3);
              else if (val > 5 && val <= 8) setLvl(4);
              else if (val > 8 && val <= 11) setLvl(5);
              else if (val > 11 && val <= 15) setLvl(6);
              else if (val > 15 && val <= 22) setLvl(7);
              else if (val > 22 && val <= 30) setLvl(8);
              else setLvl(9);
            });
          } else if (
            caracteristica === "tchm" ||
            caracteristica === "scrsa" ||
            caracteristica === "dmtro_tllo" ||
            caracteristica === "altura_planta" ||
            caracteristica === "poblacion"
          ) {
            const valTestigo = testigoLimpio[caracteristica];
            if (valTestigo) {
              [
                { val: Number(valorRealA), setLvl: (l: number) => (lvlA = l) },
                { val: Number(valorRealB), setLvl: (l: number) => (lvlB = l) }
              ].forEach(({ val, setLvl }) => {
                const pct = (val * 100) / Number(valTestigo);
                if (pct > 120) setLvl(1);
                else if (pct < 120 && pct >= 110) setLvl(2);
                else if (pct < 110 && pct >= 95) setLvl(3);
                else if (pct < 95 && pct >= 85) setLvl(4);
                else setLvl(5);
              });
            }
          } else if (caracteristica === "volcamiento") {
            const valTestigo = testigoLimpio[caracteristica];
            if (valTestigo) {
              [
                { val: Number(valorRealA), setLvl: (l: number) => (lvlA = l) },
                { val: Number(valorRealB), setLvl: (l: number) => (lvlB = l) }
              ].forEach(({ val, setLvl }) => {
                const pct = (val * 100) / Number(valTestigo);
                if (pct < 10) setLvl(1);
                else if (pct < 20 && pct >= 11) setLvl(2);
                else if (pct < 30 && pct >= 21) setLvl(3);
                else if (pct < 49 && pct >= 31) setLvl(4);
                else setLvl(5);
              });
            }
          } else if (caracteristica === "rya_cfe_r" || caracteristica === "roya_naranja") {
            lvlA = Number(valorRealA);
            lvlB = Number(valorRealB);
          }

          if (lvlA !== 999 && lvlB !== 999 && lvlA + lvlB > Number(limiteMax)) {
            motivos.push(`${p.equivalente.toUpperCase()} excede límite`);
          }
        }
      }
    });
  } else {
    motivos.push("Falta de información agronómica");
  }

  // Si tiene viabilidad manual override (ej: restaurado del localStorage como deshabilitado)
  const storedDraft = localStorage.getItem(draftKey.value);
  if (storedDraft) {
    const disabledCrosses = JSON.parse(storedDraft);
    const matches = disabledCrosses.some((d: any) => d.varA === cell.varA && d.varB === cell.varB);
    if (matches) {
      motivos.push("Excluido por usuario");
    }
  }

  return motivos.length > 0 ? motivos.join(" | ") : "Veto manual o restricciones de autogamia";
}

/**
 * Modificación realizada por: Jhon Henry Trujillo PhD
 * Fecha: 2026-05-16
 * Propósito: Añadir la función finalizarProceso para completar exitosamente el flujo de trabajo,
 * mostrando una notificación visual (toast) y redirigiendo al usuario de vuelta a la lista general de cruces.
 */
async function exportarDesempenoIndividual() {
  try {
    const filterData = SuggestionCrossingPerProjectStore.suggestionCrossingsPerProjectFilter || {};
    const viabilidad = viabilidadesMatriz.value || [];

    // Cargar ponderados si no están listos
    if (!ParametizeWeightedStore.parametizeWeightedCrossingFilter || !ParametizeWeightedStore.parametizeWeightedCrossingFilter.ponderados) {
      await ParametizeWeightedStore.getParametizeWeightedCrossingList(selectedCdCntble.value, selectedMegaAmbiente.value);
    }
    const ponderados = ParametizeWeightedStore.parametizeWeightedCrossingFilter.ponderados || [];

    // Obtener listas de perfiles completos de variedades
    const floresBG = filterData.flores_bg || [];
    const floresPR = filterData.flores_pr || [];
    const floresEIII = filterData.flores_eiii || [];
    const testigoLimpio = filterData.testigo_limpio || {};

    const findVarietyData = (varName) => {
      if (!varName) return undefined;
      const cleanVar = varName.trim().toUpperCase();
      let data = floresBG.find((f) => f.vrdad && f.vrdad.trim().toUpperCase() === cleanVar);
      if (!data) data = floresPR.find((f) => f.vrdad && f.vrdad.trim().toUpperCase() === cleanVar);
      if (!data) data = floresEIII.find((f) => f.vrdad && f.vrdad.trim().toUpperCase() === cleanVar);
      return data;
    };

    const headers = [
      "Variedad Evaluada",
      "Rol (Madre/Padre)",
      "Característica Evaluada",
      "Valor Bruto (Variedad)",
      "Valor Testigo",
      "Porcentaje vs Testigo (%)",
      "Rango Evaluado (Regla Sistema)",
      "Nivel Obtenido",
      "Límite Proyecto",
      "¿Cumple Límite Individual?"
    ];

    const headersVM = ["Variedad Evaluada", "Rol (Madre/Padre)", "Característica", "Nivel Obtenido", "Porcentaje (%)", "Aporte al VM (Nivel × % / 100)"];

    const rows = [];
    const rowsVM = [];
    const procesadas = new Set();

    if (viabilidad.length > 0) {
      viabilidad.forEach((row) => {
        if (row && row.length > 0) {
          row.forEach((cell) => {
            if (cell) {
              [
                { varName: cell.varA, rol: "Madre", florData: findVarietyData(cell.varA) },
                { varName: cell.varB, rol: "Padre", florData: findVarietyData(cell.varB) }
              ].forEach(({ varName, rol, florData }) => {
                if (varName && varName !== selectedVariety.value && !procesadas.has(varName) && florData) {
                  procesadas.add(varName);
                  let totalVM = 0;

                  ponderados.forEach((p) => {
                    const caracteristica = p.equivalente ? p.equivalente.toLowerCase() : p.caracteristica || p.nombre || "UNKNOWN";
                    const nombreCaract = p.nombre || p.caracteristica || caracteristica;
                    if (caracteristica) {
                      const valorReal = florData[caracteristica] ?? "-";
                      const valorTestigo = testigoLimpio[caracteristica] ?? "-";
                      const limiteMax = p.nivel !== null && p.nivel !== undefined ? Number(p.nivel) : "-";
                      const porcentajePeso = p.ponderado ? Number(p.ponderado) : 0;

                      let porcentaje = "-";
                      const isEnfermedad =
                        caracteristica === "msco_r" || caracteristica === "carbon" || caracteristica === "rya_cfe_r" || caracteristica === "roya_naranja";

                      if (isEnfermedad) {
                        porcentaje = "N/A (Evaluación directa)";
                      } else if (valorReal !== "-" && valorTestigo !== "-" && valorReal !== null && valorTestigo !== null && Number(valorTestigo) > 0) {
                        porcentaje = ((Number(valorReal) * 100) / Number(valorTestigo)).toFixed(2) + "%";
                      }

                      let nivel = "-";
                      let rangoRegla = "N/A";
                      let cumpleLimite = "-";
                      let aporteVM = "-";

                      if (valorReal !== "-" && valorReal !== null && valorReal !== "") {
                        let lvl = 999;

                        if (caracteristica === "msco_r" || caracteristica === "carbon") {
                          rangoRegla = "N1: <=2% | N2: 2.1-3% | N3: 3.1-5% | N4: 5.1-8% | N5: 8.1-11% | N6: 11.1-15% | N7: 15.1-22% | N8: 22.1-30% | N9: >30%";
                          const val = Number(valorReal);
                          if (val <= 2) lvl = 1;
                          else if (val > 2 && val <= 3) lvl = 2;
                          else if (val > 3 && val <= 5) lvl = 3;
                          else if (val > 5 && val <= 8) lvl = 4;
                          else if (val > 8 && val <= 11) lvl = 5;
                          else if (val > 11 && val <= 15) lvl = 6;
                          else if (val > 15 && val <= 22) lvl = 7;
                          else if (val > 22 && val <= 30) lvl = 8;
                          else lvl = 9;
                        } else if (
                          caracteristica === "tchm" ||
                          caracteristica === "scrsa" ||
                          caracteristica === "dmtro_tllo" ||
                          caracteristica === "altura_planta" ||
                          caracteristica === "poblacion"
                        ) {
                          rangoRegla = "N1: >120% | N2: 110-120% | N3: 95-109.9% | N4: 85-94.9% | N5: <85%";
                          if (valorTestigo !== "-" && valorTestigo !== null && Number(valorTestigo) > 0) {
                            const pct = (Number(valorReal) * 100) / Number(valorTestigo);
                            if (pct > 120) lvl = 1;
                            else if (pct < 120 && pct >= 110) lvl = 2;
                            else if (pct < 110 && pct >= 95) lvl = 3;
                            else if (pct < 95 && pct >= 85) lvl = 4;
                            else lvl = 5;
                          } else {
                            rangoRegla += " (Falta testigo)";
                          }
                        } else if (caracteristica === "volcamiento") {
                          rangoRegla = "N1: <10% | N2: 10-19.9% | N3: 20-29.9% | N4: 30-48.9% | N5: >=49% (Menor es mejor)";
                          if (valorTestigo !== "-" && valorTestigo !== null && Number(valorTestigo) > 0) {
                            const pct = (Number(valorReal) * 100) / Number(valorTestigo);
                            if (pct < 10) lvl = 1;
                            else if (pct < 20 && pct >= 11) lvl = 2;
                            else if (pct < 30 && pct >= 21) lvl = 3;
                            else if (pct < 49 && pct >= 31) lvl = 4;
                            else lvl = 5;
                          } else {
                            rangoRegla += " (Falta testigo)";
                          }
                        } else if (caracteristica === "rya_cfe_r" || caracteristica === "roya_naranja") {
                          rangoRegla = "Valor directo de Base de Datos";
                          lvl = Number(valorReal);
                        } else {
                          rangoRegla = "Sin regla configurada";
                        }

                        if (lvl !== 999) {
                          nivel = lvl.toString();
                          if (limiteMax !== "-") {
                            cumpleLimite = lvl <= Number(limiteMax) ? "SÍ" : "NO";
                          }
                          const ap = (lvl * porcentajePeso) / 100;
                          aporteVM = ap.toFixed(2);
                          totalVM += ap;
                        }
                      } else {
                        rangoRegla = "Sin datos (Nivel 0 automático)";
                        nivel = "0";
                        aporteVM = "0.00";
                      }

                      rows.push([varName, rol, caracteristica.toUpperCase(), valorReal, valorTestigo, porcentaje, rangoRegla, nivel, limiteMax, cumpleLimite]);

                      if (porcentajePeso > 0) {
                        rowsVM.push([varName, rol, nombreCaract.toUpperCase(), nivel, porcentajePeso.toFixed(2) + "%", Number(aporteVM)]);
                      }
                    }
                  });

                  if (rowsVM.length > 0 && rowsVM[rowsVM.length - 1][0] === varName) {
                    rowsVM.push([varName, rol, "TOTAL VM", "-", "100%", Number(totalVM.toFixed(2))]);
                    // Add empty row for spacing
                    rowsVM.push(["", "", "", "", "", ""]);
                  }
                }
              });
            }
          });
        }
      });
    }

    if (rows.length === 0) {
      toast.warning("No hay datos suficientes para exportar.");
      return;
    }

    const workbook = new ExcelJS.Workbook();

    // ----- HOJA 1: Desempeño -----
    const sheet = workbook.addWorksheet("Desempeño Individual");

    const headerContent = [
      ["CENICAÑA - MEMORIA DE DESEMPEÑO INDIVIDUAL"],
      ["Proyecto ID:", selectedCdCntble.value],
      ["Mega Ambiente:", selectedMegaAmbiente.value],
      ["Testigo de Referencia:", selectedVariety.value],
      ["Fecha de Exportación:", new Date().toLocaleDateString("es-ES")],
      [],
      headers,
      ...rows
    ];

    headerContent.forEach((r) => sheet.addRow(r));

    sheet.mergeCells("A1:I1");
    const titleCell = sheet.getCell("A1");
    titleCell.font = { size: 16, bold: true, color: { argb: "FF0B4A2F" } };
    titleCell.alignment = { horizontal: "center" };

    const headerRow = sheet.getRow(7);
    headerRow.font = { bold: true, color: { argb: "FFFFFFFF" } };
    headerRow.eachCell((cell) => {
      cell.fill = {
        type: "pattern",
        pattern: "solid",
        fgColor: { argb: "FF0E7490" }
      };
      cell.border = {
        top: { style: "thin" },
        left: { style: "thin" },
        bottom: { style: "thin" },
        right: { style: "thin" }
      };
    });

    // ----- HOJA 2: Valores de Mérito -----
    const sheetVM = workbook.addWorksheet("Cálculo Valores de Mérito");
    const headerVMContent = [
      ["CENICAÑA - CÁLCULO DEL VALOR DE MÉRITO (VM)"],
      ["Proyecto ID:", selectedCdCntble.value],
      ["Mega Ambiente:", selectedMegaAmbiente.value],
      ["Fecha de Exportación:", new Date().toLocaleDateString("es-ES")],
      [],
      headersVM,
      ...rowsVM
    ];

    headerVMContent.forEach((r) => sheetVM.addRow(r));

    sheetVM.mergeCells("A1:E1");
    const titleCellVM = sheetVM.getCell("A1");
    titleCellVM.font = { size: 16, bold: true, color: { argb: "FF0B4A2F" } };
    titleCellVM.alignment = { horizontal: "center" };

    const headerRowVM = sheetVM.getRow(6);
    headerRowVM.font = { bold: true, color: { argb: "FFFFFFFF" } };
    headerRowVM.eachCell((cell) => {
      cell.fill = { type: "pattern", pattern: "solid", fgColor: { argb: "FF0E7490" } };
      cell.border = { top: { style: "thin" }, left: { style: "thin" }, bottom: { style: "thin" }, right: { style: "thin" } };
    });

    // Colorear las filas de "TOTAL VM"
    sheetVM.eachRow((row, rowNumber) => {
      if (rowNumber > 6) {
        const colC = row.getCell(3).value;
        if (colC === "TOTAL VM") {
          row.font = { bold: true, color: { argb: "FF0B4A2F" } };
          row.fill = { type: "pattern", pattern: "solid", fgColor: { argb: "FFD1FAE5" } }; // Verde muy claro
        }
      }
    });

    const buffer = await workbook.xlsx.writeBuffer();
    const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = `Desempeno_y_ValoresMerito_${selectedCdCntble.value}_${new Date().toISOString().split("T")[0]}.xlsx`;
    link.click();
    toast.success("Memoria de cálculos exportada correctamente.");
  } catch (error) {
    console.error("Error exportando desempeño individual:", error);
    toast.error("Ocurrió un error al exportar.");
  }
}

// Generar y descargar la matriz como PNG
const matrizTable = ref<HTMLElement | null>(null);
const isDownloadingImage = ref(false);

async function descargarPNG() {
  if (!matrizTable.value) return;

  try {
    isDownloadingImage.value = true;
    toast.info("Generando imagen PNG, por favor espera...");

    // Pequeña pausa para asegurar renderizado completo de cualquier estado
    await new Promise((resolve) => setTimeout(resolve, 300));

    const canvas = await html2canvas(matrizTable.value, {
      scale: 2, // Alta resolución
      backgroundColor: "#ffffff",
      useCORS: true,
      logging: false
    });

    const link = document.createElement("a");
    link.download = `Matriz_Cruzamientos_${selectedCdCntble.value}.png`;
    link.href = canvas.toDataURL("image/png");
    link.click();

    toast.success("¡Imagen PNG descargada con éxito!");
  } catch (error) {
    console.error("Error al generar PNG:", error);
    toast.error("Hubo un error al generar la imagen");
  } finally {
    isDownloadingImage.value = false;
  }
}

async function autoOptimizarFlores(silent: boolean | Event = false) {
  const isSilent = typeof silent === "boolean" ? silent : false;
  isOptimizing.value = true;
  await new Promise((resolve) => setTimeout(resolve, 50));

  const metricType = tipoMapaCalor.value;
  const isIC = metricType === "ic";
  let adiciones = 0;

  const usadas = { madre: {} as Record<string, number>, padre: {} as Record<string, number> };
  const disp = { madre: {} as Record<string, number>, padre: {} as Record<string, number> };

  // 1. Cargar disponibilidades (DISP)
  const rows = viabilidadesMatriz.value || [];
  rows.forEach((row: any) => {
    if (row && row.length > 0) {
      const m = row[0].varA;
      if (m) disp.madre[m] = getCantidadFlores(m);
    }
  });
  floresSeleccionadas.value.forEach((padre: any) => {
    const p = padre.variedad;
    if (p) disp.padre[p] = getCantidadFlores(p);
  });

  // 2. Limpieza total y cobro de autofecundaciones (OPTIMIZADO para Vue Reactivity)
  rows.forEach((row: any) => {
    row.forEach((car: any) => {
      if (car) {
        // Solo modificamos si es estrictamente necesario para evitar colapsar la reactividad de Vue
        if (car.viabilidad) car.viabilidad = false;
        if (car.flores_madre !== 0) car.flores_madre = 0;
        if (car.flores_padre !== 0) car.flores_padre = 0;
      }
    });
  });

  for (const m in disp.madre) {
    if (usadas.madre[m] === undefined) usadas.madre[m] = 0;
    if (autofecundacionesSeleccionadas.value.has(m)) {
      usadas.madre[m] += 1;
    }
  }

  // 3. Procesar fila por fila (Dando prioridad a las Hembras)
  for (let rowIndex = 0; rowIndex < rows.length; rowIndex++) {
    const row = rows[rowIndex];
    if (!row || row.length === 0) continue;

    // Ignorar las filas de machos (el UI solo muestra Hembras con polen <= 20)
    if (Number(row[0]?.polen) > 20) continue;

    const m = row[0].varA;

    // Actualizar la UI para mostrar la madre actual y forzar el repintado en pantalla a 60fps
    optimizandoMadre.value = m;
    // El doble requestAnimationFrame garantiza que Vue aplique los cambios al DOM y el navegador los dibuje
    await new Promise((resolve) => requestAnimationFrame(() => requestAnimationFrame(resolve)));

    if (usadas.madre[m] === undefined) usadas.madre[m] = 0;

    const seenFathersInRow = new Set<string>();
    const crossesForMother: Array<{ car: any; val: number; varB: string }> = [];
    const memoCausa = new Map<string, string>();

    // Extraer cruces viables de esta madre
    row.forEach((car: any) => {
      if (!car) return;
      const p = car.varB;

      // Filtro caza-fantasmas (ignorar duplicados de la BD)
      if (seenFathersInRow.has(p)) return;
      seenFathersInRow.add(p);

      if (usadas.padre[p] === undefined) usadas.padre[p] = 0;

      // Calcular puntaje matemático (IC o DG)
      let val = 0;
      if (isIC) {
        val = getIndiceCombinado(m, p, car.vm2) || 0;
      } else {
        const dgString = getDistancia(m, p);
        val = dgString === "NA" ? -9999 : Number(dgString) || 0;
      }
      if (isNaN(val)) val = -9999;

      // Filtro biológico estricto con memoización
      const key = m + "|" + p;
      let causa = memoCausa.get(key);
      if (causa === undefined) {
        causa = getCausaInviabilidad(car);
        memoCausa.set(key, causa);
      }

      let isBiologicallyValid = true;
      if (causa.includes("Incompatibilidad de sexo")) isBiologicallyValid = false;
      if (causa.includes("Restricción de Autogamia")) isBiologicallyValid = false;
      if (causa.includes("excede límite")) isBiologicallyValid = false;

      // Regla de polen de la interfaz: El padre DEBE tener polen > 20
      if (Number(car?.polen2) <= 20) isBiologicallyValid = false;

      if (m !== p && val !== -9999 && isBiologicallyValid) {
        crossesForMother.push({ car, val, varB: p });
      }
    });

    // Ordenar los cruces DE ESTA MADRE de mejor a peor puntaje
    // (Desempate alfabético por padre para determinismo)
    crossesForMother.sort((a, b) => {
      if (b.val !== a.val) return b.val - a.val;
      return a.varB.localeCompare(b.varB);
    });

    // 4. Asignación "1 flor a la vez" para esta madre
    for (let i = 0; i < crossesForMother.length; i++) {
      const item = crossesForMother[i];
      const p = item.varB;

      // Si la madre tiene espacio Y el padre también tiene espacio
      if (disp.madre[m] > usadas.madre[m] && disp.padre[p] > usadas.padre[p]) {
        item.car.viabilidad = true;
        item.car.flores_madre = 1;
        item.car.flores_padre = 1;

        usadas.madre[m]++;
        usadas.padre[p]++;
        adiciones++;
      }
      // Si la madre ya se quedó sin flores (usadas == disp), ya no buscamos más padres para ella
      if (usadas.madre[m] >= disp.madre[m]) break;
    }
  }

  // Borrar draft para no ensuciar recargas
  optimizandoMadre.value = "";
  localStorage.removeItem(draftKey.value);

  if (!isSilent) {
    toast.success(`¡Optimización Calculada! Cruces asignados: ${adiciones}.`);
  }
  isOptimizing.value = false;
}

async function finalizarProceso() {
  // 1. Mostrar spinner de guardado
  isSaving.value = true;

  try {
    // 2. Guardar pesos ponderados en la base de datos y obtener el idPonderado
    const responseWeight = await CrossingsService.saveWeight(selectedCdCntble.value);
    const idPonderado = responseWeight ? responseWeight.data : null;
    if (!idPonderado) {
      throw new Error("No se pudo obtener el ID del ponderado");
    }

    // 3. Obtener todos los cruzamientos seleccionados (viabilidad === true)
    const selectedCrossings: any[] = [];
    const rows = viabilidadesMatriz.value || [];
    rows.forEach((row: any) => {
      if (row && row.length > 0) {
        row.forEach((cell: any) => {
          if (cell && cell.viabilidad === true) {
            // Ya no multiplicamos el cruzamiento. Se guarda 1 solo cruce asimétrico
            selectedCrossings.push({ ...cell });
          }
        });

        // Integrar Autofecundación solicitada explícitamente para esta variedad madre
        const motherCell = row[0];
        if (motherCell && autofecundacionesSeleccionadas.value.has(motherCell.varA)) {
          selectedCrossings.push({
            varA: motherCell.varA,
            varB: motherCell.varA,
            proyecto: motherCell.proyecto,
            proyecto2: motherCell.proyecto, // Mismo proyecto
            id_caracter: motherCell.id_caracter,
            id_caracter2: motherCell.id_caracter, // Mismo caracter
            viabilidad: true,
            flores_madre: 1,
            flores_padre: 1 // o 0, pero autofecundación se suma en Madre únicamente
          });
        }
      }
    });

    if (selectedCrossings.length === 0) {
      toast.warning("No hay cruzamientos seleccionados para guardar.");
      isSaving.value = false;
      return;
    }

    // 4. Guardar todos los cruzamientos en lote (batch) para rendimiento óptimo e instantáneo
    const batchPayload = selectedCrossings.map((car) => {
      const autofecundado = car.varA === car.varB ? 1 : 0;
      return {
        madre: `${car.varA}_${car.proyecto}_${car.id_caracter}`,
        padres: `${car.varB}_${car.proyecto2}_${car.id_caracter2}`,
        observaciones: "Programacion de Cruzamientos desde Matriz por Proyecto",
        id_ponderados: idPonderado,
        proyectos: `${car.proyecto}`,
        autofecundado: autofecundado,
        // Variables añadidas para consumo asimétrico de flores
        flores_madre: car.flores_madre ?? 1,
        flores_padre: car.flores_padre ?? 1,
        // Añadimos estas variables para mostrarlas en el UI del Resumen
        varA: car.varA,
        varB: car.varB
      };
    });

    await CrossingsService.saveCrossingsBatch(batchPayload);

    // 5. Limpiar el borrador correspondiente al finalizar con éxito
    localStorage.removeItem(draftKey.value);
    localStorage.removeItem("cruzamientos");

    toast.success("¡Programación de cruzamientos guardada y finalizada con éxito!");

    // 6. En lugar de redirigir a History directamente, mostrar el resumen
    resumenCrucesGuardados.value = batchPayload;
    isFinished.value = true;
  } catch (error) {
    console.error("Error al guardar la programación de cruzamientos:", error);
    toast.error("Ocurrió un error al guardar la programación de cruzamientos en la base de datos.");
  } finally {
    isSaving.value = false;
  }
}

// ==========================================
// LÓGICA VISTA DRAG & DROP
// ==========================================

const madresUnicas = computed(() => {
  const m = new Map();
  const rows = viabilidadesMatriz.value || [];
  rows.forEach((row: any) => {
    if (row && row.length > 0) {
      const madre = row[0];
      if (Number(madre.polen) <= 20 && !m.has(madre.varA)) {
        m.set(madre.varA, { variedad: madre.varA, polen: madre.polen });
      }
    }
  });
  return Array.from(m.values());
});

const manualCrosses = computed(() => {
  const crosses: any[] = [];
  const rows = viabilidadesMatriz.value || [];
  rows.forEach((row: any) => {
    if (row && row.length > 0 && Number(row[0]?.polen) <= 20) {
      row.forEach((car: any) => {
        if (car && car.viabilidad && Number(car.polen2) > 20) {
          crosses.push(car);
        }
      });
    }
  });
  return crosses;
});

function handleDragStart(event: DragEvent, item: any, type: "madre" | "padre") {
  activeDragType.value = type;
  activeDragItem.value = item;
  if (event.dataTransfer) {
    event.dataTransfer.effectAllowed = "copy";
    event.dataTransfer.setData("text/plain", JSON.stringify({ type, item: item.variedad }));
  }
}

function handleDragOver(event: DragEvent) {
  event.preventDefault();
  if (event.dataTransfer) {
    event.dataTransfer.dropEffect = "copy";
  }
}

function handleDrop(event: DragEvent, dropType: "zone") {
  event.preventDefault();

  if (dropType === "zone") {
    if (activeDragType.value === "madre") pendingMother.value = activeDragItem.value;
    else if (activeDragType.value === "padre") pendingFather.value = activeDragItem.value;
  }

  activeDragType.value = null;
  activeDragItem.value = null;

  if (pendingMother.value && pendingFather.value) {
    attemptManualCross(pendingMother.value.variedad, pendingFather.value.variedad);
  }
}

function clearPendingDrop() {
  pendingMother.value = null;
  pendingFather.value = null;
}

function attemptManualCross(m: string, p: string) {
  const rows = viabilidadesMatriz.value || [];
  let foundCar = null;

  for (let i = 0; i < rows.length; i++) {
    const row = rows[i];
    if (row && row.length > 0 && row[0].varA === m) {
      foundCar = row.find((c: any) => c && c.varB === p);
      break;
    }
  }

  if (!foundCar) {
    toast.error(`Cruce entre ${m} y ${p} no existe o no se evaluó en la matriz inicial.`);
    clearPendingDrop();
    return;
  }

  if (foundCar.viabilidad) {
    toast.info("Este cruce ya se encuentra activo.");
    clearPendingDrop();
    return;
  }

  let isBiologicallyValid = true;
  const causa = getCausaInviabilidad(foundCar);
  if (causa.includes("Incompatibilidad de sexo")) isBiologicallyValid = false;
  if (causa.includes("Restricción de Autogamia")) isBiologicallyValid = false;
  if (causa.includes("excede límite")) isBiologicallyValid = false;
  if (Number(foundCar?.polen2) <= 20) isBiologicallyValid = false; // El padre debe ser macho

  if (!isBiologicallyValid) {
    toast.error(`Cruce biológicamente inválido: ${causa}`);
    clearPendingDrop();
    return;
  }

  const dispMother = getCantidadFlores(m);
  const usedMother = getFloresUsadas(m, true);
  if (usedMother >= dispMother) {
    toast.warning(`No hay flores suficientes para la madre ${m}.`);
    clearPendingDrop();
    return;
  }

  const dispFather = getCantidadFlores(p);
  const usedFather = getFloresUsadas(p, false);
  if (usedFather >= dispFather) {
    toast.warning(`No hay flores suficientes para el macho ${p}.`);
    clearPendingDrop();
    return;
  }

  foundCar.viabilidad = true;
  foundCar.flores_madre = 1;
  foundCar.flores_padre = 1;
  toast.success(`Cruce ${m} x ${p} registrado.`);
  clearPendingDrop();
}

function removeManualCross(car: any) {
  car.viabilidad = false;
  car.flores_madre = 0;
  car.flores_padre = 0;
}
</script>

<style>
/* Estilos personalizados para la barra de desplazamiento */
.scrollbar-custom::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

.scrollbar-custom::-webkit-scrollbar-thumb {
  background-color: #10b981; /* Esmeralda */
  border-radius: 10px;
}

.scrollbar-custom::-webkit-scrollbar-track {
  background-color: #f8fafc; /* Slate 50 */
}
@media print {
  /* Ocultar elementos de navegación y filtros en impresión */
  nav,
  aside,
  header,
  footer,
  .no-print,
  button,
  a,
  .router-link-active,
  svg,
  .mb-3.border-b.border-slate-100 {
    display: none !important;
  }

  /* Ajustar contenedor principal para impresión a página completa */
  body,
  #app,
  main,
  .min-h-screen,
  .bg-slate-50,
  .space-y-6 {
    background: white !important;
    color: black !important;
    padding: 0 !important;
    margin: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    min-height: auto !important;
  }

  /* Forzar orientación horizontal y margen de impresión */
  @page {
    size: landscape;
    margin: 0.8cm;
  }

  /* Eliminar sombras y bordes redondeados innecesarios */
  .shadow-premium,
  .border-slate-100,
  .overflow-hidden,
  .rounded-xl {
    box-shadow: none !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 0 !important;
  }

  table {
    width: 100% !important;
    border-collapse: collapse !important;
    page-break-inside: avoid;
  }

  th,
  td {
    border: 1px solid #94a3b8 !important;
    padding: 6px !important;
    font-size: 9px !important;
    color: black !important;
    background: white !important;
  }

  th {
    background-color: #f1f5f9 !important;
    font-weight: bold !important;
  }

  /* Colorear e identificar cruzamientos seleccionados en impresión */
  .bg-emerald-50\/50 {
    background-color: #d1fae5 !important;
    font-weight: bold !important;
    border: 2px solid #059669 !important;
  }

  /* Ocultar elementos interactivos en celdas de impresión */
  td button,
  td input[type="checkbox"] {
    display: none !important;
  }
}
</style>
