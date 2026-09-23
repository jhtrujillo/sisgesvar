<template>
<div>
  <div class="space-y-6 w-full max-w-[98%] mx-auto px-2 sm:px-4 pt-4">
    <router-link :to="{ name: 'crossing_weighted.show' }">
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
    <!-- Encabezado con Indicador de Progreso -->
    <div class="border-b border-slate-100 pb-4">
      <div class="flex items-center justify-between mb-3">
        <h1 class="text-2xl font-extrabold text-slate-800 flex items-center">
          <div class="p-1.5 bg-emerald-50 text-cenicana rounded-lg mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
              />
            </svg>
          </div>
          Programación de Cruzamientos
        </h1>
        <span class="text-[11px] font-bold uppercase tracking-wider px-2.5 py-0.5 bg-emerald-100 text-emerald-800 rounded-full">
          Paso 3 de 3: Matriz de Cruzamientos
        </span>
      </div>
      <div class="flex flex-wrap items-center justify-between ml-9 text-xs text-slate-500">
        <span>Seleccione las combinaciones viables en la cuadrícula para programar la polinización.</span>
        <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100 mt-1 sm:mt-0">
          Proyecto: {{ selectedCdCntble }}
        </span>
      </div>
    </div>

    <!-- Contenedor de la Matriz (Fluido y Balanceado sin Scrollbar Horizontal) -->
    <div class="bg-white border border-slate-100 rounded-xl p-3 sm:p-4 shadow-premium relative min-h-[250px]">
      <!-- Estado de Procesamiento / Cargando -->
      <div
        v-if="isLoading"
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
            Cruzando progenitores, calculando valores de mérito, calculando viabilidad y calculando matriz de distancia genética...
          </span>
        </div>
      </div>

      <!-- Ayuda del Índice Combinado -->
      <div v-if="tipoMapaCalor === 'ic'" class="mb-4 mt-2 no-print">
        <button
          @click="showICHelp = !showICHelp"
          class="flex items-center space-x-1.5 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-700 hover:text-indigo-800 text-xs font-semibold rounded-md shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-1"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>{{ showICHelp ? "Ocultar explicación del IC" : "💡 ¿Cómo se calcula el Índice Combinado (IC)?" }}</span>
        </button>

        <div
          v-if="showICHelp"
          class="bg-indigo-50 border border-indigo-100 rounded-lg p-3 mt-2 flex items-start space-x-3 text-xs text-indigo-900 shadow-sm transition-all animate-fade-in-up"
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
      </div>

      <!-- Leyenda informativa y Botón de Filtro -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3 border-b border-slate-100 pb-3">
        <div class="flex flex-wrap gap-3 text-[11px] font-semibold text-slate-500">
          <span class="flex items-center">
            <span class="w-3 h-3 bg-blue-100 border border-blue-200 rounded mr-1"></span>
            Cruce Viable / Seguro
          </span>
          <span v-if="!ocultarInviables" class="flex items-center">
            <span class="w-3 h-3 bg-slate-100 border border-slate-200 rounded mr-1"></span>
            Cruce Inviable / Veto
          </span>
          <span class="flex items-center">
            <span class="font-bold text-slate-700 mr-0.5">VM:</span>
            Valor de Mérito
          </span>
          <span class="flex items-center">
            <span class="font-bold text-slate-700 mr-0.5">DG:</span>
            Distancia Genética
          </span>
        </div>

        <!-- Botones de Acción y Filtro -->
        <div class="flex items-center space-x-2 justify-end">
          <button
            @click="isExternalFlowersModalOpen = true"
            class="flex items-center px-3 py-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg shadow-sm transition-all duration-200 cursor-pointer"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            💼 Flores de Bolsa Común
          </button>
          <button
            @click="ocultarInviables = !ocultarInviables"
            class="flex items-center px-3 py-1 text-[11px] font-bold rounded-lg transition-all duration-200 border cursor-pointer"
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
        </div>
      </div>

      <!-- Alerta si no hay ningún cruce viable en modo limpio -->
      <div
        v-if="
          ocultarInviables &&
          MatrixCrossingStore.matrixCrossingsFilter.viabilidad &&
          MatrixCrossingStore.matrixCrossingsFilter.viabilidad.length > 0 &&
          !hasAnyViableCrossing
        "
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
        <span class="text-xs font-semibold text-slate-500">No se encontraron cruzamientos viables en la configuración actual.</span>
        <span class="text-[11px] text-slate-400">Haga clic en "Ver Inviables" para ver todas las combinaciones o modifique los pesos en el paso anterior.</span>
      </div>

      <!-- Cuadrícula -->
      <div v-else class="overflow-hidden border border-slate-100 rounded-xl shadow-sm">
        <div class="max-h-[500px] overflow-x-auto overflow-y-auto scrollbar-custom">
          <table class="table-auto w-full divide-y divide-slate-150">
            <thead class="bg-slate-50">
              <tr>
                <th
                  class="px-2 py-2 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500 bg-slate-50 border-r border-slate-100 sticky top-0 left-0 z-20 shadow-[2px_0_5px_rgba(0,0,0,0.02)] min-w-[110px]"
                >
                  PARENTALES
                </th>
                <!-- Envoltura <template> para evaluar v-if en el scope correcto de Vue 3 -->
                <template v-for="(flor, indexCol) in MatrixCrossingStore.matrixCrossingsFilter.flores || []" :key="flor.vrdad">
                  <th
                    v-if="!ocultarInviables || isColumnViable(indexCol)"
                    :class="['px-2 py-2 text-center text-[11px] font-bold uppercase tracking-wider border-r border-slate-100 sticky top-0 z-10 min-w-[75px]', flor.sxo === 'Hembra' || isEmasculatedLocal(flor.vrdad) ? 'bg-rose-50/80 text-rose-900' : 'bg-sky-50/80 text-sky-900']"
                  >
                    <span
                      class="block font-extrabold text-slate-800 leading-tight cursor-pointer hover:underline hover:text-emerald-700 transition-colors"
                      @click="openVarietyProfile(flor.vrdad)"
                    >
                      {{ flor.vrdad }}
                    </span>
                    <span
                      v-if="MatrixCrossingStore.matrixCrossingsFilter.viabilidad?.[0]?.[indexCol]?.vm2 !== undefined"
                      class="inline-flex items-center mt-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-50 text-slate-500 border border-slate-100"
                    >
                      VM: {{ MatrixCrossingStore.matrixCrossingsFilter.viabilidad[0][indexCol].vm2 }}
                    </span>
                    <span class="block text-[9px] text-slate-400 font-semibold mt-0.5 mb-0.5">Polen: {{ flor.polen }} ({{ flor.sxo }}) | Flores: {{ flor.cantidad_flores || 0 }}</span>
                    <button 
                      v-if="flor.sxo === 'Macho' && !isEmasculatedLocal(flor.vrdad)"
                      @click="promptEmasculate(flor.vrdad)"
                      class="mt-1 bg-rose-100 hover:bg-rose-200 text-rose-700 text-[8px] font-bold py-0.5 px-1.5 rounded uppercase mx-auto block"
                    >
                      Emascular
                    </button>
                    <span v-else-if="isEmasculatedLocal(flor.vrdad)" class="mt-1 flex flex-col items-center">
                      <span class="text-rose-600 text-[8px] font-black uppercase block">[EMASCULADA]</span>
                      <button @click.stop="revertEmasculate(flor.vrdad)" class="mt-0.5 text-[8px] underline text-slate-500 hover:text-slate-700">Deshacer</button>
                    </span>
                  </th>
                </template>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              <!-- Envoltura <template> para evaluar v-if en el scope correcto de Vue 3 -->
              <template v-for="(viabilidadRow, indexRow) in MatrixCrossingStore.matrixCrossingsFilter.viabilidad || []" :key="indexRow">
                <tr v-if="!ocultarInviables || isRowViable(viabilidadRow)" class="hover:bg-slate-50/40 transition-colors">
                  <!-- Celda Madre Fija a la izquierda -->
                  <td
                    :class="['whitespace-nowrap px-2 py-2 text-center text-[11px] font-bold border-r border-slate-100 sticky left-0 z-10 shadow-[2px_0_5px_rgba(0,0,0,0.02)] min-w-[110px]', viabilidadRow[0].sxo === 'Hembra' || isEmasculatedLocal(viabilidadRow[0].varA) ? 'bg-rose-50/60 text-rose-900' : 'bg-sky-50/60 text-sky-900']"
                  >
                    <span
                      class="block font-extrabold text-slate-800 leading-tight cursor-pointer hover:underline hover:text-emerald-700 transition-colors"
                      @click="openVarietyProfile(viabilidadRow[0].varA)"
                      >{{ viabilidadRow[0].varA }}</span
                    >
                    <span class="inline-flex items-center mt-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-50 text-slate-500 border border-slate-100">
                      VM: {{ getRowVm(viabilidadRow) }}
                    </span>
                    <span class="block text-[9px] text-slate-400 mt-0.5 font-semibold">Polen: {{ viabilidadRow[0].polen }} ({{ viabilidadRow[0].sxo }}) | Flores: {{ viabilidadRow[0].cantidad_flores || 0 }}</span>
                    <button 
                      v-if="viabilidadRow[0].sxo === 'Macho' && !isEmasculatedLocal(viabilidadRow[0].varA)"
                      @click="promptEmasculate(viabilidadRow[0].varA)"
                      class="mt-1 bg-rose-100 hover:bg-rose-200 text-rose-700 text-[8px] font-bold py-0.5 px-1.5 rounded uppercase mx-auto block"
                    >
                      Emascular
                    </button>
                    <span v-else-if="isEmasculatedLocal(viabilidadRow[0].varA)" class="mt-1 flex flex-col items-center">
                      <span class="text-rose-600 text-[8px] font-black uppercase block">[EMASCULADA]</span>
                      <button @click.stop="revertEmasculate(viabilidadRow[0].varA)" class="mt-0.5 text-[8px] underline text-slate-500 hover:text-slate-700">Deshacer</button>
                    </span>
                  </td>

                  <!-- Celdas de la matriz filtradas por columna -->
                  <template v-for="(car, indexCol) in viabilidadRow" :key="indexCol">
                    <td
                      v-if="!ocultarInviables || isColumnViable(indexCol)"
                      :class="[
                        car?.viabilidad
                          ? 'bg-blue-50/50 hover:bg-blue-100/50 border-r border-blue-100/50 text-blue-800'
                          : 'bg-slate-50/50 hover:bg-slate-100/50 border-r border-slate-100 text-slate-400 opacity-60'
                      ]"
                      class="p-1.5 text-center border-b border-slate-100 transition-all duration-200 min-w-[75px]"
                    >
                      <div class="flex flex-col items-center justify-center space-y-1">
                        <input
                          type="checkbox"
                          :checked="!!car?.viabilidad"
                          @click="toggleCruzamiento(car)"
                          class="h-3.5 w-3.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-100 transition cursor-pointer"
                        />
                        <div class="flex flex-col items-center justify-center w-full border-t border-slate-100/50 pt-1.5 mt-1 space-y-1">
                          <span class="text-[9px] font-extrabold tracking-tight leading-none text-slate-700 text-center">
                            DG: {{ getDistancia(car?.varA, car?.varB) || "NA" }}
                          </span>
                          <span v-if="car?.emasculado" class="text-[7.5px] font-black text-rose-600 block text-center uppercase mt-0.5 leading-none">
                            [EMASCULADA]
                          </span>
                          <!-- Botón Comparador Lado a Lado -->
                          <button
                            @click.stop="openParentComparator(car?.varA, car?.varB, car?.viabilidad, car?.causa_veto)"
                            class="text-[8px] font-bold px-1.5 py-0.5 bg-slate-100 hover:bg-emerald-50 text-slate-650 hover:text-emerald-700 rounded border border-slate-200/60 hover:border-emerald-200 transition-all duration-150 flex items-center justify-center space-x-0.5"
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
    <div class="flex justify-end pt-2">
      <button
        @click="submitCruzamientos"
        class="flex items-center px-5 py-2 text-xs font-bold text-white bg-cenicana hover:bg-cenicana-800 rounded-xl shadow-md transition-all duration-200"
      >
        Programar Cruzamientos
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
      </button>
    </div>
  </div>
  <input type="hidden" v-model="selectedCdCntble" id="proyecto_id_modal" />
  <input type="hidden" v-model="selectedVariety" id="variedad" />
  <input type="hidden" v-model="selectedMegaAmbiente" id="ambiente" />

  <!-- Drawer de Hoja de Vida de la Variedad (Quick Drawer) -->
  <VarietyProfileDrawer v-model:isOpen="isDrawerOpen" :varietyName="selectedVarietyForDrawer" />

  <!-- Modal de Comparación Lado a Lado -->
  <ParentComparatorModal
    v-model:isOpen="isComparatorOpen"
    :motherName="comparatorMother"
    :fatherName="comparatorFather"
    :initiallyViable="comparatorInitiallyViable"
    :causaVeto="comparatorCausa"
  />

  <!-- Modal de Flores de Otros Proyectos / Bolsa Común -->
  <ExternalFlowersModal
    v-model:isOpen="isExternalFlowersModalOpen"
    :currentProject="selectedCdCntble"
    @flowerAssigned="handleFlowerAssigned"
  />

    <!-- Modal HTML de Emasculación -->
    <div v-if="showEmasculateModal" class="relative z-[9999]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="fixed inset-0 bg-slate-900/60 transition-opacity"></div>
      <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
          <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-rose-100 sm:mx-0 sm:h-10 sm:w-10">
                  <svg class="h-6 w-6 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                </div>
                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                  <h3 class="text-lg font-black leading-6 text-slate-900" id="modal-title">Atención: Incompatibilidad de Sexo</h3>
                  <div class="mt-2">
                    <p class="text-sm text-slate-500 font-medium">
                      Ambos parentales seleccionados son masculinos. Para que la polinización sea biológicamente viable, la variedad receptora debe ser emasculada.
                    </p>
                    <div class="mt-4 bg-slate-50 border border-slate-100 rounded-lg p-3">
                      <p class="text-sm text-slate-700 font-bold">
                        ¿Autoriza registrar a la variedad <span class="text-rose-600 font-black">{{ emasculateTargetVar }}</span> como <span class="uppercase font-black">emasculada</span> para programar este cruzamiento?
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
              <button @click="confirmEmasculateAction" type="button" class="inline-flex w-full justify-center rounded-lg bg-rose-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-rose-500 sm:ml-3 sm:w-auto">Autorizar Emasculación</button>
              <button @click="cancelEmasculateAction" type="button" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-4 py-2.5 text-sm font-bold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto">Cancelar</button>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import { useMatrixCrossingStore } from "@/stores/crossingmatrix";
