<template>
  <div class="space-y-8 w-full max-w-4xl mx-auto px-4 pt-6">
    <div>
      <BaseButton variant="secondary" size="sm" :to="{ name: 'crossing_initial_data.show' }">
        <template #icon-left>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
          </svg>
        </template>
        Atrás
      </BaseButton>
    </div>
    <!-- Encabezado con Indicador de Progreso -->
    <div class="border-b border-slate-100 pb-6">
      <div class="flex items-center justify-between mb-4">
        <h1 class="text-3xl font-extrabold text-slate-800 flex items-center">
          <div class="p-2 bg-emerald-50 text-cenicana rounded-lg mr-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M12 6V4m0 2a2 2 0 100 3m0-3a2 2 0 110 3m-9 8h10M-3 14a2 2 0 110-3m3 3a2 2 0 100-3m9 15h10M7 21a2 2 0 110-3m3 3a2 2 0 100-3"
              />
            </svg>
          </div>
          Programación de Cruzamientos
        </h1>
        <span class="text-xs font-bold uppercase tracking-wider px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full">
          Paso 2 de 3: Ponderados y Niveles
        </span>
      </div>
      <div class="flex flex-wrap items-center justify-between ml-11 mt-2 text-sm text-slate-500">
        <span>Defina la variedad de referencia y los pesos de importancia de cada característica.</span>
        <span class="font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-100 mt-2 sm:mt-0">
          Proyecto: {{ selectedCdCntble }}
        </span>
      </div>
    </div>

    <!-- Panel de Configuración -->
    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-premium">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Testigo Selector -->
        <div>
          <label class="block uppercase tracking-wider text-slate-600 text-xs font-bold mb-3">Variedad Testigo (Referencia)</label>
          <div class="relative">
            <ComboBoxMultiple
              :data-list="dataListVariedades"
              :column-value="columnValueVariedades"
              :column-to-show="columnToShowVariedades"
              v-model:selectedData="selectedVariety"
              placeholder="Seleccione la variedad..."
            />
          </div>
        </div>

        <!-- Mega Ambiente Selector -->
        <div>
          <label class="block uppercase tracking-wider text-slate-600 text-xs font-bold mb-3">Mega Ambiente</label>
          <select
            v-model="selectedMegaAmbiente"
            class="w-full px-4 py-2.5 text-sm bg-white border border-slate-200 rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400 outline-none transition-all duration-200 text-slate-700 font-medium"
          >
            <option value="">Seleccione el ambiente...</option>
            <option value="Semiseco">Seco Semiseco</option>
            <option value="Humedo">Húmedo</option>
            <option value="Piedemonte">Piedemonte</option>
          </select>
        </div>

        <!-- Proyecto Selector -->
        <div>
          <label class="block uppercase tracking-wider text-slate-600 text-xs font-bold mb-3">Proyecto</label>
          <div class="relative">
            <ComboBoxMultiple
              :data-list="mappedProjects"
              :column-value="'cd_cntble'"
              :column-to-show="'combinedValue'"
              v-model:selectedData="selectedCdCntble"
              placeholder="Seleccione un proyecto..."
            />
          </div>
        </div>
      </div>

      <!-- Ficha Técnica del Testigo -->
      <Transition name="fade">
        <div
          v-if="varietyProfileData"
          class="mt-2 mb-6 p-5 bg-gradient-to-br from-emerald-50/40 to-teal-50/10 border border-emerald-100/70 rounded-2xl shadow-sm"
        >
          <div class="flex items-center space-x-2.5 mb-4 border-b border-emerald-100/50 pb-3">
            <div class="p-1.5 bg-emerald-500 text-white rounded-lg shadow-sm">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div>
              <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wide">Ficha del Testigo: {{ selectedVariety }}</h4>
              <p class="text-[10px] text-slate-500 font-medium">Valores históricos de rendimiento y sanidad extraídos del Banco de Germoplasma</p>
            </div>
          </div>

          <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <!-- TCHM -->
            <div class="bg-white p-3 rounded-xl border border-slate-100 shadow-sm flex flex-col justify-between">
              <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">TCHM</span>
              <span class="text-xl font-extrabold text-slate-800 mt-1">
                {{ varietyProfileData.tchm !== null && varietyProfileData.tchm !== undefined ? varietyProfileData.tchm.toFixed(1) : "N/A" }}
              </span>
              <span class="text-[8px] text-slate-400 font-semibold mt-0.5">Ton. Caña / Ha</span>
            </div>

            <!-- Sacarosa -->
            <div class="bg-white p-3 rounded-xl border border-slate-100 shadow-sm flex flex-col justify-between">
              <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Sacarosa</span>
              <span class="text-xl font-extrabold text-slate-800 mt-1">
                {{ varietyProfileData.sacarosa !== null && varietyProfileData.sacarosa !== undefined ? varietyProfileData.sacarosa.toFixed(2) : "N/A" }}%
              </span>
              <span class="text-[8px] text-slate-400 font-semibold mt-0.5">Concentración %</span>
            </div>

            <!-- Mosaico -->
            <div class="bg-white p-3 rounded-xl border border-slate-100 shadow-sm flex flex-col justify-between">
              <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Mosaico</span>
              <span class="text-xl font-extrabold mt-1" :class="varietyProfileData.mosaico_p > 3 ? 'text-amber-600' : 'text-emerald-700'">
                {{ varietyProfileData.mosaico_p !== null && varietyProfileData.mosaico_p !== undefined ? varietyProfileData.mosaico_p.toFixed(1) : "N/A" }}
              </span>
              <span class="text-[8px] text-slate-400 font-semibold mt-0.5">Grado susceptibilidad</span>
            </div>

            <!-- Carbón -->
            <div class="bg-white p-3 rounded-xl border border-slate-100 shadow-sm flex flex-col justify-between">
              <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Carbón</span>
              <span class="text-xl font-extrabold mt-1" :class="varietyProfileData.carbon_p > 3 ? 'text-amber-600' : 'text-emerald-700'">
                {{ varietyProfileData.carbon_p !== null && varietyProfileData.carbon_p !== undefined ? varietyProfileData.carbon_p.toFixed(1) : "N/A" }}
              </span>
              <span class="text-[8px] text-slate-400 font-semibold mt-0.5">Grado susceptibilidad</span>
            </div>
          </div>
        </div>
      </Transition>

      <!-- Spinner de Carga de Ficha -->
      <div v-if="isFetchingProfile" class="mt-2 mb-6 p-6 bg-slate-50/50 rounded-2xl border border-slate-100 flex items-center justify-center space-x-2">
        <svg class="animate-spin h-5 w-5 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path
            class="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
          ></path>
        </svg>
        <span class="text-xs font-bold text-slate-500 animate-pulse">Obteniendo datos de variedad testigo...</span>
      </div>

      <!-- Tabla de Ponderados -->
      <div v-if="selectedVariety && (selectedMegaAmbiente || selectedCdCntble)" class="mt-8 space-y-6">

        <!-- Estrategia de Selección -->
        <div class="bg-white border border-slate-100 rounded-xl p-5 shadow-sm">
          <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Estrategia de Selección</h3>
          <div class="flex flex-col sm:flex-row gap-4">
            <label class="flex items-center gap-3 cursor-pointer p-4 border rounded-xl hover:bg-slate-50 transition-colors w-full" :class="{'border-emerald-500 bg-emerald-50/30': estrategia === 'combinacion', 'border-slate-200': estrategia !== 'combinacion'}">
              <input type="radio" v-model="estrategia" value="combinacion" class="text-emerald-600 focus:ring-emerald-500 w-4 h-4" />
              <div>
                <div class="font-bold text-slate-800 text-sm">Combinación de caracteres</div>
                <div class="text-xs text-slate-500 mt-1">Índice ponderado (Por defecto)</div>
              </div>
            </label>
            <label class="flex items-center gap-3 cursor-pointer p-4 border rounded-xl hover:bg-slate-50 transition-colors w-full" :class="{'border-emerald-500 bg-emerald-50/30': estrategia === 'individual', 'border-slate-200': estrategia !== 'individual'}">
              <input type="radio" v-model="estrategia" value="individual" class="text-emerald-600 focus:ring-emerald-500 w-4 h-4" />
              <div>
                <div class="font-bold text-slate-800 text-sm">Filtrar por caracteres</div>
                <div class="text-xs text-slate-500 mt-1">Usar solo flores de ciertos caracteres</div>
              </div>
            </label>
          </div>

          <!-- Checkboxes for specific characters -->
          <div v-if="estrategia === 'individual'" class="mt-4 p-4 bg-amber-50 rounded-xl border border-amber-100">
            <label class="block uppercase tracking-wider text-amber-800 text-[10px] font-bold mb-3">Seleccione los caracteres objetivo</label>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
              <label v-for="car in caracteresProyecto" :key="car.id" class="flex items-center gap-2 cursor-pointer p-2 rounded hover:bg-amber-100/50 transition">
                <input type="checkbox" v-model="caracteresSeleccionados" :value="car.id" class="w-4 h-4 text-amber-600 focus:ring-amber-500 rounded border-amber-300" />
                <div class="flex flex-col">
                  <span class="text-sm font-medium text-amber-900 leading-tight">{{ car.nombre }}</span>
                  <span class="text-[10px] text-amber-700 font-semibold mt-0.5" v-if="car.total_flores > 0">
                    {{ car.total_variedades }} vars / {{ car.total_flores }} flores
                  </span>
                  <span class="text-[10px] text-amber-700/50 font-semibold mt-0.5" v-else>
                    Sin flores hoy
                  </span>
                </div>
              </label>
            </div>
            <p class="text-[10px] text-amber-700 mt-4 font-medium flex items-center gap-1.5">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              Solo las flores asociadas a los caracteres seleccionados serán incluidas en la matriz.
            </p>
          </div>
        </div>

        <div v-show="estrategia === 'combinacion'" class="space-y-3">
          <div class="flex items-center space-x-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">
              Ponderados de Características para la Matriz
              <span v-if="selectedMegaAmbiente" class="text-emerald-600 font-semibold">(Por Mega Ambiente: {{ selectedMegaAmbiente }})</span>
              <span v-else-if="selectedCdCntble" class="text-emerald-600 font-semibold">(Por Proyecto: {{ selectedCdCntble }})</span>
            </h3>
            <BaseButton
              variant="ghost"
              size="sm"
              iconOnly
              @click="isHelpModalOpen = true"
              class="text-emerald-500 hover:text-emerald-700"
              title="Ver ayuda sobre el cálculo de ponderados"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </BaseButton>
          </div>
          <div class="border border-slate-100 rounded-xl shadow-sm">
            <table class="table-auto w-full divide-y divide-slate-100">
              <thead class="bg-slate-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500 rounded-tl-xl">Nombre de la Característica</th>
                  <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-wide text-slate-500">Nivel de Entrada</th>
                  <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-wide text-slate-500">Valor Individual</th>
                  <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-wide text-slate-500">Porcentaje (%)</th>
                  <th class="px-6 py-3 text-right text-xs font-bold uppercase tracking-wide text-slate-500 rounded-tr-xl">Acción</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 bg-white">
                <tr v-for="(item, index) in ponderadosFiltrados" :key="index" class="hover:bg-emerald-50/20 transition-colors duration-150">
                  <td class="whitespace-nowrap px-6 py-4 text-sm font-bold text-slate-700">
                    <div class="flex items-center">
                      {{ item.nombre }}
                      <div class="relative group ml-2 flex items-center justify-center">
                        <span class="text-slate-400 hover:text-emerald-600 cursor-help transition-colors">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                        </span>
                        <div
                          class="absolute bottom-full left-0 mb-2 w-72 p-3 bg-slate-800 text-white text-xs font-medium leading-relaxed rounded-lg shadow-xl opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity z-50 whitespace-pre-wrap text-left z-[100]"
                        >
                          {{ getTooltipText(item.nombre) }}
                          <div class="absolute top-full left-3 border-4 border-transparent border-t-slate-800"></div>
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="whitespace-nowrap px-6 py-4 text-center text-sm font-semibold text-slate-600">{{ item.nivel || "-" }}</td>
                  <td class="whitespace-nowrap px-6 py-4 text-center text-sm text-slate-600 font-medium">{{ item.ponderado || 0 }}</td>
                  <td class="whitespace-nowrap px-6 py-4 text-center text-sm font-bold text-emerald-600 bg-emerald-50/30">{{ calcularPonderado(item) }}%</td>
                  <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                    <BaseButton variant="outline" size="xs" @click="openModal(item, `${!item.ponderado ? 1 : 0}`)"> Modificar </BaseButton>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Estado vacío cuando no han seleccionado testigo y (ambiente o proyecto) -->
      <div v-else class="flex flex-col items-center justify-center py-12 text-center text-slate-400 space-y-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="text-sm font-medium">Seleccione una variedad testigo y elija <strong>únicamente una opción</strong> (Mega Ambiente ó Proyecto) para desplegar y editar los pesos ponderados.</span>
      </div>
    </div>

    <!-- Botones de Navegación -->
    <div class="flex justify-end pt-4">
      <BaseButton
        variant="primary"
        size="md"
        @click="handleSiguiente"
        :disabled="!selectedVariety || (!selectedMegaAmbiente && !selectedCdCntble) || (estrategia === 'individual' && !caracteresSeleccionados.length === 0)"
      >
        Generar Matriz
        <template #icon-right>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
          </svg>
        </template>
      </BaseButton>
    </div>

    <!-- Modal para modificar características -->
    <div v-if="isModalOpen" class="fixed inset-0 flex items-center justify-center bg-slate-900/60 z-50 transition-opacity duration-300">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden border border-slate-100">
        <!-- Header del modal -->
        <div class="flex justify-between items-center border-b border-slate-100 p-5 bg-slate-50">
          <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wide">Modificar Característica</h4>
          <BaseButton variant="ghost" size="sm" iconOnly @click="closeModal" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</BaseButton>
        </div>

        <!-- Cuerpo del modal -->
        <div class="p-6 space-y-6">
          <div class="p-3 bg-emerald-50/50 text-emerald-900 text-xs font-semibold rounded-lg border border-emerald-100/50">
            Editando <span class="underline font-bold">{{ nombre }}</span>
            <span v-if="selectedCdCntble"> para el proyecto <span class="font-bold">{{ selectedCdCntble }}</span></span>
            <span v-else-if="selectedMegaAmbiente"> para el mega ambiente <span class="font-bold">{{ selectedMegaAmbiente }}</span></span>.
          </div>

          <!-- Mensaje de error -->
          <div v-if="errorMessage" class="bg-red-50 text-red-700 text-xs p-3 rounded-lg border border-red-100 font-medium">
            <strong>Error:</strong> {{ errorMessage }}
          </div>

          <!-- Contenido del formulario -->
          <div class="grid grid-cols-2 gap-4">
            <!-- Nivel -->
            <div>
              <label for="nivel_modal" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nivel</label>
              <input
                v-model="nivel"
                id="nivel_modal"
                type="text"
                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 outline-none transition-all"
                @input="validateNumber('nivel')"
                placeholder="Ej. 1"
              />
            </div>

            <!-- Ponderado -->
            <div>
              <label for="ponderado_modal" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Ponderado</label>
              <input
                v-model="ponderado"
                id="ponderado_modal"
                type="text"
                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 outline-none transition-all"
                @input="validateNumber('ponderado')"
                placeholder="Ej. 70"
              />
            </div>
          </div>
        </div>

        <!-- Footer del modal -->
        <div class="flex justify-end items-center border-t border-slate-100 p-5 bg-slate-50 gap-3">
          <BaseButton variant="secondary" size="sm" @click="closeModal"> Cancelar </BaseButton>
          <BaseButton variant="primary" size="sm" @click="modificarCaracteristica"> Guardar Cambios </BaseButton>
        </div>
      </div>
    </div>

    <!-- Modal de Ayuda de Ponderados -->
    <div v-if="isHelpModalOpen" class="fixed inset-0 flex items-center justify-center bg-slate-900/60 z-50 transition-opacity duration-300">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 overflow-hidden border border-slate-100">
        <!-- Header del modal -->
        <div class="flex justify-between items-center border-b border-slate-100 p-5 bg-slate-50">
          <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wide flex items-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2 text-emerald-600"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              stroke-width="2"
            >
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Guía de Ponderados
          </h4>
          <BaseButton variant="ghost" size="sm" iconOnly @click="isHelpModalOpen = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold"
            >&times;</BaseButton
          >
        </div>

        <!-- Cuerpo del modal -->
        <div class="p-6 space-y-4 text-sm text-slate-600 leading-relaxed">
          <p>Esta tabla permite configurar los parámetros que calcularán el <strong>Valor de Mérito</strong> de cada cruzamiento.</p>
          <ul class="space-y-3 mt-3">
            <li class="flex items-start">
              <span class="font-bold text-slate-700 min-w-[120px] mr-2">Nivel de Entrada:</span>
              <span>El límite máximo (tolerancia) de la enfermedad u observación. Se utilizará para aplicar vetos fitosanitarios.</span>
            </li>
            <li class="flex items-start">
              <span class="font-bold text-slate-700 min-w-[120px] mr-2">Valor Individual:</span>
              <span>El peso o importancia base que el programa de mejoramiento asigna a esta característica.</span>
            </li>
            <li class="flex items-start">
              <span class="font-bold text-slate-700 min-w-[120px] mr-2">Porcentaje (%):</span>
              <span>
                Es el impacto real relativo de la característica. Se calcula automáticamente dividiendo el <strong>Valor Individual</strong> de esta
                característica entre la <strong>Suma Total</strong> de los valores individuales de toda la tabla.
              </span>
            </li>
          </ul>
        </div>

        <!-- Footer del modal -->
        <div class="flex justify-end items-center border-t border-slate-100 p-5 bg-slate-50">
          <BaseButton variant="primary" size="sm" @click="isHelpModalOpen = false"> Entendido </BaseButton>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, watch, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useVarietyStore } from "@/stores/variety";
