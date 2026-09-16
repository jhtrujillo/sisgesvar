<template>
  <div class="space-y-5" @click="handleOuterClick">
    <!-- Top Interactive Toolbar Card -->
    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 shadow-xs space-y-4" @click.stop>
      <!-- Header Row -->
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pb-3 border-b border-slate-200/80">
        <div>
          <div class="flex items-center gap-2">
            <h4 class="text-xs font-black uppercase tracking-wider text-slate-900 flex items-center gap-1.5">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
              </svg>
              Evaluación Interactiva de Isoproductividad
            </h4>
            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
              IDEAR
            </span>
          </div>
          <p class="text-[11px] text-slate-500 font-medium mt-0.5">
            Usa la rueda del ratón para zoom, arrastra para moverte, o selecciona variedades para comparar rendimiento.
          </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
          <!-- Selector de Testigo de Referencia -->
          <div class="flex items-center space-x-1.5 bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-2xs">
            <span class="text-[10px] font-extrabold uppercase text-slate-500 flex items-center gap-1">⭐ Testigo Ref:</span>
            <select
              v-model="selectedTestigoRef"
              class="text-xs font-black text-slate-800 bg-transparent border-none focus:outline-none cursor-pointer"
            >
              <option v-for="v in sortedVariedades" :key="'opt-' + v.variedad" :value="v.variedad">
                {{ v.variedad }} (TSH: {{ v.tsh }})
              </option>
            </select>
          </div>

          <!-- Botones de Exportar -->
          <div class="flex items-center gap-1 bg-white p-1 rounded-xl border border-slate-200 shadow-2xs">
            <button
              @click="exportChartImage('png')"
              class="px-2 py-1 text-[10px] font-bold text-slate-700 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition-all flex items-center gap-1 cursor-pointer"
              title="Descargar PNG"
            >
              🖼️ PNG
            </button>
            <button
              @click="exportChartImage('jpeg')"
              class="px-2 py-1 text-[10px] font-bold text-slate-700 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-all flex items-center gap-1 cursor-pointer"
              title="Descargar JPG"
            >
              📷 JPG
            </button>
            <button
              @click="exportChartImage('svg')"
              class="px-2 py-1 text-[10px] font-bold text-slate-700 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-all flex items-center gap-1 cursor-pointer"
              title="Descargar SVG"
            >
              📐 SVG
            </button>
            <button
              @click="exportCsv"
              class="px-2 py-1 text-[10px] font-bold text-slate-700 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-all flex items-center gap-1 cursor-pointer"
              title="Descargar CSV"
            >
              📊 CSV
            </button>
          </div>

          <!-- Botón Pantalla Completa -->
          <button
            @click="toggleFullscreen"
            class="px-3 py-1.5 text-xs font-black text-white bg-slate-900 hover:bg-slate-800 active:scale-95 rounded-xl shadow-xs flex items-center gap-1.5 transition-all cursor-pointer"
            title="Abrir gráfico en pantalla completa"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
            </svg>
            Pantalla Completa
          </button>
        </div>
      </div>

      <!-- Controls Sections Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
        <!-- Col 1: Filtros y Búsqueda -->
        <div class="bg-white p-2.5 rounded-xl border border-slate-200/90 shadow-2xs space-y-2">
          <div class="text-[9px] font-black uppercase tracking-wider text-slate-400 flex items-center justify-between">
            <span>Filtros y Búsqueda</span>
            <span class="text-slate-500 font-bold">Variedades: {{ filteredVariedades.length }}/{{ variedadesList.length }}</span>
          </div>

          <div class="space-y-1.5">
            <!-- Filter Pills -->
            <div class="flex items-center gap-1 bg-slate-100 p-0.5 rounded-lg border border-slate-200 text-[10px]">
              <button
                @click="filterType = 'all'"
                class="flex-1 py-1 rounded-md font-bold transition-all text-center cursor-pointer"
                :class="filterType === 'all' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
              >
                Todas ({{ variedadesList.length }})
              </button>
              <button
                @click="filterType = 'candidates'"
                class="flex-1 py-1 rounded-md font-bold transition-all text-center cursor-pointer"
                :class="filterType === 'candidates' ? 'bg-emerald-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
              >
                Candidatas ({{ candidateCount }})
              </button>
              <button
                @click="filterType = 'testigos'"
                class="flex-1 py-1 rounded-md font-bold transition-all text-center cursor-pointer"
                :class="filterType === 'testigos' ? 'bg-amber-500 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
              >
                Testigos ({{ testigoCount }})
              </button>
            </div>

            <!-- Filtro Rápido Select & Search input -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
              <select
                v-model="filterType"
                class="w-full text-[11px] font-bold text-slate-800 bg-slate-50 border border-slate-200 rounded-lg px-2 py-1 focus:outline-none focus:ring-1 focus:ring-emerald-500 cursor-pointer"
              >
                <option value="all">Ver Todas ({{ variedadesList.length }})</option>
                <option value="candidates">Candidatas ({{ candidateCount }})</option>
                <option value="testigos">Testigos ({{ testigoCount }})</option>
                <option value="top_quadrant">Cuadrante Superior ({{ topQuadrantCount }})</option>
                <option value="top_testigo">Superan Testigo Ref {{ selectedTestigoRef }} ({{ topTestigoCount }})</option>
                <option value="top_all_3_vs_ref">Superan Testigo Ref en TCH, %Sac y TSH ({{ topAll3VsRefCount }})</option>
                <option value="top_5">Top 5 en Azúcar (TSH)</option>
                <option value="top_5_tch">Top 5 en Campo (TCH)</option>
                <option value="top_5_sac">Top 5 en Rendimiento (%Sacarosa)</option>
              </select>

              <input
                v-model="searchQuery"
                type="text"
                placeholder="🔍 Buscar..."
                class="w-full px-2.5 py-1 text-[11px] rounded-lg bg-slate-50 border border-slate-200 focus:outline-none focus:border-emerald-500"
              />
            </div>
          </div>
        </div>

        <!-- Col 2: Apariencia del Gráfico -->
        <div class="bg-white p-2.5 rounded-xl border border-slate-200/90 shadow-2xs space-y-2">
          <div class="text-[9px] font-black uppercase tracking-wider text-slate-400">
            Apariencia de Puntos y Etiquetas
          </div>

          <div class="grid grid-cols-2 gap-2 text-[10px]">
            <div class="space-y-1">
              <div class="flex justify-between font-bold text-slate-600">
                <span>🔵 Tamaño:</span>
                <span class="font-mono text-emerald-700">{{ Math.round(pointRadiusScale * 100) }}%</span>
              </div>
              <input
                type="range"
                min="0.5"
                max="2.2"
                step="0.1"
                v-model.number="pointRadiusScale"
                class="w-full accent-emerald-600 cursor-pointer h-1 bg-slate-200 rounded-lg"
              />
            </div>

            <div class="space-y-1">
              <div class="flex justify-between font-bold text-slate-600">
                <span>⭕ Borde:</span>
                <span class="font-mono text-emerald-700">{{ pointStrokeWidth }}px</span>
              </div>
              <input
                type="range"
                min="0.5"
                max="6"
                step="0.5"
                v-model.number="pointStrokeWidth"
                class="w-full accent-emerald-600 cursor-pointer h-1 bg-slate-200 rounded-lg"
              />
            </div>
          </div>

          <div class="flex items-center justify-between pt-1 border-t border-slate-100 text-[10px]">
            <button
              @click="showVarietyLabels = !showVarietyLabels"
              class="px-2 py-0.5 rounded-md font-bold uppercase transition-all cursor-pointer border text-[9px]"
              :class="showVarietyLabels ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-400 border-slate-200 line-through'"
            >
              {{ showVarietyLabels ? '👁️ Nombres Visibles' : '🙈 Nombres Ocultos' }}
            </button>

            <div v-if="showVarietyLabels" class="flex items-center space-x-1">
              <span class="font-bold text-slate-500">Texto:</span>
              <input
                type="range"
                min="6"
                max="18"
                step="1"
                v-model.number="varietyLabelSize"
                class="w-14 accent-emerald-600 cursor-pointer h-1 bg-slate-200 rounded-lg"
              />
              <span class="font-mono font-bold text-emerald-700 text-[9px] w-5 text-right">{{ varietyLabelSize }}px</span>
            </div>
          </div>
        </div>

        <!-- Col 3: Navegación y Zoom -->
        <div class="bg-white p-2.5 rounded-xl border border-slate-200/90 shadow-2xs space-y-2 flex flex-col justify-between">
          <div class="text-[9px] font-black uppercase tracking-wider text-slate-400 flex items-center justify-between">
            <span>Navegación del Lienzo</span>
            <span class="font-mono font-bold text-emerald-700">Zoom: {{ Math.round(zoomLevel * 100) }}%</span>
          </div>

          <div class="flex items-center gap-1.5">
            <button
              @click="zoomIn"
              class="flex-1 py-1 font-black text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-lg text-xs cursor-pointer text-center transition-all border border-slate-200"
              title="Acercar (+)"
            >
              + Acercar
            </button>
            <button
              @click="zoomOut"
              class="flex-1 py-1 font-black text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-lg text-xs cursor-pointer text-center transition-all border border-slate-200"
              title="Alejar (-)"
            >
              - Alejar
            </button>
            <button
              @click="resetZoom"
              class="px-2.5 py-1 font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg text-[10px] cursor-pointer transition-all border border-slate-200"
              title="Restablecer vista"
            >
              ↺ Reset
            </button>
          </div>

          <div class="text-[10px] text-slate-400 font-medium flex items-center justify-between">
            <span>💡 Rueda ratón = Zoom</span>
            <span>Arrastrar = Mover</span>
          </div>
        </div>
      </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-3 gap-3" @click.stop>
      <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm text-center">
        <div class="text-[10px] font-extrabold uppercase text-slate-400">TCH Promedio</div>
        <div class="text-lg font-black text-slate-800 mt-0.5">{{ promedios.tch_medio }} <span class="text-[10px] font-normal text-slate-500">t/ha</span></div>
      </div>
      <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm text-center">
        <div class="text-[10px] font-extrabold uppercase text-slate-400">% Sacarosa Medio</div>
        <div class="text-lg font-black text-emerald-700 mt-0.5">{{ promedios.sacarosa_media }}%</div>
      </div>
      <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm text-center">
        <div class="text-[10px] font-extrabold uppercase text-slate-400">TSH Promedio</div>
        <div class="text-lg font-black text-slate-700 mt-0.5">{{ promedios.tsh_medio }} <span class="text-[10px] font-normal text-slate-500">t/ha</span></div>
      </div>
    </div>

    <!-- Interactive SVG Chart Container -->
    <div
      class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden"
      @click.stop="handleChartContainerClick"
    >
      <div class="flex items-center justify-between mb-2">
        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
          Curvas de Isoproductividad (TCH vs % Sacarosa)
          <span v-if="selectedVariety" class="text-emerald-700 font-extrabold ml-2">
            • Seleccionada: {{ selectedVariety.variedad }}
          </span>
        </span>
        <div class="flex items-center space-x-4 text-[10px] font-semibold text-slate-600">
          <span class="flex items-center gap-1">
            <span class="w-3 h-3 rounded-full bg-amber-500 border border-amber-600 inline-block"></span> Testigo
          </span>
          <span class="flex items-center gap-1">
            <span class="w-3 h-3 rounded-full bg-emerald-600 border border-emerald-700 inline-block"></span> Candidata
          </span>
          <span class="flex items-center gap-1">
            <span class="w-4 h-0.5 bg-slate-400 border border-dashed border-slate-500 inline-block"></span> Curvas TSH
          </span>
        </div>
      </div>

      <!-- Interactive SVG Chart with Mouse Wheel & Pan Drag -->
      <div
        class="relative w-full aspect-[16/9] max-h-[380px] cursor-grab active:cursor-grabbing"
        @wheel.prevent="handleWheelZoom"
        @mousedown="startPan"
        @mousemove="doPan"
        @mouseup="endPan"
        @mouseleave="endPan"
      >
        <svg
          ref="chartSvgRef"
          class="w-full h-full overflow-visible select-none transition-transform duration-75"
          viewBox="0 0 830 430"
          preserveAspectRatio="xMidYMid meet"
        >
          <!-- Background Rect for catching clicks outside points -->
          <rect
            x="0"
            y="0"
            width="830"
            height="430"
            fill="transparent"
            @click="deselectAll"
          />

          <!-- SVG ClipPath to keep curves strictly inside the plot area -->
          <defs>
            <clipPath id="chart-plot-clip">
              <rect x="60" y="20" width="660" height="340" />
            </clipPath>
          </defs>

          <!-- Main Axis Lines (Eje X, Eje Y, 3er Eje Z) -->
          <!-- 1. Eje Y Line (Left) -->
          <line x1="60" y1="20" x2="60" y2="360" stroke="#334155" stroke-width="1" />
          <!-- 2. Eje X Line (Bottom) -->
          <line x1="60" y1="360" x2="720" y2="360" stroke="#334155" stroke-width="1" />
          <!-- 3. 3er EJE Z Line (Right - TSH) -->
          <line x1="720" y1="20" x2="720" y2="360" stroke="#334155" stroke-width="1" />

          <!-- Axis Ticks & Tick Labels -->
          <!-- 1. Eje Y Ticks (% Sacarosa) -->
          <g class="axis-y-ticks">
            <line
              v-for="tick in yTicks"
              :key="'tick-y-' + tick"
              x1="53"
              :y1="toSvgY(tick)"
              x2="60"
              :y2="toSvgY(tick)"
              stroke="#334155"
              stroke-width="0.75"
            />
            <text
              v-for="tick in yTicks"
              :key="'label-y-' + tick"
              x="50"
              :y="toSvgY(tick) + 4"
              fill="#475569"
              font-size="10"
              font-weight="extrabold"
              text-anchor="end"
            >
              {{ tick.toFixed(1) }}%
            </text>
          </g>

          <!-- 2. Eje X Ticks (TCH) -->
          <g class="axis-x-ticks">
            <line
              v-for="tick in xTicks"
              :key="'tick-x-' + tick"
              :x1="toSvgX(tick)"
              y1="360"
              :x2="toSvgX(tick)"
              y2="367"
              stroke="#334155"
              stroke-width="0.75"
            />
            <text
              v-for="tick in xTicks"
              :key="'label-x-' + tick"
              :x="toSvgX(tick)"
              y="382"
              fill="#475569"
              font-size="10"
              font-weight="extrabold"
              text-anchor="middle"
            >
              {{ tick }}
            </text>
          </g>

          <!-- 3. 3er EJE Z Ticks & Values (TSH Axis) -->
          <g class="axis-z-tsh-ticks">
            <template v-for="tick in tshAxisTicks" :key="'tsh-tick-' + tick.tsh">
              <!-- Right Axis Ticks & Labels -->
              <line
                v-if="tick.type === 'right'"
                x1="720"
                :y1="tick.y"
                x2="727"
                :y2="tick.y"
                stroke="#334155"
                stroke-width="0.75"
              />
              <text
                v-if="tick.type === 'right'"
                x="732"
                :y="tick.y + 4"
                fill="#334155"
                font-size="10"
                font-weight="extrabold"
              >
                {{ tick.tsh }} t/ha
              </text>

              <!-- Top Axis Boundary Ticks & Labels -->
              <line
                v-if="tick.type === 'top'"
                :x1="tick.x"
                y1="20"
                :x2="tick.x"
                y2="13"
                stroke="#334155"
                stroke-width="0.75"
              />
              <text
                v-if="tick.type === 'top'"
                :x="tick.x"
                y="9"
                fill="#334155"
                font-size="9"
                font-weight="extrabold"
                text-anchor="middle"
              >
                {{ tick.tsh }} t/ha
              </text>
            </template>
          </g>

          <!-- Axis Titles -->
          <!-- 1. Eje X Title (TCH) -->
          <text x="390" y="412" fill="#0f172a" font-size="11" font-weight="900" text-anchor="middle">
            TCH (Toneladas de Caña por Hectárea)
          </text>

          <!-- 2. Eje Y Title (% SAC) -->
          <text
            x="-190"
            y="16"
            fill="#0f172a"
            font-size="11"
            font-weight="900"
            text-anchor="middle"
            transform="rotate(-90)"
          >
            % Rendimiento de Sacarosa
          </text>

          <!-- 3. 3er EJE Z Title (TSH) -->
          <text
            x="795"
            y="190"
            fill="#0f172a"
            font-size="11"
            font-weight="900"
            text-anchor="middle"
            transform="rotate(90, 795, 190)"
          >
            TSH (Toneladas de Azúcar por Hectárea)
          </text>

          <!-- Internal Plot Viewport Group -->
          <g clip-path="url(#chart-plot-clip)">
            <g :style="plotGroupStyle">
              <!-- Grid Lines (Y-axis Sacarosa - Extended for smooth zoom out) -->
              <g class="grid-y">
                <line
                  v-for="tick in extendedYTicks"
                  :key="'grid-y-' + tick"
                  x1="-600"
                  :y1="toSvgY(tick)"
                  x2="1400"
                  :y2="toSvgY(tick)"
                  stroke="#e2e8f0"
                  stroke-dasharray="3 3"
                  stroke-width="0.5"
                />
              </g>

              <!-- Grid Lines (X-axis TCH - Extended for smooth zoom out) -->
              <g class="grid-x">
                <line
                  v-for="tick in extendedXTicks"
                  :key="'grid-x-' + tick"
                  :x1="toSvgX(tick)"
                  y1="-600"
                  :x2="toSvgX(tick)"
                  y2="1000"
                  stroke="#e2e8f0"
                  stroke-dasharray="3 3"
                  stroke-width="0.5"
                />
              </g>

              <!-- Mean TCH line (Extended) -->
              <line
                :x1="toSvgX(promedios.tch_medio)"
                y1="-600"
                :x2="toSvgX(promedios.tch_medio)"
                y2="1000"
                stroke="#94a3b8"
                stroke-width="0.75"
                stroke-dasharray="4 4"
              />
              <text
                :x="toSvgX(promedios.tch_medio) + 4"
                y="32"
                fill="#64748b"
                font-size="9"
                font-weight="bold"
              >
                TCH Medio ({{ promedios.tch_medio }})
              </text>

              <!-- Mean Sacarosa line (Extended) -->
              <line
                x1="-600"
                :y1="toSvgY(promedios.sacarosa_media)"
                x2="1400"
                :y2="toSvgY(promedios.sacarosa_media)"
                stroke="#94a3b8"
                stroke-width="0.75"
                stroke-dasharray="4 4"
              />
              <text
                x="68"
                :y="toSvgY(promedios.sacarosa_media) - 6"
                fill="#64748b"
                font-size="9"
                font-weight="bold"
              >
                %Sac Medio ({{ promedios.sacarosa_media }}%)
              </text>

              <!-- Highly Visible Isocurves (Extended range for zoom out) -->
              <g>
                <path
                  v-for="tshVal in [6, 8, 10, 12, 14, 16, 18, 20, 22, 24, 26, 28, 30]"
                  :key="'isopath-' + tshVal"
                  :d="getIsocurvePath(tshVal)"
                  fill="none"
                  stroke="#64748b"
                  stroke-width="1"
                  stroke-dasharray="6 3"
                  class="drop-shadow-xs opacity-85"
                />
              </g>

              <!-- Variety Data Points -->
              <g v-for="v in filteredVariedades" :key="'point-' + v.variedad">
                <!-- Pulsating Target Ring for Selected Variety -->
                <circle
                  v-if="selectedVariety?.variedad === v.variedad"
                  :cx="toSvgX(v.tch)"
                  :cy="toSvgY(v.sacarosa)"
                  :r="16 * pointRadiusScale"
                  fill="none"
                  stroke="#10b981"
                  :stroke-width="pointStrokeWidth + 1.5"
                  class="animate-ping opacity-75"
                />

                <!-- Point Marker -->
                <circle
                  :cx="toSvgX(v.tch)"
                  :cy="toSvgY(v.sacarosa)"
                  :r="(v.variedad === selectedTestigoRef ? 9 : v.es_testigo ? 7.5 : 6.5) * pointRadiusScale"
                  :fill="v.variedad === selectedTestigoRef ? '#d97706' : v.es_testigo ? '#f59e0b' : '#059669'"
                  :stroke="v.variedad === selectedVariety?.variedad ? '#0284c7' : v.es_testigo ? '#92400e' : '#064e3b'"
                  :stroke-width="selectedVariety?.variedad === v.variedad ? Math.max(pointStrokeWidth + 1.5, 3) : pointStrokeWidth"
                  class="cursor-pointer transition-all hover:scale-130"
                  @click.stop="selectVariety(v)"
                  @mouseenter="hoveredVariety = v"
                  @mouseleave="hoveredVariety = null"
                />

                <!-- Variety Label -->
                <text
                  v-if="showVarietyLabels"
                  :x="toSvgX(v.tch) + ((v.es_testigo ? 10 : 8) * Math.sqrt(pointRadiusScale))"
                  :y="toSvgY(v.sacarosa) - (8 * Math.sqrt(pointRadiusScale))"
                  :fill="v.variedad === selectedTestigoRef ? '#92400e' : v.es_testigo ? '#b45309' : '#047857'"
                  :font-size="varietyLabelSize * (v.variedad === selectedVariety?.variedad ? 1.15 : 1)"
                  :font-weight="v.variedad === selectedVariety?.variedad || v.es_testigo ? 'black' : 'extrabold'"
                  class="pointer-events-none drop-shadow-sm select-none"
                >
                  {{ v.variedad }} {{ v.variedad === selectedTestigoRef ? '⭐' : '' }}
                </text>
              </g>
            </g>
          </g>
        </svg>

        <!-- Hover / Selection Tooltip Card with Close (✕) Button -->
        <Transition name="fade">
          <div
            v-if="displayVariety"
            class="absolute top-4 right-4 bg-slate-900/95 backdrop-blur-md text-white p-4 rounded-2xl shadow-2xl border border-slate-700 text-xs space-y-2 min-w-[220px] z-20"
            @click.stop
          >
            <div class="flex items-center justify-between border-b border-slate-700 pb-2">
              <span class="font-black text-emerald-400 text-sm">{{ displayVariety.variedad }}</span>
              <div class="flex items-center space-x-1.5">
                <span
                  class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase"
                  :class="displayVariety.es_testigo ? 'bg-amber-500/20 text-amber-300 border border-amber-400/30' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-400/30'"
                >
                  {{ displayVariety.variedad === selectedTestigoRef ? '⭐ Testigo Ref' : displayVariety.es_testigo ? 'Testigo' : '🌱 Candidata' }}
                </span>
                <button
                  @click.stop="deselectAll"
                  class="p-0.5 text-slate-400 hover:text-white rounded-full hover:bg-slate-800 cursor-pointer font-bold"
                  title="Cerrar Ficha de Variedad"
                >
                  ✕
                </button>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-2 text-[11px] pt-1">
              <div><span class="text-slate-400">TCH:</span> <strong class="text-white">{{ displayVariety.tch }} t/ha</strong></div>
              <div><span class="text-slate-400">%Sacarosa:</span> <strong class="text-white">{{ displayVariety.sacarosa }}%</strong></div>
              <div><span class="text-slate-400">TSH:</span> <strong class="text-slate-200">{{ displayVariety.tsh }} t/ha</strong></div>
              <div><span class="text-slate-400">IDEAR vs {{ selectedTestigoRef }}:</span> <strong class="text-emerald-300">{{ displayVariety.calculatedIdear }}%</strong></div>
            </div>
          </div>
        </Transition>
      </div>
    </div>

    <!-- IDEAR Ranking Table -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3" @click.stop>
      <div class="flex items-center justify-between">
        <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 flex items-center gap-1.5">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
          </svg>
          Tabla de Ranking IDEAR y Desempeño Productivo
        </h4>
        <span class="text-[10px] text-slate-500 font-bold">Calculado dinámicamente frente a {{ selectedTestigoRef }}</span>
      </div>

      <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm max-h-[320px] overflow-y-auto">
        <table class="w-full text-left text-xs border-collapse">
          <thead class="bg-slate-100/90 sticky top-0 backdrop-blur-md text-slate-700 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-200 z-10">
            <tr>
              <th class="px-4 py-2.5">#</th>
              <th class="px-4 py-2.5">Variedad</th>
              <th class="px-4 py-2.5 text-center">Tipo</th>
              <th class="px-4 py-2.5 text-right">TCH (t/ha)</th>
              <th class="px-4 py-2.5 text-right">% Sacarosa</th>
              <th class="px-4 py-2.5 text-right">TSH (t/ha)</th>
              <th class="px-4 py-2.5 text-right">IDEAR TSH (%)</th>
              <th class="px-4 py-2.5 text-center">Desempeño vs {{ selectedTestigoRef }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
            <tr
              v-for="(v, index) in sortedVariedades"
              :key="'row-' + v.variedad"
              @click.stop="selectVariety(v)"
              class="hover:bg-slate-50 transition-colors cursor-pointer"
              :class="{
                'bg-emerald-50/70 border-l-4 border-l-emerald-600': selectedVariety?.variedad === v.variedad,
                'bg-amber-50/40 font-bold': v.variedad === selectedTestigoRef
              }"
            >
              <td class="px-4 py-2.5 text-slate-400 font-mono">{{ index + 1 }}</td>
              <td class="px-4 py-2.5 font-bold text-slate-900 flex items-center gap-1.5">
                {{ v.variedad }}
                <span v-if="v.variedad === selectedTestigoRef" class="text-amber-500 text-xs">⭐</span>
              </td>
              <td class="px-4 py-2.5 text-center">
                <span
                  class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase border"
                  :class="v.es_testigo ? 'bg-amber-100 text-amber-900 border-amber-200' : 'bg-emerald-100 text-emerald-900 border-emerald-200'"
                >
                  {{ v.es_testigo ? 'Testigo' : 'Candidata' }}
                </span>
              </td>
              <td class="px-4 py-2.5 text-right font-mono">{{ v.tch.toFixed(1) }}</td>
              <td class="px-4 py-2.5 text-right font-mono">{{ v.sacarosa.toFixed(1) }}%</td>
              <td class="px-4 py-2.5 text-right font-mono font-bold text-slate-800">{{ v.tsh.toFixed(2) }}</td>
              <td class="px-4 py-2.5 text-right font-mono font-extrabold text-emerald-700">{{ v.calculatedIdear.toFixed(1) }}%</td>
              <td class="px-4 py-2.5 text-center">
                <span
                  class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border inline-flex items-center gap-1"
                  :class="{
                    'bg-emerald-100 text-emerald-800 border-emerald-200': v.calculatedIdear >= 105,
                    'bg-slate-100 text-slate-700 border-slate-200': v.calculatedIdear >= 98 && v.calculatedIdear < 105,
                    'bg-rose-100 text-rose-800 border-rose-200': v.calculatedIdear < 98
                  }"
                >
                  <span v-if="v.calculatedIdear >= 105">⬆️ +{{ (v.calculatedIdear - 100).toFixed(1) }}%</span>
                  <span v-else-if="v.calculatedIdear >= 98">⏩ Similar</span>
                  <span v-else>⬇️ {{ (v.calculatedIdear - 100).toFixed(1) }}%</span>
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Fullscreen Modal Overlay (Light Theme) -->
    <Teleport to="body">
      <Transition name="fade">
        <div
          v-if="isFullscreen"
          class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-md p-4 sm:p-6 flex flex-col justify-between overflow-hidden text-slate-900 select-none"
          @click="handleOuterClick"
        >
          <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 w-full h-full p-6 flex flex-col justify-between overflow-hidden relative">
            <!-- Fullscreen Header & Controls Container -->
            <div class="space-y-3 pb-3 border-b border-slate-200/90" @click.stop>
              <!-- Row 1: Title & Main Action Buttons -->
              <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                  <div class="p-1.5 bg-emerald-100 rounded-xl border border-emerald-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 flex items-center gap-2">
                      Evaluación de Isoproductividad
                      <span class="text-emerald-700 font-mono text-[10px] font-extrabold bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                        Pantalla Completa
                      </span>
                    </h3>
                  </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                  <!-- Selector de Testigo Ref -->
                  <div class="flex items-center space-x-1.5 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200 text-xs shadow-2xs">
                    <span class="text-[10px] font-extrabold uppercase text-slate-500">⭐ Testigo Ref:</span>
                    <select
                      v-model="selectedTestigoRef"
                      class="text-xs font-black text-slate-800 bg-transparent border-none focus:outline-none cursor-pointer"
                    >
                      <option v-for="v in sortedVariedades" :key="'fs-opt-' + v.variedad" :value="v.variedad">
                        {{ v.variedad }} (TSH: {{ v.tsh }})
                      </option>
                    </select>
                  </div>

                  <!-- Export Buttons -->
                  <div class="flex items-center gap-1 bg-slate-50 p-1 rounded-xl border border-slate-200 text-xs shadow-2xs">
                    <button @click="exportChartImage('png')" class="px-2 py-1 text-[10px] font-bold text-slate-700 hover:text-emerald-700 hover:bg-white rounded-lg transition-all cursor-pointer">🖼️ PNG</button>
                    <button @click="exportChartImage('jpeg')" class="px-2 py-1 text-[10px] font-bold text-slate-700 hover:text-slate-900 hover:bg-white rounded-lg transition-all cursor-pointer">📷 JPG</button>
                    <button @click="exportChartImage('svg')" class="px-2 py-1 text-[10px] font-bold text-slate-700 hover:text-slate-900 hover:bg-white rounded-lg transition-all cursor-pointer">📐 SVG</button>
                    <button @click="exportCsv" class="px-2 py-1 text-[10px] font-bold text-slate-700 hover:text-slate-900 hover:bg-white rounded-lg transition-all cursor-pointer">📊 CSV</button>
                  </div>

                  <!-- Exit Button -->
                  <button
                    @click="isFullscreen = false"
                    class="px-3.5 py-1.5 text-xs font-black bg-slate-900 hover:bg-slate-800 active:scale-95 text-white rounded-xl shadow-xs transition-all flex items-center gap-1.5 cursor-pointer"
                  >
                    ✕ Salir (ESC)
                  </button>
                </div>
              </div>

              <!-- Row 2: Categorized Controls Grid -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-2.5 text-xs">
                <!-- Col 1: Filter Pills & Filtro Rápido -->
                <div class="flex items-center gap-1.5 bg-slate-50 p-1.5 rounded-xl border border-slate-200/90 shadow-2xs">
                  <div class="flex items-center gap-1 bg-white p-0.5 rounded-lg border border-slate-200 text-[10px]">
                    <button @click="filterType = 'all'" class="px-2 py-0.5 rounded-md font-bold transition-all" :class="filterType === 'all' ? 'bg-slate-900 text-white' : 'text-slate-600'">Todas</button>
                    <button @click="filterType = 'candidates'" class="px-2 py-0.5 rounded-md font-bold transition-all" :class="filterType === 'candidates' ? 'bg-emerald-600 text-white' : 'text-slate-600'">Candidatas</button>
                    <button @click="filterType = 'testigos'" class="px-2 py-0.5 rounded-md font-bold transition-all" :class="filterType === 'testigos' ? 'bg-amber-500 text-white' : 'text-slate-600'">Testigos</button>
                  </div>
                  <select v-model="filterType" class="flex-1 text-[11px] font-bold text-slate-800 bg-white border border-slate-200 rounded-lg px-2 py-1 focus:outline-none cursor-pointer">
                    <option value="all">Ver Todas ({{ variedadesList.length }})</option>
                    <option value="candidates">Candidatas ({{ candidateCount }})</option>
                    <option value="testigos">Testigos ({{ testigoCount }})</option>
                    <option value="top_quadrant">Cuadrante Superior ({{ topQuadrantCount }})</option>
                    <option value="top_testigo">Superan Testigo Ref {{ selectedTestigoRef }} ({{ topTestigoCount }})</option>
                    <option value="top_all_3_vs_ref">Superan Testigo Ref en TCH, %Sac y TSH ({{ topAll3VsRefCount }})</option>
                    <option value="top_5">Top 5 en Azúcar (TSH)</option>
                    <option value="top_5_tch">Top 5 en Campo (TCH)</option>
                    <option value="top_5_sac">Top 5 en Rendimiento (%Sacarosa)</option>
                  </select>
                </div>

                <!-- Col 2: Appearance Sliders & Labels -->
                <div class="flex items-center gap-3 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200/90 shadow-2xs text-[10px]">
                  <div class="flex items-center space-x-1" title="Tamaño puntos">
                    <span class="font-extrabold text-slate-500">🔵</span>
                    <input type="range" min="0.5" max="2.2" step="0.1" v-model.number="pointRadiusScale" class="w-14 accent-emerald-600 h-1 bg-slate-200 rounded cursor-pointer" />
                  </div>
                  <div class="flex items-center space-x-1" title="Grosor borde">
                    <span class="font-extrabold text-slate-500">⭕</span>
                    <input type="range" min="0.5" max="6" step="0.5" v-model.number="pointStrokeWidth" class="w-14 accent-emerald-600 h-1 bg-slate-200 rounded cursor-pointer" />
                  </div>
                  <button @click="showVarietyLabels = !showVarietyLabels" class="px-2 py-0.5 rounded-md font-bold uppercase border text-[9px] cursor-pointer" :class="showVarietyLabels ? 'bg-emerald-50 text-emerald-700 border-emerald-300' : 'bg-white text-slate-400 border-slate-200 line-through'">
                    Nombres
                  </button>
                  <input v-if="showVarietyLabels" type="range" min="6" max="18" step="1" v-model.number="varietyLabelSize" class="w-12 accent-emerald-600 h-1 bg-slate-200 rounded cursor-pointer" />
                </div>

                <!-- Col 3: Zoom Controls -->
                <div class="flex items-center justify-between bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200/90 shadow-2xs text-xs">
                  <span class="text-[10px] font-extrabold uppercase text-slate-400">Zoom:</span>
                  <div class="flex items-center gap-1.5">
                    <button @click="zoomIn" class="px-2.5 py-0.5 font-bold hover:bg-slate-200 rounded-lg bg-white border border-slate-200 text-slate-800 cursor-pointer">+</button>
                    <span class="text-xs font-mono font-bold text-emerald-700 px-1">{{ Math.round(zoomLevel * 100) }}%</span>
                    <button @click="zoomOut" class="px-2.5 py-0.5 font-bold hover:bg-slate-200 rounded-lg bg-white border border-slate-200 text-slate-800 cursor-pointer">-</button>
                    <button @click="resetZoom" class="px-2.5 py-0.5 font-bold text-slate-500 hover:text-slate-900 bg-white border border-slate-200 text-[10px] cursor-pointer">↺ Reset</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Fullscreen Interactive Canvas -->
            <div
              class="flex-1 w-full relative flex items-center justify-center py-4 overflow-hidden cursor-grab active:cursor-grabbing bg-white"
              @wheel.prevent="handleWheelZoom"
              @mousedown="startPan"
              @mousemove="doPan"
              @mouseup="endPan"
              @mouseleave="endPan"
              @click.stop="handleChartContainerClick"
            >
              <svg
                ref="fsChartSvgRef"
                class="w-full h-full max-h-[calc(100vh-140px)] overflow-visible select-none transition-transform duration-75"
                viewBox="0 0 830 430"
                preserveAspectRatio="xMidYMid meet"
              >
                <!-- Background Rect for catching clicks outside points -->
                <rect
                  x="0"
                  y="0"
                  width="830"
                  height="430"
                  fill="transparent"
                  @click="deselectAll"
                />

                <!-- SVG ClipPath to keep curves strictly inside the plot area -->
                <defs>
                  <clipPath id="chart-plot-clip-fs">
                    <rect x="60" y="20" width="660" height="340" />
                  </clipPath>
                </defs>

                <!-- Main Axis Lines (Eje X, Eje Y, 3er Eje Z) -->
                <line x1="60" y1="20" x2="60" y2="360" stroke="#334155" stroke-width="1" />
                <line x1="60" y1="360" x2="720" y2="360" stroke="#334155" stroke-width="1" />
                <line x1="720" y1="20" x2="720" y2="360" stroke="#334155" stroke-width="1" />

                <!-- Axis Ticks & Tick Labels -->
                <!-- 1. Eje Y Ticks (% Sacarosa) -->
                <g class="axis-y-ticks">
                  <line
                    v-for="tick in yTicks"
                    :key="'fs-tick-y-' + tick"
                    x1="53"
                    :y1="toSvgY(tick)"
                    x2="60"
                    :y2="toSvgY(tick)"
                    stroke="#334155"
                    stroke-width="0.75"
                  />
                  <text
                    v-for="tick in yTicks"
                    :key="'fs-label-y-' + tick"
                    x="50"
                    :y="toSvgY(tick) + 4"
                    fill="#475569"
                    font-size="10"
                    font-weight="extrabold"
                    text-anchor="end"
                  >
                    {{ tick.toFixed(1) }}%
                  </text>
                </g>

                <!-- 2. Eje X Ticks (TCH) -->
                <g class="axis-x-ticks">
                  <line
                    v-for="tick in xTicks"
                    :key="'fs-tick-x-' + tick"
                    :x1="toSvgX(tick)"
                    y1="360"
                    :x2="toSvgX(tick)"
                    y2="367"
                    stroke="#334155"
                    stroke-width="0.75"
                  />
                  <text
                    v-for="tick in xTicks"
                    :key="'fs-label-x-' + tick"
                    :x="toSvgX(tick)"
                    y="382"
                    fill="#475569"
                    font-size="10"
                    font-weight="extrabold"
                    text-anchor="middle"
                  >
                    {{ tick }}
                  </text>
                </g>

                <!-- 3. 3er EJE Z Ticks & Values (TSH Axis) -->
                <g class="axis-z-tsh-ticks">
                  <template v-for="tick in tshAxisTicks" :key="'fs-tsh-tick-' + tick.tsh">
                    <line
                      v-if="tick.type === 'right'"
                      x1="720"
                      :y1="tick.y"
                      x2="727"
                      :y2="tick.y"
                      stroke="#334155"
                      stroke-width="0.75"
                    />
                    <text
                      v-if="tick.type === 'right'"
                      x="732"
                      :y="tick.y + 4"
                      fill="#334155"
                      font-size="10"
                      font-weight="extrabold"
                    >
                      {{ tick.tsh }} t/ha
                    </text>

                    <line
                      v-if="tick.type === 'top'"
                      :x1="tick.x"
                      y1="20"
                      :x2="tick.x"
                      y2="13"
                      stroke="#334155"
                      stroke-width="0.75"
                    />
                    <text
                      v-if="tick.type === 'top'"
                      :x="tick.x"
                      y="9"
                      fill="#334155"
                      font-size="9"
                      font-weight="extrabold"
                      text-anchor="middle"
                    >
                      {{ tick.tsh }} t/ha
                    </text>
                  </template>
                </g>

                <!-- Axis Titles -->
                <!-- 1. Eje X Title (TCH) -->
                <text x="390" y="412" fill="#0f172a" font-size="11" font-weight="900" text-anchor="middle">
                  TCH (Toneladas de Caña por Hectárea)
                </text>

                <!-- 2. Eje Y Title (% SAC) -->
                <text
                  x="-190"
                  y="16"
                  fill="#0f172a"
                  font-size="11"
                  font-weight="900"
                  text-anchor="middle"
                  transform="rotate(-90)"
                >
                  % Rendimiento de Sacarosa
                </text>

                <!-- 3. 3er EJE Z Title (TSH) -->
                <text
                  x="795"
                  y="190"
                  fill="#0f172a"
                  font-size="11"
                  font-weight="900"
                  text-anchor="middle"
                  transform="rotate(90, 795, 190)"
                >
                  TSH (Toneladas de Azúcar por Hectárea)
                </text>

                <!-- Internal Plot Viewport Group -->
                <g clip-path="url(#chart-plot-clip-fs)">
                  <g :style="plotGroupStyle">
                    <!-- Grid Lines (Y-axis % Sacarosa - Extended for smooth zoom out) -->
                    <g class="grid-y">
                      <line
                        v-for="tick in extendedYTicks"
                        :key="'fs-grid-y-' + tick"
                        x1="-600"
                        :y1="toSvgY(tick)"
                        x2="1400"
                        :y2="toSvgY(tick)"
                        stroke="#e2e8f0"
                        stroke-dasharray="3 3"
                        stroke-width="0.5"
                      />
                    </g>

                    <!-- Grid Lines (X-axis TCH - Extended for smooth zoom out) -->
                    <g class="grid-x">
                      <line
                        v-for="tick in extendedXTicks"
                        :key="'fs-grid-x-' + tick"
                        :x1="toSvgX(tick)"
                        y1="-600"
                        :x2="toSvgX(tick)"
                        y2="1000"
                        stroke="#e2e8f0"
                        stroke-dasharray="3 3"
                        stroke-width="0.5"
                      />
                    </g>

                    <!-- Mean TCH line (Extended) -->
                    <line
                      :x1="toSvgX(promedios.tch_medio)"
                      y1="-600"
                      :x2="toSvgX(promedios.tch_medio)"
                      y2="1000"
                      stroke="#94a3b8"
                      stroke-width="0.75"
                      stroke-dasharray="4 4"
                    />
                    <text
                      :x="toSvgX(promedios.tch_medio) + 4"
                      y="32"
                      fill="#64748b"
                      font-size="9"
                      font-weight="bold"
                    >
                      TCH Medio ({{ promedios.tch_medio }})
                    </text>

                    <!-- Mean Sacarosa line (Extended) -->
                    <line
                      x1="-600"
                      :y1="toSvgY(promedios.sacarosa_media)"
                      x2="1400"
                      :y2="toSvgY(promedios.sacarosa_media)"
                      stroke="#94a3b8"
                      stroke-width="0.75"
                      stroke-dasharray="4 4"
                    />
                    <text
                      x="68"
                      :y="toSvgY(promedios.sacarosa_media) - 6"
                      fill="#64748b"
                      font-size="9"
                      font-weight="bold"
                    >
                      %Sac Medio ({{ promedios.sacarosa_media }}%)
                    </text>

                    <!-- Highly Visible Isocurves (Extended range for zoom out) -->
                    <g>
                      <path
                        v-for="tshVal in [6, 8, 10, 12, 14, 16, 18, 20, 22, 24, 26, 28, 30]"
                        :key="'fs-isopath-' + tshVal"
                        :d="getIsocurvePath(tshVal)"
                        fill="none"
                        stroke="#64748b"
                        stroke-width="1"
                        stroke-dasharray="6 3"
                        class="drop-shadow-xs opacity-85"
                      />
                    </g>

                    <!-- Variety Data Points -->
                    <g v-for="v in filteredVariedades" :key="'fs-point-' + v.variedad">
                      <!-- Pulsating Target Ring for Selected Variety -->
                      <circle
                        v-if="selectedVariety?.variedad === v.variedad"
                        :cx="toSvgX(v.tch)"
                        :cy="toSvgY(v.sacarosa)"
                        :r="16 * pointRadiusScale"
                        fill="none"
                        stroke="#10b981"
                        :stroke-width="pointStrokeWidth + 1.5"
                        class="animate-ping opacity-75"
                      />

                      <!-- Point Marker -->
                      <circle
                        :cx="toSvgX(v.tch)"
                        :cy="toSvgY(v.sacarosa)"
                        :r="(v.variedad === selectedTestigoRef ? 9 : v.es_testigo ? 7.5 : 6.5) * pointRadiusScale"
                        :fill="v.variedad === selectedTestigoRef ? '#d97706' : v.es_testigo ? '#f59e0b' : '#059669'"
                        :stroke="v.variedad === selectedVariety?.variedad ? '#0284c7' : v.es_testigo ? '#92400e' : '#064e3b'"
                        :stroke-width="selectedVariety?.variedad === v.variedad ? Math.max(pointStrokeWidth + 1.5, 3) : pointStrokeWidth"
                        class="cursor-pointer transition-all hover:scale-130"
                        @click.stop="selectVariety(v)"
                        @mouseenter="hoveredVariety = v"
                        @mouseleave="hoveredVariety = null"
                      />

                      <!-- Variety Label -->
                      <text
                        v-if="showVarietyLabels"
                        :x="toSvgX(v.tch) + ((v.es_testigo ? 10 : 8) * Math.sqrt(pointRadiusScale))"
                        :y="toSvgY(v.sacarosa) - (8 * Math.sqrt(pointRadiusScale))"
                        :fill="v.variedad === selectedTestigoRef ? '#92400e' : v.es_testigo ? '#b45309' : '#047857'"
                        :font-size="varietyLabelSize * (v.variedad === selectedVariety?.variedad ? 1.15 : 1)"
                        :font-weight="v.variedad === selectedVariety?.variedad || v.es_testigo ? 'black' : 'extrabold'"
                        class="pointer-events-none drop-shadow-sm select-none"
                      >
                        {{ v.variedad }} {{ v.variedad === selectedTestigoRef ? '⭐' : '' }}
                      </text>
                    </g>
                  </g>
                </g>
              </svg>

              <!-- Floating Details Card in Fullscreen -->
              <Transition name="fade">
                <div
                  v-if="displayVariety"
                  class="absolute bottom-6 right-6 bg-slate-900/95 backdrop-blur-md text-white p-4 rounded-2xl shadow-2xl border border-slate-700 text-xs space-y-2 min-w-[240px] z-30"
                  @click.stop
                >
                  <div class="flex items-center justify-between pb-1 border-b border-slate-700">
                    <div class="flex items-center gap-1.5">
                      <span class="font-black text-sm text-emerald-400">{{ displayVariety.variedad }}</span>
                      <span
                        class="px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase"
                        :class="displayVariety.es_testigo ? 'bg-amber-500/20 text-amber-300' : 'bg-emerald-500/20 text-emerald-300'"
                      >
                        {{ displayVariety.es_testigo ? 'Testigo' : 'Candidata' }}
                      </span>
                    </div>
                    <button
                      @click.stop="deselectAll"
                      class="p-0.5 text-slate-400 hover:text-white rounded-full hover:bg-slate-800 cursor-pointer font-bold"
                      title="Cerrar Ficha"
                    >
                      ✕
                    </button>
                  </div>

                  <div class="grid grid-cols-2 gap-2 text-[11px] pt-1">
                    <div><span class="text-slate-400">TCH:</span> <strong class="text-white">{{ displayVariety.tch }} t/ha</strong></div>
                    <div><span class="text-slate-400">%Sacarosa:</span> <strong class="text-white">{{ displayVariety.sacarosa }}%</strong></div>
                    <div><span class="text-slate-400">TSH:</span> <strong class="text-slate-200">{{ displayVariety.tsh }} t/ha</strong></div>
                    <div><span class="text-slate-400">IDEAR vs {{ selectedTestigoRef }}:</span> <strong class="text-emerald-300">{{ displayVariety.calculatedIdear }}%</strong></div>
                  </div>
                </div>
              </Transition>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, watch, onMounted, onUnmounted } from "vue";

