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
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
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

          <div class="flex items-center gap-3">
            <span class="text-xs font-semibold text-slate-600">
              <span class="font-extrabold text-cenicana">{{ selectedVariablesFamilia.length }}</span> seleccionadas
            </span>
            <button
              type="button"
              @click="() => guardarLibroDeCampo(false)"
              :disabled="selectedVariablesFamilia.length === 0 || isSaving"
              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-xs font-bold rounded-xl text-white bg-cenicana hover:bg-cenicana-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all disabled:opacity-50 cursor-pointer"
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

        <!-- Filtro y Acciones Rápidas en Modo Configuración -->
        <div class="mt-4 pt-3 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
          <div class="relative w-full sm:max-w-xs">
            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input
              type="text"
              v-model="configFilterSearch"
              placeholder="Filtrar variables..."
              class="block w-full pl-8 pr-3 py-1.5 border border-slate-200 rounded-xl text-xs text-slate-700 placeholder-slate-400 focus:ring-2 focus:ring-emerald-100 focus:border-cenicana transition-all bg-white"
            />
          </div>

          <div class="flex items-center gap-2">
            <button
              type="button"
              @click="seleccionarTodasGlobal"
              class="text-xs font-bold text-cenicana hover:underline cursor-pointer"
            >
              Seleccionar todas
            </button>
            <span class="text-slate-300">|</span>
            <button
              type="button"
              @click="deseleccionarTodasGlobal"
              class="text-xs font-bold text-slate-400 hover:text-slate-600 hover:underline cursor-pointer"
            >
              Deseleccionar todas
            </button>
          </div>
        </div>
      </div>

      <!-- Grupos de Variables por Área -->
      <div class="space-y-6">
        <div v-for="(areaGroup, gIdx) in filteredConfigVariables" :key="gIdx" class="border border-slate-100 rounded-xl p-4 bg-slate-50/40">
          <div class="flex items-center justify-between border-b border-slate-200/60 pb-2.5 mb-3">
            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-blue-500"></span>
              Área: {{ areaGroup.area || "General" }}
              <span class="text-[11px] font-normal text-slate-500">({{ areaGroup.variables.length }} variables)</span>
            </h4>
            <div class="flex items-center gap-2">
              <button type="button" @click="seleccionarTodasDelArea(areaGroup)" class="text-[11px] font-semibold text-cenicana hover:underline cursor-pointer">
                Seleccionar todas
              </button>
              <span class="text-slate-300">|</span>
              <button
                type="button"
                @click="deseleccionarTodasDelArea(areaGroup)"
                class="text-[11px] font-semibold text-slate-400 hover:text-slate-600 hover:underline cursor-pointer"
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
              <span class="truncate flex-1" :title="v.nmbre_cmpo">{{ v.nmbre_cmpo }}</span>
              <span class="text-[10px] text-slate-400 font-mono">({{ v.nmro_cmpo }})</span>
            </label>
          </div>
        </div>

        <div v-if="filteredConfigVariables.length === 0" class="py-8 text-center text-slate-400 text-xs font-semibold">
          No se encontraron variables con el término de búsqueda "{{ configFilterSearch }}".
        </div>
      </div>
    </div>

    <!-- ESTADO 2: Visualización de Libro de Campo Existente -->
    <div v-if="hasLibroData" class="bg-white rounded-2xl border border-slate-100 shadow-premium overflow-hidden animate-fade-in">
      <!-- Pestañas de Tipo de Ensayo (Familias / Individual) -->
      <div class="border-b border-slate-100 px-5 pt-4 bg-slate-50/40 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <nav class="flex space-x-4" aria-label="Tabs">
          <button
            v-if="hasLibroF"
            type="button"
            @click="activeSubTab = 'F'"
            class="pb-3 px-2 border-b-2 font-bold text-xs flex items-center gap-2 transition-all cursor-pointer"
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
            class="pb-3 px-2 border-b-2 font-bold text-xs flex items-center gap-2 transition-all cursor-pointer"
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

        <!-- Acciones en Toolbar: Buscador local, Modo Edición, Importar/Exportar y Configurar Columnas -->
        <div class="flex flex-wrap items-center gap-2.5 pb-3">
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

          <!-- Botón: Modo Edición Manual -->
          <button
            type="button"
            @click="toggleModoEdicionManual"
            class="inline-flex items-center px-3 py-1.5 border shadow-sm text-xs font-bold rounded-lg transition-all shrink-0 cursor-pointer"
            :class="
              isEditingManual
                ? 'bg-amber-500 border-amber-600 text-white shadow-amber-200'
                : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50 hover:border-cenicana'
            "
            :title="isEditingManual ? 'Desactivar modo edición' : 'Editar evaluaciones directamente en la tabla'"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1.5" :class="isEditingManual ? 'text-white' : 'text-amber-600'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
            </svg>
            {{ isEditingManual ? 'Modo Edición Activo' : 'Edición Manual' }}
          </button>

          <!-- Botón: Importar Excel -->
          <button
            type="button"
            @click="abrirModalImportarExcel"
            class="inline-flex items-center px-3 py-1.5 border border-slate-200 shadow-sm text-xs font-bold rounded-lg text-slate-700 bg-white hover:bg-slate-50 hover:border-cenicana transition-all shrink-0 cursor-pointer"
            title="Importar evaluaciones desde archivo Excel (.xlsx)"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
            Importar Excel
          </button>

          <!-- Botón: Exportar Excel -->
          <button
            type="button"
            @click="exportarLibroAExcel"
            class="inline-flex items-center px-3 py-1.5 border border-transparent shadow-sm text-xs font-bold rounded-lg text-white bg-cenicana hover:bg-cenicana-700 transition-all shrink-0 cursor-pointer"
            title="Exportar a archivo Excel .xlsx"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Exportar Excel
          </button>

          <!-- Botón: Editar Columnas a Evaluar -->
          <button
            type="button"
            @click="abrirModalEditarVariables"
            class="inline-flex items-center px-3 py-1.5 border border-slate-200 shadow-sm text-xs font-bold rounded-lg text-slate-700 bg-white hover:bg-slate-50 hover:border-cenicana transition-all shrink-0 cursor-pointer"
            title="Editar o modificar las variables / columnas a evaluar en el libro de campo"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1.5 text-cenicana" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Columnas
            <span class="ml-1.5 py-0.5 px-1.5 rounded-full text-[10px] bg-emerald-100 text-cenicana font-black">
              {{ currentActiveCampos.length }}
            </span>
          </button>
        </div>
      </div>

      <!-- Banner cuando el Modo Edición Manual está Activo -->
      <div v-if="isEditingManual" class="px-5 py-3 bg-amber-50/90 border-b border-amber-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-amber-900 animate-fade-in">
        <div class="flex items-center gap-2">
          <span class="p-1 bg-amber-200 rounded-md text-amber-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
          </span>
          <span>
            <strong>Modo de Edición Manual Activo:</strong> Ingrese o modifique los valores de evaluación en las celdas de la tabla.
            <span v-if="dirtyCellsCount > 0" class="font-bold text-emerald-800 ml-1">({{ dirtyCellsCount }} cambios detectados)</span>
          </span>
        </div>
        <div class="flex items-center gap-2">
          <button
            type="button"
            @click="cancelarModoEdicionManual"
            :disabled="isSavingValues"
            class="px-3 py-1.5 border border-slate-300 text-xs font-semibold rounded-lg text-slate-700 bg-white hover:bg-slate-50 transition-all cursor-pointer"
          >
            Cancelar
          </button>
          <button
            type="button"
            @click="guardarEdicionManual"
            :disabled="isSavingValues"
            class="inline-flex items-center px-4 py-1.5 border border-transparent text-xs font-bold rounded-lg text-white bg-cenicana hover:bg-cenicana-700 shadow-sm transition-all cursor-pointer disabled:opacity-50"
          >
            <svg v-if="isSavingValues" class="animate-spin -ml-1 mr-1.5 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            Guardar Evaluaciones
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

              <!-- Valores Dinámicos de Variables (Visualización o Edición Manual) -->
              <td
                v-for="campo in currentActiveCampos"
                :key="campo.nmro_cmpo"
                class="px-2 py-1.5 text-center text-xs font-semibold text-slate-800 bg-emerald-50/10"
              >
                <input
                  v-if="isEditingManual"
                  type="text"
                  v-model="row[campo.nmro_cmpo]"
                  @input="onCellChange"
                  class="w-full min-w-[70px] text-center py-1 px-1.5 text-xs font-semibold rounded-lg border border-emerald-300 focus:border-cenicana focus:ring-1 focus:ring-emerald-400 bg-white shadow-2xs transition-all placeholder:text-slate-300"
                  placeholder="--"
                />
                <span v-else>
                  {{ row[campo.nmro_cmpo] !== undefined && row[campo.nmro_cmpo] !== null && row[campo.nmro_cmpo] !== "" ? row[campo.nmro_cmpo] : "--" }}
                </span>
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

    <!-- MODAL: Configuración y Edición de Variables a Evaluar -->
    <div
      v-if="showEditVariablesModal"
      class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 animate-fade-in"
      @click.self="cerrarModalEditarVariables"
    >
      <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-100 w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden animate-scale-in">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-emerald-100/80 text-cenicana rounded-xl border border-emerald-200/60 shadow-xs">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
            </div>
            <div>
              <h3 class="text-base font-extrabold text-slate-800">
                Editar Columnas y Variables a Evaluar
              </h3>
              <p class="text-xs text-slate-500">
                Seleccione las variables que desea evaluar o desmarque las no deseadas para el libro de campo.
              </p>
            </div>
          </div>
          <button
            type="button"
            @click="cerrarModalEditarVariables"
            class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-all cursor-pointer"
            title="Cerrar modal"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Modal Toolbar / Filter -->
        <div class="px-6 py-3 bg-white border-b border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
          <div class="relative w-full sm:max-w-xs">
            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input
              type="text"
              v-model="modalFilterSearch"
              placeholder="Buscar variable por nombre o código..."
              class="block w-full pl-8 pr-3 py-1.5 border border-slate-200 rounded-xl text-xs text-slate-700 placeholder-slate-400 focus:ring-2 focus:ring-emerald-100 focus:border-cenicana transition-all bg-white"
            />
          </div>

          <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end">
            <span class="text-xs font-semibold text-slate-600">
              <span class="font-extrabold text-cenicana">{{ selectedVariablesFamilia.length }}</span> variables seleccionadas
            </span>
            <div class="flex items-center gap-2">
              <button
                type="button"
                @click="seleccionarTodasGlobal"
                class="text-xs font-bold text-cenicana hover:underline cursor-pointer"
              >
                Seleccionar todas
              </button>
              <span class="text-slate-300">|</span>
              <button
                type="button"
                @click="deseleccionarTodasGlobal"
                class="text-xs font-bold text-slate-400 hover:text-slate-600 hover:underline cursor-pointer"
              >
                Deseleccionar todas
              </button>
            </div>
          </div>
        </div>

        <!-- Modal Body (Areas & Variables) -->
        <div class="p-6 overflow-y-auto max-h-[55vh] space-y-5 scrollbar-custom">
          <div
            v-for="(areaGroup, gIdx) in filteredModalVariables"
            :key="gIdx"
            class="border border-slate-100 rounded-xl p-4 bg-slate-50/40"
          >
            <div class="flex items-center justify-between border-b border-slate-200/60 pb-2 mb-3">
              <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                Área: {{ areaGroup.area || "General" }}
                <span class="text-[11px] font-normal text-slate-500">({{ areaGroup.variables.length }} variables)</span>
              </h4>
              <div class="flex items-center gap-2">
                <button
                  type="button"
                  @click="seleccionarTodasDelArea(areaGroup)"
                  class="text-[11px] font-semibold text-cenicana hover:underline cursor-pointer"
                >
                  Seleccionar todas
                </button>
                <span class="text-slate-300">|</span>
                <button
                  type="button"
                  @click="deseleccionarTodasDelArea(areaGroup)"
                  class="text-[11px] font-semibold text-slate-400 hover:text-slate-600 hover:underline cursor-pointer"
                >
                  Quitar
                </button>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
              <label
                v-for="v in areaGroup.variables"
                :key="v.nmro_cmpo"
                class="flex items-center gap-2.5 p-2.5 rounded-lg border text-xs cursor-pointer transition-all select-none"
                :class="
                  isVariableSelected(v.nmro_cmpo)
                    ? 'bg-emerald-50/90 border-cenicana text-cenicana-900 font-bold shadow-2xs'
                    : 'bg-white border-slate-200/80 text-slate-600 hover:bg-slate-50'
                "
              >
                <input
                  type="checkbox"
                  :value="v.nmro_cmpo"
                  :checked="isVariableSelected(v.nmro_cmpo)"
                  @change="toggleVariable(v)"
                  class="rounded border-slate-300 text-cenicana focus:ring-emerald-400 h-4 w-4 cursor-pointer"
                />
                <span class="truncate flex-1" :title="v.nmbre_cmpo">{{ v.nmbre_cmpo }}</span>
                <span class="text-[10px] text-slate-400 font-mono">({{ v.nmro_cmpo }})</span>
              </label>
            </div>
          </div>

          <div v-if="filteredModalVariables.length === 0" class="py-8 text-center text-slate-400 text-xs font-semibold">
            No se encontraron variables con el término de búsqueda "{{ modalFilterSearch }}".
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 bg-slate-50/80 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
          <p class="text-[11px] text-slate-500">
            Los cambios se aplicarán inmediatamente a las columnas del libro de campo.
          </p>
          <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
            <button
              type="button"
              @click="cerrarModalEditarVariables"
              :disabled="isSaving"
              class="px-4 py-2 border border-slate-200 text-xs font-semibold rounded-xl text-slate-600 bg-white hover:bg-slate-50 transition-all cursor-pointer"
            >
              Cancelar
            </button>
            <button
              type="button"
              @click="guardarCambiosModalVariables"
              :disabled="selectedVariablesFamilia.length === 0 || isSaving"
              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-xs font-bold rounded-xl text-white bg-cenicana hover:bg-cenicana-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all disabled:opacity-50 cursor-pointer"
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
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              Guardar y Actualizar Columnas
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL: Importar Evaluaciones desde Excel -->
    <div
      v-if="showImportExcelModal"
      class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 animate-fade-in"
      @click.self="cerrarModalImportarExcel"
    >
      <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-100 w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden animate-scale-in">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-blue-100/80 text-blue-700 rounded-xl border border-blue-200/60 shadow-xs">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
              </svg>
            </div>
            <div>
              <h3 class="text-base font-extrabold text-slate-800">
                Importar Evaluaciones desde Excel
              </h3>
              <p class="text-xs text-slate-500">
                Cargue el archivo Excel diligenciado para actualizar automáticamente las columnas del libro.
              </p>
            </div>
          </div>
          <button
            type="button"
            @click="cerrarModalImportarExcel"
            class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-all cursor-pointer"
            title="Cerrar modal"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 overflow-y-auto max-h-[60vh] space-y-5 scrollbar-custom">
          <!-- Info Alert -->
          <div class="p-3.5 bg-blue-50 border border-blue-200/80 rounded-xl flex items-start gap-2.5 text-xs text-blue-900">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
              <strong>Formato compatible:</strong> Puede utilizar el mismo archivo obtenido desde <strong>"Exportar Excel"</strong> con los valores diligenciados en las columnas correspondientes a las variables.
            </div>
          </div>

          <!-- Drop zone -->
          <div
            @click="triggerExcelFileInput"
            @dragover.prevent
            @drop.prevent="handleExcelDrop"
            class="border-2 border-dashed border-slate-300 hover:border-cenicana hover:bg-emerald-50/20 rounded-2xl p-6 text-center cursor-pointer transition-all space-y-2 group"
          >
            <input
              type="file"
              ref="excelFileInputRef"
              accept=".xlsx, .xls, .csv"
              @change="onExcelFileSelected"
              class="hidden"
            />
            <div class="w-12 h-12 mx-auto rounded-xl bg-slate-100 group-hover:bg-emerald-100 text-slate-500 group-hover:text-cenicana flex items-center justify-center transition-all">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
              </svg>
            </div>
            <div>
              <p class="text-xs font-bold text-slate-700">
                <span class="text-cenicana underline">Haga clic para seleccionar</span> o arrastre el archivo aquí
              </p>
              <p class="text-[11px] text-slate-400 mt-0.5">Soporta archivos .xlsx, .xls y .csv</p>
            </div>
          </div>

          <!-- Processing Spinner -->
          <div v-if="isProcessingExcel" class="py-6 text-center text-xs text-slate-500 flex items-center justify-center gap-2">
            <svg class="animate-spin h-5 w-5 text-cenicana" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Analizando y validando estructura del archivo...
          </div>

          <!-- Summary & Preview after Parsing -->
          <div v-else-if="excelImportSummary" class="space-y-4 animate-fade-in">
            <!-- File Info Card -->
            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between text-xs">
              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="font-bold text-slate-700 truncate max-w-xs">{{ excelFileName }}</span>
              </div>
              <span class="text-[11px] text-slate-500 font-semibold">{{ excelImportSummary.totalExcelRows }} filas encontradas</span>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-3 gap-3 text-center">
              <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl">
                <div class="text-[10px] uppercase font-bold text-slate-400">Filas en Archivo</div>
                <div class="text-base font-extrabold text-slate-800 mt-0.5">{{ excelImportSummary.totalExcelRows }}</div>
              </div>
              <div class="p-3 bg-emerald-50/80 border border-emerald-200 rounded-xl">
                <div class="text-[10px] uppercase font-bold text-cenicana-700">Parcelas Coincidentes</div>
                <div class="text-base font-extrabold text-cenicana mt-0.5">{{ excelImportSummary.matchedRowsCount }}</div>
              </div>
              <div class="p-3 bg-blue-50/80 border border-blue-200 rounded-xl">
                <div class="text-[10px] uppercase font-bold text-blue-700">Valores a Cargar</div>
                <div class="text-base font-extrabold text-blue-900 mt-0.5">{{ excelImportSummary.totalUpdates }}</div>
              </div>
            </div>

            <!-- Matched Variables Badges -->
            <div class="space-y-1.5">
              <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                Columnas / Variables Reconocidas ({{ excelImportSummary.matchedColumns.length }}):
              </label>
              <div v-if="excelImportSummary.matchedColumns.length > 0" class="flex flex-wrap gap-1.5">
                <span
                  v-for="col in excelImportSummary.matchedColumns"
                  :key="col"
                  class="px-2 py-0.5 text-[11px] font-bold rounded-md bg-emerald-100 text-cenicana border border-emerald-200"
                >
                  {{ col }}
                </span>
              </div>
              <p v-else class="text-xs text-rose-600 font-semibold">
                No se encontraron columnas que coincidan con las variables configuradas actualmente en el libro.
              </p>
            </div>

            <!-- Preview of First Rows -->
            <div v-if="excelImportSummary.previewRows.length > 0" class="space-y-1.5">
              <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                Vista Previa de Datos (primeras filas):
              </label>
              <div class="overflow-x-auto border border-slate-200 rounded-xl">
                <table class="min-w-full divide-y divide-slate-200 text-xs">
                  <thead class="bg-slate-50">
                    <tr>
                      <th class="px-3 py-1.5 text-center font-bold text-slate-600">Parcela</th>
                      <th class="px-3 py-1.5 text-left font-bold text-slate-600">Tratamiento</th>
                      <th
                        v-for="col in excelImportSummary.matchedColumns"
                        :key="col"
                        class="px-3 py-1.5 text-center font-bold text-cenicana bg-emerald-50/50"
                      >
                        {{ col }}
                      </th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100 bg-white">
                    <tr v-for="(pRow, pIdx) in excelImportSummary.previewRows" :key="pIdx">
                      <td class="px-3 py-1 text-center font-mono font-bold text-slate-700">{{ pRow.prcla }}</td>
                      <td class="px-3 py-1 text-slate-700 truncate max-w-[120px]">{{ pRow.trtmnto }}</td>
                      <td
                        v-for="col in excelImportSummary.matchedColumns"
                        :key="col"
                        class="px-3 py-1 text-center font-semibold text-slate-800 bg-emerald-50/20"
                      >
                        {{ pRow.values[col] !== "" ? pRow.values[col] : "--" }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between gap-3">
          <button
            type="button"
            @click="cerrarModalImportarExcel"
            :disabled="isSavingValues"
            class="px-4 py-2 border border-slate-200 text-xs font-semibold rounded-xl text-slate-600 bg-white hover:bg-slate-50 transition-all cursor-pointer"
          >
            Cancelar
          </button>
          <button
            type="button"
            @click="confirmarImportacionExcel"
            :disabled="!excelImportSummary || excelImportSummary.totalUpdates === 0 || isSavingValues"
            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-xs font-bold rounded-xl text-white bg-cenicana hover:bg-cenicana-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all disabled:opacity-50 cursor-pointer"
          >
            <svg v-if="isSavingValues" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            Confirmar e Importar Datos
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
const isSavingValues = ref(false);
const isParamsLocked = ref(false);
const feedbackMessage = ref("");
const feedbackType = ref<"info" | "success" | "error">("info");

// Modal de edición de variables
const showEditVariablesModal = ref(false);
const modalFilterSearch = ref("");
const configFilterSearch = ref("");
const backupSelectedVariables = ref<any[]>([]);

// Edición manual en tabla
const isEditingManual = ref(false);
const dirtyCellsCount = ref(0);
const originalRowValuesBackup = ref<any[]>([]);

// Modal de importación de Excel
const showImportExcelModal = ref(false);
const excelFileInputRef = ref<HTMLInputElement | null>(null);
const excelFileName = ref("");
const isProcessingExcel = ref(false);
const excelImportSummary = ref<any>(null);
const excelParsedUpdates = ref<any[]>([]);

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

const currentActiveDsnoEnc = computed(() => {
  if (activeSubTab.value === "F") {
    const f = experimentoData.value.find((e: any) => e.tpo_ensyo === "F") || experimentoData.value[0];
    return f ? f.id_dsno_enc : null;
  } else {
    const i = experimentoData.value.find((e: any) => e.tpo_ensyo === "I") || experimentoData.value[1] || experimentoData.value[0];
    return i ? i.id_dsno_enc : null;
  }
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

// Filtrado de variables en Modo Configuración
const filteredConfigVariables = computed(() => {
  const q = configFilterSearch.value.trim().toLowerCase();
  if (!q) return listAvailableVariables.value;

  return listAvailableVariables.value
    .map((group) => {
      const areaMatches = (group.area || "").toLowerCase().includes(q);
      const filteredVars = (group.variables || []).filter((v: any) => {
        return (
          areaMatches ||
          (v.nmbre_cmpo || "").toLowerCase().includes(q) ||
          String(v.nmro_cmpo || "").toLowerCase().includes(q)
        );
      });
      return {
        ...group,
        variables: filteredVars
      };
    })
    .filter((group) => group.variables.length > 0);
});

// Filtrado de variables en Modal
const filteredModalVariables = computed(() => {
  const q = modalFilterSearch.value.trim().toLowerCase();
  if (!q) return listAvailableVariables.value;

  return listAvailableVariables.value
    .map((group) => {
      const areaMatches = (group.area || "").toLowerCase().includes(q);
      const filteredVars = (group.variables || []).filter((v: any) => {
        return (
          areaMatches ||
          (v.nmbre_cmpo || "").toLowerCase().includes(q) ||
          String(v.nmro_cmpo || "").toLowerCase().includes(q)
        );
      });
      return {
        ...group,
        variables: filteredVars
      };
    })
    .filter((group) => group.variables.length > 0);
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

// Sincronizar variables activas en el libro con la lista seleccionada
const sincronizarVariablesActuales = () => {
  const activeCampos = (libroFCampos.value.length > 0 ? libroFCampos.value : libroICampos.value) || [];

  const varMap = new Map<string, any>();
  listAvailableVariables.value.forEach((group: any) => {
    if (group.variables) {
      group.variables.forEach((v: any) => {
        varMap.set(String(v.nmro_cmpo), v);
      });
    }
  });

  const selected: any[] = [];
  activeCampos.forEach((c: any) => {
    const code = String(c.nmro_cmpo);
    if (varMap.has(code)) {
      selected.push(varMap.get(code));
    } else {
      selected.push({ nmro_cmpo: c.nmro_cmpo, nmbre_cmpo: c.nmbre_cmpo });
    }
  });

  selectedVariablesFamilia.value = selected;
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
  isEditingManual.value = false;
  dirtyCellsCount.value = 0;

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
    listAvailableVariables.value = data.listVariables || [];

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
      sincronizarVariablesActuales();
      feedbackMessage.value = "Libro de campo cargado con éxito.";
      feedbackType.value = "success";
      isParamsLocked.value = true;
    } else if (listAvailableVariables.value.length > 0) {
      feedbackMessage.value = "Experimento localizado. Configure las variables requeridas para generar el libro de campo.";
      feedbackType.value = "info";
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
  return selectedVariablesFamilia.value.some((v) => String(v.nmro_cmpo) === String(nmroCmpo));
};

const toggleVariable = (variable: any) => {
  const idx = selectedVariablesFamilia.value.findIndex((v) => String(v.nmro_cmpo) === String(variable.nmro_cmpo));
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
  const idsToRemove = new Set(areaGroup.variables.map((v: any) => String(v.nmro_cmpo)));
  selectedVariablesFamilia.value = selectedVariablesFamilia.value.filter((v) => !idsToRemove.has(String(v.nmro_cmpo)));
};

const seleccionarTodasGlobal = () => {
  const allVars: any[] = [];
  listAvailableVariables.value.forEach((group: any) => {
    if (group.variables) {
      group.variables.forEach((v: any) => {
        if (!allVars.some((item) => String(item.nmro_cmpo) === String(v.nmro_cmpo))) {
          allVars.push(v);
        }
      });
    }
  });
  selectedVariablesFamilia.value = allVars;
};

const deseleccionarTodasGlobal = () => {
  selectedVariablesFamilia.value = [];
};

// Abrir y Cerrar Modal de Variables
const abrirModalEditarVariables = () => {
  sincronizarVariablesActuales();
  backupSelectedVariables.value = [...selectedVariablesFamilia.value];
  modalFilterSearch.value = "";
  showEditVariablesModal.value = true;
};

const cerrarModalEditarVariables = () => {
  if (isSaving.value) return;
  selectedVariablesFamilia.value = [...backupSelectedVariables.value];
  showEditVariablesModal.value = false;
};

const guardarCambiosModalVariables = async () => {
  await guardarLibroDeCampo(true);
};

// Guardar / Crear Libro de Campo
const guardarLibroDeCampo = async (isFromModal = false) => {
  if (selectedVariablesFamilia.value.length === 0) {
    feedbackMessage.value = "Debe seleccionar al menos una variable a evaluar.";
    feedbackType.value = "error";
    return;
  }

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
      feedbackMessage.value = isFromModal
        ? "Columnas del libro de campo actualizadas con éxito."
        : "Libro de campo creado con éxito. Actualizando vista...";
      feedbackType.value = "success";
      if (isFromModal) {
        showEditVariablesModal.value = false;
      }
      await buscarLibroCampo();
    } else {
      feedbackMessage.value = res.data?.message || "No se pudo actualizar el libro de campo.";
      feedbackType.value = "error";
    }
  } catch (error: any) {
    console.error("Error al guardar libro de campo:", error);
    feedbackMessage.value = error.response?.data?.message || "Error al actualizar las columnas del libro de campo.";
    feedbackType.value = "error";
  } finally {
    isSaving.value = false;
  }
};

// ==========================================
// MODO EDICIÓN MANUAL DE EVALUACIONES
// ==========================================
const toggleModoEdicionManual = () => {
  if (isEditingManual.value) {
    cancelarModoEdicionManual();
  } else {
    activarModoEdicionManual();
  }
};

const activarModoEdicionManual = () => {
  originalRowValuesBackup.value = JSON.parse(JSON.stringify(currentActiveRows.value));
  dirtyCellsCount.value = 0;
  isEditingManual.value = true;
};

const cancelarModoEdicionManual = () => {
  if (originalRowValuesBackup.value.length > 0) {
    if (activeSubTab.value === "F") {
      libroFRows.value = JSON.parse(JSON.stringify(originalRowValuesBackup.value));
    } else {
      libroIRows.value = JSON.parse(JSON.stringify(originalRowValuesBackup.value));
    }
  }
  dirtyCellsCount.value = 0;
  isEditingManual.value = false;
};

const onCellChange = () => {
  dirtyCellsCount.value++;
};

const guardarEdicionManual = async () => {
  if (!currentActiveDsnoEnc.value) return;

  isSavingValues.value = true;
  feedbackMessage.value = "";

  const updates: { id_dissalida_det: any; nmro_cmpo: any; vlor: any }[] = [];
  const rows = currentActiveRows.value;
  const campos = currentActiveCampos.value;

  rows.forEach((r: any) => {
    campos.forEach((c: any) => {
      const val = r[c.nmro_cmpo];
      updates.push({
        id_dissalida_det: r.id_dissalida_det,
        nmro_cmpo: c.nmro_cmpo,
        vlor: val !== undefined && val !== null ? String(val).trim() : ""
      });
    });
  });

  try {
    const res = await LibroCampoService.actualizarValoresLibroCampo(currentActiveDsnoEnc.value, updates);
    if (res.data && res.data.code === 200) {
      feedbackMessage.value = "Evaluaciones de campo guardadas con éxito.";
      feedbackType.value = "success";
      isEditingManual.value = false;
      dirtyCellsCount.value = 0;
      await buscarLibroCampo();
    } else {
      feedbackMessage.value = res.data?.message || "No se pudieron guardar las evaluaciones.";
      feedbackType.value = "error";
    }
  } catch (error: any) {
    console.error("Error al guardar evaluaciones manuales:", error);
    feedbackMessage.value = error.response?.data?.message || "Error al guardar las evaluaciones.";
    feedbackType.value = "error";
  } finally {
    isSavingValues.value = false;
  }
};

// ==========================================
// IMPORTACIÓN DE EXCEL
// ==========================================
const abrirModalImportarExcel = () => {
  excelFileName.value = "";
  excelImportSummary.value = null;
  excelParsedUpdates.value = [];
  isProcessingExcel.value = false;
  if (excelFileInputRef.value) {
    excelFileInputRef.value.value = "";
  }
  showImportExcelModal.value = true;
};

const cerrarModalImportarExcel = () => {
  if (isSavingValues.value) return;
  showImportExcelModal.value = false;
};

const triggerExcelFileInput = () => {
  if (excelFileInputRef.value) {
    excelFileInputRef.value.click();
  }
};

const handleExcelDrop = (e: DragEvent) => {
  if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
    procesarArchivoExcel(e.dataTransfer.files[0]);
  }
};

const onExcelFileSelected = (e: Event) => {
  const target = e.target as HTMLInputElement;
  if (target.files && target.files.length > 0) {
    procesarArchivoExcel(target.files[0]);
  }
};

const procesarArchivoExcel = async (file: File) => {
  excelFileName.value = file.name;
  isProcessingExcel.value = true;
  excelImportSummary.value = null;
  excelParsedUpdates.value = [];

  try {
    const data = await file.arrayBuffer();
    const workbook = XLSX.read(data, { type: "array" });
    const firstSheetName = workbook.SheetNames[0];
    const worksheet = workbook.Sheets[firstSheetName];
    const jsonRows: any[] = XLSX.utils.sheet_to_json(worksheet, { defval: "" });

    if (jsonRows.length === 0) {
      feedbackMessage.value = "El archivo Excel está vacío o no contiene filas de datos.";
      feedbackType.value = "error";
      isProcessingExcel.value = false;
      return;
    }

    // Identificar columnas de variables
    const activeCampos = currentActiveCampos.value;
    const matchedColumns: { excelHeader: string; campo: any }[] = [];

    const firstRowKeys = Object.keys(jsonRows[0]);
    firstRowKeys.forEach((header) => {
      const trimmed = header.trim().toLowerCase();
      const foundCampo = activeCampos.find(
        (c: any) =>
          c.nmbre_cmpo.trim().toLowerCase() === trimmed ||
          String(c.nmro_cmpo).trim().toLowerCase() === trimmed
      );
      if (foundCampo) {
        matchedColumns.push({ excelHeader: header, campo: foundCampo });
      }
    });

    // Emparejar filas por parcela o repetición + entrada
    const activeRows = currentActiveRows.value;
    const updates: { id_dissalida_det: any; nmro_cmpo: any; vlor: any }[] = [];
    const previewRows: any[] = [];
    let matchedRowsCount = 0;

    jsonRows.forEach((excelRow) => {
      const parcelaVal = excelRow["Parcela"] ?? excelRow["prcla"] ?? excelRow["PARCELA"] ?? excelRow["parcela"];
      const rptcionVal = excelRow["Repetición"] ?? excelRow["rptcion"] ?? excelRow["REPETICION"] ?? excelRow["repeticion"];
      const entrdaVal = excelRow["Entrada"] ?? excelRow["entrda"] ?? excelRow["ENTRADA"] ?? excelRow["entrada"];

      let matchedRow = null;
      if (parcelaVal !== undefined && parcelaVal !== "") {
        matchedRow = activeRows.find((r: any) => String(r.prcla).trim() === String(parcelaVal).trim());
      } else if (rptcionVal !== undefined && entrdaVal !== undefined) {
        matchedRow = activeRows.find(
          (r: any) => String(r.rptcion).trim() === String(rptcionVal).trim() && String(r.entrda).trim() === String(entrdaVal).trim()
        );
      }

      if (matchedRow) {
        matchedRowsCount++;
        const rowPreview: any = {
          prcla: matchedRow.prcla,
          trtmnto: matchedRow.trtmnto,
          rptcion: matchedRow.rptcion,
          values: {}
        };

        matchedColumns.forEach((col) => {
          const rawVal = excelRow[col.excelHeader];
          const valStr = rawVal !== undefined && rawVal !== null ? String(rawVal).trim() : "";
          updates.push({
            id_dissalida_det: matchedRow.id_dissalida_det,
            nmro_cmpo: col.campo.nmro_cmpo,
            vlor: valStr
          });
          rowPreview.values[col.campo.nmbre_cmpo] = valStr;
        });

        if (previewRows.length < 5) {
          previewRows.push(rowPreview);
        }
      }
    });

    excelParsedUpdates.value = updates;
    excelImportSummary.value = {
      totalExcelRows: jsonRows.length,
      matchedRowsCount,
      matchedColumns: matchedColumns.map((c) => c.campo.nmbre_cmpo),
      totalUpdates: updates.length,
      previewRows
    };
  } catch (err: any) {
    console.error("Error al procesar archivo Excel:", err);
    feedbackMessage.value = "Error al leer el archivo Excel: " + (err.message || "");
    feedbackType.value = "error";
  } finally {
    isProcessingExcel.value = false;
  }
};

const confirmarImportacionExcel = async () => {
  if (!currentActiveDsnoEnc.value || excelParsedUpdates.value.length === 0) return;

  isSavingValues.value = true;
  feedbackMessage.value = "";

  try {
    const res = await LibroCampoService.actualizarValoresLibroCampo(
      currentActiveDsnoEnc.value,
      excelParsedUpdates.value
    );

    if (res.data && res.data.code === 200) {
      feedbackMessage.value = `Importación exitosa: ${res.data.updatedCount || excelParsedUpdates.value.length} valores de variables actualizados en el libro de campo.`;
      feedbackType.value = "success";
      showImportExcelModal.value = false;
      await buscarLibroCampo();
    } else {
      feedbackMessage.value = res.data?.message || "Error al guardar los datos importados.";
      feedbackType.value = "error";
    }
  } catch (error: any) {
    console.error("Error al importar evaluaciones:", error);
    feedbackMessage.value = error.response?.data?.message || "Ocurrió un error al guardar los datos importados.";
    feedbackType.value = "error";
  } finally {
    isSavingValues.value = false;
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
  showEditVariablesModal.value = false;
  showImportExcelModal.value = false;
  isEditingManual.value = false;
  dirtyCellsCount.value = 0;
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