import { useCrossingInitialDataStore } from "@/stores/crossinginitialdata";
import { useParametizeWeightedCrossingStore } from "@/stores/crossignparametizeweighted";
import { useModifyFeaturesCrossingStore } from "@/stores/crossignmodifyfeatures";
import { useToast } from "vue-toastification";
import { useMainStore } from "@/stores/main";
import ComboBoxMultiple from "@/components/ComboBoxMultiple.vue";
import api from "@/services/api";
import urls from "@/services/urls";

// Declaración de variables
const router = useRouter();
const varietyStore = useVarietyStore();
const crossingInitialDataStore = useCrossingInitialDataStore();
const parametizeWeightedCrossignStore = useParametizeWeightedCrossingStore();
const modifyFeaturesStore = useModifyFeaturesCrossingStore();
const toast = useToast();
const mainStore = useMainStore();

// Estrategia de Selección
const estrategia = ref<'combinacion'|'individual'>('combinacion');
const caracteresSeleccionados = ref<any[]>([]);
const caracteresProyecto = ref<any[]>([]);

const fetchCaracteresProyecto = async (proyectoId: string) => {
  try {
    const res = await api.get(`${urls.API_URL}siembra-campo/proyectos/${proyectoId}/caracteres`, {}, true);
    caracteresProyecto.value = res.data;
  } catch (error) {
    console.error("Error al obtener los caracteres del proyecto:", error);
    caracteresProyecto.value = [];
  }
};