const props = defineProps<{
  data: {
    variedades: Array<{
      variedad: string;
      tch: number;
      sacarosa: number;
      tsh: number;
      es_testigo: boolean;
      idear_tsh: number;
      idear_tch: number;
      idear_sac: number;
    }>;
    testigo_referencia: string;
    promedios: {
      tch_medio: number;
      sacarosa_media: number;
      tsh_medio: number;
    };
  } | null;
}>();

const selectedTestigoRef = ref<string>("CC 85-92");
const filterType = ref<"all" | "candidates" | "testigos" | "top_quadrant" | "top_testigo" | "top_all_3_vs_ref" | "top_5" | "top_5_tch" | "top_5_sac">("all");
const searchQuery = ref("");
const zoomLevel = ref<number>(1);
const panOffsetX = ref<number>(0);
const panOffsetY = ref<number>(0);
const isFullscreen = ref<boolean>(false);

const pointRadiusScale = ref<number>(0.5);
const pointStrokeWidth = ref<number>(0.5);
const showVarietyLabels = ref<boolean>(false);
const varietyLabelSize = ref<number>(6);

const chartSvgRef = ref<SVGElement | null>(null);
const fsChartSvgRef = ref<SVGElement | null>(null);
const showExportMenu = ref<boolean>(false);

