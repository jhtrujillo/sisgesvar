<template>
  <Teleport to="body">
    <transition name="modal-fade">
      <div 
        v-if="isOpen" 
        class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 overflow-y-auto"
      >
        <!-- Backdrop Blur de Fondo -->
        <div 
          class="fixed inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity"
          @click="closeModal"
        ></div>

        <!-- Ventana del Modal Comparador -->
        <div 
          class="relative w-full max-w-5xl bg-white rounded-3xl shadow-2xl border border-slate-100 flex flex-col max-h-[90vh] overflow-hidden transform transition-all duration-350"
        >
          <!-- Cabecera Premium del Modal -->
          <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white px-6 py-4 flex items-center justify-between border-b border-slate-700/40 shrink-0">
            <div class="flex items-center space-x-3">
              <div class="p-2 bg-emerald-500/20 text-emerald-400 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-black tracking-tight">Comparador de Progenitores Lado a Lado</h3>
                <p class="text-xs text-slate-300 font-semibold mt-0.5">Análisis comparativo de viabilidad, características agronómicas y sanidad</p>
              </div>
            </div>
            
            <!-- Botón de Cerrar -->
            <button 
              @click="closeModal"
              class="text-slate-400 hover:text-white bg-slate-800/60 hover:bg-slate-700/60 p-2 rounded-xl transition"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>

          <!-- Spinner de Carga -->
          <div v-if="isLoading" class="flex-1 flex flex-col items-center justify-center py-24 space-y-4">
            <div class="relative w-12 h-12">
              <div class="absolute inset-0 rounded-full border-4 border-emerald-100 animate-pulse"></div>
              <div class="absolute inset-0 rounded-full border-4 border-emerald-600 border-t-transparent animate-spin"></div>
            </div>
            <span class="text-xs font-bold text-slate-600 animate-pulse">Consultando datos científicos de ambos progenitores...</span>
          </div>

          <!-- Contenido Principal (Scrollable) -->
          <div v-else class="flex-1 overflow-y-auto p-6 space-y-6 scrollbar-custom bg-slate-50/30">
            
            <!-- SECCIÓN 1: Tarjetas Cabecera Progenitores -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Tarjeta Madre (Hembra) -->
              <div class="bg-gradient-to-br from-pink-500/5 to-rose-500/5 border border-pink-100 rounded-3xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between">
                <div class="absolute top-2 right-4 text-[42px] font-black text-rose-500/10 pointer-events-none select-none">♀</div>
                <div>
                  <div class="flex items-center space-x-2.5">
                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-rose-100 text-rose-800 border border-rose-200">Madre (Hembra)</span>
                    <span v-if="motherProfile.traits?.procedencia" class="text-[10px] text-slate-400 font-bold">• {{ motherProfile.traits.procedencia }}</span>
                  </div>
                  <h4 class="text-2xl font-black text-slate-900 mt-2 tracking-tight">{{ motherName }}</h4>
                </div>
                <div class="grid grid-cols-2 gap-3 mt-4 pt-4 border-t border-rose-100/50 text-xs font-semibold text-slate-600">
                  <div>
                    <span class="text-[9px] font-extrabold text-slate-400 block uppercase">Padre (Abuelo M)</span>
                    <span class="text-slate-800 font-extrabold mt-0.5 block truncate">{{ motherProfile.variety?.padre || 'Desconocido' }}</span>
                  </div>
                  <div>
                    <span class="text-[9px] font-extrabold text-slate-400 block uppercase">Madre (Abuela M)</span>
                    <span class="text-slate-800 font-extrabold mt-0.5 block truncate">{{ motherProfile.variety?.madre || 'Desconocido' }}</span>
                  </div>
                </div>
              </div>

              <!-- Tarjeta Padre (Macho) -->
              <div class="bg-gradient-to-br from-sky-500/5 to-cyan-500/5 border border-sky-100 rounded-3xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between">
                <div class="absolute top-2 right-4 text-[42px] font-black text-sky-500/10 pointer-events-none select-none">♂</div>
                <div>
                  <div class="flex items-center space-x-2.5">
                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-sky-100 text-sky-800 border border-sky-200">Padre (Macho)</span>
                    <span v-if="fatherProfile.traits?.procedencia" class="text-[10px] text-slate-400 font-bold">• {{ fatherProfile.traits.procedencia }}</span>
                  </div>
                  <h4 class="text-2xl font-black text-slate-900 mt-2 tracking-tight">{{ fatherName }}</h4>
                </div>
                <div class="grid grid-cols-2 gap-3 mt-4 pt-4 border-t border-sky-100/50 text-xs font-semibold text-slate-600">
                  <div>
                    <span class="text-[9px] font-extrabold text-slate-400 block uppercase">Padre (Abuelo P)</span>
                    <span class="text-slate-800 font-extrabold mt-0.5 block truncate">{{ fatherProfile.variety?.padre || 'Desconocido' }}</span>
                  </div>
                  <div>
                    <span class="text-[9px] font-extrabold text-slate-400 block uppercase">Madre (Abuela P)</span>
                    <span class="text-slate-800 font-extrabold mt-0.5 block truncate">{{ fatherProfile.variety?.madre || 'Desconocido' }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- SECCIÓN 2: Métricas Agronómicas Clave -->
            <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm space-y-4">
              <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Características Agronómicas</h4>
              
              <div class="space-y-4 divide-y divide-slate-100/80">
                <!-- Sacarosa -->
                <div class="grid grid-cols-12 gap-4 items-center pt-3 first:pt-0">
                  <!-- Valor Madre -->
                  <div class="col-span-4 text-right">
                    <span class="text-sm font-black text-rose-900 bg-rose-50 px-2.5 py-0.5 rounded-lg border border-rose-100">
                      {{ formatNumber(motherProfile.traits?.sacarosa) }}%
                    </span>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden mt-2 relative">
                      <div class="h-full bg-rose-400 rounded-full transition-all" :style="{ width: getPercentageOf(motherProfile.traits?.sacarosa, 25), float: 'right' }"></div>
                    </div>
                  </div>
                  <!-- Nombre y Comparador -->
                  <div class="col-span-4 text-center flex flex-col items-center">
                    <span class="text-xs font-bold text-slate-700">Sacarosa (%)</span>
                    <div class="mt-1 flex items-center justify-center">
                      <span 
                        class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase"
                        :class="getWinnerClass('sacarosa')"
                      >
                        {{ getWinnerLabel('sacarosa', 'Sacarosa') }}
                      </span>
                    </div>
                  </div>
                  <!-- Valor Padre -->
                  <div class="col-span-4 text-left">
                    <span class="text-sm font-black text-sky-900 bg-sky-50 px-2.5 py-0.5 rounded-lg border border-sky-100">
                      {{ formatNumber(fatherProfile.traits?.sacarosa) }}%
                    </span>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden mt-2">
                      <div class="h-full bg-sky-400 rounded-full transition-all" :style="{ width: getPercentageOf(fatherProfile.traits?.sacarosa, 25) }"></div>
                    </div>
                  </div>
                </div>

                <!-- TCHM -->
                <div class="grid grid-cols-12 gap-4 items-center pt-3">
                  <!-- Valor Madre -->
                  <div class="col-span-4 text-right">
                    <span class="text-sm font-black text-rose-900 bg-rose-50 px-2.5 py-0.5 rounded-lg border border-rose-100">
                      {{ formatNumber(motherProfile.traits?.tchm) }}
                    </span>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden mt-2">
                      <div class="h-full bg-rose-400 rounded-full transition-all" :style="{ width: getPercentageOf(motherProfile.traits?.tchm, 20), float: 'right' }"></div>
                    </div>
                  </div>
                  <!-- Nombre y Comparador -->
                  <div class="col-span-4 text-center flex flex-col items-center">
                    <span class="text-xs font-bold text-slate-700">TCHM (Productividad)</span>
                    <div class="mt-1 flex items-center justify-center">
                      <span 
                        class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase"
                        :class="getWinnerClass('tchm')"
                      >
                        {{ getWinnerLabel('tchm', 'TCHM') }}
                      </span>
                    </div>
                  </div>
                  <!-- Valor Padre -->
                  <div class="col-span-4 text-left">
                    <span class="text-sm font-black text-sky-900 bg-sky-50 px-2.5 py-0.5 rounded-lg border border-sky-100">
                      {{ formatNumber(fatherProfile.traits?.tchm) }}
                    </span>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden mt-2">
                      <div class="h-full bg-sky-400 rounded-full transition-all" :style="{ width: getPercentageOf(fatherProfile.traits?.tchm, 20) }"></div>
                    </div>
                  </div>
                </div>

                <!-- Fibra -->
                <div class="grid grid-cols-12 gap-4 items-center pt-3">
                  <!-- Valor Madre -->
                  <div class="col-span-4 text-right">
                    <span class="text-sm font-black text-rose-900 bg-rose-50 px-2.5 py-0.5 rounded-lg border border-rose-100">
                      {{ formatNumber(motherProfile.traits?.fibra) }}%
                    </span>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden mt-2">
                      <div class="h-full bg-rose-400 rounded-full transition-all" :style="{ width: getPercentageOf(motherProfile.traits?.fibra, 25), float: 'right' }"></div>
                    </div>
                  </div>
                  <!-- Nombre y Comparador -->
                  <div class="col-span-4 text-center flex flex-col items-center">
                    <span class="text-xs font-bold text-slate-700">Fibra (%)</span>
                    <div class="mt-1 flex items-center justify-center">
                      <!-- En caña de azúcar, menos fibra suele ser mejor para extracción, a menos que sea para cogeneración. Evaluamos menor fibra como mejor para el azúcar -->
                      <span 
                        class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase"
                        :class="getWinnerClass('fibra', true)"
                      >
                        {{ getWinnerLabel('fibra', 'Fibra', true) }}
                      </span>
                    </div>
                  </div>
                  <!-- Valor Padre -->
                  <div class="col-span-4 text-left">
                    <span class="text-sm font-black text-sky-900 bg-sky-50 px-2.5 py-0.5 rounded-lg border border-sky-100">
                      {{ formatNumber(fatherProfile.traits?.fibra) }}%
                    </span>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden mt-2">
                      <div class="h-full bg-sky-400 rounded-full transition-all" :style="{ width: getPercentageOf(fatherProfile.traits?.fibra, 25) }"></div>
                    </div>
                  </div>
                </div>

                <!-- Pureza -->
                <div class="grid grid-cols-12 gap-4 items-center pt-3">
                  <!-- Valor Madre -->
                  <div class="col-span-4 text-right">
                    <span class="text-sm font-black text-rose-900 bg-rose-50 px-2.5 py-0.5 rounded-lg border border-rose-100">
                      {{ formatNumber(motherProfile.traits?.pureza) }}%
                    </span>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden mt-2">
                      <div class="h-full bg-rose-400 rounded-full transition-all" :style="{ width: getPercentageOf(motherProfile.traits?.pureza, 100), float: 'right' }"></div>
                    </div>
                  </div>
                  <!-- Nombre y Comparador -->
                  <div class="col-span-4 text-center flex flex-col items-center">
                    <span class="text-xs font-bold text-slate-700">Pureza (%)</span>
                    <div class="mt-1 flex items-center justify-center">
                      <span 
                        class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase"
                        :class="getWinnerClass('pureza')"
                      >
                        {{ getWinnerLabel('pureza', 'Pureza') }}
                      </span>
                    </div>
                  </div>
                  <!-- Valor Padre -->
                  <div class="col-span-4 text-left">
                    <span class="text-sm font-black text-sky-900 bg-sky-50 px-2.5 py-0.5 rounded-lg border border-sky-100">
                      {{ formatNumber(fatherProfile.traits?.pureza) }}%
                    </span>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden mt-2">
                      <div class="h-full bg-sky-400 rounded-full transition-all" :style="{ width: getPercentageOf(fatherProfile.traits?.pureza, 100) }"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- SECCIÓN 3: Diagnóstico de Resistencia Sanitaria -->
            <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm space-y-4">
              <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Comparativa y Diagnóstico Sanitario</h4>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Roya Café -->
                <div class="border rounded-2xl p-4 flex flex-col justify-between bg-slate-50/50">
                  <div class="flex items-center justify-between border-b border-slate-200/50 pb-2">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Roya Café</span>
                    <span class="text-[9.5px] font-black px-2 py-0.5 rounded-full bg-slate-200 text-slate-700">Suma: {{ (Number(motherProfile.traits?.roya_cafe_r || 0) + Number(fatherProfile.traits?.roya_cafe_r || 0)).toFixed(1) }}</span>
                  </div>
                  <div class="grid grid-cols-2 gap-2 mt-3 text-center">
                    <div class="p-2 rounded-xl" :class="getDiseaseClass(motherProfile.traits?.roya_cafe_r)">
                      <span class="text-[9px] font-bold block opacity-60">Madre</span>
                      <span class="text-xs font-black block mt-0.5">{{ getDiseaseLabel(motherProfile.traits?.roya_cafe_r) }}</span>
                      <span class="text-[10px] font-extrabold mt-1 inline-block">Grado {{ formatScale(motherProfile.traits?.roya_cafe_r) }}</span>
                    </div>
                    <div class="p-2 rounded-xl" :class="getDiseaseClass(fatherProfile.traits?.roya_cafe_r)">
                      <span class="text-[9px] font-bold block opacity-60">Padre</span>
                      <span class="text-xs font-black block mt-0.5">{{ getDiseaseLabel(fatherProfile.traits?.roya_cafe_r) }}</span>
                      <span class="text-[10px] font-extrabold mt-1 inline-block">Grado {{ formatScale(fatherProfile.traits?.roya_cafe_r) }}</span>
                    </div>
                  </div>
                </div>

                <!-- Roya Naranja -->
                <div class="border rounded-2xl p-4 flex flex-col justify-between bg-slate-50/50">
                  <div class="flex items-center justify-between border-b border-slate-200/50 pb-2">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Roya Naranja</span>
                    <span class="text-[9.5px] font-black px-2 py-0.5 rounded-full bg-slate-200 text-slate-700">Suma: {{ (Number(motherProfile.traits?.roya_naranja_r || 0) + Number(fatherProfile.traits?.roya_naranja_r || 0)).toFixed(1) }}</span>
                  </div>
                  <div class="grid grid-cols-2 gap-2 mt-3 text-center">
                    <div class="p-2 rounded-xl" :class="getDiseaseClass(motherProfile.traits?.roya_naranja_r)">
                      <span class="text-[9px] font-bold block opacity-60">Madre</span>
                      <span class="text-xs font-black block mt-0.5">{{ getDiseaseLabel(motherProfile.traits?.roya_naranja_r) }}</span>
                      <span class="text-[10px] font-extrabold mt-1 inline-block">Grado {{ formatScale(motherProfile.traits?.roya_naranja_r) }}</span>
                    </div>
                    <div class="p-2 rounded-xl" :class="getDiseaseClass(fatherProfile.traits?.roya_naranja_r)">
                      <span class="text-[9px] font-bold block opacity-60">Padre</span>
                      <span class="text-xs font-black block mt-0.5">{{ getDiseaseLabel(fatherProfile.traits?.roya_naranja_r) }}</span>
                      <span class="text-[10px] font-extrabold mt-1 inline-block">Grado {{ formatScale(fatherProfile.traits?.roya_naranja_r) }}</span>
                    </div>
                  </div>
                </div>

                <!-- Mosaico -->
                <div class="border rounded-2xl p-4 flex flex-col justify-between bg-slate-50/50">
                  <div class="flex items-center justify-between border-b border-slate-200/50 pb-2">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Mosaico</span>
                    <span class="text-[9.5px] font-black px-2 py-0.5 rounded-full bg-slate-200 text-slate-700">Suma: {{ (Number(motherProfile.traits?.mosaico_p || 0) + Number(fatherProfile.traits?.mosaico_p || 0)).toFixed(1) }}</span>
                  </div>
                  <div class="grid grid-cols-2 gap-2 mt-3 text-center">
                    <div class="p-2 rounded-xl" :class="getDiseaseClass(motherProfile.traits?.mosaico_p)">
                      <span class="text-[9px] font-bold block opacity-60">Madre</span>
                      <span class="text-xs font-black block mt-0.5">{{ getDiseaseLabel(motherProfile.traits?.mosaico_p) }}</span>
                      <span class="text-[10px] font-extrabold mt-1 inline-block">Grado {{ formatScale(motherProfile.traits?.mosaico_p) }}</span>
                    </div>
                    <div class="p-2 rounded-xl" :class="getDiseaseClass(fatherProfile.traits?.mosaico_p)">
                      <span class="text-[9px] font-bold block opacity-60">Padre</span>
                      <span class="text-xs font-black block mt-0.5">{{ getDiseaseLabel(fatherProfile.traits?.mosaico_p) }}</span>
                      <span class="text-[10px] font-extrabold mt-1 inline-block">Grado {{ formatScale(fatherProfile.traits?.mosaico_p) }}</span>
                    </div>
                  </div>
                </div>

                <!-- Carbón -->
                <div class="border rounded-2xl p-4 flex flex-col justify-between bg-slate-50/50">
                  <div class="flex items-center justify-between border-b border-slate-200/50 pb-2">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Carbón</span>
                    <span class="text-[9.5px] font-black px-2 py-0.5 rounded-full bg-slate-200 text-slate-700">Suma: {{ (Number(motherProfile.traits?.carbon_p || 0) + Number(fatherProfile.traits?.carbon_p || 0)).toFixed(1) }}</span>
                  </div>
                  <div class="grid grid-cols-2 gap-2 mt-3 text-center">
                    <div class="p-2 rounded-xl" :class="getDiseaseClass(motherProfile.traits?.carbon_p)">
                      <span class="text-[9px] font-bold block opacity-60">Madre</span>
                      <span class="text-xs font-black block mt-0.5">{{ getDiseaseLabel(motherProfile.traits?.carbon_p) }}</span>
                      <span class="text-[10px] font-extrabold mt-1 inline-block">Grado {{ formatScale(motherProfile.traits?.carbon_p) }}</span>
                    </div>
                    <div class="p-2 rounded-xl" :class="getDiseaseClass(fatherProfile.traits?.carbon_p)">
                      <span class="text-[9px] font-bold block opacity-60">Padre</span>
                      <span class="text-xs font-black block mt-0.5">{{ getDiseaseLabel(fatherProfile.traits?.carbon_p) }}</span>
                      <span class="text-[10px] font-extrabold mt-1 inline-block">Grado {{ formatScale(fatherProfile.traits?.carbon_p) }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- SECCIÓN 4: Predicción Progenie F1 -->
            <div class="bg-gradient-to-r from-emerald-500/10 via-teal-500/5 to-emerald-500/10 border border-emerald-200/80 rounded-3xl p-5 shadow-sm relative overflow-hidden">
              <div class="absolute top-2 right-4 text-[42px] font-black text-emerald-500/10 pointer-events-none select-none">F1</div>
              <h4 class="text-[11px] font-black text-emerald-800 uppercase tracking-widest">Predicción Estimada de la Progenie (Híbridos F1)</h4>
              <p class="text-[11px] text-slate-500 mt-1 font-semibold flex flex-wrap items-center gap-1.5">
                <span>Valor teórico calculado a partir del comportamiento genético aditivo:</span>
                <span class="inline-flex items-center px-2 py-0.5 bg-emerald-50 text-emerald-800 rounded-md border border-emerald-200/60 font-mono text-[9px] font-bold">
                  F1 = (♀ Madre + ♂ Padre) / 2
                </span>
              </p>
              
              <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4">
                <div class="bg-white border border-emerald-200/50 rounded-2xl p-3 text-center shadow-xs">
                  <span class="text-[9px] font-bold text-slate-400 block uppercase">Sacarosa Estimada</span>
                  <span class="text-base font-black text-emerald-700 mt-1 block">
                    {{ formatNumber((Number(motherProfile.traits?.sacarosa || 0) + Number(fatherProfile.traits?.sacarosa || 0)) / 2) }}%
                  </span>
                </div>
                <div class="bg-white border border-emerald-200/50 rounded-2xl p-3 text-center shadow-xs">
                  <span class="text-[9px] font-bold text-slate-400 block uppercase">TCHM Estimado</span>
                  <span class="text-base font-black text-emerald-700 mt-1 block">
                    {{ formatNumber((Number(motherProfile.traits?.tchm || 0) + Number(fatherProfile.traits?.tchm || 0)) / 2) }}
                  </span>
                </div>
                <div class="bg-white border border-emerald-200/50 rounded-2xl p-3 text-center shadow-xs">
                  <span class="text-[9px] font-bold text-slate-400 block uppercase">Fibra Estimada</span>
                  <span class="text-base font-black text-emerald-700 mt-1 block">
                    {{ formatNumber((Number(motherProfile.traits?.fibra || 0) + Number(fatherProfile.traits?.fibra || 0)) / 2) }}%
                  </span>
                </div>
                <div class="bg-white border border-emerald-200/50 rounded-2xl p-3 text-center shadow-xs">
                  <span class="text-[9px] font-bold text-slate-400 block uppercase">Pureza Estimada</span>
                  <span class="text-base font-black text-emerald-700 mt-1 block">
                    {{ formatNumber((Number(motherProfile.traits?.pureza || 0) + Number(fatherProfile.traits?.pureza || 0)) / 2) }}%
                  </span>
                </div>
              </div>
            </div>

          </div>

          <!-- Pie del Modal con el Diagnóstico de Viabilidad del Cruce -->
          <div class="border-t border-slate-100 p-5 bg-white flex flex-col sm:flex-row items-center justify-between gap-4 shrink-0 rounded-b-3xl">
            <div class="flex items-center space-x-3.5 w-full sm:w-auto">
              <div 
                class="p-3 rounded-2xl shrink-0" 
                :class="isViable ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-rose-50 text-rose-600 border border-rose-100'"
              >
                <svg v-if="isViable" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
              </div>
              <div>
                <h4 class="text-sm font-black" :class="isViable ? 'text-emerald-800' : 'text-rose-800'">
                  {{ isViable ? 'DIAGNÓSTICO: COMBINACIÓN VIABLE' : 'DIAGNÓSTICO: CRUZAMIENTO CON VETO SANITARIO' }}
                </h4>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">
                  {{ isViable 
                    ? 'Esta pareja progenitora cumple con los criterios sanitarios y de mérito para la polinización en campo.' 
                    : 'La susceptibilidad acumulada en Royas, Mosaico o Carbón sobrepasa el umbral sanitario de seguridad.' 
                  }}
                </p>
              </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex items-center space-x-3 w-full sm:w-auto shrink-0 justify-end">
              <button 
                @click="closeModal"
                class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition"
              >
                Cerrar Comparación
              </button>
            </div>
          </div>

        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, watch, computed } from "vue";
import api from "@/services/api";
import urls from "@/services/urls";

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  motherName: {
    type: String,
    required: true
  },
  fatherName: {
    type: String,
    required: true
  },
  // Opcional para saber si ya viene vetado o no de la matriz
  initiallyViable: {
    type: Boolean,
    default: true
  }
});

