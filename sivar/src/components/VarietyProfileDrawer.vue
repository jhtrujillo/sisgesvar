<template>
  <Teleport to="body">
    <div class="relative z-[99999]">
    <!-- Overlay de Fondo con Difuminado -->
    <transition
      enter-active-class="ease-out duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="isOpen"
        class="fixed inset-0 bg-slate-900/40 backdrop-blur-[3px] transition-opacity cursor-pointer"
        @click="closeDrawer"
      ></div>
    </transition>

    <!-- Contenedor del Drawer Deslizante -->
    <div
      class="fixed inset-y-0 right-0 max-w-full flex pl-10"
      :class="isOpen ? 'pointer-events-auto' : 'pointer-events-none'"
    >
      <transition
        enter-active-class="transform transition ease-out duration-300 sm:duration-500"
        enter-from-class="translate-x-full"
        enter-to-class="translate-x-0"
        leave-active-class="transform transition ease-in duration-300 sm:duration-500"
        leave-from-class="translate-x-0"
        leave-to-class="translate-x-full"
      >
        <div v-if="isOpen" class="w-screen max-w-md">
          <div class="h-full flex flex-col bg-white/95 backdrop-blur-md shadow-2xl border-l border-slate-100 overflow-y-auto scrollbar-custom relative">
            
            <!-- Botón de Cerrar Absoluto -->
            <button
              type="button"
              class="absolute top-4 right-4 p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-all duration-200 z-50"
              @click="closeDrawer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>

            <!-- Cargando... -->
            <div v-if="isLoading" class="flex-1 flex flex-col items-center justify-center p-8 space-y-4">
              <div class="relative w-12 h-12">
                <div class="absolute inset-0 rounded-full border-4 border-emerald-50"></div>
                <div class="absolute inset-0 rounded-full border-4 border-t-cenicana animate-spin"></div>
              </div>
              <p class="text-xs font-bold text-slate-500 animate-pulse">Obteniendo ficha técnica de {{ currentVarietyName }}...</p>
            </div>

            <!-- Contenido Principal -->
            <div v-else class="flex-1 flex flex-col">
              
              <!-- Cabecera Premium con Gradiente -->
              <div class="bg-gradient-to-br from-cenicana-800 to-emerald-600 px-6 py-8 text-white relative overflow-hidden shadow-md">
                <!-- Decoración de Fondo -->
                <div class="absolute -right-8 -bottom-8 opacity-10 transform rotate-12 pointer-events-none">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-44 w-44" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                  </svg>
                </div>

                <!-- Botón Historial de Navegación -->
                <button
                  v-if="navigationHistory.length > 1"
                  @click="goBackInHistory"
                  class="mb-3 inline-flex items-center px-2.5 py-1 text-[10px] font-bold text-white bg-white/10 hover:bg-white/20 border border-white/20 rounded-lg backdrop-blur-sm transition-all duration-150"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                  </svg>
                  Volver a {{ navigationHistory[navigationHistory.length - 2] }}
                </button>

                <div class="flex items-center gap-3">
                  <h2 class="text-3xl font-black tracking-tight drop-shadow-sm">{{ currentVarietyName }}</h2>
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-white/20 text-white border border-white/30 uppercase tracking-wider backdrop-blur-sm">
                    {{ profile.variety?.tpo || 'BG' }}
                  </span>
                </div>
                <p class="mt-2 text-xs text-emerald-100 font-semibold max-w-xs">Hoja de Vida Agronómica de Banco de Germoplasma.</p>
              </div>

              <!-- Contenido del Perfil -->
              <div class="p-6 space-y-6 flex-1">
                
                <!-- SECCIÓN 1: Pedigree e Identidad -->
                <div class="bg-slate-50/50 border border-slate-100 rounded-2xl p-4.5 space-y-3.5">
                  <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16" />
                    </svg>
                    Genética & Linaje
                  </h3>
                  
                  <div class="grid grid-cols-2 gap-3.5">
                    <!-- Madre -->
                    <div class="bg-white border border-slate-200/60 rounded-xl p-3 shadow-sm hover:border-emerald-200 transition-all duration-200">
                      <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider mb-0.5">Variedad Madre</span>
                      <span
                        v-if="profile.variety?.vrdad_madre && profile.variety.vrdad_madre !== 'null'"
                        class="text-xs font-black text-emerald-700 hover:underline cursor-pointer flex items-center gap-1"
                        @click="loadVarietyProfile(profile.variety.vrdad_madre)"
                      >
                        {{ profile.variety.vrdad_madre }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline opacity-60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                      </span>
                      <span v-else class="text-xs font-bold text-slate-400 block">— Sin Registro</span>
                    </div>

                    <!-- Padre -->
                    <div class="bg-white border border-slate-200/60 rounded-xl p-3 shadow-sm hover:border-sky-200 transition-all duration-200">
                      <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider mb-0.5">Variedad Padre</span>
                      <span
                        v-if="profile.variety?.vrdad_pdre && profile.variety.vrdad_pdre !== 'null'"
                        class="text-xs font-black text-sky-700 hover:underline cursor-pointer flex items-center gap-1"
                        @click="loadVarietyProfile(profile.variety.vrdad_pdre)"
                      >
                        {{ profile.variety.vrdad_pdre }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline opacity-60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                      </span>
                      <span v-else class="text-xs font-bold text-slate-400 block">— Sin Registro</span>
                    </div>
                  </div>

                  <!-- Pedigrí Compuesto -->
                  <div class="bg-white border border-slate-200/60 rounded-xl p-3 shadow-sm">
                    <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider mb-1">Pedigrí Completo</span>
                    <span class="text-xs font-mono font-bold text-slate-650 bg-slate-50/70 py-1.5 px-2 rounded-lg border border-slate-100 block tracking-tight overflow-x-auto">
                      {{ profile.variety?.pdgree || 'No hay pedigrí registrado.' }}
                    </span>
                  </div>
                </div>

                <!-- SECCIÓN 2: Caracterización de Banco de Germoplasma -->
                <div v-if="profile.traits" class="space-y-4">
                  <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-1.5 border-b border-slate-100 pb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2zM9 19h6m-6 0l6-2m0 2V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Ficha Técnica de Campo
                  </h3>

                  <!-- Tarjetas de Parámetros Clave (Comparador Visual con Promedio Global) -->
                  <div class="space-y-4">
                    
                    <!-- Sacarosa -->
                    <div class="space-y-1">
                      <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-slate-700">Sacarosa (%)</span>
                        <span class="font-black text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100">
                          {{ formatNumber(profile.traits.sacarosa) }}%
                        </span>
                      </div>
                      <div class="relative w-full h-3.5 bg-slate-100 rounded-full overflow-hidden shadow-inner border border-slate-200/50">
                        <div
                          class="h-full bg-gradient-to-r from-emerald-400 to-emerald-600 rounded-full transition-all duration-500"
                          :style="{ width: getPercentageOf(profile.traits.sacarosa, 20) }"
                        ></div>
                        <!-- Marcador de promedio global -->
                        <div
                          v-if="profile.globalAverages?.sacarosa"
                          class="absolute top-0 h-full w-0.5 bg-rose-500 z-10 hover:scale-x-150 transition-transform group"
                          :style="{ left: getPercentageOf(profile.globalAverages.sacarosa, 20) }"
                          :title="'Promedio global: ' + formatNumber(profile.globalAverages.sacarosa) + '%'"
                        >
                          <span class="absolute -top-3.5 -left-4 text-[8px] font-black text-rose-600 bg-rose-50 border border-rose-200/60 rounded px-1 scale-0 group-hover:scale-100 transition-transform shadow-sm">
                            Prom: {{ formatNumber(profile.globalAverages.sacarosa) }}%
                          </span>
                        </div>
                      </div>
                    </div>

                    <!-- TCHM (Toneladas Caña Hectárea Mes) -->
                    <div class="space-y-1">
                      <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-slate-700">TCHM (Toneladas Caña/Hectárea/Mes)</span>
                        <span class="font-black text-sky-700 bg-sky-50 px-2 py-0.5 rounded border border-sky-100">
                          {{ formatNumber(profile.traits.tchm) }}
                        </span>
                      </div>
                      <div class="relative w-full h-3.5 bg-slate-100 rounded-full overflow-hidden shadow-inner border border-slate-200/50">
                        <div
                          class="h-full bg-gradient-to-r from-sky-400 to-sky-600 rounded-full transition-all duration-500"
                          :style="{ width: getPercentageOf(profile.traits.tchm, 200) }"
                        ></div>
                        <!-- Marcador de promedio global -->
                        <div
                          v-if="profile.globalAverages?.tchm"
                          class="absolute top-0 h-full w-0.5 bg-rose-500 z-10 hover:scale-x-150 transition-transform group"
                          :style="{ left: getPercentageOf(profile.globalAverages.tchm, 200) }"
                          :title="'Promedio global: ' + formatNumber(profile.globalAverages.tchm)"
                        >
                          <span class="absolute -top-3.5 -left-4 text-[8px] font-black text-rose-600 bg-rose-50 border border-rose-200/60 rounded px-1 scale-0 group-hover:scale-100 transition-transform shadow-sm">
                            Prom: {{ formatNumber(profile.globalAverages.tchm) }}
                          </span>
                        </div>
                      </div>
                    </div>

                    <!-- Fibra -->
                    <div class="space-y-1">
                      <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-slate-700">Fibra (%)</span>
                        <span class="font-black text-slate-700 bg-slate-100 px-2 py-0.5 rounded border border-slate-200/60">
                          {{ formatNumber(profile.traits.fibra) }}%
                        </span>
                      </div>
                      <div class="relative w-full h-3.5 bg-slate-100 rounded-full overflow-hidden shadow-inner border border-slate-200/50">
                        <div
                          class="h-full bg-gradient-to-r from-slate-400 to-slate-500 rounded-full transition-all duration-500"
                          :style="{ width: getPercentageOf(profile.traits.fibra, 25) }"
                        ></div>
                        <!-- Marcador de promedio global -->
                        <div
                          v-if="profile.globalAverages?.fibra"
                          class="absolute top-0 h-full w-0.5 bg-rose-500 z-10 hover:scale-x-150 transition-transform group"
                          :style="{ left: getPercentageOf(profile.globalAverages.fibra, 25) }"
                          :title="'Promedio global: ' + formatNumber(profile.globalAverages.fibra) + '%'"
                        >
                          <span class="absolute -top-3.5 -left-4 text-[8px] font-black text-rose-600 bg-rose-50 border border-rose-200/60 rounded px-1 scale-0 group-hover:scale-100 transition-transform shadow-sm">
                            Prom: {{ formatNumber(profile.globalAverages.fibra) }}%
                          </span>
                        </div>
                      </div>
                    </div>

                    <!-- Pureza -->
                    <div class="space-y-1">
                      <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-slate-700">Pureza (%)</span>
                        <span class="font-black text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-150">
                          {{ formatNumber(profile.traits.pureza) }}%
                        </span>
                      </div>
                      <div class="relative w-full h-3.5 bg-slate-100 rounded-full overflow-hidden shadow-inner border border-slate-200/50">
                        <div
                          class="h-full bg-gradient-to-r from-teal-400 to-teal-600 rounded-full transition-all duration-500"
                          :style="{ width: getPercentageOf(profile.traits.pureza, 100) }"
                        ></div>
                        <!-- Marcador de promedio global -->
                        <div
                          v-if="profile.globalAverages?.pureza"
                          class="absolute top-0 h-full w-0.5 bg-rose-500 z-10 hover:scale-x-150 transition-transform group"
                          :style="{ left: getPercentageOf(profile.globalAverages.pureza, 100) }"
                          :title="'Promedio global: ' + formatNumber(profile.globalAverages.pureza) + '%'"
                        >
                          <span class="absolute -top-3.5 -left-4 text-[8px] font-black text-rose-600 bg-rose-50 border border-rose-200/60 rounded px-1 scale-0 group-hover:scale-100 transition-transform shadow-sm">
                            Prom: {{ formatNumber(profile.globalAverages.pureza) }}%
                          </span>
                        </div>
                      </div>
                    </div>

                  </div>

                  <!-- Diagnóstico de Resistencia a Plagas y Enfermedades -->
                  <div class="mt-6 space-y-3">
                    <h4 class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest">Resistencia Sanitaria</h4>
                    <div class="grid grid-cols-2 gap-3">
                      <!-- Roya Café -->
                      <div class="border rounded-xl p-3 flex flex-col justify-between" :class="getDiseaseClass(profile.traits.roya_cafe_r)">
                        <span class="text-[10px] font-bold block uppercase tracking-wider">Roya Café</span>
                        <div class="flex items-center justify-between mt-1">
                          <span class="text-xs font-black">{{ getDiseaseLabel(profile.traits.roya_cafe_r) }}</span>
                          <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-white/40 border border-current/25">Escala: {{ formatScale(profile.traits.roya_cafe_r) }}</span>
                        </div>
                      </div>

                      <!-- Roya Naranja -->
                      <div class="border rounded-xl p-3 flex flex-col justify-between" :class="getDiseaseClass(profile.traits.roya_naranja_r)">
                        <span class="text-[10px] font-bold block uppercase tracking-wider">Roya Naranja</span>
                        <div class="flex items-center justify-between mt-1">
                          <span class="text-xs font-black">{{ getDiseaseLabel(profile.traits.roya_naranja_r) }}</span>
                          <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-white/40 border border-current/25">Escala: {{ formatScale(profile.traits.roya_naranja_r) }}</span>
                        </div>
                      </div>

                      <!-- Mosaico -->
                      <div class="border rounded-xl p-3 flex flex-col justify-between" :class="getDiseaseClass(profile.traits.mosaico_p)">
                        <span class="text-[10px] font-bold block uppercase tracking-wider">Mosaico</span>
                        <div class="flex items-center justify-between mt-1">
                          <span class="text-xs font-black">{{ getDiseaseLabel(profile.traits.mosaico_p) }}</span>
                          <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-white/40 border border-current/25">Escala: {{ formatScale(profile.traits.mosaico_p) }}</span>
                        </div>
                      </div>

                      <!-- Carbón -->
                      <div class="border rounded-xl p-3 flex flex-col justify-between" :class="getDiseaseClass(profile.traits.carbon_p)">
                        <span class="text-[10px] font-bold block uppercase tracking-wider">Carbón</span>
                        <div class="flex items-center justify-between mt-1">
                          <span class="text-xs font-black">{{ getDiseaseLabel(profile.traits.carbon_p) }}</span>
                          <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-white/40 border border-current/25">Escala: {{ formatScale(profile.traits.carbon_p) }}</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Morfología y Otros -->
                  <div class="bg-slate-50/50 rounded-2xl p-4 border border-slate-100 grid grid-cols-2 gap-3.5 text-xs text-slate-700">
                    <div>
                      <span class="text-[10px] font-bold text-slate-400 block uppercase mb-0.5">Altura Planta</span>
                      <span class="font-extrabold text-slate-800">{{ formatNumber(profile.traits.altura_planta) }} cm</span>
                    </div>
                    <div>
                      <span class="text-[10px] font-bold text-slate-400 block uppercase mb-0.5">Diámetro Tallo</span>
                      <span class="font-extrabold text-slate-800">{{ formatNumber(profile.traits.diametro_tallo) }} mm</span>
                    </div>
                    <div>
                      <span class="text-[10px] font-bold text-slate-400 block uppercase mb-0.5">Aspecto Planta</span>
                      <span class="font-extrabold text-slate-800">Grado {{ formatNumber(profile.traits.aspecto_planta) }} de 5</span>
                    </div>
                    <div>
                      <span class="text-[10px] font-bold text-slate-400 block uppercase mb-0.5">Procedencia</span>
                      <span class="font-extrabold text-slate-800">{{ profile.traits.procedencia || 'Colombia' }}</span>
                    </div>
                  </div>
                </div>

                <!-- Fallback sin caracterización -->
                <div v-else class="bg-amber-50/60 border border-amber-200/60 rounded-2xl p-5 text-center flex flex-col items-center justify-center space-y-2">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                  <h4 class="text-xs font-black text-amber-800 uppercase tracking-wide">Ficha agronómica no disponible</h4>
                  <p class="text-[11px] text-amber-600 max-w-xs font-semibold leading-relaxed">
                    Esta variedad está registrada en el linaje base pero no posee mediciones cuantitativas cargadas en el caracterizador 2009.
                  </p>
                </div>

                <!-- SECCIÓN 3: Observaciones y Notas -->
                <div v-if="profile.variety?.obsrvcnes" class="border border-slate-150 rounded-2xl p-4 bg-slate-50/20">
                  <span class="text-[10px] font-bold text-slate-400 block uppercase mb-1">Observaciones de Registro</span>
                  <p class="text-xs text-slate-600 leading-relaxed font-medium italic">"{{ profile.variety.obsrvcnes }}"</p>
                </div>

              </div>
            </div>

          </div>
        </div>
      </transition>
    </div>
  </div>
</Teleport>
</template>

<script setup lang="ts">
import { ref, watch } from "vue";
import api from "@/services/api";
import urls from "@/services/urls";

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  varietyName: {
    type: String,
    required: true
  }
});

