<template>
  <div class="w-full max-w-[98%] mx-auto px-2 sm:px-4 space-y-6 pt-2 pb-12 animate-fade-in">
    <!-- Botón Volver -->
    <BackButton :to="{ name: 'mejoramiento.show' }" label="Volver a Mejoramiento" />

    <!-- Encabezado Principal -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between border-b border-slate-100 pb-4 gap-4">
      <div>
        <h1
          class="text-3xl font-extrabold tracking-tight text-slate-800 bg-gradient-to-r from-cenicana-800 to-emerald-600 bg-clip-text text-transparent flex items-center"
        >
          <div class="p-2 bg-emerald-50 text-cenicana rounded-xl mr-3 shadow-sm border border-emerald-100/60">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
              />
            </svg>
          </div>
          Libro de Campo
        </h1>
        <p class="mt-1.5 text-xs font-semibold text-slate-500 ml-12">
          Consulte, configure variables agronómicas y exporte bitácoras experimentales de familias e individuales.
        </p>
      </div>
    </div>

    <!-- Panel de Parámetros y Filtros en Cascada -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6 transition-all">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-5">
        <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
          Parámetros del Experimento
        </h2>
        <span class="text-xs text-slate-400">Seleccione los criterios de búsqueda</span>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- 1. Programa / Servicio -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5" for="select-programa">
            Programa / Servicio: <span class="text-rose-500">*</span>
          </label>
          <select
            id="select-programa"
            v-model="selectedPrograma"
            @change="onProgramaChange"
            :disabled="isSearching || isParamsLocked"
            class="block w-full px-3 py-2 border border-slate-200 rounded-xl text-xs text-slate-700 bg-white focus:ring-2 focus:ring-emerald-100 focus:border-cenicana transition-all disabled:bg-slate-50 disabled:cursor-not-allowed"
          >
            <option value="">Seleccione un programa...</option>
            <option v-for="item in listProgramas" :key="item.id" :value="item.id">
              {{ item.text }}
            </option>
          </select>
        </div>

        <!-- 2. Área -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5" for="select-area"> Área: <span class="text-rose-500">*</span> </label>
          <select
            id="select-area"
            v-model="selectedArea"
            @change="onAreaChange"
            :disabled="!selectedPrograma || isSearching || isParamsLocked"
            class="block w-full px-3 py-2 border border-slate-200 rounded-xl text-xs text-slate-700 bg-white focus:ring-2 focus:ring-emerald-100 focus:border-cenicana transition-all disabled:bg-slate-50 disabled:cursor-not-allowed"
          >
            <option value="">Seleccione un área...</option>
            <option v-for="item in listAreas" :key="item.id" :value="item.id">
              {{ item.text }}
            </option>
          </select>
        </div>

        <!-- 3. Proyecto -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5" for="select-proyecto"> Proyecto: <span class="text-rose-500">*</span> </label>
          <select
            id="select-proyecto"
            v-model="selectedProyecto"
            :disabled="!selectedArea || isSearching || isParamsLocked"
            class="block w-full px-3 py-2 border border-slate-200 rounded-xl text-xs text-slate-700 bg-white focus:ring-2 focus:ring-emerald-100 focus:border-cenicana transition-all disabled:bg-slate-50 disabled:cursor-not-allowed"
          >
            <option value="">Seleccione un proyecto...</option>
            <option v-for="item in listProyectos" :key="item.id" :value="item.id">
              {{ item.text }}
            </option>
          </select>
        </div>

        <!-- 4. Serie -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5" for="select-serie"> Serie (Año): <span class="text-rose-500">*</span> </label>
          <select
            id="select-serie"
            v-model="selectedSerie"
            :disabled="isSearching || isParamsLocked"
            class="block w-full px-3 py-2 border border-slate-200 rounded-xl text-xs text-slate-700 bg-white focus:ring-2 focus:ring-emerald-100 focus:border-cenicana transition-all disabled:bg-slate-50 disabled:cursor-not-allowed"
          >
            <option value="">Seleccione una serie...</option>
            <option v-for="item in listSeries" :key="item.id" :value="item.id">
              {{ item.text }}
            </option>
          </select>
        </div>

        <!-- 5. Estado -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5" for="select-estado">
            Estado del Ensayo: <span class="text-rose-500">*</span>
          </label>
          <select
            id="select-estado"
            v-model="selectedEstado"
            :disabled="isSearching || isParamsLocked"
            class="block w-full px-3 py-2 border border-slate-200 rounded-xl text-xs text-slate-700 bg-white focus:ring-2 focus:ring-emerald-100 focus:border-cenicana transition-all disabled:bg-slate-50 disabled:cursor-not-allowed"
          >
            <option value="">Seleccione un estado...</option>
            <option v-for="item in listEstados" :key="item.id" :value="item.text">
              {{ item.text }}
            </option>
          </select>
        </div>

        <!-- Botones de Acción -->
        <div class="flex items-end gap-2 pt-1">
          <button
            type="button"
            @click="buscarLibroCampo"
            :disabled="!canSearch || isSearching"
            class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-xs font-bold rounded-xl text-white bg-cenicana hover:bg-cenicana-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg v-if="isSearching" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            Buscar Experimento
          </button>

          <button
            type="button"
            @click="limpiarFiltros"
            :disabled="isSearching"
            class="px-3.5 py-2 border border-slate-200 shadow-sm text-xs font-semibold rounded-xl text-slate-600 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300 transition-all"
            title="Restablecer formulario"
          >
            Limpiar
          </button>
        </div>
      </div>
    </div>

    <!-- Alertas informativas / Error -->
    <div v-if="feedbackMessage" class="p-4 rounded-2xl border flex items-start gap-3 transition-all" :class="feedbackClass">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path v-if="feedbackType === 'error'" stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        <path v-else-if="feedbackType === 'success'" stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        <path v-else stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <div class="text-xs font-semibold">
        {{ feedbackMessage }}
      </div>
    </div>

    <!-- ESTADO 1: Configuración de Variables (cuando el experimento existe pero no tiene variables creadas) -->
    <div v-if="hasVariablesConfigMode" class="bg-white rounded-2xl border border-slate-100 shadow-premium p-6 space-y-6 animate-fade-in">
      <div class="border-b border-slate-100 pb-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
          <div>
            <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-2">
              <span class="p-1.5 bg-blue-50 text-blue-600 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                  />
                </svg>
              </span>
              Configuración de Variables para el Libro de Campo
            </h3>
            <p class="text-xs text-slate-500 mt-1">
              El experimento ha sido localizado pero aún no tiene variables de campo asignadas. Seleccione las variables a capturar.
            </p>
          </div>

          <button
            type="button"
            @click="guardarLibroDeCampo"
            :disabled="selectedVariablesFamilia.length === 0 || isSaving"
            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-xs font-bold rounded-xl text-white bg-cenicana hover:bg-cenicana-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all disabled:opacity-50"
          >
            <svg v-if="isSaving" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"
              />
            </svg>
            Crear Libro de Campo
          </button>
        </div>
      </div>

      <!-- Grupos de Variables por Área -->
      <div class="space-y-6">
        <div v-for="(areaGroup, gIdx) in listAvailableVariables" :key="gIdx" class="border border-slate-100 rounded-xl p-4 bg-slate-50/40">
          <div class="flex items-center justify-between border-b border-slate-200/60 pb-2.5 mb-3">
            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-blue-500"></span>
              Área: {{ areaGroup.area || "General" }}
            </h4>
            <div class="flex items-center gap-2">
              <button type="button" @click="seleccionarTodasDelArea(areaGroup)" class="text-[11px] font-semibold text-cenicana hover:underline">
                Seleccionar todas
              </button>
              <span class="text-slate-300">|</span>
              <button
                type="button"
                @click="deseleccionarTodasDelArea(areaGroup)"
                class="text-[11px] font-semibold text-slate-400 hover:text-slate-600 hover:underline"
              >
                Quitar
              </button>
            </div>
          </div>

          <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5">
            <label
              v-for="v in areaGroup.variables"
              :key="v.nmro_cmpo"
              class="flex items-center gap-2 p-2 rounded-lg border text-xs cursor-pointer transition-all select-none"
              :class="
                isVariableSelected(v.nmro_cmpo)
                  ? 'bg-emerald-50/80 border-cenicana text-cenicana-900 font-bold shadow-2xs'
                  : 'bg-white border-slate-200/80 text-slate-600 hover:bg-slate-50'
              "
            >
              <input
                type="checkbox"
                :value="v.nmro_cmpo"
                :checked="isVariableSelected(v.nmro_cmpo)"
                @change="toggleVariable(v)"
                class="rounded border-slate-300 text-cenicana focus:ring-emerald-400 h-3.5 w-3.5"
              />
              <span class="truncate" :title="v.nmbre_cmpo">{{ v.nmbre_cmpo }}</span>
            </label>
          </div>
        </div>
      </div>
    </div>

    <!-- ESTADO 2: Visualización de Libro de Campo Existente -->
    <div v-if="hasLibroData" class="bg-white rounded-2xl border border-slate-100 shadow-premium overflow-hidden animate-fade-in">
      <!-- Pestañas de Tipo de Ensayo (Familias / Individual) -->
      <div class="border-b border-slate-100 px-5 pt-4 bg-slate-50/40 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <nav class="flex space-x-4" aria-label="Tabs">
          <button
            v-if="hasLibroF"
            type="button"
            @click="activeSubTab = 'F'"
            class="pb-3 px-2 border-b-2 font-bold text-xs flex items-center gap-2 transition-all"
            :class="activeSubTab === 'F' ? 'border-cenicana text-cenicana font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-700'"
          >
            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
            Libro de Campo Familias (F)
            <span
              class="py-0.5 px-2 rounded-full text-[10px]"
              :class="activeSubTab === 'F' ? 'bg-emerald-100 text-cenicana font-black' : 'bg-slate-200 text-slate-600 font-semibold'"
            >
              {{ libroFRows.length }}
            </span>
          </button>

          <button
            v-if="hasLibroI"
            type="button"
            @click="activeSubTab = 'I'"
            class="pb-3 px-2 border-b-2 font-bold text-xs flex items-center gap-2 transition-all"
            :class="activeSubTab === 'I' ? 'border-cenicana text-cenicana font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-700'"
          >
            <span class="w-2 h-2 rounded-full bg-purple-500"></span>
            Libro de Campo Individual (I)
            <span
              class="py-0.5 px-2 rounded-full text-[10px]"
              :class="activeSubTab === 'I' ? 'bg-emerald-100 text-cenicana font-black' : 'bg-slate-200 text-slate-600 font-semibold'"
            >
              {{ libroIRows.length }}
            </span>
          </button>
        </nav>

        <!-- Acciones en Toolbar: Buscador local y Exportar Excel -->
        <div class="flex items-center gap-3 pb-3">
          <div class="relative rounded-xl shadow-sm max-w-xs w-full">
            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input
              type="text"
              placeholder="Filtrar registros..."
              v-model="tableSearchQuery"
              class="block w-full pl-8 pr-3 py-1.5 border border-slate-200 rounded-lg text-xs text-slate-700 placeholder-slate-400 focus:ring-1 focus:ring-emerald-200 focus:border-cenicana transition-all bg-white"
            />
          </div>

          <button
            type="button"
            @click="exportarLibroAExcel"
            class="inline-flex items-center px-3 py-1.5 border border-transparent shadow-sm text-xs font-bold rounded-lg text-white bg-cenicana hover:bg-cenicana-700 transition-all shrink-0"
            title="Exportar a archivo Excel .xlsx"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Exportar Excel
          </button>
        </div>
      </div>

      <!-- Barra superior de conteo y selector de páginas -->
      <div class="px-5 py-2.5 bg-slate-50/20 border-b border-slate-100 flex items-center justify-between text-xs text-slate-500 font-medium">
        <div>
          Mostrando <span class="font-bold text-slate-700">{{ paginatedRows.length }}</span> de
          <span class="font-bold text-slate-700">{{ filteredRows.length }}</span> registros
          <span v-if="tableSearchQuery" class="text-emerald-600 font-semibold">(filtrados)</span>
        </div>

        <div class="flex items-center gap-2">
          <span>Filas por página:</span>
          <select
            v-model="rowsPerPage"
            class="text-xs border border-slate-200 rounded-lg px-2 py-0.5 bg-white text-slate-700 focus:ring-1 focus:ring-emerald-500 focus:border-cenicana"
          >
            <option :value="10">10</option>
            <option :value="25">25</option>
            <option :value="50">50</option>
            <option :value="100">100</option>
          </select>
        </div>
      </div>

      <!-- Tabla Dinámica del Libro de Campo -->
      <div class="overflow-x-auto scrollbar-custom">
        <table class="min-w-full divide-y divide-slate-100">
          <thead class="bg-slate-50/80">
            <tr>
              <th
                scope="col"
                class="px-4 py-3 text-center border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-600 whitespace-nowrap"
              >
                Repetición
              </th>
              <th
                scope="col"
                class="px-4 py-3 text-center border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-600 whitespace-nowrap"
              >
                Entrada
              </th>
              <th
                scope="col"
                class="px-4 py-3 text-center border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-600 whitespace-nowrap"
              >
                Localidad
              </th>
              <th
                scope="col"
                class="px-4 py-3 text-left border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-600 whitespace-nowrap min-w-[150px]"
              >
                Tratamiento / Variedad
              </th>
              <th
                scope="col"
                class="px-4 py-3 text-center border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-600 whitespace-nowrap"
              >
                Parcela
              </th>
              <th
                scope="col"
                class="px-4 py-3 text-center border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-600 whitespace-nowrap"
              >
                Testigo
              </th>
              <th
                scope="col"
                class="px-4 py-3 text-center border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-600 whitespace-nowrap"
              >
                No. Clones
              </th>

              <!-- Columnas Dinámicas de Variables -->
              <th
                v-for="campo in currentActiveCampos"
                :key="campo.nmro_cmpo"
                scope="col"
                class="px-4 py-3 text-center border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-cenicana-800 bg-emerald-50/30 whitespace-nowrap"
              >
                {{ campo.nmbre_cmpo }}
              </th>
            </tr>
          </thead>

          <tbody class="bg-white divide-y divide-slate-50">
            <tr
              v-for="(row, rIdx) in paginatedRows"
              :key="rIdx"
              class="hover:bg-slate-50/60 hover:shadow-[inset_4px_0_0_#10b981] transition-all duration-150 group"
            >
              <td class="px-4 py-2.5 text-center text-xs font-semibold text-slate-600 font-mono">
                {{ row.rptcion ?? "--" }}
              </td>
              <td class="px-4 py-2.5 text-center text-xs font-semibold text-slate-600 font-mono">
                {{ row.entrda ?? "--" }}
              </td>
              <td class="px-4 py-2.5 text-center text-xs font-medium text-slate-700">
                {{ row.lcldad ?? "--" }}
              </td>
              <td class="px-4 py-2.5 text-xs font-bold text-slate-800">
                <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-slate-100 border border-slate-200 text-slate-700">
                  {{ row.trtmnto ?? "--" }}
                </span>
              </td>
              <td class="px-4 py-2.5 text-center text-xs font-medium text-slate-600 font-mono">
                {{ row.prcla ?? "--" }}
              </td>
              <td class="px-4 py-2.5 text-center text-xs">
                <span
                  v-if="row.tstgo && row.tstgo !== '0'"
                  class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200"
                >
                  Sí
                </span>
                <span v-else class="text-slate-400 text-[11px] font-normal">No</span>
              </td>
              <td class="px-4 py-2.5 text-center text-xs text-slate-600 font-mono">
                {{ row.nmro_clnes ?? 0 }}
              </td>

              <!-- Valores Dinámicos de Variables -->
              <td
                v-for="campo in currentActiveCampos"
                :key="campo.nmro_cmpo"
                class="px-4 py-2.5 text-center text-xs font-semibold text-slate-800 bg-emerald-50/10"
              >
                {{ row[campo.nmro_cmpo] !== undefined && row[campo.nmro_cmpo] !== null && row[campo.nmro_cmpo] !== "" ? row[campo.nmro_cmpo] : "--" }}
              </td>
            </tr>

            <!-- Estado Vacío -->
            <tr v-if="paginatedRows.length === 0">
              <td :colspan="7 + currentActiveCampos.length" class="px-4 py-12 text-center text-slate-400">
                <div class="max-w-xs mx-auto space-y-2">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="1.5"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                    />
                  </svg>
                  <p class="text-sm font-semibold text-slate-600">No se encontraron registros en el libro</p>
                  <p class="text-xs text-slate-400">Intente modificar el filtro de búsqueda.</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Paginador Inferior -->
      <div class="px-5 py-3.5 bg-slate-50/60 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="text-xs text-slate-500 font-medium">
          Página <span class="font-bold text-slate-700">{{ currentPage }}</span> de
          <span class="font-bold text-slate-700">{{ totalPages || 1 }}</span>
        </div>

        <div class="flex items-center space-x-1">
          <button
            type="button"
            @click="goToPage(1)"
            :disabled="currentPage === 1"
            class="px-2.5 py-1.5 border border-slate-200 rounded-lg text-xs font-bold text-slate-600 bg-white hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed transition-all"
            title="Primera página"
          >
            ««
          </button>
          <button
            type="button"
            @click="goToPage(currentPage - 1)"
            :disabled="currentPage === 1"
            class="px-3 py-1.5 border border-slate-200 rounded-lg text-xs font-bold text-slate-600 bg-white hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed transition-all"
          >
            Anterior
          </button>
          <span class="px-3 py-1.5 bg-cenicana text-white rounded-lg text-xs font-extrabold shadow-sm">
            {{ currentPage }}
          </span>
          <button
            type="button"
            @click="goToPage(currentPage + 1)"
            :disabled="currentPage >= totalPages"
            class="px-3 py-1.5 border border-slate-200 rounded-lg text-xs font-bold text-slate-600 bg-white hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed transition-all"
          >
            Siguiente
          </button>
          <button
            type="button"
            @click="goToPage(totalPages)"
            :disabled="currentPage >= totalPages"
            class="px-2.5 py-1.5 border border-slate-200 rounded-lg text-xs font-bold text-slate-600 bg-white hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed transition-all"
            title="Última página"
          >
            »»
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from "vue";
import * as XLSX from "xlsx";
import BackButton from "@/components/BackButton.vue";
import LibroCampoService from "@/services/librocampo.services";