const toggleFullscreen = () => {
  isFullscreen.value = !isFullscreen.value;
};

const handleKeyDown = (e: KeyboardEvent) => {
  if (e.key === "Escape" && isFullscreen.value) {
    isFullscreen.value = false;
  }
};

onMounted(() => {
  window.addEventListener("keydown", handleKeyDown);
});

onUnmounted(() => {
  window.removeEventListener("keydown", handleKeyDown);
  window.removeEventListener("mousemove", doPan);
  window.removeEventListener("mouseup", endPan);
});

const isDragging = ref(false);
const dragStart = ref({ x: 0, y: 0 });

const hoveredVariety = ref<any>(null);
const selectedVariety = ref<any>(null);

const exportChartImage = (format: "png" | "jpeg" | "svg") => {
  const targetSvg = isFullscreen.value && fsChartSvgRef.value ? fsChartSvgRef.value : chartSvgRef.value;
  if (!targetSvg) return;

  const filename = `isoproductividad_${selectedTestigoRef.value}_${Date.now()}`;

  if (format === "svg") {
    const serializer = new XMLSerializer();
    let svgString = serializer.serializeToString(targetSvg);
    if (!svgString.match(/^<svg[^>]+xmlns="http\:\/\/www\.w3\.org\/2000\/svg"/)) {
      svgString = svgString.replace(/^<svg/, '<svg xmlns="http://www.w3.org/2000/svg"');
    }
    const blob = new Blob([svgString], { type: "image/svg+xml;charset=utf-8" });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href = url;
    link.download = `${filename}.svg`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
    return;
  }

  const serializer = new XMLSerializer();
  let svgString = serializer.serializeToString(targetSvg);
  if (!svgString.match(/^<svg[^>]+xmlns="http\:\/\/www\.w3\.org\/2000\/svg"/)) {
    svgString = svgString.replace(/^<svg/, '<svg xmlns="http://www.w3.org/2000/svg"');
  }

  const canvas = document.createElement("canvas");
  const ctx = canvas.getContext("2d");
  if (!ctx) return;

  const scale = 3;
  const width = 830 * scale;
  const height = 430 * scale;
  canvas.width = width;
  canvas.height = height;

  const img = new Image();
  const svgBlob = new Blob([svgString], { type: "image/svg+xml;charset=utf-8" });
  const url = URL.createObjectURL(svgBlob);

  img.onload = () => {
    ctx.fillStyle = "#ffffff";
    ctx.fillRect(0, 0, width, height);

    ctx.drawImage(img, 0, 0, width, height);
    URL.revokeObjectURL(url);

    const mimeType = format === "jpeg" ? "image/jpeg" : "image/png";
    const dataUrl = canvas.toDataURL(mimeType, 0.95);

    const link = document.createElement("a");
    link.href = dataUrl;
    link.download = `${filename}.${format === "jpeg" ? "jpg" : "png"}`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  };

  img.src = url;
};