// Variables para almacenar las selecciones
const selectedVariety = ref<string | null>(null);
const selectedMegaAmbiente = ref<string | null>("");
const selectedCdCntble = ref<string | null>(localStorage.getItem("selectedCdCntble") || "");
const dataListVariedades = varietyStore.Variety;
const columnValueVariedades = "nm_vrdad";
const columnToShowVariedades = "nm_vrdad";

const mappedProjects = computed(() => {
  return crossingInitialDataStore.crossingInitialDataList.map((item) => ({
    ...item,
    combinedValue: `${item.cd_cntble} - ${item.nm_prycto}`
  }));
});

const varietyProfileData = ref<any>(null);
const isFetchingProfile = ref(false);

// Computed para almacenar y recuperar los valores desde localStorage
const storedVariety = computed(() => localStorage.getItem("selectedVariety"));
const storedMegaAmbiente = computed(() => localStorage.getItem("selectedMegaAmbiente"));
const storedCdCntble = computed(() => localStorage.getItem("selectedCdCntble"));

// Variables y estado para manejar el modal
const isModalOpen = ref(false);
const isHelpModalOpen = ref(false);
const proyecto = ref("");
const nombre = ref("");
const ponderado = ref("");
const nivel = ref("");
const id_caracteristica = ref("");
const nuevo = ref("");
const errorMessage = ref<string | null>(null);