// Estados de carga y feedback
const isSearching = ref(false);
const isSaving = ref(false);
const isParamsLocked = ref(false);
const feedbackMessage = ref("");
const feedbackType = ref<"info" | "success" | "error">("info");

// Parámetros de búsqueda seleccionados
const selectedPrograma = ref<string | number>("");
const selectedArea = ref<string | number>("");
const selectedProyecto = ref<string | number>("");
const selectedSerie = ref<string | number>("");
const selectedEstado = ref<string>("");

// Listas para los selects en cascada
const listProgramas = ref<any[]>([]);
const listAreas = ref<any[]>([]);
const listProyectos = ref<any[]>([]);
const listSeries = ref<any[]>([]);
const listEstados = ref<any[]>([]);

// Resultados de la consulta
const experimentoData = ref<any[]>([]);
const listAvailableVariables = ref<any[]>([]);
const selectedVariablesFamilia = ref<any[]>([]);
const selectedVariablesIndividual = ref<any[]>([]);

// Datos de Libro de Campo
const libroFRows = ref<any[]>([]);
const libroFCampos = ref<any[]>([]);
const libroIRows = ref<any[]>([]);
const libroICampos = ref<any[]>([]);

// Pestaña activa ('F' = Familias, 'I' = Individual)
const activeSubTab = ref<"F" | "I">("F");