watch(
  () => props.data,
  (newVal) => {
    if (newVal?.testigo_referencia) {
      selectedTestigoRef.value = newVal.testigo_referencia;
    }
  },
  { immediate: true }
);

const deselectAll = () => {
  selectedVariety.value = null;
  hoveredVariety.value = null;
  showExportMenu.value = false;
};

const handleOuterClick = () => {
  deselectAll();
  showExportMenu.value = false;
};

const handleChartContainerClick = () => {
  // Container click handler
};

const selectVariety = (v: any) => {
  if (selectedVariety.value?.variedad === v.variedad) {
    selectedVariety.value = null;
  } else {
    selectedVariety.value = v;
  }
};

const variedadesList = computed(() => props.data?.variedades || []);

const candidateCount = computed(() => variedadesList.value.filter((v) => !v.es_testigo).length);
const testigoCount = computed(() => variedadesList.value.filter((v) => v.es_testigo).length);

const topQuadrantCount = computed(() => {
  return processedVariedades.value.filter(
    (v) => v.tch >= promedios.value.tch_medio && v.sacarosa >= promedios.value.sacarosa_media
  ).length;
});

const topTestigoCount = computed(() => {
  const refTsh = referenceCheckData.value.tsh;
  return processedVariedades.value.filter((v) => v.tsh > refTsh).length;
});