import { useToast } from "vue-toastification";
import type { CruzamientoSeleccionado } from "@/services/types";
import VarietyProfileDrawer from "@/components/VarietyProfileDrawer.vue";
import ParentComparatorModal from "@/components/ParentComparatorModal.vue";
import ExternalFlowersModal from "@/components/ExternalFlowersModal.vue";
const showEmasculateModal = ref(false);
const emasculateTargetVar = ref("");
const emasculateTargetCar = ref<any>(null);

const emasculadasLocales = ref(new Set<string>());
const emasculadasOriginalData = ref(new Map<string, any>());

const isEmasculatedLocal = (varName: string) => {
  return emasculadasLocales.value.has(varName);
};

const promptEmasculate = (varName: string) => {
  emasculateTargetVar.value = varName;
  // Usamos el mismo modal pero sin "emasculateTargetCar", indicando que es global para la variedad
  emasculateTargetCar.value = null; 
  showEmasculateModal.value = true;
};


const confirmGlobalEmasculate = () => {
  const varName = emasculateTargetVar.value;
  emasculadasLocales.value.add(varName);

  // Guardar datos originales
  const flores = MatrixCrossingStore.matrixCrossingsFilter.flores || [];
  const flor = flores.find((f: any) => f.vrdad === varName);
  if (flor) {
    if (!emasculadasOriginalData.value.has(varName)) {
      emasculadasOriginalData.value.set(varName, { polen: flor.polen, sxo: flor.sxo });
    }
    flor.polen = 0;
    flor.sxo = 'Hembra'; // <- Ahora dice Hembra
  }

  recalculateMatrixViability();
  
  toast.success('Variedad ' + varName + ' emasculada. Matriz recalculada.');
  showEmasculateModal.value = false;
};

