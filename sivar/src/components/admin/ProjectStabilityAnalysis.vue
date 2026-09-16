<template>
  <div class="space-y-6">
    <!-- Header Summary & KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-gradient-to-br from-emerald-50 to-teal-50/80 p-4 rounded-2xl border border-emerald-100 shadow-sm">
        <div class="text-[10px] font-extrabold uppercase text-emerald-800 tracking-wider">Variable Analizada</div>
        <div class="text-xl font-black text-emerald-950 mt-1 capitalize">
          {{ variableActual === 'tsh' ? 'TSH (Ton Azúcar/ha)' : variableActual === 'tch' ? 'TCH (Ton Caña/ha)' : '% Sacarosa' }}
        </div>
        <div class="text-[11px] text-emerald-700 font-semibold mt-1">
          Media General: <span class="font-bold text-emerald-900">{{ grandMean }}</span>
        </div>
      </div>

      <div class="bg-gradient-to-br from-blue-50 to-indigo-50/80 p-4 rounded-2xl border border-blue-100 shadow-sm">
        <div class="text-[10px] font-extrabold uppercase text-blue-800 tracking-wider">Varianza GGE Biplot (PC1+PC2)</div>
        <div class="text-xl font-black text-blue-950 mt-1">
          {{ ggeBiplot?.var_explicada_total || 0 }}%
        </div>
        <div class="text-[11px] text-blue-700 font-semibold mt-1">
          PC1: {{ ggeBiplot?.var_explicada_pc1 }}% • PC2: {{ ggeBiplot?.var_explicada_pc2 }}%
        </div>
      </div>

      <div class="bg-gradient-to-br from-purple-50 to-fuchsia-50/80 p-4 rounded-2xl border border-purple-100 shadow-sm">
        <div class="text-[10px] font-extrabold uppercase text-purple-800 tracking-wider">Varianza AMMI Biplot</div>
        <div class="text-xl font-black text-purple-950 mt-1">
          {{ ammiBiplot?.var_explicada_total || 0 }}%
        </div>
        <div class="text-[11px] text-purple-700 font-semibold mt-1">
          PC1: {{ ammiBiplot?.var_explicada_pc1 }}% • PC2: {{ ammiBiplot?.var_explicada_pc2 }}%
        </div>
      </div>

      <div class="bg-gradient-to-br from-amber-50 to-orange-50/80 p-4 rounded-2xl border border-amber-100 shadow-sm">
        <div class="text-[10px] font-extrabold uppercase text-amber-800 tracking-wider">Ensayos / Ambientes</div>
        <div class="text-xl font-black text-amber-950 mt-1">
          {{ ambientes?.length || 0 }} Ambientes
        </div>
        <div class="text-[11px] text-amber-700 font-semibold mt-1">
          {{ variedades?.length || 0 }} Variedades Evaluadas
        </div>
      </div>
    </div>

    <!-- 3 Categorized Control Cards Architecture -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
      <!-- Card 1: Variables & Testigo -->
      <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-200/80 space-y-3">
        <div class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
          <span>⚙️ 1. Métrica & Referencia</span>
        </div>
        <div class="space-y-2">
          <div>
            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Variable Agronómica</label>
            <div class="grid grid-cols-3 gap-1 bg-white p-1 rounded-xl border border-slate-200">
              <button
                v-for="v in [
                  { id: 'tsh', label: 'TSH' },
                  { id: 'tch', label: 'TCH' },
                  { id: 'sacarosa', label: '% Sac' }
                ]"
                :key="v.id"
                @click="cambiarVariable(v.id)"
                class="py-1 px-2 text-xs font-bold rounded-lg transition-all cursor-pointer"
                :class="variableActual === v.id ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'"
              >
                {{ v.label }}
              </button>
            </div>
          </div>

          <div>
            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Testigo Referencia</label>
            <select
              v-model="testigoSeleccionado"
              class="w-full text-xs font-semibold bg-white border border-slate-200 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
            >
              <option v-for="varName in variedades" :key="varName" :value="varName">
                {{ varName }} {{ esTestigo(varName) ? ' (Testigo Standard)' : '' }}
              </option>
            </select>
          </div>
        </div>
      </div>

      <!-- Card 2: Biplot Options -->
      <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-200/80 space-y-3">
        <div class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
          <span>📊 2. Modo & Opciones Biplot</span>
        </div>
        <div class="space-y-2">
          <div>
            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Modelo de Análisis</label>
            <select
              v-model="tipoBiplot"
              class="w-full text-xs font-semibold bg-white border border-slate-200 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
            >
              <option value="gge_which_won">GGE Biplot — Which-Won-Where (Sectores)</option>
              <option value="gge_mean_stability">GGE Biplot — Rendimiento Medio vs Estabilidad</option>
              <option value="ammi1">AMMI1 — Rendimiento vs CP1</option>
              <option value="ammi2">AMMI2 — CP1 vs CP2 Interacción</option>
            </select>
          </div>

          <div class="flex flex-wrap gap-2 pt-1">
            <label class="inline-flex items-center text-xs font-semibold text-slate-600 cursor-pointer">
              <input type="checkbox" v-model="mostrarVectores" class="rounded text-emerald-600 focus:ring-emerald-500 mr-1.5" />
              Vectores Ambiente
            </label>
            <label class="inline-flex items-center text-xs font-semibold text-slate-600 cursor-pointer">
              <input type="checkbox" v-model="mostrarConvexHull" class="rounded text-emerald-600 focus:ring-emerald-500 mr-1.5" />
              Polígono Convexo
            </label>
            <label class="inline-flex items-center text-xs font-semibold text-slate-600 cursor-pointer">
              <input type="checkbox" v-model="mostrarEtiquetas" class="rounded text-emerald-600 focus:ring-emerald-500 mr-1.5" />
              Etiquetas
            </label>
          </div>
        </div>
      </div>

      <!-- Card 3: Navigation & Export -->
      <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-200/80 space-y-3">
        <div class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
          <span>🔍 3. Navegación & Reportes</span>
        </div>
        <div class="space-y-2">
          <div class="flex items-center space-x-1.5">
            <button
              @click="zoomIn"
              class="flex-1 py-1.5 bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all shadow-sm cursor-pointer"
            >
              🔍 + Zoom
            </button>
            <button
              @click="zoomOut"
              class="flex-1 py-1.5 bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all shadow-sm cursor-pointer"
            >
              🔍 - Zoom
            </button>
            <button
              @click="resetZoom"
              class="py-1.5 px-3 bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all shadow-sm cursor-pointer"
            >
              ↺ Reset
            </button>
          </div>

          <div class="grid grid-cols-2 gap-1.5">
            <button
              @click="exportarSVG"
              class="py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm cursor-pointer flex items-center justify-center gap-1"
            >
              📥 SVG / Image
            </button>
            <button
              @click="exportarCSV"
              class="py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition-all shadow-sm cursor-pointer flex items-center justify-center gap-1"
            >
              📊 Export CSV
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Biplot Canvas Area -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-md p-4 relative overflow-hidden">
      <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-2">
        <div class="flex items-center space-x-2">
          <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
          <h3 class="text-sm font-black text-slate-800 tracking-tight">
            {{ biplotTitulo }}
          </h3>
        </div>
        <span class="text-[11px] text-slate-400 font-semibold">
          Arrastra para desplazar • Rueda para zoom
        </span>
      </div>

      <!-- SVG Container -->
      <div
        ref="svgContainer"
        class="w-full h-[540px] bg-slate-950/95 rounded-2xl relative cursor-grab active:cursor-grabbing overflow-hidden border border-slate-900"
        @mousedown="startPan"
        @mousemove="doPan"
        @mouseup="endPan"
        @mouseleave="endPan"
        @wheel.prevent="handleWheel"
      >
        <!-- Loading overlay -->
        <div v-if="isLoading" class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm z-20 flex flex-col items-center justify-center text-white space-y-3">
          <div class="w-8 h-8 rounded-full border-4 border-emerald-400 border-t-transparent animate-spin"></div>
          <span class="text-xs font-bold text-slate-300">Generando matriz GxE y descomposición SVD...</span>
        </div>

        <svg
          ref="svgElement"
          width="100%"
          height="100%"
          viewBox="0 0 900 600"
          preserveAspectRatio="xMidYMid meet"
          class="w-full h-full select-none"
        >
          <defs>
            <!-- Marker for environment vectors -->
            <marker
              id="env-arrow"
              viewBox="0 0 10 10"
              refX="7"
              refY="5"
              markerWidth="6"
              markerHeight="6"
              orient="auto-start-reverse"
            >
              <path d="M 0 0 L 10 5 L 0 10 z" fill="#38bdf8" />
            </marker>

            <!-- Glow Filters -->
            <filter id="glow-gen" x="-20%" y="-20%" width="140%" height="140%">
              <feGaussianBlur stdDeviation="3" result="blur" />
              <feComposite in="SourceGraphic" in2="blur" operator="over" />
            </filter>
          </defs>

          <!-- Main Zoom & Pan Group -->
          <g :transform="`translate(${panX}, ${panY}) scale(${zoomScale})`">
            <!-- Grid Background -->
            <g class="grid-lines" opacity="0.15">
              <line v-for="x in gridX" :key="'gx-'+x" :x1="x" y1="-2000" :x2="x" y2="2000" stroke="#94a3b8" stroke-dasharray="4,4" />
              <line v-for="y in gridY" :key="'gy-'+y" :x1="-2000" :y1="y" x2="2000" :y2="y" stroke="#94a3b8" stroke-dasharray="4,4" />
            </g>

            <!-- Origin Axes (0,0) -->
            <line x1="-2000" y1="300" x2="2000" y2="300" stroke="#64748b" stroke-width="1.5" stroke-dasharray="6,6" />
            <line x1="450" y1="-2000" x2="450" y2="2000" stroke="#64748b" stroke-width="1.5" stroke-dasharray="6,6" />

            <!-- Axis Labels -->
            <text x="870" y="290" fill="#94a3b8" font-size="11" font-weight="bold" text-anchor="end">
              {{ tipoBiplot === 'ammi1' ? 'Rendimiento Medio (Diferencia vs Media)' : 'PC1' }}
            </text>
            <text x="460" y="25" fill="#94a3b8" font-size="11" font-weight="bold">
              {{ tipoBiplot === 'ammi1' ? 'PC1' : 'PC2' }}
            </text>

            <!-- Concentric Stability Rings -->
            <g v-if="tipoBiplot === 'gge_mean_stability'" opacity="0.25">
              <circle v-for="r in [60, 120, 180, 240, 300]" :key="'ring-'+r" cx="450" cy="300" :r="r" fill="none" stroke="#38bdf8" stroke-dasharray="4,4" />
            </g>

            <!-- Convex Hull Polygon & Sector Lines -->
            <g v-if="mostrarConvexHull && hullPointsSVG.length > 2">
              <polygon
                :points="hullPointsSVG"
                fill="rgba(16, 185, 129, 0.08)"
                stroke="#10b981"
                stroke-width="2"
                stroke-dasharray="6,4"
              />
              <!-- Perpendicular Sector Rays -->
              <g v-if="tipoBiplot === 'gge_which_won'">
                <line
                  v-for="(ray, rIdx) in sectorRays"
                  :key="'ray-'+rIdx"
                  :x1="ray.x1"
                  :y1="ray.y1"
                  :x2="ray.x2"
                  :y2="ray.y2"
                  stroke="#f59e0b"
                  stroke-width="1.5"
                  stroke-dasharray="4,4"
                  opacity="0.75"
                />
              </g>
            </g>

            <!-- Environment Vectors -->
            <g v-if="mostrarVectores">
              <g v-for="env in ambientesBiplot" :key="env.id">
                <line
                  x1="450"
                  y1="300"
                  :x2="toSvgX(getEnvX(env))"
                  :y2="toSvgY(getEnvY(env))"
                  stroke="#38bdf8"
                  stroke-width="2"
                  marker-end="url(#env-arrow)"
                  opacity="0.85"
                />
                <!-- Environment Label -->
                <text
                  :x="toSvgX(getEnvX(env)) + 8"
                  :y="toSvgY(getEnvY(env)) + 4"
                  fill="#7dd3fc"
                  font-size="11"
                  font-weight="bold"
                  class="pointer-events-none"
                >
                  {{ env.nombre }}
                </text>
              </g>
            </g>

            <!-- Genotype Markers -->
            <g v-for="gen in genotiposBiplot" :key="gen.variedad">
              <!-- Glow for Reference Check -->
              <circle
                v-if="gen.es_testigo || gen.variedad === testigoSeleccionado"
                :cx="toSvgX(getGenX(gen))"
                :cy="toSvgY(getGenY(gen))"
                r="10"
                fill="#f59e0b"
                opacity="0.3"
                filter="url(#glow-gen)"
              />

              <!-- Marker Circle / Square -->
              <circle
                v-if="!gen.es_testigo"
                :cx="toSvgX(getGenX(gen))"
                :cy="toSvgY(getGenY(gen))"
                :r="gen.variedad === testigoSeleccionado ? 7.5 : 6"
                :fill="gen.variedad === testigoSeleccionado ? '#f59e0b' : '#10b981'"
                stroke="#ffffff"
                stroke-width="1.5"
                class="transition-all hover:scale-125 cursor-pointer"
                @mouseenter="hoverGenotipo = gen"
                @mouseleave="hoverGenotipo = null"
              />
              <rect
                v-else
                :x="toSvgX(getGenX(gen)) - 6"
                :y="toSvgY(getGenY(gen)) - 6"
                width="12"
                height="12"
                fill="#ef4444"
                stroke="#ffffff"
                stroke-width="1.5"
                class="transition-all hover:scale-125 cursor-pointer"
                @mouseenter="hoverGenotipo = gen"
                @mouseleave="hoverGenotipo = null"
              />

              <!-- Label -->
              <text
                v-if="mostrarEtiquetas"
                :x="toSvgX(getGenX(gen)) + 9"
                :y="toSvgY(getGenY(gen)) + 4"
                :fill="gen.es_testigo ? '#fca5a5' : gen.variedad === testigoSeleccionado ? '#fcd34d' : '#e2e8f0'"
                font-size="11"
                :font-weight="gen.es_testigo || gen.variedad === testigoSeleccionado ? 'bold' : 'normal'"
                class="pointer-events-none"
              >
                {{ gen.variedad }}
              </text>
            </g>
          </g>
        </svg>

        <!-- Tooltip Hover Card -->
        <div
          v-if="hoverGenotipo"
          class="absolute bottom-4 right-4 bg-slate-900/90 text-white p-3 rounded-2xl border border-slate-700 backdrop-blur-md shadow-xl text-xs space-y-1 z-10 pointer-events-none min-w-[200px]"
        >
          <div class="font-black text-emerald-400 text-sm flex items-center justify-between">
            <span>{{ hoverGenotipo.variedad }}</span>
            <span v-if="hoverGenotipo.es_testigo" class="text-[10px] bg-red-500/30 text-red-300 px-1.5 py-0.5 rounded-full border border-red-400/40">TESTIGO</span>
          </div>
          <div class="text-slate-300 flex justify-between">
            <span>Rendimiento Medio:</span>
            <span class="font-bold text-white">{{ hoverGenotipo.media }}</span>
          </div>
          <div class="text-slate-300 flex justify-between">
            <span>PC1 (Adaptabilidad):</span>
            <span class="font-bold text-sky-400">{{ hoverGenotipo.pc1 }}</span>
          </div>
          <div class="text-slate-300 flex justify-between">
            <span>PC2 (Estabilidad):</span>
            <span class="font-bold text-purple-400">{{ hoverGenotipo.pc2 }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Stability Statistics Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Eberhart & Russell Table -->
      <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h4 class="text-sm font-black text-slate-800">Modelo de Eberhart & Russell (1966)</h4>
            <p class="text-[11px] text-slate-500">Parámetros de adaptabilidad ($b_i$) y desvío de regresión ($S^2_{di}$)</p>
          </div>
          <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
            Regresión Ambiental
          </span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-xs text-left">
            <thead class="bg-slate-50 text-slate-500 font-extrabold uppercase text-[10px] border-b border-slate-100">
              <tr>
                <th class="py-2.5 px-3">Variedad</th>
                <th class="py-2.5 px-3 text-right">Media</th>
                <th class="py-2.5 px-3 text-right">Coef. b_i</th>
                <th class="py-2.5 px-3 text-right">Desv. S2di</th>
                <th class="py-2.5 px-3 text-center">Diagnóstico</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
              <tr
                v-for="row in eberhartRussellData"
                :key="row.variedad"
                :class="row.variedad === testigoSeleccionado ? 'bg-amber-50/60 font-bold' : 'hover:bg-slate-50/60'"
              >
                <td class="py-2.5 px-3 flex items-center gap-1.5">
                  <span v-if="row.es_testigo" class="text-xs">⭐️</span>
                  {{ row.variedad }}
                </td>
                <td class="py-2.5 px-3 text-right font-bold text-slate-900">{{ row.media }}</td>
                <td class="py-2.5 px-3 text-right font-mono font-bold text-sky-700">{{ row.bi }}</td>
                <td class="py-2.5 px-3 text-right font-mono text-slate-500">{{ row.s2di }}</td>
                <td class="py-2.5 px-3 text-center">
                  <span
                    class="px-2 py-0.5 rounded-full text-[10px] font-extrabold"
                    :class="{
                      'bg-emerald-100 text-emerald-800 border border-emerald-200': row.adaptabilidad_type === 'success',
                      'bg-blue-100 text-blue-800 border border-blue-200': row.adaptabilidad_type === 'primary',
                      'bg-amber-100 text-amber-800 border border-amber-200': row.adaptabilidad_type === 'warning'
                    }"
                  >
                    {{ row.adaptabilidad_label }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Lin & Binns Superiority Table -->
      <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h4 class="text-sm font-black text-slate-800">Índice de Superioridad de Lin & Binns (1988)</h4>
            <p class="text-[11px] text-slate-500">Mide la proximidad ($P_i$) al máximo rendimiento local</p>
          </div>
          <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-purple-100 text-purple-800 border border-purple-200">
            Índice P_i
          </span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-xs text-left">
            <thead class="bg-slate-50 text-slate-500 font-extrabold uppercase text-[10px] border-b border-slate-100">
              <tr>
                <th class="py-2.5 px-3 text-center">Rank</th>
                <th class="py-2.5 px-3">Variedad</th>
                <th class="py-2.5 px-3 text-right">Media</th>
                <th class="py-2.5 px-3 text-right">Índice P_i</th>
                <th class="py-2.5 px-3 text-center">Nivel Superioridad</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
              <tr
                v-for="row in linBinnsData"
                :key="row.variedad"
                :class="row.variedad === testigoSeleccionado ? 'bg-amber-50/60 font-bold' : 'hover:bg-slate-50/60'"
              >
                <td class="py-2.5 px-3 text-center">
                  <span
                    class="w-5 h-5 rounded-full inline-flex items-center justify-center text-[10px] font-black"
                    :class="row.ranking === 1 ? 'bg-amber-400 text-slate-900 shadow-sm' : row.ranking <= 3 ? 'bg-slate-200 text-slate-800' : 'text-slate-400'"
                  >
                    {{ row.ranking }}
                  </span>
                </td>
                <td class="py-2.5 px-3 flex items-center gap-1.5">
                  <span v-if="row.es_testigo" class="text-xs">⭐️</span>
                  {{ row.variedad }}
                </td>
                <td class="py-2.5 px-3 text-right font-bold text-slate-900">{{ row.media }}</td>
                <td class="py-2.5 px-3 text-right font-mono font-bold text-purple-700">{{ row.pi_index }}</td>
                <td class="py-2.5 px-3 text-center">
                  <span
                    class="px-2 py-0.5 rounded-full text-[10px] font-extrabold"
                    :class="row.ranking <= 3 ? 'bg-purple-100 text-purple-800 border border-purple-200' : 'bg-slate-100 text-slate-600'"
                  >
                    {{ row.ranking <= 3 ? 'Alta Superioridad' : 'Estándar' }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import projectManagementService from '@/services/projectManagement.services';

const props = defineProps({
  projectId: {
    type: [Number, String],
    required: true
  }
});

// State variables
const variableActual = ref('tsh');
const testigoSeleccionado = ref('CC 85-92');
const tipoBiplot = ref('gge_which_won');
const mostrarVectores = ref(true);
const mostrarConvexHull = ref(true);
const mostrarEtiquetas = ref(true);

const isLoading = ref(false);
const rawData = ref(null);
const hoverGenotipo = ref(null);

// Pan & Zoom state
const panX = ref(0);
const panY = ref(0);
const zoomScale = ref(1);
const isPanning = ref(false);
const startMouseX = ref(0);
const startMouseY = ref(0);

// API fetch method using standard projectManagementService
const fetchEstabilidadData = async () => {
  if (!props.projectId) return;
  isLoading.value = true;
  try {
    const res = await projectManagementService.getEstabilidadAgronomica(props.projectId, variableActual.value);
    rawData.value = res.data || res;
  } catch (err) {
    console.error('Error al obtener datos de estabilidad:', err);
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchEstabilidadData();
});

const cambiarVariable = (v) => {
  variableActual.value = v;
  fetchEstabilidadData();
};

watch(() => props.projectId, () => {
  fetchEstabilidadData();
});

// Computed properties
const grandMean = computed(() => rawData.value?.grand_mean || 0);
const ambientes = computed(() => rawData.value?.ambientes || []);
const variedades = computed(() => rawData.value?.variedades || []);

const ggeBiplot = computed(() => rawData.value?.gge_biplot || {});
const ammiBiplot = computed(() => rawData.value?.ammi_biplot || {});

const eberhartRussellData = computed(() => rawData.value?.estabilidad?.eberhart_russell || []);
const linBinnsData = computed(() => rawData.value?.estabilidad?.lin_binns || []);

const biplotTitulo = computed(() => {
  switch (tipoBiplot.value) {
    case 'gge_which_won': return 'GGE Biplot — Análisis "Which-Won-Where" (Sectores y Polígono Convexo)';
    case 'gge_mean_stability': return 'GGE Biplot — Rendimiento Medio vs Estabilidad (Eje AEC)';
    case 'ammi1': return 'Biplot AMMI1 — Rendimiento Medio vs Primer Componente Principal (PC1)';
    case 'ammi2': return 'Biplot AMMI2 — Interacción PC1 vs PC2';
    default: return 'Biplot de Estabilidad Agronómica';
  }
});

const genotiposBiplot = computed(() => {
  if (tipoBiplot.value.startsWith('ammi')) {
    return ammiBiplot.value.genotipos || [];
  }
  return ggeBiplot.value.genotipos || [];
});

const ambientesBiplot = computed(() => {
  if (tipoBiplot.value.startsWith('ammi')) {
    return ammiBiplot.value.ambientes || [];
  }
  return ggeBiplot.value.ambientes || [];
});

// Dynamic Coordinate Mappings & Scaling
const getGenX = (gen) => {
  if (tipoBiplot.value === 'ammi1') {
    return (gen.media || 0) - grandMean.value;
  }
  return gen.pc1 || 0;
};

const getGenY = (gen) => {
  if (tipoBiplot.value === 'ammi1') {
    return gen.pc1 || 0;
  }
  return gen.pc2 || 0;
};

const getEnvX = (env) => {
  if (tipoBiplot.value === 'ammi1') {
    return (env.media || 0) - grandMean.value;
  }
  return env.pc1 || 0;
};

const getEnvY = (env) => {
  if (tipoBiplot.value === 'ammi1') {
    return env.pc1 || 0;
  }
  return env.pc2 || 0;
};

const scaleFactor = computed(() => {
  const gens = genotiposBiplot.value;
  const envs = ambientesBiplot.value;
  let maxVal = 0.5;

  gens.forEach(g => {
    const x = Math.abs(getGenX(g));
    const y = Math.abs(getGenY(g));
    if (x > maxVal) maxVal = x;
    if (y > maxVal) maxVal = y;
  });

  envs.forEach(e => {
    const x = Math.abs(getEnvX(e));
    const y = Math.abs(getEnvY(e));
    if (x > maxVal) maxVal = x;
    if (y > maxVal) maxVal = y;
  });

  return 240 / maxVal;
});

const toSvgX = (val) => 450 + (val * scaleFactor.value);
const toSvgY = (val) => 300 - (val * scaleFactor.value);

const gridX = [-300, -150, 0, 150, 300, 450, 600, 750, 900, 1050, 1200];
const gridY = [-300, -150, 0, 150, 300, 450, 600, 750, 900, 1050, 1200];

// Convex Hull SVG polygon string
const hullPointsSVG = computed(() => {
  const indices = ggeBiplot.value.convex_hull_indices || [];
  const gens = ggeBiplot.value.genotipos || [];
  if (indices.length < 3) return '';
  
  return indices.map(idx => {
    const g = gens[idx];
    if (!g) return '';
    return `${toSvgX(getGenX(g))},${toSvgY(getGenY(g))}`;
  }).filter(Boolean).join(' ');
});

// Perpendicular Sector Rays for "Which-Won-Where"
const sectorRays = computed(() => {
  const indices = ggeBiplot.value.convex_hull_indices || [];
  const gens = ggeBiplot.value.genotipos || [];
  if (indices.length < 3) return [];

  const rays = [];
  const count = indices.length;
  for (let k = 0; k < count; k++) {
    const g1 = gens[indices[k]];
    const g2 = gens[indices[(k + 1) % count]];
    if (!g1 || !g2) continue;

    const dx = getGenX(g2) - getGenX(g1);
    const dy = getGenY(g2) - getGenY(g1);
    // Perpendicular vector (-dy, dx)
    const perpX = -dy;
    const perpY = dx;

    // Extend ray from origin (0,0)
    rays.push({
      x1: 450,
      y1: 300,
      x2: 450 + perpX * 350,
      y2: 300 - perpY * 350
    });
  }
  return rays;
});

const esTestigo = (varName) => {
  return varName === 'CC 85-92' || varName === 'CC 01-1940';
};

// Pan & Zoom controls
const startPan = (e) => {
  isPanning.value = true;
  startMouseX.value = e.clientX - panX.value;
  startMouseY.value = e.clientY - panY.value;
};

const doPan = (e) => {
  if (!isPanning.value) return;
  panX.value = e.clientX - startMouseX.value;
  panY.value = e.clientY - startMouseY.value;
};

const endPan = () => {
  isPanning.value = false;
};

const handleWheel = (e) => {
  const delta = e.deltaY > 0 ? -0.1 : 0.1;
  zoomScale.value = Math.max(0.4, Math.min(3.5, zoomScale.value + delta));
};

const zoomIn = () => { zoomScale.value = Math.min(3.5, zoomScale.value + 0.25); };
const zoomOut = () => { zoomScale.value = Math.max(0.4, zoomScale.value - 0.25); };
const resetZoom = () => {
  panX.value = 0;
  panY.value = 0;
  zoomScale.value = 1;
};

// Export functions
const exportarSVG = () => {
  const svgEl = document.querySelector('svg');
  if (!svgEl) return;
  const serializer = new XMLSerializer();
  const source = serializer.serializeToString(svgEl);
  const blob = new Blob([source], { type: 'image/svg+xml;charset=utf-8' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `Estabilidad_Agronomica_${props.projectId}_${variableActual.value}.svg`;
  a.click();
  URL.revokeObjectURL(url);
};

const exportarCSV = () => {
  let csv = 'Variedad,EsTestigo,Media,Bi_Eberhart,S2di_Eberhart,Diagnostico,Pi_LinBinns,Ranking\n';
  const erMap = {};
  eberhartRussellData.value.forEach(row => { erMap[row.variedad] = row; });
  
  linBinnsData.value.forEach(lb => {
    const er = erMap[lb.variedad] || {};
    csv += `"${lb.variedad}",${lb.es_testigo ? 'SI' : 'NO'},${lb.media},${er.bi || ''},${er.s2di || ''},"${er.adaptabilidad_label || ''}",${lb.pi_index},${lb.ranking}\n`;
  });

  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `Indices_Estabilidad_${props.projectId}_${variableActual.value}.csv`;
  a.click();
  URL.revokeObjectURL(url);
};
</script>