const topAll3VsRefCount = computed(() => {
  const ref = referenceCheckData.value;
  return processedVariedades.value.filter(
    (v) => v.tch > ref.tch && v.sacarosa > ref.sacarosa && v.tsh > ref.tsh
  ).length;
});

const top5CandidateNames = computed(() => {
  return [...processedVariedades.value]
    .filter((v) => !v.es_testigo)
    .sort((a, b) => b.tsh - a.tsh)
    .slice(0, 5)
    .map((v) => v.variedad);
});

const top5TchCandidateNames = computed(() => {
  return [...processedVariedades.value]
    .filter((v) => !v.es_testigo)
    .sort((a, b) => b.tch - a.tch)
    .slice(0, 5)
    .map((v) => v.variedad);
});

const top5SacCandidateNames = computed(() => {
  return [...processedVariedades.value]
    .filter((v) => !v.es_testigo)
    .sort((a, b) => b.sacarosa - a.sacarosa)
    .slice(0, 5)
    .map((v) => v.variedad);
});

// Reference Check Variety Data for dynamic IDEAR calculations
const referenceCheckData = computed(() => {
  const refVar = variedadesList.value.find((v) => v.variedad === selectedTestigoRef.value);
  return refVar || { tch: 124.5, sacarosa: 13.6, tsh: 16.93 };
});