const revertEmasculate = (varName: string) => {
  emasculadasLocales.value.delete(varName);
  
  const flores = MatrixCrossingStore.matrixCrossingsFilter.flores || [];
  const flor = flores.find((f: any) => f.vrdad === varName);
  if (flor) {
    const orig = emasculadasOriginalData.value.get(varName);
    if (orig) {
      flor.polen = orig.polen;
      flor.sxo = orig.sxo;
    }
  }

  recalculateMatrixViability();
  toast.info('Variedad ' + varName + ' restaurada a su estado original.');
};

const recalculateMatrixViability = () => {
  const viabilidades = MatrixCrossingStore.matrixCrossingsFilter.viabilidad || [];
  viabilidades.forEach((row: any) => {
    row.forEach((cell: any) => {
      if (!cell || !cell.varA || !cell.varB) return;
      
      const motherEmasc = emasculadasLocales.value.has(cell.varA);
      const fatherEmasc = emasculadasLocales.value.has(cell.varB);
      
      if (motherEmasc) {
          cell.polen = 0;
          cell.sxo = 'Hembra';
      } else {
          const origA = emasculadasOriginalData.value.get(cell.varA);
          if (origA) {
              cell.polen = origA.polen;
              cell.sxo = origA.sxo;
          }
      }
      
      if (fatherEmasc) {
          cell.polen2 = 0;
          cell.sxo2 = 'Hembra';
      } else {
          const origB = emasculadasOriginalData.value.get(cell.varB);
          if (origB) {
              cell.polen2 = origB.polen;
              cell.sxo2 = origB.sxo;
          }
      }

      const motherSex = cell.sxo;
      const fatherSex = cell.sxo2;

      // Restablecer el veto primero, borrando vetos previos inyectados por emasculacion o desemasculacion
      if (cell.causa_veto) {
        cell.causa_veto = cell.causa_veto.replace(/\s*\|?\s*Incompatibilidad de sexo \(Ambos son Macho\)/, '');
        cell.causa_veto = cell.causa_veto.replace(/\s*\|?\s*Incompatibilidad de sexo \(Ambas son Hembra\)/, '');
        if (cell.causa_veto === 'Incompatibilidad de sexo (Ambos son Macho)' || cell.causa_veto === 'Incompatibilidad de sexo (Ambas son Hembra)' || cell.causa_veto === 'Cruce viable') {
           cell.causa_veto = '';
        }
      }

      // Re-aplicar vetos
      if (motherSex === 'Macho' && fatherSex === 'Macho') {
         cell.viabilidad = false;
         cell.emasculado = false;
         cell.causa_veto = (cell.causa_veto && cell.causa_veto !== '' ? cell.causa_veto + ' | ' : '') + 'Incompatibilidad de sexo (Ambos son Macho)';
      } else if (motherSex === 'Hembra' && fatherSex === 'Hembra') {
         cell.viabilidad = false;
         cell.emasculado = false;
         cell.causa_veto = (cell.causa_veto && cell.causa_veto !== '' ? cell.causa_veto + ' | ' : '') + 'Incompatibilidad de sexo (Ambas son Hembra)';
      } else {
         if (!cell.causa_veto || cell.causa_veto.trim() === '') {
            cell.viabilidad = true;
            if (motherEmasc) cell.emasculado = true;
            else cell.emasculado = false;
            cell.causa_veto = 'Cruce viable';
         } else {
            cell.viabilidad = false;
            cell.emasculado = false;
         }
      }
    });
  });
};




