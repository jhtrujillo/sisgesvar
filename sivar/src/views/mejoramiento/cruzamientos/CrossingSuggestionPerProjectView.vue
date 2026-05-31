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
    <!-- Encabezado con Indicador de Progreso -->
    <div class="border-b border-slate-100 pb-4">
      <div class="flex items-center justify-between mb-3">
        <h1 class="text-2xl font-extrabold text-slate-800 flex items-center">
          <div class="p-1.5 bg-emerald-50 text-cenicana rounded-lg mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
            </svg>
          </div>
          Sugerencia de Cruzamientos
        </h1>
        <span class="text-[11px] font-bold uppercase tracking-wider px-2.5 py-0.5 bg-emerald-100 text-emerald-800 rounded-full">
          Proyecto Final
        </span>
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
      <div v-if="isLoading" class="absolute inset-0 bg-white/95 rounded-xl z-30 flex flex-col items-center justify-center space-y-4 transition-all duration-300">
        <div class="relative w-14 h-14">
          <!-- Círculo de base -->
          <div class="absolute inset-0 rounded-full border-4 border-emerald-50"></div>
          <!-- Círculo giratorio -->
          <div class="absolute inset-0 rounded-full border-4 border-t-cenicana animate-spin"></div>
        </div>
        <div class="flex flex-col items-center text-center px-4">
          <span class="text-sm font-bold text-slate-700 animate-pulse">Calculando las mejores sugerencias de cruzamientos...</span>
        </div>
      </div>

      <!-- Estado de Guardado / Persistiendo en Base de Datos -->
      <div v-if="isSaving" class="absolute inset-0 bg-white/95 rounded-xl z-30 flex flex-col items-center justify-center space-y-4 transition-all duration-300">
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

      <!-- Leyenda informativa y Botón de Filtro -->
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mb-3 border-b border-slate-100 pb-3 no-print">
        <div class="flex flex-wrap items-center gap-3 text-[11px] font-semibold text-slate-500">
          <span class="flex items-center">
            <span class="w-3 h-3 bg-rose-100 border border-rose-200 rounded mr-1"></span>
            Hembra (Polen &le; 20)
          </span>
          <span class="flex items-center">
            <span class="w-3 h-3 bg-sky-100 border border-sky-200 rounded mr-1"></span>
            Hembra (Polen &gt; 20)
          </span>
          <span v-if="!mostrarMapaCalor" class="flex items-center">
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

          <!-- Leyenda del Mapa de Calor (Solo visible si está activo) -->
          <span v-if="mostrarMapaCalor" class="flex flex-wrap items-center gap-2 border-l border-slate-200 pl-3">
            <span class="text-[9px] text-slate-400 font-extrabold uppercase mr-1">Mapa de Calor (DG):</span>
            <span class="flex items-center">
              <span class="w-3.5 h-3.5 bg-emerald-600 rounded mr-1"></span>
              <span class="text-[9px] font-bold text-emerald-800 mr-2">Exc (&ge;0.65)</span>
            </span>
            <span class="flex items-center">
              <span class="w-3.5 h-3.5 bg-emerald-400/80 rounded mr-1"></span>
              <span class="text-[9px] font-bold text-emerald-600 mr-2">Bueno (&ge;0.55)</span>
            </span>
            <span class="flex items-center">
              <span class="w-3.5 h-3.5 bg-emerald-200/50 rounded mr-1"></span>
              <span class="text-[9px] font-bold text-emerald-500 mr-2">Acept (&ge;0.45)</span>
            </span>
            <span class="flex items-center">
              <span class="w-3.5 h-3.5 bg-amber-100 rounded mr-1"></span>
              <span class="text-[9px] font-bold text-amber-700 mr-2">Cerca (&ge;0.35)</span>
            </span>
            <span class="flex items-center">
              <span class="w-3.5 h-3.5 bg-rose-100 rounded mr-1"></span>
              <span class="text-[9px] font-bold text-rose-700">Riesgo (&lt;0.35)</span>
            </span>
          </span>
        </div>

        <!-- Botones de Control Interactivos -->
        <div class="flex justify-end items-center space-x-2">
          <!-- Botón de Mapa de Calor -->
          <button 
            @click="mostrarMapaCalor = !mostrarMapaCalor"
            class="flex items-center px-3 py-1 text-[11px] font-bold rounded-lg transition-all duration-200 border"
            :class="mostrarMapaCalor ? 'bg-amber-500 border-amber-500 text-white shadow-sm hover:bg-amber-600' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50'"
            title="Mostrar mapa de calor basado en la Distancia Genética (DG)"
          >
            <span class="mr-1">🔥</span>
            {{ mostrarMapaCalor ? 'Desactivar Mapa de Calor' : 'Activar Mapa de Calor' }}
          </button>

          <!-- Botón de Filtro Ocultar/Ver Inviables -->
          <button 
            @click="ocultarInviables = !ocultarInviables"
            class="flex items-center px-3 py-1 text-[11px] font-bold rounded-lg transition-all duration-200 border"
            :class="!ocultarInviables ? 'bg-emerald-600 border-emerald-600 text-white shadow-sm hover:bg-emerald-700' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50'"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            {{ ocultarInviables ? 'Ver Inviables' : 'Ocultar Inviables' }}
          </button>
        </div>
      </div>

      <!-- Alerta si no hay ningún cruce viable en modo limpio -->
      <div v-if="ocultarInviables && viabilidadesMatriz.length > 0 && !hasAnyViableCrossing" class="flex flex-col items-center justify-center py-10 text-center text-slate-400 space-y-2 bg-slate-50/50 rounded-xl border border-slate-100">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-350" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <span class="text-xs font-semibold text-slate-500">No se encontraron cruzamientos sugeridos en este proyecto.</span>
        <span class="text-[11px] text-slate-400">Haga clic en "Ver Inviables" para ver todas las opciones o modifique los pesos en el paso anterior.</span>
      </div>

      <!-- Cuadrícula Estilo Clásico/Compacto de Oro (Sin Barra Horizontal) -->
      <div v-else class="overflow-hidden border border-slate-100 rounded-xl shadow-sm">
        <div class="max-h-[500px] overflow-x-auto overflow-y-auto scrollbar-custom">
          <table class="table-auto w-full divide-y divide-slate-150">
            <thead class="bg-slate-50">
              <tr>
                <th class="px-2 py-2 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500 bg-slate-50 border-r border-slate-100 sticky top-0 left-0 z-20 shadow-[2px_0_5px_rgba(0,0,0,0.02)] min-w-[140px]">
                  Hembra / Posibles Machos
                </th>
                <!-- Envoltura <template> para evaluar v-if en el scope correcto de Vue 3 -->
                <template 
                  v-for="(flor, indexCol) in floresSeleccionadas || []"
                  :key="indexCol"
                >
                  <th
                    v-if="!ocultarInviables || isColumnViable(indexCol)"
                    class="px-2 py-2 text-center text-[11px] font-bold uppercase tracking-wider text-slate-650 bg-slate-50 border-r border-slate-100 sticky top-0 z-10 min-w-[75px]"
                  >
                    <span 
                      class="block text-slate-800 font-extrabold leading-tight cursor-pointer hover:underline hover:text-emerald-700 transition-colors"
                      @click="openVarietyProfile(flor.variedad)"
                    >
                      {{ flor.variedad }}
                    </span>
                    <span class="block text-[9px] text-slate-400 font-bold mt-0.5">Cantidad: {{ flor.cantidad }}</span>
                  </th>
                </template>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              <!-- Envoltura <template> para evaluar v-if en el scope correcto de Vue 3 -->
              <template 
                v-for="(viabilidadRow, indexRow) in viabilidadesMatriz || []" 
                :key="indexRow"
              >
                <tr 
                  v-if="!ocultarInviables || isRowViable(viabilidadRow)"
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
                      <span class="font-extrabold text-slate-800 leading-tight cursor-pointer hover:underline hover:text-emerald-700 transition-colors" @click="openVarietyProfile(viabilidadRow[0]?.varA)">{{ viabilidadRow[0]?.varA || 'N/A' }}</span>
                    </div>
                    <div class="flex items-center justify-center space-x-2 text-[9px] font-semibold text-slate-500">
                      <span>VM: {{ getRowVm(viabilidadRow) }}</span>
                      <span>Polen: {{ viabilidadRow[0]?.polen || '0' }}</span>
                    </div>
                    <div class="text-[9px] text-slate-400 mt-0.5 font-semibold">Cant. Flores: {{ getCantidadFlores(viabilidadRow[0]?.varA) }}</div>
                    
                    <!-- Autofecundación Estilizada -->
                    <div class="mt-1.5 flex items-center justify-center space-x-1 bg-white/70 py-0.5 px-1.5 rounded-lg border border-slate-200/50 shadow-sm max-w-[120px] mx-auto">
                      <input 
                        type="checkbox" 
                        @click="toggleAutofecundar(viabilidadRow)" 
                        class="h-3 w-3 rounded border-slate-350 text-emerald-600 focus:ring-emerald-100 cursor-pointer"
                      />
                      <span class="text-[8px] font-extrabold uppercase text-slate-600">Autofecundar</span>
                    </div>
                  </td>
                  
                  <!-- Celdas de la matriz filtradas por columna -->
                  <template 
                    v-for="(car, indexCol) in viabilidadRow" 
                    :key="indexCol"
                  >
                    <td 
                      v-if="!ocultarInviables || isColumnViable(indexCol)"
                      :class="[getHeatmapClass(car?.varA, car?.varB, !!car?.viabilidad)]"
                      class="p-2 text-center border-b border-slate-100 transition-all duration-200 min-w-[75px]"
                    >
                      <div 
                        class="flex flex-col items-center justify-center space-y-1"
                      >
                        <input 
                          type="checkbox" 
                          :checked="!!car?.viabilidad" 
                          @click="toggleCruzamiento(car)" 
                          :disabled="!car?.viabilidad"
                          class="h-3.5 w-3.5 rounded border-slate-350 text-emerald-600 focus:ring-emerald-100 transition cursor-pointer"
                        />
                        <div 
                          class="text-[9px] font-extrabold leading-tight"
                          :class="[mostrarMapaCalor && isHighDg(car.varA, car.varB) ? 'text-white' : 'text-slate-800']"
                        >
                          {{ car.varB }}
                        </div>
                        <div 
                          class="text-[8px] font-semibold leading-tight"
                          :class="[mostrarMapaCalor && isHighDg(car.varA, car.varB) ? 'text-emerald-100' : 'text-slate-400']"
                        >
                          Polen: {{ car.polen2 }} | VM: {{ car.vm2 }}
                        </div>
                        <div class="flex flex-col items-center justify-center w-full border-t border-slate-100/50 pt-1 mt-1 space-y-1">
                          <span 
                            class="text-[9px] font-extrabold tracking-tight leading-none text-center"
                            :class="[mostrarMapaCalor && isHighDg(car.varA, car.varB) ? 'text-white' : 'text-slate-700']"
                          >
                            DG: {{ getDistancia(car.varA, car.varB) || "NA" }}
                          </span>
                          <!-- Botón Comparador Lado a Lado -->
                          <button 
                            @click.stop="openParentComparator(car?.varA, car?.varB, car?.viabilidad)"
                            class="text-[8px] font-bold px-1.5 py-0.5 rounded transition-all duration-150 flex items-center justify-center space-x-0.5 border mx-auto"
                            :class="[
                              mostrarMapaCalor && isHighDg(car.varA, car.varB)
                                ? 'bg-white/20 hover:bg-white/30 text-white border-white/25'
                                : 'bg-slate-100 hover:bg-emerald-50 text-slate-650 hover:text-emerald-700 border-slate-200/60 hover:border-emerald-200'
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
          @click="exportarExcel"
          class="flex items-center px-4 py-2 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl shadow-sm hover:bg-emerald-100 transition-all duration-200"
          title="Descargar cuadrícula en formato Excel"
        >
          <span class="mr-1.5">📊</span>
          Descargar Excel
        </button>

        <button
          type="button"
          @click="exportarMemoriaCalculos"
          class="flex items-center px-4 py-2 text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200 rounded-xl shadow-sm hover:bg-amber-100 transition-all duration-200"
          title="Descargar Memoria de Cálculos con todo el sustento matemático de VM y Vetos"
        >
          <span class="mr-1.5">🧮</span>
          Memoria de Cálculos
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
          @click="finalizarProceso"
          class="flex items-center px-5 py-2 text-xs font-bold text-white bg-cenicana hover:bg-cenicana-800 rounded-xl shadow-md transition-all duration-200"
        >
          Finalizar
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
          </svg>
        </button>
      </div>
    </div>
  </div>

  <!-- Drawer de Hoja de Vida de la Variedad (Quick Drawer) -->
  <VarietyProfileDrawer
    v-model:isOpen="isDrawerOpen"
    :varietyName="selectedVarietyForDrawer"
  />

  <!-- Modal de Comparación Lado a Lado -->
  <ParentComparatorModal
    v-model:isOpen="isComparatorOpen"
    :motherName="comparatorMother"
    :fatherName="comparatorFather"
    :initiallyViable="comparatorInitiallyViable"
  />
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from "vue";
import { useRouter } from "vue-router";
import { useToast } from "vue-toastification";
import { useSuggestionCrossingPerProjectStore } from "@/stores/crossingsuggestionperproject";
import { useParametizeWeightedCrossingStore } from "@/stores/crossignparametizeweighted";
import VarietyProfileDrawer from "@/components/VarietyProfileDrawer.vue";
import ParentComparatorModal from "@/components/ParentComparatorModal.vue";
import * as XLSX from "xlsx";
import CrossingsService from "@/services/crossings.services";