const fetchVarietyProfile = async (varName: string) => {
  if (!varName) {
    varietyProfileData.value = null;
    return;
  }
  isFetchingProfile.value = true;
  try {
    const response = await api.get(`${urls.API_VARIETY_PROFILE}/${encodeURIComponent(varName)}`, {});
    if (response && response.data) {
      varietyProfileData.value = response.data.traits || null;
    } else {
      varietyProfileData.value = null;
    }
  } catch (err) {
    console.error("Error fetching variety profile:", err);
    varietyProfileData.value = null;
  } finally {
    isFetchingProfile.value = false;
  }
};

const getActiveProjectCode = () => {
  if (selectedCdCntble.value) {
    return selectedCdCntble.value;
  }
  if (selectedMegaAmbiente.value) {
    return "General";
  }
  const storedProj = storedCdCntble.value || localStorage.getItem("selectedCdCntble");
  if (storedProj) {
    return storedProj;
  }
  const storedAmb = storedMegaAmbiente.value || localStorage.getItem("selectedMegaAmbiente");
  if (storedAmb) {
    return "General";
  }
  return "General";
};

const getActiveAmbiente = () => {
  if (selectedMegaAmbiente.value) {
    return selectedMegaAmbiente.value;
  }
  return storedMegaAmbiente.value || localStorage.getItem("selectedMegaAmbiente") || "Semiseco";
};