const confirmEmasculateAction = () => {
  if (!emasculateTargetCar.value) {
    return confirmGlobalEmasculate();
  }

  if (emasculateTargetCar.value) {
    const car = emasculateTargetCar.value;
    car.emasculado = true;
    
    // Continuar con la logica original
    car.viabilidad = true;
    if (car.flores_madre !== undefined) {
      car.flores_madre = 1;
      car.flores_padre = 1;
    }
    
    // Save draft
    if (typeof draftKey !== 'undefined' && typeof viabilidadesMatriz !== 'undefined') {
       const rows = viabilidadesMatriz.value || [];
       const savedState = [];
       rows.forEach((row) => {
         if (row && Array.isArray(row)) {
           row.forEach((c) => {
             if (c && c.varA && c.varB) {
               savedState.push({ varA: c.varA.trim(), varB: c.varB.trim(), viabilidad: !!c.viabilidad, emasculado: !!c.emasculado });
             }
           });
         }
       });
       localStorage.setItem(draftKey.value, JSON.stringify(savedState));
    } else if (typeof draftKey !== 'undefined' && typeof MatrixCrossingStore !== 'undefined') {
       const savedState = [];
       const viabilidades = MatrixCrossingStore.matrixCrossingsFilter.viabilidad || [];
       viabilidades.forEach((row) => {
         if (row && Array.isArray(row)) {
           row.forEach((c) => {
             if (c && c.varA && c.varB) {
               savedState.push({ varA: c.varA.trim(), varB: c.varB.trim(), viabilidad: !!c.viabilidad, emasculado: !!c.emasculado });
             }
           });
         }
       });
       localStorage.setItem(draftKey.value, JSON.stringify(savedState));
    }
  }
  showEmasculateModal.value = false;
};