const SuggestionCrossingPerProjectStore = useSuggestionCrossingPerProjectStore();
const ParametizeWeightedStore = useParametizeWeightedCrossingStore();
const router = useRouter();
const toast = useToast();
const selectedCdCntble = ref(localStorage.getItem("selectedCdCntble") || "");
const selectedMegaAmbiente = ref(localStorage.getItem("selectedMegaAmbiente") || "");
const selectedVariety = ref(localStorage.getItem("selectedVariety") || "");
const selectedIdProject = ref(localStorage.getItem("selectedIdProject") || "");
const draftKey = computed(() => `sivarcc_draft_crossings_${selectedCdCntble.value}_${selectedMegaAmbiente.value}`);
const isSaving = ref(false);

const ocultarInviables = ref(true); // Vista compacta limpia por defecto
const isLoading = ref(false); // Ref para spinner de carga
const mostrarMapaCalor = ref(true); // Vista con mapa de calor por defecto

function isHighDg(varA: string, varB: string) {
  const dgVal = getDistancia(varA, varB);
  const val = Number(dgVal);
  return !isNaN(val) && val >= 0.65;
}

function getHeatmapClass(varA: string, varB: string, viabilidad: boolean) {
  if (!viabilidad) {
    return 'bg-slate-50/50 hover:bg-slate-100/50 border-r border-slate-100 text-slate-400 opacity-60';
  }
  if (!mostrarMapaCalor.value) {
    return 'bg-emerald-50/50 hover:bg-emerald-100/50 border-r border-emerald-100/50 text-emerald-800';
  }
  
  const dgVal = getDistancia(varA, varB);
  const val = Number(dgVal);
  if (isNaN(val)) {
    return 'bg-emerald-50/50 border-r border-emerald-100/50 text-emerald-800 hover:bg-emerald-100/50';
  }
  
  if (val >= 0.65) {
    return 'bg-emerald-600/90 text-white font-black border-r border-emerald-700/50 shadow-inner hover:bg-emerald-700';
  } else if (val >= 0.55) {
    return 'bg-emerald-400/60 text-slate-900 font-extrabold border-r border-emerald-500/30 hover:bg-emerald-500/50';
  } else if (val >= 0.45) {
    return 'bg-emerald-200/40 text-slate-800 font-bold border-r border-emerald-300/30 hover:bg-emerald-300/40';
  } else if (val >= 0.35) {
    return 'bg-amber-100 text-amber-900 font-semibold border-r border-amber-200/30 hover:bg-amber-200/50';
  } else {
    return 'bg-rose-100 text-rose-900 font-semibold border-r border-rose-200/30 hover:bg-rose-200/50';
  }
}