const fetchPonderados = async () => {
  const proj = getActiveProjectCode();
  const amb = getActiveAmbiente();
  await parametizeWeightedCrossignStore.getParametizeWeightedCrossingList(proj, amb);
};

// OnMounted para cargar datos y recuperar valores de localStorage
onMounted(async () => {
  await varietyStore.getVariety();
  await crossingInitialDataStore.getCrossingInitialDataList();

  // Cargar valores desde el localStorage si existen
  if (storedVariety.value) {
    selectedVariety.value = storedVariety.value;
    fetchVarietyProfile(storedVariety.value);
  }
  if (storedMegaAmbiente.value) selectedMegaAmbiente.value = storedMegaAmbiente.value;
  if (storedCdCntble.value) {
    selectedCdCntble.value = storedCdCntble.value;
    const proj = crossingInitialDataStore.crossingInitialDataList.find((p) => p.cd_cntble === storedCdCntble.value);
    if (proj) fetchCaracteresProyecto(proj.id_prycto.toString());
  }

  if (selectedMegaAmbiente.value || selectedCdCntble.value) {
    await fetchPonderados();
  }
});

// Watcher para Mega Ambiente (Exclusivo)
watch(
  () => selectedMegaAmbiente.value,
  (newMegaAmbiente) => {
    if (newMegaAmbiente) {
      if (selectedCdCntble.value) {
        localStorage.setItem("lastSelectedCdCntble", selectedCdCntble.value);
        selectedCdCntble.value = "";
        localStorage.removeItem("selectedCdCntble");
      }
      localStorage.setItem("selectedMegaAmbiente", newMegaAmbiente);
    }
  }
);