const cancelEmasculateAction = () => {
  emasculateTargetCar.value = null;
  showEmasculateModal.value = false;
};


const MatrixCrossingStore = useMatrixCrossingStore();
const toast = useToast();
const router = useRouter();

const selectedVariety = ref("");
const selectedMegaAmbiente = ref("");
const selectedCdCntble = ref("");
const ocultarInviables = ref(false); // Vista compacta limpia por defecto
const isLoading = ref(false);
const showICHelp = ref(false);
const tipoMapaCalor = ref("");

// State for ExternalFlowersModal
const isExternalFlowersModalOpen = ref(false);

const handleFlowerAssigned = async () => {
  const activeProj = selectedCdCntble.value || localStorage.getItem("lastSelectedCdCntble") || localStorage.getItem("selectedCdCntble") || "010105";
  const activeAmb = selectedMegaAmbiente.value || localStorage.getItem("selectedMegaAmbiente") || "Semiseco";
  const activeVar = selectedVariety.value || localStorage.getItem("selectedVariety") || "";

  if (activeProj && activeVar) {
    isLoading.value = true;
    try {
            const caracterFiltro = localStorage.getItem("filtroCaracterIndividual");
      const pondProj = localStorage.getItem("selectedCdCntble") || "General";
      await MatrixCrossingStore.getMatrixCrossingList(activeProj, pondProj, activeVar, activeAmb, caracterFiltro);
    } catch (error) {
      console.error("Error al recargar matriz tras asignar flor:", error);
    } finally {
      isLoading.value = false;
    }
  }
};