// Paginación y búsqueda local en la tabla
const tableSearchQuery = ref("");
const currentPage = ref(1);
const rowsPerPage = ref(25);

// Computed: ¿Se puede buscar?
const canSearch = computed(() => {
  return (
    selectedPrograma.value !== "" && selectedArea.value !== "" && selectedProyecto.value !== "" && selectedSerie.value !== "" && selectedEstado.value !== ""
  );
});

// Computed: Modos de visualización
const hasVariablesConfigMode = computed(() => {
  return listAvailableVariables.value.length > 0 && !hasLibroData.value;
});

const hasLibroF = computed(() => libroFRows.value.length > 0);
const hasLibroI = computed(() => libroIRows.value.length > 0);
const hasLibroData = computed(() => hasLibroF.value || hasLibroI.value);

const currentActiveRows = computed(() => {
  return activeSubTab.value === "F" ? libroFRows.value : libroIRows.value;
});

const currentActiveCampos = computed(() => {
  return activeSubTab.value === "F" ? libroFCampos.value : libroICampos.value;
});

// Clases para alertas
const feedbackClass = computed(() => {
  if (feedbackType.value === "error") {
    return "bg-rose-50 border-rose-200 text-rose-800";
  }
  if (feedbackType.value === "success") {
    return "bg-emerald-50 border-emerald-200 text-cenicana-800";
  }
  return "bg-blue-50 border-blue-200 text-blue-800";
});