// Calculate live IDEAR index for every variety against the currently selected reference check
const processedVariedades = computed(() => {
  const refTsh = referenceCheckData.value.tsh || 16.93;
  return variedadesList.value.map((v) => ({
    ...v,
    calculatedIdear: Math.round((v.tsh / Math.max(refTsh, 0.01)) * 1000) / 10
  }));
});

const sortedVariedades = computed(() => {
  return [...processedVariedades.value].sort((a, b) => b.tsh - a.tsh);
});

const filteredVariedades = computed(() => {
  return processedVariedades.value.filter((v) => {
    let matchType = true;
    if (filterType.value === "candidates") {
      matchType = !v.es_testigo;
    } else if (filterType.value === "testigos") {
      matchType = v.es_testigo;
    } else if (filterType.value === "top_quadrant") {
      matchType = !v.es_testigo && v.tch >= promedios.value.tch_medio && v.sacarosa >= promedios.value.sacarosa_media;
    } else if (filterType.value === "top_testigo") {
      matchType = !v.es_testigo && v.tsh > referenceCheckData.value.tsh;
    } else if (filterType.value === "top_all_3_vs_ref") {
      const ref = referenceCheckData.value;
      matchType = !v.es_testigo && v.tch > ref.tch && v.sacarosa > ref.sacarosa && v.tsh > ref.tsh;
    } else if (filterType.value === "top_5") {
      matchType = top5CandidateNames.value.includes(v.variedad);
    } else if (filterType.value === "top_5_tch") {
      matchType = top5TchCandidateNames.value.includes(v.variedad);
    } else if (filterType.value === "top_5_sac") {
      matchType = top5SacCandidateNames.value.includes(v.variedad);
    }

    // Always include the active Testigo Ref point on top filters as visual baseline benchmark
    if (filterType.value.startsWith("top_") && v.variedad === selectedTestigoRef.value) {
      matchType = true;
    }

    let matchSearch = true;
    if (searchQuery.value.trim()) {
      matchSearch = v.variedad.toLowerCase().includes(searchQuery.value.toLowerCase().trim());
    }

    return matchType && matchSearch;
  });
});