// Watcher para Proyecto (Exclusivo)
watch(
  () => selectedCdCntble.value,
  (newCdCntble) => {
    if (newCdCntble) {
      if (selectedMegaAmbiente.value) {
        selectedMegaAmbiente.value = "";
        localStorage.removeItem("selectedMegaAmbiente");
      }
      localStorage.setItem("selectedCdCntble", newCdCntble);
      localStorage.setItem("lastSelectedCdCntble", newCdCntble);
      const proj = crossingInitialDataStore.crossingInitialDataList.find((p) => p.cd_cntble === newCdCntble);
      if (proj) {
        localStorage.setItem("selectedIdProject", proj.id_prycto.toString());
        fetchCaracteresProyecto(proj.id_prycto.toString());
      }
    } else {
      caracteresProyecto.value = [];
      caracteresSeleccionados.value = [];
    }
  }
);

// Watchers para guardar variedad en localStorage
watch(
  () => selectedVariety.value,
  (newVariety) => {
    if (newVariety) {
      localStorage.setItem("selectedVariety", newVariety);
      fetchVarietyProfile(newVariety);
    } else {
      varietyProfileData.value = null;
    }
  }
);

// Watch para consultar los ponderados según la opción seleccionada
watch([selectedMegaAmbiente, selectedCdCntble], async ([newAmbiente, newCdCntble]) => {
  if (newAmbiente || newCdCntble) {
    await fetchPonderados();
  }
});

// Abrir modal y asignar valores iniciales
const openModal = (data: any, nuevoFlag: string) => {
  proyecto.value = data.selectedCdCntble;
  nombre.value = data.nombre;
  ponderado.value = data.ponderado;
  nivel.value = data.nivel;
  id_caracteristica.value = data.id_caracteristica;
  nuevo.value = nuevoFlag;
  isModalOpen.value = true;
};

// Cerrar modal
const closeModal = () => {
  isModalOpen.value = false;
  errorMessage.value = null;
};

// Validación para que solo se ingresen números
const validateNumber = (field: "ponderado" | "nivel") => {
  if (field === "ponderado") {
    ponderado.value = ponderado.value.replace(/[^0-9]/g, "");
  } else if (field === "nivel") {
    nivel.value = nivel.value.replace(/[^0-9]/g, "");
  }
};