// Filtro de filas en tabla activa
const filteredRows = computed(() => {
  let rows = currentActiveRows.value;
  const q = tableSearchQuery.value.trim().toLowerCase();
  if (!q) return rows;

  return rows.filter((r: any) => {
    const trtmnto = String(r.trtmnto || "").toLowerCase();
    const lcldad = String(r.lcldad || "").toLowerCase();
    const prcla = String(r.prcla || "").toLowerCase();
    const rptcion = String(r.rptcion || "").toLowerCase();
    const entrda = String(r.entrda || "").toLowerCase();

    const matchesBase = trtmnto.includes(q) || lcldad.includes(q) || prcla.includes(q) || rptcion.includes(q) || entrda.includes(q);

    if (matchesBase) return true;

    for (const c of currentActiveCampos.value) {
      if (r[c.nmro_cmpo] !== undefined && String(r[c.nmro_cmpo]).toLowerCase().includes(q)) {
        return true;
      }
    }

    return false;
  });
});

// Paginación
const totalPages = computed(() => {
  return Math.ceil(filteredRows.value.length / rowsPerPage.value) || 1;
});

const paginatedRows = computed(() => {
  const start = (currentPage.value - 1) * rowsPerPage.value;
  return filteredRows.value.slice(start, start + rowsPerPage.value);
});