const promedios = computed(
  () =>
    props.data?.promedios || {
      tch_medio: 130,
      sacarosa_media: 14.0,
      tsh_medio: 18.2
    }
);

const displayVariety = computed(() => hoveredVariety.value || selectedVariety.value);

// Mouse Wheel Zooming
const handleWheelZoom = (e: WheelEvent) => {
  if (e.deltaY < 0) {
    zoomIn();
  } else {
    zoomOut();
  }
};

const zoomIn = () => {
  if (zoomLevel.value < 2.2) zoomLevel.value = Math.round((zoomLevel.value + 0.15) * 100) / 100;
};

const zoomOut = () => {
  if (zoomLevel.value > 0.6) zoomLevel.value = Math.round((zoomLevel.value - 0.15) * 100) / 100;
};

const resetZoom = () => {
  zoomLevel.value = 1;
  panOffsetX.value = 0;
  panOffsetY.value = 0;
};

// Drag / Pan Controls (GPU-Accelerated 2D Transform, 0 distortion)
const startPan = (e: MouseEvent) => {
  isDragging.value = true;
  dragStart.value = { x: e.clientX, y: e.clientY };
};

const doPan = (e: MouseEvent) => {
  if (!isDragging.value) return;
  const dx = e.clientX - dragStart.value.x;
  const dy = e.clientY - dragStart.value.y;
  dragStart.value = { x: e.clientX, y: e.clientY };

  panOffsetX.value += dx;
  panOffsetY.value += dy;
};