// Refs for VarietyProfileDrawer
const isDrawerOpen = ref(false);
const selectedVarietyForDrawer = ref("");

const getRowVm = (row: any[]) => {
  if (!row || row.length === 0) return "0";
  const validCell = row.find((cell) => cell && cell.vm !== 1 && cell.vm !== "1" && cell.vm !== 0 && cell.vm !== "0");
  return validCell ? validCell.vm : row[0]?.vm || "0";
};

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
const comparatorCausa = ref("");

const openParentComparator = (mother: string, father: string, viable: boolean, causa: string = "") => {
  if (mother && father) {
    comparatorMother.value = mother;
    comparatorFather.value = father;
    comparatorInitiallyViable.value = viable;
    comparatorCausa.value = causa;
    isComparatorOpen.value = true;
  }
};

// Cargar datos del localStorage al montar el componente
onMounted(() => {
  const storedVariety = localStorage.getItem("selectedVariety");
  if (storedVariety) {
    selectedVariety.value = storedVariety;
  }

  const storedMegaAmbiente = localStorage.getItem("selectedMegaAmbiente");
  if (storedMegaAmbiente) {
    selectedMegaAmbiente.value = storedMegaAmbiente;
  }

  const storedCdCntble = localStorage.getItem("selectedCdCntble");
  if (storedCdCntble) {
    selectedCdCntble.value = storedCdCntble;
  }

});