const emit = defineEmits(["update:isOpen"]);

const isLoading = ref(false);
const motherProfile = ref<any>({ variety: null, traits: null, globalAverages: null });
const fatherProfile = ref<any>({ variety: null, traits: null, globalAverages: null });

// Monitorear apertura
watch(
  () => props.isOpen,
  (newVal) => {
    if (newVal) {
      loadProgenitorsData();
    }
  }
);

// Cargar ambos perfiles concurrentemente
const loadProgenitorsData = async () => {
  if (!props.motherName || !props.fatherName) return;

  isLoading.value = true;
  try {
    const urlMother = `${urls.API_VARIETY_PROFILE}/${encodeURIComponent(props.motherName)}`;
    const urlFather = `${urls.API_VARIETY_PROFILE}/${encodeURIComponent(props.fatherName)}`;

    const [resMother, resFather] = await Promise.all([
      api.get(urlMother, {}, true),
      api.get(urlFather, {}, true)
    ]);

    if (resMother && resMother.data && resMother.data.success) {
      motherProfile.value = resMother.data;
    } else {
      motherProfile.value = { variety: { nm_vrdad: props.motherName }, traits: null, globalAverages: null };
    }

    if (resFather && resFather.data && resFather.data.success) {
      fatherProfile.value = resFather.data;
    } else {
      fatherProfile.value = { variety: { nm_vrdad: props.fatherName }, traits: null, globalAverages: null };
    }
  } catch (error) {
    console.error("Error al cargar los progenitores para comparación:", error);
    motherProfile.value = { variety: { nm_vrdad: props.motherName }, traits: null, globalAverages: null };
    fatherProfile.value = { variety: { nm_vrdad: props.fatherName }, traits: null, globalAverages: null };
  } finally {
    isLoading.value = false;
  }
};