const endPan = () => {
  isDragging.value = false;
};

const plotGroupStyle = computed(() => {
  return {
    transform: `translate(${panOffsetX.value}px, ${panOffsetY.value}px) scale(${zoomLevel.value})`,
    transformOrigin: "390px 190px",
    transition: isDragging.value ? "none" : "transform 0.15s ease-out"
  };
});

// Dynamic SVG Chart Scaling Logic
const baseMinX = computed(() => {
  const vals = variedadesList.value.map((v) => v.tch);
  if (vals.length === 0) return 100;
  return Math.max(80, Math.floor(Math.min(...vals) - 5));
});

const baseMaxX = computed(() => {
  const vals = variedadesList.value.map((v) => v.tch);
  if (vals.length === 0) return 160;
  return Math.min(200, Math.ceil(Math.max(...vals) + 5));
});

const baseMinY = computed(() => {
  const vals = variedadesList.value.map((v) => v.sacarosa);
  if (vals.length === 0) return 11.0;
  return Math.max(9.0, Math.floor((Math.min(...vals) - 0.5) * 10) / 10);
});

const baseMaxY = computed(() => {
  const vals = variedadesList.value.map((v) => v.sacarosa);
  if (vals.length === 0) return 17.0;
  return Math.min(20.0, Math.ceil((Math.max(...vals) + 0.5) * 10) / 10);
});

// Stable fixed bounds (proportions and curves 100% locked)
const minX = computed(() => baseMinX.value);
const maxX = computed(() => baseMaxX.value);
const minY = computed(() => baseMinY.value);
const maxY = computed(() => baseMaxY.value);

const xTicks = computed(() => {
  const ticks: number[] = [];
  const step = Math.ceil((maxX.value - minX.value) / 5 / 5) * 5 || 10;
  for (let x = Math.ceil(minX.value / 10) * 10; x <= maxX.value; x += step) {
    ticks.push(x);
  }
  return ticks;
});

const extendedXTicks = computed(() => {
  const ticks: number[] = [];
  const baseTicks = xTicks.value;
  const step = baseTicks.length >= 2 ? baseTicks[1] - baseTicks[0] : 10;
  const start = Math.floor((minX.value - 120) / step) * step;
  const end = Math.ceil((maxX.value + 120) / step) * step;
  for (let x = start; x <= end; x += step) {
    ticks.push(x);
  }
  return ticks;
});

const yTicks = computed(() => {
  const ticks: number[] = [];
  const step = 1.0;
  for (let y = Math.ceil(minY.value); y <= maxY.value; y += step) {
    ticks.push(y);
  }
  return ticks;
});

const extendedYTicks = computed(() => {
  const ticks: number[] = [];
  const step = 1.0;
  const start = Math.floor(minY.value - 15);
  const end = Math.ceil(maxY.value + 15);
  for (let y = start; y <= end; y += step) {
    ticks.push(y);
  }
  return ticks;
});

const toSvgX = (tch: number) => {
  const norm = (tch - minX.value) / Math.max(maxX.value - minX.value, 1);
  return 60 + norm * 660;
};

const toSvgY = (sac: number) => {
  const norm = (sac - minY.value) / Math.max(maxY.value - minY.value, 0.1);
  return 360 - norm * 340;
};

interface TshAxisTick {
  tsh: number;
  x: number;
  y: number;
  type: 'right' | 'top';
}

const tshAxisTicks = computed<TshAxisTick[]>(() => {
  const ticks: TshAxisTick[] = [];
  const tshValues = [10, 12, 14, 16, 18, 20, 22, 24];

  for (const tsh of tshValues) {
    // Check right axis intersection (TCH = maxX)
    const sacAtMaxX = (tsh * 100) / maxX.value;
    if (sacAtMaxX >= minY.value && sacAtMaxX <= maxY.value) {
      ticks.push({
        tsh,
        x: 720,
        y: toSvgY(sacAtMaxX),
        type: 'right'
      });
      continue;
    }

    // Check top axis intersection (% SAC = maxY)
    const tchAtMaxY = (tsh * 100) / maxY.value;
    if (tchAtMaxY >= minX.value && tchAtMaxY <= maxX.value) {
      ticks.push({
        tsh,
        x: toSvgX(tchAtMaxY),
        y: 20,
        type: 'top'
      });
      continue;
    }
  }

  return ticks;
});

// Generate SVG Path for TSH Isocurve: SAC = (TSH * 100) / TCH
const getIsocurvePath = (tsh: number) => {
  const points: string[] = [];
  const extendedMinX = Math.max(5, minX.value - 120);
  const extendedMaxX = maxX.value + 120;
  const step = (extendedMaxX - extendedMinX) / 120;

  const extendedMinY = minY.value - 15;
  const extendedMaxY = maxY.value + 15;

  for (let tch = extendedMinX; tch <= extendedMaxX; tch += step) {
    const sac = (tsh * 100) / tch;
    if (sac >= extendedMinY && sac <= extendedMaxY) {
      const x = toSvgX(tch);
      const y = toSvgY(sac);
      points.push(`${points.length === 0 ? "M" : "L"} ${x.toFixed(1)} ${y.toFixed(1)}`);
    }
  }

  return points.join(" ");
};

const getIsocurveLabelPos = (tsh: number) => {
  const tchMid = minX.value + (maxX.value - minX.value) * 0.7;
  const sac = (tsh * 100) / tchMid;
  if (sac >= minY.value && sac <= maxY.value) {
    return {
      x: toSvgX(tchMid),
      y: toSvgY(sac) - 6
    };
  }
  return null;
};

// Export to CSV
const exportCsv = () => {
  const headers = ["Variedad", "Tipo", "TCH (t/ha)", "Rendimiento % Sacarosa", "TSH (t/ha)", `IDEAR TSH vs ${selectedTestigoRef.value} (%)`];
  const rows = sortedVariedades.value.map((v) => [
    v.variedad,
    v.es_testigo ? "Testigo" : "Candidata",
    v.tch,
    v.sacarosa,
    v.tsh,
    v.calculatedIdear
  ]);

  const csvContent = "data:text/csv;charset=utf-8," + [headers.join(","), ...rows.map((r) => r.join(","))].join("\n");
  const encodedUri = encodeURI(csvContent);
  const link = document.createElement("a");
  link.setAttribute("href", encodedUri);
  link.setAttribute("download", `isoproductividad_idear_${selectedTestigoRef.value}.csv`);
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.15s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