const goToPage = (page: number) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};

watch([activeSubTab, tableSearchQuery, rowsPerPage], () => {
  currentPage.value = 1;
});

// Carga inicial de parámetros
onMounted(async () => {
  await cargarParametrosIniciales();
});

const cargarParametrosIniciales = async () => {
  try {
    const res = await LibroCampoService.getSearchParameters();
    if (res.data) {
      listProgramas.value = res.data.listProgramas || [];
      listSeries.value = res.data.listSeries || [];
      listEstados.value = res.data.listEstados || [];
    }
  } catch (error) {
    console.error("Error al cargar parámetros iniciales:", error);
    feedbackMessage.value = "Error al conectar con los parámetros del servidor.";
    feedbackType.value = "error";
  }
};

// Eventos de selección en cascada
const onProgramaChange = async () => {
  selectedArea.value = "";
  selectedProyecto.value = "";
  listAreas.value = [];
  listProyectos.value = [];

  if (!selectedPrograma.value) return;

  try {
    const res = await LibroCampoService.getAreasProgram(String(selectedPrograma.value));
    if (res.data && res.data.listAreas) {
      listAreas.value = res.data.listAreas;
    }
  } catch (error) {
    console.error("Error al cargar áreas:", error);
  }
};

const onAreaChange = async () => {
  selectedProyecto.value = "";
  listProyectos.value = [];

  if (!selectedArea.value) return;

  try {
    const res = await LibroCampoService.getProjectsArea(String(selectedArea.value));
    if (res.data && res.data.listProyectos) {
      listProyectos.value = res.data.listProyectos;
    }
  } catch (error) {
    console.error("Error al cargar proyectos:", error);
  }
};