const emit = defineEmits(["update:isOpen"]);

const isLoading = ref(false);
const currentVarietyName = ref("");
const navigationHistory = ref<string[]>([]);
const profile = ref<any>({
  variety: null,
  traits: null,
  globalAverages: null
});

// Monitorear cuando se abre el drawer con una variedad
watch(
  () => props.isOpen,
  (newVal) => {
    if (newVal) {
      navigationHistory.value = [];
      loadVarietyProfile(props.varietyName);
    }
  }
);

// Carga la información de la variedad llamando a nuestro endpoint del backend
const loadVarietyProfile = async (name: string) => {
  if (!name || name === "null" || name === "?") return;
  
  isLoading.value = true;
  currentVarietyName.value = name;
  
  // Agregar al historial de navegación si es una variedad nueva
  if (!navigationHistory.value.includes(name)) {
    navigationHistory.value.push(name);
  }

  try {
    const url = `${urls.API_VARIETY_PROFILE}/${encodeURIComponent(name)}`;
    const response = await api.get(url, {}, true);
    if (response && response.data && response.data.success) {
      profile.value = response.data;
    } else {
      profile.value = { variety: { nm_vrdad: name }, traits: null, globalAverages: null };
    }
  } catch (error) {
    console.error("Error al cargar la hoja de vida de la variedad:", error);
    profile.value = { variety: { nm_vrdad: name }, traits: null, globalAverages: null };
  } finally {
    isLoading.value = false;
  }
};