// Filtrar los ponderados correctamente
const ponderadosFiltrados = computed(() => {
  return parametizeWeightedCrossignStore.parametizeWeightedCrossingFilter.ponderados || [];
});

// Función para obtener la ayuda (tooltip) de cada característica según su lógica en el backend
const getTooltipText = (nombre: string) => {
  const n = (nombre || "").toLowerCase();

  const isEnfermedad = n.includes("roya") || n.includes("carb") || n.includes("mosaico") || n.includes("enfermedad");
  const isRendimiento =
    n.includes("tchm") || n.includes("sacarosa") || n.includes("diámetro") || n.includes("altura") || n.includes("poblaci") || n.includes("volcamiento");

  if (isEnfermedad) {
    return `Enfermedad (Doble Filtro de Sanidad):
• ¿Qué significa el Nivel?: Es el "presupuesto" máximo de susceptibilidad permitido.
• ¿Cómo se usa en el código?:
  1. Individual: Si la Madre o el Padre superan solos este Nivel, se alerta con un Veto Fitosanitario.
  2. Sumatoria: Internamente, si (Valor Madre + Valor Padre > Nivel), el cruce entero se marca como INVIABLE y se oculta. Esto evita cruzar dos parentales mediocres.`;
  } else if (isRendimiento) {
    return `Rendimiento (Viabilidad):
• ¿Qué significa el Nivel?: Es el límite máximo de "deficiencia combinada" tolerada.
• ¿Cómo se usa en el código?: El sistema compara a la Madre y al Padre contra el Testigo y les pone una nota interna (1=Excelente a 5=Malo). Luego suma ambas notas.
Si (Nota Madre + Nota Padre > Nivel asignado), el cruce entero se marca como INVIABLE y se oculta. Por ej: Si pones 4, obligas a que no haya padres mediocres.`;
  }
  return "Característica evaluable.\nEl Nivel define el umbral requerido para considerar el cruce viable.";
};

// Cálculo del porcentaje ponderado
const calcularPonderado = (item: any) => {
  const sumaPonderados = Number(ponderadosFiltrados.value.reduce((acc: number, curr: any) => acc + (Number(curr.ponderado) || 0), 0));

  const itemPonderado = Number(item.ponderado) || 0;
  return sumaPonderados ? ((itemPonderado / sumaPonderados) * 100).toFixed(2) : "0.00";
};

// Función para modificar las características
const modificarCaracteristica = async () => {
  const nivelValue = Number(nivel.value);
  const ponderadoValue = Number(ponderado.value);

  if (isNaN(nivelValue)) {
    errorMessage.value = "El nivel debe ser un número válido.";
    return;
  } else if (isNaN(ponderadoValue)) {
    errorMessage.value = "El ponderado debe ser un número válido.";
    return;
  }

  const nuevoValue = Number(nuevo.value);
  if (isNaN(nuevoValue)) {
    errorMessage.value = "El nuevo debe ser un número válido.";
    return;
  }

  const proyectoParam = getActiveProjectCode();
  const ambienteParam = getActiveAmbiente();

  try {
    const result = await modifyFeaturesStore.getModifyFeaturesCrossingList(
      id_caracteristica.value ?? "",
      proyectoParam,
      nivelValue.toString(),
      ponderadoValue.toString(),
      ambienteParam,
      nuevoValue
    );

    if (result) {
      toast.success("Actualizado con éxito");
      await fetchPonderados();
      closeModal();
    }
  } catch (error) {
    console.error(error);
    toast.error("Error al modificar");
  }
};

// Manejar el clic en "Generar Matriz"
const handleSiguiente = async () => {
  if (estrategia.value === 'individual') {
    if (caracteresSeleccionados.value.length === 0) {
      toast.error("Debe seleccionar al menos un carácter objetivo");
      return;
    }
    // Convert array to comma-separated string for local storage
    localStorage.setItem('filtroCaracterIndividual', caracteresSeleccionados.value.join(','));
  } else {
    localStorage.removeItem('filtroCaracterIndividual');
  }
  
  // Continuar a la siguiente vista
  router.push({ name: 'crossing_matrix.show' });
};

watch(
  () => mainStore.error,
  () => {
    const error = mainStore.error;
    if (error) {
      toast.error(error);
    }
  }
);
</script>