const getRowVm = (row: any[]) => {
  if (!row || row.length === 0) return "0";
  const validCell = row.find(cell => cell && cell.vm !== 1 && cell.vm !== "1" && cell.vm !== 0 && cell.vm !== "0");
  return validCell ? validCell.vm : (row[0]?.vm || "0");
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

// Cargar datos iniciales
onMounted(() => {
  loadSuggestionCrossings();
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
              const matches = disabledCrosses.some(
                (d: any) => d.varA === car.varA && d.varB === car.varB
              );
              if (matches) {
                car.viabilidad = false;
              }
            }
          });
        });
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
      cantidad: cantidadesMap.value[cell.varB] || 0
    }));
  }
  
  // Fallback
  const rawFlores = SuggestionCrossingPerProjectStore.suggestionCrossingsPerProjectFilter?.flores || [];
  return rawFlores.map((flor: any) => ({
    variedad: flor.vrdad,
    cantidad: flor.numero
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
  return cantidadesMap.value[vrdad] || 0;
}

function getIcon(polen: string | number) {
  return +polen <= 20 ? "fa fa-venus text-rose-500 font-bold" : "fa fa-mars text-sky-500 font-bold";
}

function toggleCruzamiento(car: any) {
  car.viabilidad = !car.viabilidad;
  
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
  console.info(`Autofecundación seleccionada para ${viabilidadRow.varA}`);
}

function getDistancia(varA: string, varB: string) {
  if (!varA || !varB) return "NA";
  const distancias = SuggestionCrossingPerProjectStore.suggestionCrossingsPerProjectFilter?.distancias || {};
  const cleanA = varA.trim().toUpperCase();
  const cleanB = varB.trim().toUpperCase();
  
  const keyA = Object.keys(distancias).find(k => k.trim().toUpperCase() === cleanA);
  if (!keyA) return "NA";
  
  const subDist = distancias[keyA];
  if (!subDist) return "NA";
  
  const keyB = Object.keys(subDist).find(k => k.trim().toUpperCase() === cleanB);
  if (!keyB) return "NA";
  
  const val = subDist[keyB];
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

  const findVarietyData = (varName: string) => {
    if (!varName) return undefined;
    const cleanVar = varName.trim().toUpperCase();
    let data = floresBG.find((f: any) => f.vrdad && f.vrdad.trim().toUpperCase() === cleanVar);
    if (!data) data = floresPR.find((f: any) => f.vrdad && f.vrdad.trim().toUpperCase() === cleanVar);
    if (!data) data = floresEIII.find((f: any) => f.vrdad && f.vrdad.trim().toUpperCase() === cleanVar);
    return data;
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
              { val: Number(valorRealA), setLvl: (l: number) => lvlA = l },
              { val: Number(valorRealB), setLvl: (l: number) => lvlB = l }
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
          } else if (caracteristica === "tchm" || caracteristica === "scrsa" || caracteristica === "dmtro_tllo" || caracteristica === "altura_planta" || caracteristica === "poblacion") {
            const valTestigo = testigoLimpio[caracteristica];
            if (valTestigo) {
              [
                { val: Number(valorRealA), setLvl: (l: number) => lvlA = l },
                { val: Number(valorRealB), setLvl: (l: number) => lvlB = l }
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
                { val: Number(valorRealA), setLvl: (l: number) => lvlA = l },
                { val: Number(valorRealB), setLvl: (l: number) => lvlB = l }
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
          
          if (lvlA !== 999 && lvlB !== 999 && (lvlA + lvlB) > Number(limiteMax)) {
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
    const matches = disabledCrosses.some(
      (d: any) => d.varA === cell.varA && d.varB === cell.varB
    );
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
function exportarExcel() {
  try {
    const headers = ["Hembra / Macho", ...floresSeleccionadas.value.map(f => f.variedad)];
    const rows: any[][] = [];
    
    // Añadir metadatos en la cabecera
    const metadata = [
      ["CENICAÑA - PROGRAMACIÓN DE CRUZAMIENTOS POR PROYECTO"],
      ["Proyecto ID:", selectedCdCntble.value],
      ["Mega Ambiente:", selectedMegaAmbiente.value],
      ["Variedad Testigo:", selectedVariety.value],
      ["Fecha de Generación:", new Date().toLocaleDateString("es-ES")],
      [] // Fila vacía
    ];
    
    // Construir la matriz
    viabilidadesMatriz.value.forEach((row: any) => {
      if (row && row.length > 0) {
        const varA = row[0].varA;
        const excelRow: string[] = [varA];
        
        row.forEach((cell: any) => {
          if (cell) {
            if (cell.viabilidad) {
              excelRow.push(`SÍ (DG: ${getDistancia(cell.varA, cell.varB)} | VM: ${cell.vm2})`);
            } else {
              const causa = getCausaInviabilidad(cell);
              excelRow.push(`NO - ${causa}`);
            }
          } else {
            excelRow.push("-");
          }
        });
        rows.push(excelRow);
      }
    });

    const worksheet = XLSX.utils.aoa_to_sheet([
      ...metadata,
      headers,
      ...rows
    ]);

    // Aplicar algunos anchos de columna automáticos
    const maxCols = headers.length;
    worksheet["!cols"] = [];
    for (let i = 0; i < maxCols; i++) {
      worksheet["!cols"].push({ wch: i === 0 ? 18 : 25 });
    }

    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Hoja de Campo");
    
    const filename = `Programacion_Cruzamientos_${selectedCdCntble.value}_${selectedMegaAmbiente.value}.xlsx`;
    XLSX.writeFile(workbook, filename);
    toast.success("¡Excel de cruzamientos generado con éxito!");
  } catch (error) {
    console.error("Error al exportar Excel:", error);
    toast.error("Ocurrió un error al generar el archivo Excel");
  }
}

function imprimirHojaCampo() {
  window.print();
}

async function exportarMemoriaCalculos() {
  try {
    const filterData = SuggestionCrossingPerProjectStore.suggestionCrossingsPerProjectFilter || {};
    const viabilidad = viabilidadesMatriz.value || [];
    const distancias = filterData.distancias || {};
    
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

    const findVarietyData = (varName: string) => {
      if (!varName) return undefined;
      const cleanVar = varName.trim().toUpperCase();
      let data = floresBG.find((f: any) => f.vrdad && f.vrdad.trim().toUpperCase() === cleanVar);
      if (!data) data = floresPR.find((f: any) => f.vrdad && f.vrdad.trim().toUpperCase() === cleanVar);
      if (!data) data = floresEIII.find((f: any) => f.vrdad && f.vrdad.trim().toUpperCase() === cleanVar);
      return data;
    };
    
    // --- HOJA 1: RESUMEN DE CRUZAMIENTOS ---
    const summaryHeaders = [
      "Hembra (Madre)",
      "Macho (Padre)",
      "Proyecto Hembra",
      "Proyecto Macho",
      "Viabilidad",
      "Motivo de Inviabilidad / Veto",
      "Valor de Mérito Hembra",
      "Valor de Mérito Macho",
      "Distancia Genética (DG)",
      "Autofecundado"
    ];
    
    const summaryRows: any[][] = [];
    
    // --- HOJA 2: MEMORIA DE CÁLCULO - VM ---
    const vmHeaders = [
      "Variedad Evaluada",
      "Rol (Madre/Padre)",
      "Característica",
      "Valor Real Variedad",
      "Valor Testigo",
      "Porcentaje vs Testigo",
      "Nivel de Calidad",
      "Ponderación Asignada (Peso)",
      "Valor de Mérito Ponderado"
    ];
    
    const vmRows: any[][] = [];
    
    // --- HOJA 3: VETO FITOSANITARIO ---
    const fitosanitarioHeaders = [
      "Variedad Hembra",
      "Variedad Macho",
      "Enfermedad",
      "Valor Hembra",
      "Valor Macho",
      "Límite Máximo Permitido (Tolerancia)",
      "Veto Aplicado"
    ];
    
    const fitosanitarioRows: any[][] = [];
 
    // Para evitar duplicación de cálculos de VM en la Hoja 2
    const procesadasVM = new Set<string>();
    
    // Recorrer la matriz de viabilidades
    viabilidad.forEach((row: any) => {
      if (row && row.length > 0) {
        row.forEach((cell: any) => {
          if (cell) {
            // Buscar perfiles de la madre y el padre
            const florAData = findVarietyData(cell.varA);
            const florBData = findVarietyData(cell.varB);
 
            // 1. Agregar a Hoja 1: Resumen de Cruzamientos
            const isViable = cell.viabilidad === true;
            const dgValue = getDistancia(cell.varA, cell.varB);
            const autofecundado = cell.varA && cell.varB && cell.varA.trim() === cell.varB.trim() ? "SÍ" : "NO";
            
            // Buscar motivos de inviabilidad
            let motivoInviabilidad = "-";
            if (!isViable) {
              const motivos: string[] = [];
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
                          { val: Number(valorRealA), setLvl: (l: number) => lvlA = l },
                          { val: Number(valorRealB), setLvl: (l: number) => lvlB = l }
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
                      } else if (caracteristica === "tchm" || caracteristica === "scrsa" || caracteristica === "dmtro_tllo" || caracteristica === "altura_planta" || caracteristica === "poblacion") {
                        const valTestigo = testigoLimpio[caracteristica];
                        if (valTestigo) {
                          [
                            { val: Number(valorRealA), setLvl: (l: number) => lvlA = l },
                            { val: Number(valorRealB), setLvl: (l: number) => lvlB = l }
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
                            { val: Number(valorRealA), setLvl: (l: number) => lvlA = l },
                            { val: Number(valorRealB), setLvl: (l: number) => lvlB = l }
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
                      
                      if (lvlA !== 999 && lvlB !== 999 && (lvlA + lvlB) > Number(limiteMax)) {
                        motivos.push(`${p.equivalente.toUpperCase()} excede límite (Suma Niveles: ${lvlA}+${lvlB} = ${lvlA + lvlB} > Tolerancia: ${limiteMax})`);
                      }
                    }
                  }
                });
              } else {
                motivos.push("Falta de información agronómica en uno o ambos progenitores");
              }
              motivoInviabilidad = motivos.length > 0 ? motivos.join(" | ") : "Veto manual o restricciones de autogamia";
            }
            
            summaryRows.push([
              cell.varA,
              cell.varB,
              cell.proyecto,
              cell.proyecto2,
              isViable ? "VIABLE" : "INVIABLE / VETO",
              motivoInviabilidad,
              cell.vm || 0,
              cell.vm2 || 0,
              dgValue,
              autofecundado
            ]);
            
            // 2. Agregar a Hoja 2: Memoria de Cálculo - VM (Madre y Padre)
            [
              { varName: cell.varA, rol: "Madre", vmTot: cell.vm, florData: florAData },
              { varName: cell.varB, rol: "Padre", vmTot: cell.vm2, florData: florBData }
            ].forEach(({ varName, rol, vmTot, florData }) => {
              if (varName && !procesadasVM.has(varName) && florData) {
                procesadasVM.add(varName);
                
                ponderados.forEach((p: any) => {
                  const caracteristica = p.equivalente ? p.equivalente.toLowerCase() : "";
                  if (p.ponderado > 0 && caracteristica) {
                    const valorReal = florData[caracteristica] ?? "-";
                    const valorTestigo = testigoLimpio[caracteristica] ?? "-";
                    
                    let porcentaje = "-";
                    let nivel = "-";
                    let vmPonderado = "-";
                    
                    if (valorReal !== "-" && valorTestigo !== "-" && valorReal !== null && valorTestigo !== null) {
                      porcentaje = ((Number(valorReal) * 100) / Number(valorTestigo)).toFixed(2) + "%";
                      
                      // Determinar el nivel de calidad (emulando calcularValorMerito del backend)
                      let lvl = 999;
                      if (caracteristica === "msco_r" || caracteristica === "carbon") {
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
                      } else if (caracteristica === "tchm" || caracteristica === "scrsa" || caracteristica === "dmtro_tllo" || caracteristica === "altura_planta" || caracteristica === "poblacion") {
                        const pct = (Number(valorReal) * 100) / Number(valorTestigo);
                        if (pct > 120) lvl = 1;
                        else if (pct < 120 && pct >= 110) lvl = 2;
                        else if (pct < 110 && pct >= 95) lvl = 3;
                        else if (pct < 95 && pct >= 85) lvl = 4;
                        else lvl = 5;
                      } else if (caracteristica === "volcamiento") {
                        const pct = (Number(valorReal) * 100) / Number(valorTestigo);
                        if (pct < 10) lvl = 1;
                        else if (pct < 20 && pct >= 11) lvl = 2;
                        else if (pct < 30 && pct >= 21) lvl = 3;
                        else if (pct < 49 && pct >= 31) lvl = 4;
                        else lvl = 5;
                      }
                      
                      nivel = lvl === 999 ? "NA" : lvl.toString();
                      vmPonderado = lvl === 999 ? "0.00" : (p.ponderado * lvl / 100).toFixed(2);
                    }
                    
                    vmRows.push([
                      varName,
                      rol,
                      p.equivalente.toUpperCase(),
                      valorReal,
                      valorTestigo,
                      porcentaje,
                      nivel,
                      p.ponderado + "%",
                      vmPonderado
                    ]);
                  }
                });
              }
            });
            
            // 3. Agregar a Hoja 3: Evaluación Fitosanitaria (Vetos)
            const enfermedades = [
              { key: "msco_r", label: "Mosaico" },
              { key: "carbon", label: "Carbón" },
              { key: "rya_cfe_r", label: "Roya Café" },
              { key: "roya_naranja", label: "Roya Naranja" }
            ];
            
            enfermedades.forEach((enfermedad) => {
              const valHembra = florAData?.[enfermedad.key] ?? "-";
              const valMacho = florBData?.[enfermedad.key] ?? "-";
              
              const pondEnf = ponderados.find((p: any) => p.equivalente && p.equivalente.toLowerCase() === enfermedad.key);
              const limiteMax = pondEnf?.nivel ?? "No definido";
              
              let vetoAplicado = "NO";
              if (valHembra !== "-" && valHembra !== null && limiteMax !== "No definido" && Number(valHembra) > Number(limiteMax)) {
                vetoAplicado = "SÍ (Hembra excede límite)";
              } else if (valMacho !== "-" && valMacho !== null && limiteMax !== "No definido" && Number(valMacho) > Number(limiteMax)) {
                vetoAplicado = "SÍ (Macho excede límite)";
              }
              
              fitosanitarioRows.push([
                cell.varA,
                cell.varB,
                enfermedad.label,
                valHembra,
                valMacho,
                limiteMax,
                vetoAplicado
              ]);
            });
          }
        });
      }
    });
    
    // --- CONSTRUIR EL LIBRO DE TRABAJO (WORKBOOK) ---
    const workbook = XLSX.utils.book_new();
    
    // Hoja 1: Resumen de Cruzamientos
    const summaryWorksheet = XLSX.utils.aoa_to_sheet([
      ["CENICAÑA - RESUMEN DE CRUZAMIENTOS Y COMPATIBILIDAD"],
      ["Proyecto ID:", selectedCdCntble.value],
      ["Mega Ambiente:", selectedMegaAmbiente.value],
      ["Testigo de Referencia:", selectedVariety.value],
      ["Fecha de Exportación:", new Date().toLocaleDateString("es-ES")],
      [],
      summaryHeaders,
      ...summaryRows
    ]);
    summaryWorksheet["!cols"] = [
      { wch: 18 }, { wch: 18 }, { wch: 18 }, { wch: 18 }, { wch: 18 }, { wch: 45 }, { wch: 24 }, { wch: 24 }, { wch: 24 }, { wch: 15 }
    ];
    XLSX.utils.book_append_sheet(workbook, summaryWorksheet, "Resumen Cruzamientos");
    
    // Hoja 2: Memoria de Cálculo VM
    const vmWorksheet = XLSX.utils.aoa_to_sheet([
      ["CENICAÑA - MEMORIA DE CÁLCULO DE VALOR DE MÉRITO (VM)"],
      ["Proyecto ID:", selectedCdCntble.value],
      ["Mega Ambiente:", selectedMegaAmbiente.value],
      ["Testigo de Referencia:", selectedVariety.value],
      ["Fecha de Exportación:", new Date().toLocaleDateString("es-ES")],
      [],
      vmHeaders,
      ...vmRows
    ]);
    vmWorksheet["!cols"] = [
      { wch: 18 }, { wch: 12 }, { wch: 18 }, { wch: 18 }, { wch: 15 }, { wch: 22 }, { wch: 18 }, { wch: 24 }, { wch: 24 }
    ];
    XLSX.utils.book_append_sheet(workbook, vmWorksheet, "Memoria de Cálculo VM");
    
    // Hoja 3: Evaluación Fitosanitaria
    const fitosanitarioWorksheet = XLSX.utils.aoa_to_sheet([
      ["CENICAÑA - EVALUACIÓN FITOSANITARIA (FILTROS DE VETO)"],
      ["Proyecto ID:", selectedCdCntble.value],
      ["Mega Ambiente:", selectedMegaAmbiente.value],
      ["Testigo de Referencia:", selectedVariety.value],
      ["Fecha de Exportación:", new Date().toLocaleDateString("es-ES")],
      [],
      fitosanitarioHeaders,
      ...fitosanitarioRows
    ]);
    fitosanitarioWorksheet["!cols"] = [
      { wch: 18 }, { wch: 18 }, { wch: 18 }, { wch: 15 }, { wch: 15 }, { wch: 32 }, { wch: 24 }
    ];
    XLSX.utils.book_append_sheet(workbook, fitosanitarioWorksheet, "Filtros Fitosanitarios");
    
    // Escribir y descargar el archivo
    const filename = `Memoria_Calculos_Cruzamientos_${selectedCdCntble.value}.xlsx`;
    XLSX.writeFile(workbook, filename);
    toast.success("¡Memoria de cálculos generada y descargada con éxito!");
  } catch (error) {
    console.error("Error al exportar la memoria de cálculos:", error);
    toast.error("Ocurrió un error al generar la memoria de cálculos");
  }
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
            selectedCrossings.push(cell);
          }
        });
      }
    });
    
    if (selectedCrossings.length === 0) {
      toast.warning("No hay cruzamientos seleccionados para guardar.");
      isSaving.value = false;
      return;
    }
    
    // 4. Guardar todos los cruzamientos en lote (batch) para rendimiento óptimo e instantáneo
    const batchPayload = selectedCrossings.map((car) => {
      return {
        madre: `${car.varA}_${car.proyecto}_${car.id_caracter}`,
        padres: `${car.varB}_${car.proyecto2}_${car.id_caracter2}`,
        observaciones: "Programacion de Cruzamientos desde Matriz por Proyecto",
        id_ponderados: idPonderado,
        proyectos: `${car.proyecto}`,
        autofecundado: car.varA === car.varB ? 1 : 0
      };
    });

    await CrossingsService.saveCrossingsBatch(batchPayload);
    
    // 5. Limpiar el borrador correspondiente al finalizar con éxito
    localStorage.removeItem(draftKey.value);
    localStorage.removeItem("cruzamientos");
    
    toast.success("¡Programación de cruzamientos guardada y finalizada con éxito!");
    router.push({ name: "crossing_list.show" });
  } catch (error) {
    console.error("Error al guardar la programación de cruzamientos:", error);
    toast.error("Ocurrió un error al guardar la programación de cruzamientos en la base de datos.");
  } finally {
    isSaving.value = false;
  }
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

  th, td {
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