// Determinar viabilidad acumulada (Veto Sanitario)
// En Cenicaña, si la suma de escalas en Mosaico, Carbón o Royas supera cierto umbral, hay veto.
// Evaluamos en base a los datos recuperados:
const isViable = computed(() => {
  if (!motherProfile.value.traits || !fatherProfile.value.traits) return props.initiallyViable;
  
  const mTraits = motherProfile.value.traits;
  const fTraits = fatherProfile.value.traits;

  // Umbral máximo acumulado para Mosaico: 10 (ej: Madre Susceptible 8 + Padre Tolerante 4 = 12 > 10, VETO)
  const sumaMosaico = Number(mTraits.mosaico_p || 0) + Number(fTraits.mosaico_p || 0);
  if (sumaMosaico > 10) return false;

  // Umbral Carbón: 10
  const sumaCarbon = Number(mTraits.carbon_p || 0) + Number(fTraits.carbon_p || 0);
  if (sumaCarbon > 10) return false;

  // Royas sumatoria de seguridad
  const sumaRoyaCafe = Number(mTraits.roya_cafe_r || 0) + Number(fTraits.roya_cafe_r || 0);
  if (sumaRoyaCafe > 11) return false;

  return props.initiallyViable;
});

const closeModal = () => {
  emit("update:isOpen", false);
};