// Watch para recargar los datos al cambiar los filtros con indicador de carga
watch([selectedMegaAmbiente, selectedCdCntble, selectedVariety], async ([newMegaAmbiente, newCdCntble, newVariety]) => {
  const activeProj = newCdCntble || localStorage.getItem("lastSelectedCdCntble") || localStorage.getItem("selectedCdCntble") || "010105";
  const activeAmb = newMegaAmbiente || localStorage.getItem("selectedMegaAmbiente") || "Semiseco";

  if ((newMegaAmbiente || newCdCntble) && newVariety) {
    isLoading.value = true;
    try {
            const caracterFiltro = localStorage.getItem("filtroCaracterIndividual");
      const pondProj = localStorage.getItem("selectedCdCntble") || "General";
      await MatrixCrossingStore.getMatrixCrossingList(activeProj, pondProj, newVariety, activeAmb, caracterFiltro);

      // Restaurar el borrador para sincronizar Step 2 y Step 3 en ambas direcciones
      const storedDraft = localStorage.getItem(`sivarcc_draft_crossings_${activeProj}_${activeAmb}`);
      if (storedDraft) {
        const savedState = JSON.parse(storedDraft);
        const viabilidades = MatrixCrossingStore.matrixCrossingsFilter.viabilidad || [];
        viabilidades.forEach((row: any) => {
          row.forEach((car: any) => {
            if (car && car.varA && car.varB) {
              const match = savedState.find((d: any) => d.varA === car.varA.trim() && d.varB === car.varB.trim());
              if (match) {
                car.viabilidad = match.viabilidad;
              }
            }
          });
        });
      }

    } catch (error) {
      console.error("Error al cargar la matriz de cruzamientos:", error);
      toast.error("Error al calcular la matriz de cruzamientos");
    } finally {
      isLoading.value = false;
    }
  }
});

// Helper reactivo para verificar si hay al menos UN cruce viable en toda la matriz
const hasAnyViableCrossing = computed(() => {
  const viabilidad = MatrixCrossingStore.matrixCrossingsFilter.viabilidad || [];
  return viabilidad.some((row: any) => row.some((car: any) => car?.viabilidad));
});

// Auto-desactivar "Ocultar Inviables" si no hay ninguna combinación viable
watch(
  hasAnyViableCrossing,
  (newVal) => {
    const viabilidad = MatrixCrossingStore.matrixCrossingsFilter.viabilidad || [];
    if (newVal === false && viabilidad.length > 0) {
      ocultarInviables.value = false;
    }
  },
  { immediate: true }
);

// Helper para verificar si un índice de columna (Padre) tiene al menos un cruce viable
const isColumnViable = (indexCol: number) => {
  const viabilidad = MatrixCrossingStore.matrixCrossingsFilter.viabilidad || [];
  return viabilidad.some((row: any) => row[indexCol]?.viabilidad);
};

// Helper para verificar si una fila (Madre) tiene al menos un cruce viable
const isRowViable = (viabilidadRow: any[]) => {
  return viabilidadRow.some((car: any) => car?.viabilidad);
};

// Función para obtener la distancia entre dos variables
const getDistancia = (varA: string, varB: string) => {
  if (varA && varB && varA === varB) return "NA";
  const distancias = MatrixCrossingStore.matrixCrossingsFilter.distancias || {};
  return distancias[varA]?.[varB] || "NA";
};


// El draftKey debe coincidir con el usado en la vista de Programacion de Cruzamientos
const draftKey = computed(() => `sivarcc_draft_crossings_${selectedCdCntble.value}_${selectedMegaAmbiente.value}`);

// Función para alternar el cruzamiento cuando se hace click
const toggleCruzamiento = (car: any) => {
  if (!car.viabilidad && car.causa_veto && car.causa_veto.includes("Ambos son Macho")) {
    emasculateTargetVar.value = car.varA;
    emasculateTargetCar.value = car;
    showEmasculateModal.value = true;
    return; // Esperamos la accion del modal
  } else if (car.viabilidad) {
    // Si se deselecciona, quitamos el flag por si acaso
    car.emasculado = false;
  }

  // Mutar la viabilidad localmente
  car.viabilidad = !car.viabilidad;


  // Recolectar todos los cruces para guardarlos en el borrador exacto
  const savedState: Array<{ varA: string; varB: string; viabilidad: boolean }> = [];
  const viabilidades = MatrixCrossingStore.matrixCrossingsFilter.viabilidad || [];
  viabilidades.forEach((row: any) => {
    row.forEach((c: any) => {
      if (c && c.varA && c.varB) {
        savedState.push({ varA: c.varA.trim(), varB: c.varB.trim(), viabilidad: !!c.viabilidad, emasculado: !!c.emasculado });
      }
    });
  });

  // Guardar en localStorage para que el Paso 3 lo recupere
  localStorage.setItem(draftKey.value, JSON.stringify(savedState));

};

// Función para enviar los cruzamientos y pasar al siguiente paso
const submitCruzamientos = () => {
  router.push({ name: "crossing_suggestion_per_project.show" });
};
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
</style>