// Buscar Libro de Campo
const buscarLibroCampo = async () => {
  if (!canSearch.value) return;

  isSearching.value = true;
  feedbackMessage.value = "";
  listAvailableVariables.value = [];
  selectedVariablesFamilia.value = [];
  selectedVariablesIndividual.value = [];
  libroFRows.value = [];
  libroFCampos.value = [];
  libroIRows.value = [];
  libroICampos.value = [];

  try {
    const res = await LibroCampoService.getLibroCampo(String(selectedProyecto.value), String(selectedSerie.value), String(selectedEstado.value));

    const data = res.data;

    if (data.error === 1 || (data.mensajeError && data.mensajeError.length > 0)) {
      feedbackMessage.value = data.mensajeError ? data.mensajeError.join(", ") : "El experimento no existe.";
      feedbackType.value = "error";
      isParamsLocked.value = false;
      return;
    }

    experimentoData.value = data.experimento || [];

    // Si hay variables por configurar
    if (data.listVariables && data.listVariables.length > 0) {
      listAvailableVariables.value = data.listVariables;
      feedbackMessage.value = "Experimento localizado. Configure las variables requeridas para generar el libro de campo.";
      feedbackType.value = "info";
      isParamsLocked.value = true;
      return;
    }

    // Si ya tiene libro creado
    let foundAny = false;
    if (data.libroF && data.libroF.libroCampo && data.libroF.libroCampo.length > 0) {
      libroFRows.value = data.libroF.libroCampo;
      libroFCampos.value = data.libroF.camposLibro || [];
      activeSubTab.value = "F";
      foundAny = true;
    }

    if (data.libroI && data.libroI.libroCampo && data.libroI.libroCampo.length > 0) {
      libroIRows.value = data.libroI.libroCampo;
      libroICampos.value = data.libroI.camposLibro || [];
      if (!foundAny) activeSubTab.value = "I";
      foundAny = true;
    }

    if (foundAny) {
      feedbackMessage.value = "Libro de campo cargado con éxito.";
      feedbackType.value = "success";
      isParamsLocked.value = true;
    } else {
      feedbackMessage.value = "No se encontraron datos o salidas de diseño para este experimento.";
      feedbackType.value = "info";
    }
  } catch (error: any) {
    console.error("Error al buscar libro de campo:", error);
    feedbackMessage.value = error.response?.data?.message || "Ocurrió un error al consultar el libro de campo.";
    feedbackType.value = "error";
  } finally {
    isSearching.value = false;
  }
};