// Formateadores
const formatNumber = (val: any) => {
  if (val === undefined || val === null || isNaN(Number(val))) return "N/D";
  return Number(val).toFixed(1);
};

const formatScale = (val: any) => {
  if (val === undefined || val === null || val === "" || isNaN(Number(val))) return "N/D";
  return Number(val).toFixed(1);
};

const getPercentageOf = (val: any, maxVal: number) => {
  if (!val || isNaN(Number(val))) return "0%";
  const pct = Math.min((Number(val) / maxVal) * 100, 100);
  return `${pct}%`;
};

// Determinar qué progenitor es superior para cada característica
const getWinnerClass = (trait: string, lowerIsBetter: boolean = false) => {
  const mVal = Number(motherProfile.value.traits?.[trait] || 0);
  const fVal = Number(fatherProfile.value.traits?.[trait] || 0);

  if (mVal === fVal || (!mVal && !fVal)) {
    return "bg-slate-100 text-slate-650 border border-slate-200/50";
  }

  const isMotherBetter = lowerIsBetter ? mVal < fVal : mVal > fVal;
  return isMotherBetter
    ? "bg-rose-50 text-rose-700 border border-rose-100"
    : "bg-sky-50 text-sky-700 border border-sky-100";
};

const getWinnerLabel = (trait: string, label: string, lowerIsBetter: boolean = false) => {
  const mVal = Number(motherProfile.value.traits?.[trait] || 0);
  const fVal = Number(fatherProfile.value.traits?.[trait] || 0);

  if (mVal === fVal || (!mVal && !fVal)) return "Equilibrado";

  const isMotherBetter = lowerIsBetter ? mVal < fVal : mVal > fVal;
  return isMotherBetter ? "♀ Madre Superior" : "♂ Padre Superior";
};