// Historial para poder volver atrás en el árbol genealógico
const goBackInHistory = () => {
  if (navigationHistory.value.length > 1) {
    navigationHistory.value.pop(); // Quitar la actual
    const previous = navigationHistory.value[navigationHistory.value.length - 1];
    loadVarietyProfile(previous);
  }
};

const closeDrawer = () => {
  emit("update:isOpen", false);
};

// Formateador de números decimales a 1 dígito
const formatNumber = (val: any) => {
  if (val === undefined || val === null || isNaN(Number(val))) return "0";
  return Number(val).toFixed(1);
};

// Formateador de escalas de resistencia sanitaria
const formatScale = (val: any) => {
  if (val === undefined || val === null || val === "" || isNaN(Number(val))) return "N/A";
  return Number(val).toFixed(1);
};

// Obtiene el porcentaje relativo de un valor respecto al máximo
const getPercentageOf = (val: any, maxVal: number) => {
  if (!val || isNaN(Number(val))) return "0%";
  const pct = Math.min((Number(val) / maxVal) * 100, 100);
  return `${pct}%`;
};

// Clases CSS semánticas para la escala de resistencia de Cenicaña (1-3: Resistente, 4-6: Intermedio, 7-9: Susceptible)
const getDiseaseClass = (val: any) => {
  if (val === undefined || val === null || val === "") {
    return "bg-slate-50 border-slate-200 text-slate-400";
  }
  const n = Number(val);
  if (n <= 3) {
    return "bg-emerald-50/60 border-emerald-200 text-emerald-800";
  } else if (n <= 6) {
    return "bg-amber-50/60 border-amber-200 text-amber-800";
  } else {
    return "bg-rose-50/60 border-rose-200 text-rose-800";
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

<style>
/* Animaciones y estilos del scrollbar */
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