// Gestión de Variables Checkbox
const isVariableSelected = (nmroCmpo: any) => {
  return selectedVariablesFamilia.value.some((v) => v.nmro_cmpo === nmroCmpo);
};

const toggleVariable = (variable: any) => {
  const idx = selectedVariablesFamilia.value.findIndex((v) => v.nmro_cmpo === variable.nmro_cmpo);
  if (idx >= 0) {
    selectedVariablesFamilia.value.splice(idx, 1);
  } else {
    selectedVariablesFamilia.value.push(variable);
  }
};

const seleccionarTodasDelArea = (areaGroup: any) => {
  if (!areaGroup.variables) return;
  areaGroup.variables.forEach((v: any) => {
    if (!isVariableSelected(v.nmro_cmpo)) {
      selectedVariablesFamilia.value.push(v);
    }
  });
};

const deseleccionarTodasDelArea = (areaGroup: any) => {
  if (!areaGroup.variables) return;
  const idsToRemove = new Set(areaGroup.variables.map((v: any) => v.nmro_cmpo));
  selectedVariablesFamilia.value = selectedVariablesFamilia.value.filter((v) => !idsToRemove.has(v.nmro_cmpo));
};

// Guardar / Crear Libro de Campo
const guardarLibroDeCampo = async () => {
  if (selectedVariablesFamilia.value.length === 0) return;

  const primerDiseno = experimentoData.value[0];
  if (!primerDiseno) return;

  isSaving.value = true;
  feedbackMessage.value = "";

  const payload = [
    {
      id_dsno_enc: primerDiseno.id_dsno_enc,
      campos: selectedVariablesFamilia.value,
      tipo_ensayo: primerDiseno.tpo_ensyo || "F"
    }
  ];

  if (experimentoData.value.length > 1) {
    const segundoDiseno = experimentoData.value[1];
    payload.push({
      id_dsno_enc: segundoDiseno.id_dsno_enc,
      campos: selectedVariablesFamilia.value,
      tipo_ensayo: segundoDiseno.tpo_ensyo || "I"
    });
  }

  try {
    const res = await LibroCampoService.crearLibroCampo(payload);
    if (res.data && res.data.code === 200) {
      feedbackMessage.value = "Libro de campo creado con éxito. Actualizando vista...";
      feedbackType.value = "success";
      await buscarLibroCampo();
    } else {
      feedbackMessage.value = res.data?.message || "No se pudo crear el libro de campo.";
      feedbackType.value = "error";
    }
  } catch (error: any) {
    console.error("Error al crear libro de campo:", error);
    feedbackMessage.value = error.response?.data?.message || "Error al crear el libro de campo.";
    feedbackType.value = "error";
  } finally {
    isSaving.value = false;
  }
};