// Enfermedades de Cenicaña
const getDiseaseClass = (val: any) => {
  if (val === undefined || val === null || val === "") {
    return "bg-slate-50 border-slate-200 text-slate-400 border";
  }
  const n = Number(val);
  if (n <= 3) {
    return "bg-emerald-50/60 border border-emerald-200 text-emerald-800";
  } else if (n <= 6) {
    return "bg-amber-50/60 border border-amber-200 text-amber-800";
  } else {
    return "bg-rose-50/60 border border-rose-200 text-rose-800";
  }
};

const getDiseaseLabel = (val: any) => {
  if (val === undefined || val === null || val === "") return "Sin Datos";
  const n = Number(val);
  if (n <= 3) return "Resistente";
  if (n <= 6) return "Intermedio";
  return "Susceptible";
};
</script>

<style scoped>
/* Transición del Modal */
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.35s ease, transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
.modal-fade-enter-to,
.modal-fade-leave-from {
  opacity: 1;
}

/* Custom scrollbar para modal */
.scrollbar-custom::-webkit-scrollbar {
  width: 6px;
}
.scrollbar-custom::-webkit-scrollbar-thumb {
  background-color: #cbd5e1;
  border-radius: 9px;
}
.scrollbar-custom::-webkit-scrollbar-track {
  background-color: transparent;
}
</style>