// Limpiar filtros
const limpiarFiltros = () => {
  selectedPrograma.value = "";
  selectedArea.value = "";
  selectedProyecto.value = "";
  selectedSerie.value = "";
  selectedEstado.value = "";
  listAreas.value = [];
  listProyectos.value = [];
  experimentoData.value = [];
  listAvailableVariables.value = [];
  selectedVariablesFamilia.value = [];
  selectedVariablesIndividual.value = [];
  libroFRows.value = [];
  libroFCampos.value = [];
  libroIRows.value = [];
  libroICampos.value = [];
  feedbackMessage.value = "";
  isParamsLocked.value = false;
};

// Exportar a Excel (.xlsx)
const exportarLibroAExcel = () => {
  const rows = filteredRows.value;
  const campos = currentActiveCampos.value;
  const tipoLabel = activeSubTab.value === "F" ? "Familias" : "Individual";

  const exportData = rows.map((r: any) => {
    const obj: any = {
      Repetición: r.rptcion ?? "",
      Entrada: r.entrda ?? "",
      Localidad: r.lcldad ?? "",
      "Tratamiento / Variedad": r.trtmnto ?? "",
      Parcela: r.prcla ?? "",
      Testigo: r.tstgo && r.tstgo !== "0" ? "Sí" : "No",
      "No. Clones": r.nmro_clnes ?? 0
    };

    campos.forEach((c) => {
      obj[c.nmbre_cmpo] = r[c.nmro_cmpo] !== undefined && r[c.nmro_cmpo] !== null ? r[c.nmro_cmpo] : "";
    });

    return obj;
  });

  const worksheet = XLSX.utils.json_to_sheet(exportData);
  const workbook = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(workbook, worksheet, `LibroCampo_${tipoLabel}`);

  const fileName = `libro_campo_${tipoLabel.toLowerCase()}_${selectedSerie.value || "exp"}_${new Date().toISOString().split("T")[0]}.xlsx`;
  XLSX.writeFile(workbook, fileName);
};
</script>

<style scoped>
.scrollbar-custom::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

.scrollbar-custom::-webkit-scrollbar-thumb {
  background-color: #10b981;
  border-radius: 10px;
}

.scrollbar-custom::-webkit-scrollbar-track {
  background-color: #f8fafc;
}
</style>
