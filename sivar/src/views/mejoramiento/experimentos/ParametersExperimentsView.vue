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
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <rect x="9" y="3" width="6" height="1.5" rx="0.75" />
              <path d="M10 4.5v3.5L5.2 17.1A2.5 2.5 0 0 0 7.4 21h9.2a2.5 2.5 0 0 0 2.2-3.9L14 8V4.5" />
              <path d="M8.5 16.5l2.5-5" />
            </svg>
          </div>
          Parámetros de Experimentos
        </h1>
        <p class="mt-1.5 text-xs font-semibold text-slate-500 ml-12">
          Consulte, configure tratamientos, testigos y diseñe experimentalmente los ensayos de mejoramiento.
        </p>
      </div>
    </div>

    <!-- Stepper de Pasos -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 sm:p-5">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Paso 1 -->
        <div
          class="flex items-center gap-3 p-3 rounded-xl border transition-all"
          :class="[hasExperimentFound ? 'bg-emerald-50/60 border-emerald-200 text-cenicana-800' : 'bg-slate-50 border-slate-200 text-slate-700']"
        >
          <div
            class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs shrink-0 transition-all"
            :class="[hasExperimentFound ? 'bg-cenicana text-white' : 'bg-slate-200 text-slate-600']"
          >
            1
          </div>
          <div>
            <h4 class="text-xs font-bold uppercase tracking-wider">Filtros de Búsqueda</h4>
            <p class="text-[11px] text-slate-500">Programa, Proyecto, Serie y Estado</p>
          </div>
        </div>

        <!-- Paso 2 -->
        <div
          class="flex items-center gap-3 p-3 rounded-xl border transition-all"
          :class="[hasExperimentFound ? 'bg-blue-50/60 border-blue-200 text-blue-900' : 'bg-slate-50/40 border-slate-100 text-slate-400 opacity-60']"
        >
          <div
            class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs shrink-0 transition-all"
            :class="[hasExperimentFound ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-400']"
          >
            2
          </div>
          <div>
            <h4 class="text-xs font-bold uppercase tracking-wider">Tratamientos y Testigos</h4>
            <p class="text-[11px] text-slate-500">Familias e Individual</p>
          </div>
        </div>

        <!-- Paso 3 -->
        <div
          class="flex items-center gap-3 p-3 rounded-xl border transition-all"
          :class="[hasExperimentFound ? 'bg-purple-50/60 border-purple-200 text-purple-900' : 'bg-slate-50/40 border-slate-100 text-slate-400 opacity-60']"
        >
          <div
            class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs shrink-0 transition-all"
            :class="[hasExperimentFound ? 'bg-purple-600 text-white' : 'bg-slate-200 text-slate-400']"
          >
            3
          </div>
          <div>
            <h4 class="text-xs font-bold uppercase tracking-wider">Diseño Estadístico</h4>
            <p class="text-[11px] text-slate-500">Localidades, Repeticiones y Bloques</p>
          </div>
        </div>
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
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5" for="nPrograma">
            Programa / Servicio: <span class="text-rose-500">*</span>
          </label>
          <ComboBoxMultiple
            id="nPrograma"
            :data-list="dataListProgram"
            :column-value="columnValueProgram"
            :column-to-show="columnToShowProgram"
            placeholder="Seleccione un programa..."
            v-model:selectedData="model.nPrograma"
            class="w-full"
          />
        </div>

        <!-- 2. Área -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5" for="nArea"> Área: <span class="text-rose-500">*</span> </label>
          <ComboBoxMultiple
            id="nArea"
            :data-list="dataListAreas"
            :column-value="columnValueAreas"
            :column-to-show="columnToShowAreas"
            placeholder="Seleccione un área..."
            v-model:selectedData="model.nArea"
            :disabled="!model.nPrograma"
            class="w-full"
          />
        </div>

        <!-- 3. Proyecto -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5" for="nProyecto"> Proyecto: <span class="text-rose-500">*</span> </label>
          <ComboBoxMultiple
            id="nProyecto"
            :data-list="dataListProject"
            :column-value="columnValueProject"
            :column-to-show="columnToShowProject"
            placeholder="Seleccione un proyecto..."
            v-model:selectedData="model.nProyecto"
            :disabled="!model.nArea"
            class="w-full"
          />
        </div>

        <!-- 4. Serie -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5" for="nSerie"> Serie (Año): <span class="text-rose-500">*</span> </label>
          <ComboBoxMultiple
            id="nSerie"
            :data-list="dataListSerie"
            :column-value="columnValueSerie"
            :column-to-show="columnToShowSerie"
            placeholder="Seleccione una serie..."
            v-model:selectedData="model.nSerie"
            :disabled="!model.nProyecto"
            class="w-full"
          />
        </div>

        <!-- 5. Estado -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5" for="nEstado"> Estado del Ensayo: <span class="text-rose-500">*</span> </label>
          <ComboBoxMultiple
            id="nEstado"
            :data-list="dataListEstado"
            :column-value="columnToShowEstado"
            :column-to-show="columnToShowEstado"
            placeholder="Seleccione un estado..."
            v-model:selectedData="model.nEstado"
            :disabled="!model.nSerie"
            class="w-full"
          />
        </div>

        <!-- Botones de Acción -->
        <div class="flex items-end gap-2 pt-1">
          <button
            type="button"
            @click.prevent="buscarExperimento"
            :disabled="!model.nProyecto || !model.nSerie || !model.nEstado || isSearching"
            class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-xs font-bold rounded-xl text-white bg-cenicana hover:bg-cenicana-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
          >
            <svg v-if="isSearching" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
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
            @click.prevent="limpiarCampos"
            :disabled="isSearching"
            class="px-3.5 py-2 border border-slate-200 shadow-sm text-xs font-semibold rounded-xl text-slate-600 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300 transition-all cursor-pointer"
            title="Restablecer formulario"
          >
            Limpiar
          </button>
        </div>
      </div>
    </div>

    <!-- Alerta cuando no existe el experimento con opción de crearlo -->
    <div
      v-if="experimentsStore.experimentsFilter != null && Object.keys(experimentsStore.experimentsFilter.experimento).length === 0"
      class="bg-amber-50 border border-amber-200/80 rounded-2xl p-5 shadow-sm animate-fade-in"
    >
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-start gap-3">
          <div class="p-2.5 bg-amber-100 text-amber-700 rounded-xl shrink-0 mt-0.5 shadow-2xs border border-amber-200/60">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
              />
            </svg>
          </div>
          <div>
            <h4 class="text-sm font-bold text-amber-900">No existe un experimento registrado para estos parámetros</h4>
            <p class="text-xs text-amber-800 mt-1">
              Serie: <span class="font-bold">{{ model.nSerie }}</span> | Estado: <span class="font-bold">{{ model.nEstado }}</span>
            </p>
            <p class="text-xs text-slate-600 mt-1">
              Puede inicializar el encabezado del experimento para comenzar a configurar tratamientos, testigos y posteriormente generar el libro de campo.
            </p>
          </div>
        </div>

        <button
          type="button"
          @click.prevent="crearNuevoExperimento"
          :disabled="isCreatingExperiment"
          class="inline-flex items-center px-5 py-2.5 bg-cenicana hover:bg-cenicana-700 text-white text-xs font-bold rounded-xl shadow-md transition-all whitespace-nowrap disabled:opacity-50 cursor-pointer self-start md:self-auto"
        >
          <svg v-if="isCreatingExperiment" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
          </svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
          </svg>
          Inicializar y Crear Experimento
        </button>
      </div>
    </div>

    <!-- PASO 1: SELECCIÓN DE TRATAMIENTOS -->
    <div v-if="hasExperimentFound" class="bg-white rounded-2xl border border-slate-100 shadow-premium p-6 space-y-6 animate-fade-in">
      <div class="border-b border-slate-100 pb-4">
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
          Paso 1: Selección de Tratamientos y Filtros
        </h3>
        <p class="text-xs text-slate-500 mt-1">Defina la temporada, grupos de cruzamiento y seleccione tratamientos y testigos para el experimento.</p>
      </div>

      <!-- Configuración Inicial de Tratamientos -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 bg-slate-50/50 p-4 rounded-xl border border-slate-100">
        <!-- 1. Temporada cruzamientos -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5"> Temporada de Cruzamientos: <span class="text-rose-500">*</span> </label>
          <ComboBoxMultiple
            :data-list="dataListTemporadas"
            :column-value="columnValueTemporadas"
            :column-to-show="columnToShowTemporadas"
            placeholder="Seleccione temporada..."
            v-model:selectedData="model.nTemporada"
            :disabled="!model.nProyecto"
            class="w-full"
          />
        </div>

        <!-- 2. Grupo Madre -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Grupo Cruzamiento Madre:</label>
          <ComboBoxMultiple
            :data-list="dataListCruzamientoMadre"
            :column-value="columnValueCruzamientoMadre"
            :column-to-show="columnToShowCruzamientoMadre"
            placeholder="Seleccione..."
            v-model:selectedData="model.nCruzMadre"
            :disabled="!model.nTemporada"
            class="w-full"
          />
        </div>

        <!-- 3. Grupo Padre -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Grupo Cruzamiento Padre:</label>
          <ComboBoxMultiple
            :data-list="dataListCruzamientoPadre"
            :column-value="columnValueCruzamientoPadre"
            :column-to-show="columnToShowCruzamientoPadre"
            placeholder="Seleccione..."
            v-model:selectedData="model.nCruzPadre"
            :disabled="!model.nCruzMadre"
            class="w-full"
          />
        </div>

        <!-- 4. Tipo de Ensayo -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Tipo de Ensayo:</label>
          <ComboBoxMultiple
            :data-list="dataListTipoEnsayo"
            :column-value="columnValueTipoEnsayo"
            :column-to-show="columnToShowTipoEnsayo"
            placeholder="Seleccione..."
            v-model:selectedData="model.cTipoEnsayo"
            :disabled="!model.nCruzPadre"
            class="w-full"
          />
        </div>

        <!-- 5. Tipo de Parcela -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Tipo de Parcela:</label>
          <ComboBoxMultiple
            :data-list="dataListTipoParcela"
            :column-value="columnValueTipoParcela"
            :column-to-show="columnToShowTipoParcela"
            placeholder="Seleccione..."
            v-model:selectedData="model.nTipoParcela"
            :disabled="!model.cTipoEnsayo"
            class="w-full"
          />
        </div>

        <!-- 6. Mínimo Plantas -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Mínimo Número de Plantas:</label>
          <input
            type="number"
            min="1"
            class="block w-full px-3 py-2 border border-slate-200 rounded-xl text-xs text-slate-700 bg-white focus:ring-2 focus:ring-emerald-100 focus:border-cenicana transition-all disabled:bg-slate-50 disabled:cursor-not-allowed"
            v-model="model.nMinimoPlantas"
            :disabled="!model.nTipoParcela"
          />
        </div>

        <!-- 7. Total Plantas Siembra -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">
            <span v-if="model.cTipoEnsayo === 'F'">Total plantas siembra Familias:</span>
            <span v-else-if="model.cTipoEnsayo === 'I'">Total plantas siembra Individual:</span>
            <span v-else>Total plantas siembra:</span>
          </label>
          <input
            type="number"
            min="1"
            class="block w-full px-3 py-2 border border-slate-200 rounded-xl text-xs text-slate-700 bg-white focus:ring-2 focus:ring-emerald-100 focus:border-cenicana transition-all disabled:bg-slate-50 disabled:cursor-not-allowed"
            v-model="model.nTotalPlantas"
            :disabled="!model.nTipoParcela"
          />
        </div>

        <!-- Botón Ver Tratamientos Disponibles -->
        <div class="flex items-end sm:col-span-2 lg:col-span-2">
          <button
            type="button"
            @click.prevent="openModalTratamientosDisponibles"
            :disabled="!model.nTemporada"
            class="inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-xs font-bold rounded-xl text-white bg-cenicana hover:bg-cenicana-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer w-full sm:w-auto"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Ver y Seleccionar Tratamientos Disponibles
          </button>
        </div>
      </div>

      <!-- Pestañas Principales: Ensayo Familias (F) vs Ensayo Individual (I) -->
      <div class="border border-slate-100 rounded-2xl overflow-hidden shadow-sm">
        <div class="border-b border-slate-100 px-5 pt-4 bg-slate-50/60 flex items-center justify-between">
          <nav class="flex space-x-4" aria-label="Trial Type Tabs">
            <button
              type="button"
              @click="activeTrialTypeTab = 'F'"
              class="pb-3 px-2 border-b-2 font-bold text-xs flex items-center gap-2 transition-all cursor-pointer"
              :class="activeTrialTypeTab === 'F' ? 'border-cenicana text-cenicana font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-700'"
            >
              <span class="w-2 h-2 rounded-full bg-blue-500"></span>
              Tratamientos Ensayo Familias (F)
              <span class="py-0.5 px-2 rounded-full text-[10px] bg-blue-100 text-blue-800 font-bold">
                {{ tableDataTreatmentsExperimentsF.length }}
              </span>
            </button>

            <button
              type="button"
              @click="activeTrialTypeTab = 'I'"
              class="pb-3 px-2 border-b-2 font-bold text-xs flex items-center gap-2 transition-all cursor-pointer"
              :class="activeTrialTypeTab === 'I' ? 'border-cenicana text-cenicana font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-700'"
            >
              <span class="w-2 h-2 rounded-full bg-purple-500"></span>
              Tratamientos Ensayo Individual (I)
              <span class="py-0.5 px-2 rounded-full text-[10px] bg-purple-100 text-purple-800 font-bold">
                {{ tableDataTreatmentsExperimentsI.length }}
              </span>
            </button>
          </nav>
        </div>

        <!-- CONTENIDO DE ENSAYO FAMILIAS (F) -->
        <div v-show="activeTrialTypeTab === 'F'" class="p-5 space-y-4">
          <!-- Sub-pestañas Familias -->
          <div class="border-b border-slate-200/60 pb-3 flex items-center justify-between gap-3">
            <div class="flex space-x-2">
              <button
                @click="setActiveTab('tratamientos')"
                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer"
                :class="activeTab === 'tratamientos' ? 'bg-cenicana text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
              >
                Tratamientos ({{ tableDataTreatmentsExperimentsF.length }})
              </button>
              <button
                @click="setActiveTab('subparcelas')"
                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer"
                :class="activeTab === 'subparcelas' ? 'bg-cenicana text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
              >
                Subparcelas
              </button>
              <button
                @click="setActiveTab('testigos')"
                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer"
                :class="activeTab === 'testigos' ? 'bg-cenicana text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
              >
                Testigos ({{ tableDataTestigosF.length }})
              </button>
              <button
                @click="setActiveTab('testigosMoviles')"
                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer"
                :class="activeTab === 'testigosMoviles' ? 'bg-cenicana text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
              >
                Testigos Móviles ({{ tableDataTestigosM.length }})
              </button>
            </div>

            <!-- Acciones Toolbar -->
            <div class="flex items-center gap-2" v-if="activeTab === 'tratamientos'">
              <button
                @click="selectAllTreatmentsExperimentsF"
                class="px-2.5 py-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-bold rounded-lg transition-all cursor-pointer"
              >
                Seleccionar Todos
              </button>
              <button
                @click="deselectAllTreatmentsExperimentsF"
                class="px-2.5 py-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-bold rounded-lg transition-all cursor-pointer"
              >
                Deseleccionar Todos
              </button>
              <button
                @click="addSelectedTreatmentsExperimentsF"
                class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-2xs transition-all cursor-pointer inline-flex items-center"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Añadir Selección
              </button>
            </div>
          </div>

          <!-- Tabla Tratamientos Familias -->
          <div v-show="activeTab === 'tratamientos'" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 border border-slate-100 rounded-xl overflow-hidden">
              <thead class="bg-slate-50/80">
                <tr>
                  <th class="px-3 py-2.5 text-center text-[11px] font-bold uppercase text-slate-600">
                    <input
                      type="checkbox"
                      :checked="allSelectedTreatmentsExperimentsF"
                      @click="toggleAllSelectionTreatmentsExperimentsF"
                      class="rounded border-slate-300 text-cenicana focus:ring-emerald-400"
                    />
                  </th>
                  <th class="px-4 py-2.5 text-left text-[11px] font-bold uppercase text-slate-600">Familia</th>
                  <th class="px-4 py-2.5 text-left text-[11px] font-bold uppercase text-slate-600">Pedigree</th>
                  <th class="px-4 py-2.5 text-left text-[11px] font-bold uppercase text-slate-600">Origen</th>
                  <th class="px-4 py-2.5 text-center text-[11px] font-bold uppercase text-slate-600">No. Plantas</th>
                  <th class="px-4 py-2.5 text-center text-[11px] font-bold uppercase text-slate-600">Plantas Almacenadas</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-50 bg-white">
                <tr v-for="row in paginatedDataTreatmentsExperimentsF" :key="row.id_dsno_det" class="hover:bg-slate-50/60 transition-all">
                  <td class="px-3 py-2 text-center">
                    <input type="checkbox" v-model="row.selected" class="rounded border-slate-300 text-cenicana focus:ring-emerald-400" />
                  </td>
                  <td class="px-4 py-2 text-xs font-bold text-slate-800 font-mono">{{ row.no_crzmnto }}</td>
                  <td class="px-4 py-2 text-xs font-medium text-slate-700">{{ row.pdgree }}</td>
                  <td class="px-4 py-2 text-xs text-slate-600">{{ row.orgen }}</td>
                  <td class="px-4 py-2 text-xs text-center font-mono font-semibold text-slate-700">{{ row.nmro_clnes }}</td>
                  <td class="px-4 py-2 text-xs text-center font-mono text-slate-600">{{ row.plntlas_ttles }}</td>
                </tr>
                <tr v-if="paginatedDataTreatmentsExperimentsF.length === 0">
                  <td colspan="6" class="px-4 py-8 text-center text-slate-400 text-xs">No hay tratamientos asignados a Familias.</td>
                </tr>
              </tbody>
            </table>

            <!-- Paginación Tratamientos F -->
            <div class="flex items-center justify-between mt-3 text-xs text-slate-500">
              <span>Página {{ currentPageTreatmentsExperimentsF }} de {{ totalPagesTreatmentsExperimentsF || 1 }}</span>
              <div class="flex gap-1">
                <button
                  @click="currentPageTreatmentsExperimentsF > 1 && currentPageTreatmentsExperimentsF--"
                  :disabled="currentPageTreatmentsExperimentsF === 1"
                  class="px-2.5 py-1 border rounded-lg bg-white hover:bg-slate-50 disabled:opacity-40"
                >
                  Anterior
                </button>
                <button
                  @click="currentPageTreatmentsExperimentsF < totalPagesTreatmentsExperimentsF && currentPageTreatmentsExperimentsF++"
                  :disabled="currentPageTreatmentsExperimentsF >= totalPagesTreatmentsExperimentsF"
                  class="px-2.5 py-1 border rounded-lg bg-white hover:bg-slate-50 disabled:opacity-40"
                >
                  Siguiente
                </button>
              </div>
            </div>
          </div>

          <!-- Tabla Testigos Fijos Familias -->
          <div v-show="activeTab === 'testigos'" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 border border-slate-100 rounded-xl">
              <thead class="bg-slate-50/80">
                <tr>
                  <th class="px-3 py-2.5 text-center text-[11px] font-bold uppercase text-slate-600">
                    <input
                      type="checkbox"
                      :checked="allSelectedTestigosF"
                      @click="toggleAllSelectionTestigosF"
                      class="rounded border-slate-300 text-cenicana focus:ring-emerald-400"
                    />
                  </th>
                  <th class="px-4 py-2.5 text-left text-[11px] font-bold uppercase text-slate-600">Variedad</th>
                  <th class="px-4 py-2.5 text-left text-[11px] font-bold uppercase text-slate-600">Pedigree</th>
                  <th class="px-4 py-2.5 text-left text-[11px] font-bold uppercase text-slate-600">Origen</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-50 bg-white">
                <tr v-for="row in paginatedDataTestigosF" :key="row.id_dsno_det" class="hover:bg-slate-50/60 transition-all">
                  <td class="px-3 py-2 text-center">
                    <input type="checkbox" v-model="row.selected" class="rounded border-slate-300 text-cenicana focus:ring-emerald-400" />
                  </td>
                  <td class="px-4 py-2 text-xs font-bold text-slate-800">{{ row.nm_vrdad }}</td>
                  <td class="px-4 py-2 text-xs text-slate-700">{{ row.pdgree }}</td>
                  <td class="px-4 py-2 text-xs text-slate-600">{{ row.orgen }}</td>
                </tr>
                <tr v-if="paginatedDataTestigosF.length === 0">
                  <td colspan="4" class="px-4 py-8 text-center text-slate-400 text-xs">No hay testigos fijos registrados.</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Tabla Testigos Móviles Familias -->
          <div v-show="activeTab === 'testigosMoviles'" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 border border-slate-100 rounded-xl">
              <thead class="bg-slate-50/80">
                <tr>
                  <th class="px-3 py-2.5 text-center text-[11px] font-bold uppercase text-slate-600">
                    <input
                      type="checkbox"
                      :checked="allSelectedTestigosM"
                      @click="toggleAllSelectionTestigosM"
                      class="rounded border-slate-300 text-cenicana focus:ring-emerald-400"
                    />
                  </th>
                  <th class="px-4 py-2.5 text-left text-[11px] font-bold uppercase text-slate-600">Variedad</th>
                  <th class="px-4 py-2.5 text-left text-[11px] font-bold uppercase text-slate-600">Pedigree</th>
                  <th class="px-4 py-2.5 text-left text-[11px] font-bold uppercase text-slate-600">Origen</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-50 bg-white">
                <tr v-for="row in paginatedDataTestigosM" :key="row.id_dsno_det" class="hover:bg-slate-50/60 transition-all">
                  <td class="px-3 py-2 text-center">
                    <input type="checkbox" v-model="row.selected" class="rounded border-slate-300 text-cenicana focus:ring-emerald-400" />
                  </td>
                  <td class="px-4 py-2 text-xs font-bold text-slate-800">{{ row.nm_vrdad }}</td>
                  <td class="px-4 py-2 text-xs text-slate-700">{{ row.pdgree }}</td>
                  <td class="px-4 py-2 text-xs text-slate-600">{{ row.orgen }}</td>
                </tr>
                <tr v-if="paginatedDataTestigosM.length === 0">
                  <td colspan="4" class="px-4 py-8 text-center text-slate-400 text-xs">No hay testigos móviles registrados.</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div v-show="activeTab === 'subparcelas'" class="p-4 text-slate-400 text-xs text-center">Configuración de Subparcelas para Ensayo Familias.</div>
        </div>

        <!-- CONTENIDO DE ENSAYO INDIVIDUAL (I) -->
        <div v-show="activeTrialTypeTab === 'I'" class="p-5 space-y-4">
          <!-- Sub-pestañas Individual -->
          <div class="border-b border-slate-200/60 pb-3 flex items-center justify-between gap-3">
            <div class="flex space-x-2">
              <button
                @click="setActiveTabI('tratamientosI')"
                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer"
                :class="activeTabI === 'tratamientosI' ? 'bg-cenicana text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
              >
                Tratamientos ({{ tableDataTreatmentsExperimentsI.length }})
              </button>
              <button
                @click="setActiveTabI('subparcelasI')"
                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer"
                :class="activeTabI === 'subparcelasI' ? 'bg-cenicana text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
              >
                Subparcelas
              </button>
              <button
                @click="setActiveTabI('testigosI')"
                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer"
                :class="activeTabI === 'testigosI' ? 'bg-cenicana text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
              >
                Testigos ({{ tableDataTestigosFI.length }})
              </button>
              <button
                @click="setActiveTabI('testigosMovilesI')"
                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer"
                :class="activeTabI === 'testigosMovilesI' ? 'bg-cenicana text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
              >
                Testigos Móviles ({{ tableDataTestigosMI.length }})
              </button>
            </div>

            <!-- Acciones Toolbar Individual -->
            <div class="flex items-center gap-2" v-if="activeTabI === 'tratamientosI'">
              <button
                @click="selectAllTreatmentsExperimentsI"
                class="px-2.5 py-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-bold rounded-lg transition-all cursor-pointer"
              >
                Seleccionar Todos
              </button>
              <button
                @click="deselectAllTreatmentsExperimentsI"
                class="px-2.5 py-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-bold rounded-lg transition-all cursor-pointer"
              >
                Deseleccionar Todos
              </button>
              <button
                @click="addSelectedTreatmentsExperimentsI"
                class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-2xs transition-all cursor-pointer inline-flex items-center"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Añadir Selección
              </button>
            </div>
          </div>

          <!-- Tabla Tratamientos Individual -->
          <div v-show="activeTabI === 'tratamientosI'" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 border border-slate-100 rounded-xl overflow-hidden">
              <thead class="bg-slate-50/80">
                <tr>
                  <th class="px-3 py-2.5 text-center text-[11px] font-bold uppercase text-slate-600">
                    <input
                      type="checkbox"
                      :checked="allSelectedTreatmentsExperimentsI"
                      @click="toggleAllSelectionTreatmentsExperimentsI"
                      class="rounded border-slate-300 text-cenicana focus:ring-emerald-400"
                    />
                  </th>
                  <th class="px-4 py-2.5 text-left text-[11px] font-bold uppercase text-slate-600">Familia</th>
                  <th class="px-4 py-2.5 text-left text-[11px] font-bold uppercase text-slate-600">Pedigree</th>
                  <th class="px-4 py-2.5 text-left text-[11px] font-bold uppercase text-slate-600">Origen</th>
                  <th class="px-4 py-2.5 text-center text-[11px] font-bold uppercase text-slate-600">No. Plantas</th>
                  <th class="px-4 py-2.5 text-center text-[11px] font-bold uppercase text-slate-600">Plantas Almacenadas</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-50 bg-white">
                <tr v-for="row in paginatedDataTreatmentsExperimentsI" :key="row.id_dsno_det" class="hover:bg-slate-50/60 transition-all">
                  <td class="px-3 py-2 text-center">
                    <input type="checkbox" v-model="row.selected" class="rounded border-slate-300 text-cenicana focus:ring-emerald-400" />
                  </td>
                  <td class="px-4 py-2 text-xs font-bold text-slate-800 font-mono">{{ row.no_crzmnto }}</td>
                  <td class="px-4 py-2 text-xs font-medium text-slate-700">{{ row.pdgree }}</td>
                  <td class="px-4 py-2 text-xs text-slate-600">{{ row.orgen }}</td>
                  <td class="px-4 py-2 text-xs text-center font-mono font-semibold text-slate-700">{{ row.nmro_clnes }}</td>
                  <td class="px-4 py-2 text-xs text-center font-mono text-slate-600">{{ row.plntlas_ttles }}</td>
                </tr>
                <tr v-if="paginatedDataTreatmentsExperimentsI.length === 0">
                  <td colspan="6" class="px-4 py-8 text-center text-slate-400 text-xs">No hay tratamientos asignados a Individual.</td>
                </tr>
              </tbody>
            </table>

            <!-- Paginación Tratamientos I -->
            <div class="flex items-center justify-between mt-3 text-xs text-slate-500">
              <span>Página {{ currentPageTreatmentsExperimentsI }} de {{ totalPagesTreatmentsExperimentsI || 1 }}</span>
              <div class="flex gap-1">
                <button
                  @click="currentPageTreatmentsExperimentsI > 1 && currentPageTreatmentsExperimentsI--"
                  :disabled="currentPageTreatmentsExperimentsI === 1"
                  class="px-2.5 py-1 border rounded-lg bg-white hover:bg-slate-50 disabled:opacity-40"
                >
                  Anterior
                </button>
                <button
                  @click="currentPageTreatmentsExperimentsI < totalPagesTreatmentsExperimentsI && currentPageTreatmentsExperimentsI++"
                  :disabled="currentPageTreatmentsExperimentsI >= totalPagesTreatmentsExperimentsI"
                  class="px-2.5 py-1 border rounded-lg bg-white hover:bg-slate-50 disabled:opacity-40"
                >
                  Siguiente
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- PASO 2: DEFINICIÓN DISEÑO ESTADÍSTICO -->
    <div v-if="hasExperimentFound" class="bg-white rounded-2xl border border-slate-100 shadow-premium p-6 space-y-6 animate-fade-in">
      <div class="border-b border-slate-100 pb-4">
        <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-2">
          <span class="p-1.5 bg-purple-50 text-purple-600 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
              />
            </svg>
          </span>
          Paso 2: Definición del Diseño Estadístico
        </h3>
        <p class="text-xs text-slate-500 mt-1">Configure los parámetros de diseño experimental para Familias e Individual.</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Diseño Familias -->
        <div class="border border-slate-100 rounded-2xl p-5 bg-slate-50/30 space-y-4">
          <div class="flex items-center justify-between border-b border-slate-200/60 pb-2">
            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-blue-500"></span>
              Ensayo Familias
            </h4>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Diseño Experimental:</label>
            <ComboBoxMultiple
              :data-list="dataListDisenoExp"
              :column-value="columnValueDisenoExp"
              :column-to-show="columnToShowDisenoExp"
              placeholder="Seleccione..."
              v-model:selectedData="model.nDisenoExpF"
              class="w-full"
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Localidades:</label>
              <input
                type="number"
                min="0"
                v-model="model.nLocalidadesF"
                class="w-full px-3 py-1.5 border border-slate-200 rounded-xl text-xs bg-white focus:ring-1 focus:ring-emerald-200"
              />
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Repeticiones:</label>
              <input
                type="number"
                min="0"
                v-model="model.nRepeticionesF"
                class="w-full px-3 py-1.5 border border-slate-200 rounded-xl text-xs bg-white focus:ring-1 focus:ring-emerald-200"
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tratamientos:</label>
              <input type="number" v-model="model.nTratamientoF" class="w-full px-3 py-1.5 border border-slate-200 rounded-xl text-xs bg-white" />
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Testigos:</label>
              <input type="number" v-model="model.nTestigosF" class="w-full px-3 py-1.5 border border-slate-200 rounded-xl text-xs bg-white" />
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Descripción:</label>
            <textarea v-model="model.cDescripcionF" rows="2" class="w-full p-2 border border-slate-200 rounded-xl text-xs bg-white"></textarea>
          </div>

          <button
            type="button"
            class="w-full py-2 bg-cenicana hover:bg-cenicana-700 text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer"
          >
            Actualizar Diseño Familias
          </button>
        </div>

        <!-- Diseño Individual -->
        <div class="border border-slate-100 rounded-2xl p-5 bg-slate-50/30 space-y-4">
          <div class="flex items-center justify-between border-b border-slate-200/60 pb-2">
            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-purple-500"></span>
              Ensayo Individual
            </h4>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Diseño Experimental:</label>
            <ComboBoxMultiple
              :data-list="dataListDisenoExp"
              :column-value="columnValueDisenoExp"
              :column-to-show="columnToShowDisenoExp"
              placeholder="Seleccione..."
              v-model:selectedData="model.nDisenoExpI"
              class="w-full"
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Localidades:</label>
              <input
                type="number"
                min="0"
                v-model="model.nLocalidadesI"
                class="w-full px-3 py-1.5 border border-slate-200 rounded-xl text-xs bg-white focus:ring-1 focus:ring-emerald-200"
              />
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Repeticiones:</label>
              <input
                type="number"
                min="0"
                v-model="model.nRepeticionesI"
                class="w-full px-3 py-1.5 border border-slate-200 rounded-xl text-xs bg-white focus:ring-1 focus:ring-emerald-200"
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tratamientos:</label>
              <input type="number" v-model="model.nTratamientoI" class="w-full px-3 py-1.5 border border-slate-200 rounded-xl text-xs bg-white" />
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Testigos:</label>
              <input type="number" v-model="model.nTestigosI" class="w-full px-3 py-1.5 border border-slate-200 rounded-xl text-xs bg-white" />
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Descripción:</label>
            <textarea v-model="model.cDescripcionI" rows="2" class="w-full p-2 border border-slate-200 rounded-xl text-xs bg-white"></textarea>
          </div>

          <button
            type="button"
            class="w-full py-2 bg-purple-700 hover:bg-purple-800 text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer"
          >
            Actualizar Diseño Individual
          </button>
        </div>
      </div>
    </div>

    <!-- MODAL: TRATAMIENTOS DISPONIBLES DE TEMPORADA DE CRUZAMIENTOS -->
    <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4 animate-fade-in">
      <div class="bg-white rounded-2xl shadow-xl max-w-4xl w-full max-h-[90vh] flex flex-col overflow-hidden border border-slate-100">
        <!-- Modal Header -->
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
          <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
            <span class="p-1 bg-emerald-100 text-cenicana rounded-lg">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
            </span>
            Tratamientos Disponibles (Temporada {{ model.nTemporada }})
          </h3>
          <button @click="closeModal" class="text-slate-400 hover:text-slate-600 rounded-lg p-1 hover:bg-slate-200/60 transition-all cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Modal Body & Toolbar -->
        <div class="p-6 overflow-y-auto space-y-4">
          <div class="flex flex-col sm:flex-row items-center justify-between gap-3 bg-slate-50/80 p-3 rounded-xl border border-slate-100">
            <div class="flex items-center gap-2">
              <button
                @click="selectAll"
                class="px-3 py-1.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold rounded-lg shadow-2xs transition-all cursor-pointer"
              >
                Seleccionar Todos
              </button>
              <button
                @click="deselectAll"
                class="px-3 py-1.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold rounded-lg shadow-2xs transition-all cursor-pointer"
              >
                Deseleccionar Todos
              </button>
            </div>
            <button
              @click="addSelected"
              class="px-4 py-1.5 bg-cenicana hover:bg-cenicana-700 text-white text-xs font-bold rounded-lg shadow-xs transition-all cursor-pointer inline-flex items-center"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
              </svg>
              Añadir Selección
            </button>
          </div>

          <!-- Table -->
          <div class="overflow-x-auto border border-slate-100 rounded-xl">
            <table class="min-w-full divide-y divide-slate-100">
              <thead class="bg-slate-50">
                <tr>
                  <th class="px-3 py-2.5 text-center text-[11px] font-bold uppercase text-slate-600">
                    <input
                      type="checkbox"
                      :checked="allSelected"
                      @click="toggleAllSelection"
                      class="rounded border-slate-300 text-cenicana focus:ring-emerald-400"
                    />
                  </th>
                  <th class="px-3 py-2.5 text-left text-[11px] font-bold uppercase text-slate-600">ID</th>
                  <th class="px-3 py-2.5 text-left text-[11px] font-bold uppercase text-slate-600">Madre</th>
                  <th class="px-3 py-2.5 text-left text-[11px] font-bold uppercase text-slate-600">Padre</th>
                  <th class="px-3 py-2.5 text-left text-[11px] font-bold uppercase text-slate-600">Vivero</th>
                  <th class="px-3 py-2.5 text-left text-[11px] font-bold uppercase text-slate-600">Pedigree / Cruza</th>
                  <th class="px-3 py-2.5 text-center text-[11px] font-bold uppercase text-slate-600">Plántulas Totales</th>
                  <th class="px-3 py-2.5 text-left text-[11px] font-bold uppercase text-slate-600">Origen</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-50 bg-white">
                <tr v-for="row in paginatedData" :key="row.id" class="hover:bg-slate-50/60 transition-all">
                  <td class="px-3 py-2 text-center">
                    <input type="checkbox" v-model="row.selected" class="rounded border-slate-300 text-cenicana focus:ring-emerald-400" />
                  </td>
                  <td class="px-3 py-2 text-xs font-mono text-slate-500 font-extrabold">#{{ row.id }}</td>
                  <td class="px-3 py-2 text-xs font-extrabold text-emerald-800">{{ row.madre }}</td>
                  <td class="px-3 py-2 text-xs font-semibold text-sky-800">{{ row.padre }}</td>
                  <td class="px-3 py-2 text-xs font-bold text-purple-800">{{ row.vivero }}</td>
                  <td class="px-3 py-2 text-xs font-mono text-slate-700 font-semibold">{{ row.pedigree }}</td>
                  <td class="px-3 py-2 text-xs text-center font-mono font-bold text-emerald-700">{{ row.plantulasTotales }}</td>
                  <td class="px-3 py-2 text-xs text-slate-600">{{ row.origen }}</td>
                </tr>
                <tr v-if="paginatedData.length === 0">
                  <td colspan="8" class="px-4 py-8 text-center text-slate-400 text-xs">No se encontraron tratamientos disponibles para esta temporada.</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Paginación Modal -->
          <div class="flex items-center justify-between text-xs text-slate-500 pt-2">
            <span>Página {{ currentPage }} de {{ totalPages || 1 }}</span>
            <div class="flex gap-1">
              <button
                @click="currentPage > 1 && currentPage--"
                :disabled="currentPage === 1"
                class="px-3 py-1 border rounded-lg bg-white hover:bg-slate-50 disabled:opacity-40"
              >
                Anterior
              </button>
              <button
                @click="currentPage < totalPages && currentPage++"
                :disabled="currentPage >= totalPages"
                class="px-3 py-1 border rounded-lg bg-white hover:bg-slate-50 disabled:opacity-40"
              >
                Siguiente
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, onMounted, watch, computed, ref } from "vue";
import BackButton from "@/components/BackButton.vue";
import ComboBoxMultiple from "@/components/ComboBoxMultiple.vue";
import { useSearchParametersStore } from "@/stores/parametersexperiments";
import { useAreasProgramStore } from "@/stores/areasprogram";
import { useProjectsAreaStore } from "@/stores/projectsarea";
import { useExperimentsStore } from "@/stores/experiments";
import { useTreatmentsSeasonStore } from "@/stores/treatmentsseason";
import { useTreatmentsExperimentsStore } from "@/stores/treatmentsexperiments";
import { useAddDesingsDetailsStore } from "@/stores/adddesingsdetails";
import { useToast } from "vue-toastification";
import { useMainStore } from "@/stores/main";
import type { DiseñosDetalles } from "../../../services/types";

const toast = useToast();
const mainStore = useMainStore();
const searchParametersStore = useSearchParametersStore();
const areasProgramStore = useAreasProgramStore();
const projectsAreaStore = useProjectsAreaStore();
const experimentsStore = useExperimentsStore();
const treatmentsSeasonStore = useTreatmentsSeasonStore();
const treatmentsExperimentsStore = useTreatmentsExperimentsStore();
const addDesingsDetailsStore = useAddDesingsDetailsStore();

const isSearching = ref(false);
const activeTrialTypeTab = ref<"F" | "I">("F");

const model = reactive<{
  nPrograma: string | null;
  nArea: string | null;
  nProyecto: string | null;
  nSerie: string | null;
  nEstado: string | null;
  nTemporada: string | null;
  nCruzMadre: string | null;
  nCruzPadre: string | null;
  cTipoEnsayo: string | null;
  nTipoParcela: string | null;
  nMinimoPlantas: number | null;
  nTotalPlantas: number | null;
  nIdDisenoF: string | null;
  nIdDisenoI: string | null;
  nIdDiseno: string | null;
  tipoTabla: string | null;
  listRegistros: Array<{ id_crzmnto: number; plntlas_ttles: number }> | null;
  selectedRegistro: Array<{ id_crzmnto: number; plntlas_ttles: number }> | null;
  nPlantulasDisponibles: number | null;
  nPlantulasTratamiento: string | null;
  nDisenoExpF: string | null;
  nLocalidadesF: number | null;
  nRepeticionesF: number | null;
  nTratamientoF: number | null;
  nTestigosF: number | null;
  nTestigosMovilF: number | null;
  nParcelaPpalF: number | null;
  cDescripcionF: string | null;
  nDisenoExpI: string | null;
  nLocalidadesI: number | null;
  nRepeticionesI: number | null;
  nTratamientoI: number | null;
  nTestigosI: number | null;
  nTestigosMovilI: number | null;
  nParcelaPpalI: number | null;
  cDescripcionI: string | null;
}>({
  nPrograma: null,
  nArea: null,
  nProyecto: null,
  nSerie: null,
  nEstado: null,
  nTemporada: null,
  nCruzMadre: null,
  nCruzPadre: null,
  cTipoEnsayo: null,
  nTipoParcela: null,
  nMinimoPlantas: 0,
  nTotalPlantas: 0,
  nIdDisenoF: null,
  nIdDisenoI: null,
  nIdDiseno: null,
  tipoTabla: null,
  listRegistros: null,
  selectedRegistro: null,
  nPlantulasDisponibles: 0,
  nPlantulasTratamiento: "",
  nDisenoExpF: null,
  nLocalidadesF: 0,
  nRepeticionesF: 0,
  nTratamientoF: 0,
  nTestigosF: 0,
  nTestigosMovilF: 0,
  nParcelaPpalF: 0,
  cDescripcionF: null,
  nDisenoExpI: null,
  nLocalidadesI: 0,
  nRepeticionesI: 0,
  nTratamientoI: 0,
  nTestigosI: 0,
  nTestigosMovilI: 0,
  nParcelaPpalI: 0,
  cDescripcionI: null
});

// Listas de datos para las tablas reactivas
const tableData = ref<
  {
    id: string;
    pedigree: string;
    origen: string;
    plantulasTotales: number;
    grupoMadre: string;
    grupoPadre: string;
    selected: boolean;
  }[]
>([]);

const tableDataTreatmentsExperimentsF = ref<
  {
    id_dsno_det: string;
    trtmnto: string;
    id_dsno_enc: string;
    no_crzmnto: string;
    pdgree: string;
    orgen: string;
    nmro_clnes: string;
    plntlas_ttles: string;
    selected: boolean;
  }[]
>([]);

const tableDataTreatmentsExperimentsI = ref<
  {
    id_dsno_det: string;
    trtmnto: string;
    id_dsno_enc: string;
    no_crzmnto: string;
    pdgree: string;
    orgen: string;
    nmro_clnes: string;
    plntlas_ttles: string;
    selected: boolean;
  }[]
>([]);

const tableDataTestigosF = ref<
  {
    id_dsno_det: string;
    nm_vrdad: string;
    pdgree: string;
    orgen: string;
    selected: boolean;
  }[]
>([]);

const tableDataTestigosM = ref<
  {
    id_dsno_det: string;
    nm_vrdad: string;
    pdgree: string;
    orgen: string;
    selected: boolean;
  }[]
>([]);

const tableDataTestigosFI = ref<
  {
    id_dsno_det: string;
    nm_vrdad: string;
    pdgree: string;
    orgen: string;
    selected: boolean;
  }[]
>([]);

const tableDataTestigosMI = ref<
  {
    id_dsno_det: string;
    nm_vrdad: string;
    pdgree: string;
    orgen: string;
    selected: boolean;
  }[]
>([]);

// Variables para los ComboBox
const dataListProgram = computed(() => searchParametersStore.searchParameters?.listProgramas || []);
const columnValueProgram = "id";
const columnToShowProgram = "text";

const dataListAreas = computed(() => areasProgramStore.areasProgramFilter?.listAreas || []);
const columnValueAreas = "id";
const columnToShowAreas = "text";

const dataListProject = computed(() => projectsAreaStore.projectsAreaFilter?.listProyectos || []);
const columnValueProject = "id";
const columnToShowProject = "text";

const dataListSerie = computed(() => searchParametersStore.searchParameters?.listSeries || []);
const columnValueSerie = "id";
const columnToShowSerie = "text";

const dataListEstado = computed(() => searchParametersStore.searchParameters?.listEstados || []);
const columnToShowEstado = "text";

const dataListTemporadas = computed(() => searchParametersStore.searchParameters?.listTemporadas || []);
const columnValueTemporadas = "id";
const columnToShowTemporadas = "text";

const dataListCruzamientoMadre = computed(() => searchParametersStore.searchParameters?.listCruzamientoMadre || []);
const columnValueCruzamientoMadre = "id";
const columnToShowCruzamientoMadre = "text";

const dataListCruzamientoPadre = computed(() => searchParametersStore.searchParameters?.listCruzamientoPadre || []);
const columnValueCruzamientoPadre = "id";
const columnToShowCruzamientoPadre = "text";

const dataListTipoEnsayo = computed(() => searchParametersStore.searchParameters?.listTipoEnsayo || []);
const columnValueTipoEnsayo = "id";
const columnToShowTipoEnsayo = "text";

const dataListTipoParcela = computed(() => searchParametersStore.searchParameters?.listTipoParcela || []);
const columnValueTipoParcela = "id";
const columnToShowTipoParcela = "text";

const dataListDisenoExp = computed(() => searchParametersStore.searchParameters?.listDisenoExp || []);
const columnValueDisenoExp = "id";
const columnToShowDisenoExp = "text";

// Computed: ¿Se ha encontrado o creado un experimento?
const hasExperimentFound = computed(() => {
  return (
    experimentsStore.experimentsFilter &&
    experimentsStore.experimentsFilter.experimento &&
    Object.keys(experimentsStore.experimentsFilter.experimento).length > 0
  );
});

// Modal State
const isModalOpen = ref(false);
const openModalTratamientosDisponibles = async () => {
  await tratamientosDisponibles();
  isModalOpen.value = true;
};
const closeModal = () => {
  isModalOpen.value = false;
};

// Carga Inicial
onMounted(async () => {
  try {
    await searchParametersStore.getSearchParametersResult();
  } catch (error) {
    console.error("Error al cargar datos iniciales:", error);
  }
});

// Watchers de cascada
watch(
  () => model.nPrograma,
  async (newProgram) => {
    if (newProgram) {
      model.nArea = null;
      model.nProyecto = null;
      await areasProgramStore.getAreasProgramList(newProgram);
    }
  },
  { immediate: true }
);

watch(
  () => model.nArea,
  async (newArea) => {
    if (newArea) {
      model.nProyecto = null;
      await projectsAreaStore.getProjectsAreaList(newArea);
    }
  },
  { immediate: true }
);

// Buscar experimento
const buscarExperimento = async () => {
  if (model.nProyecto && model.nSerie && model.nEstado) {
    isSearching.value = true;
    try {
      await experimentsStore.getExperimentsList(model.nProyecto, model.nSerie, model.nEstado);
    } finally {
      isSearching.value = false;
    }
  }
};

// Crear nuevo experimento
const isCreatingExperiment = ref(false);
const crearNuevoExperimento = async () => {
  if (!model.nProyecto || !model.nSerie || !model.nEstado) {
    toast.error("Debe seleccionar Proyecto, Serie y Estado para crear el experimento");
    return;
  }

  isCreatingExperiment.value = true;
  try {
    const res = await experimentsStore.createExperiment(model.nProyecto, model.nSerie, model.nEstado);
    const data = res?.data || res;
    if (data && (data.code === 200 || data.status === 200 || data.IdsDisenosCreados || res?.status === 200)) {
      toast.success(data.message || "Experimento inicializado con éxito");
      await buscarExperimento();
    } else {
      toast.error(data?.message || "No se pudo crear el experimento");
    }
  } catch (err: any) {
    console.error("Error al crear experimento:", err);
    toast.error(err.response?.data?.message || "Error al registrar el experimento en el servidor");
  } finally {
    isCreatingExperiment.value = false;
  }
};

// Carga de tratamientos y detalles
const dataListIdDisenoF = computed(() => experimentsStore.experimentsFilter?.experimento[0]?.id_dsno_enc);
const dataListIdDisenoI = computed(() => experimentsStore.experimentsFilter?.experimento[1]?.id_dsno_enc);
const tratamientosF = computed(() => treatmentsExperimentsStore.treatmentsExperimentsFilter?.tratamientosF || []);
const tratamientosI = computed(() => treatmentsExperimentsStore.treatmentsExperimentsFilter?.tratamientosI || []);
const testigosFijosF = computed(() => treatmentsExperimentsStore.treatmentsExperimentsFilter?.testigosFijosF || []);
const testigosFijosI = computed(() => treatmentsExperimentsStore.treatmentsExperimentsFilter?.testigosFijosI || []);
const testigosMovilesF = computed(() => treatmentsExperimentsStore.treatmentsExperimentsFilter?.testigosMovilesF || []);
const testigosMovilesI = computed(() => treatmentsExperimentsStore.treatmentsExperimentsFilter?.testigosMovilesI || []);

watch(
  [dataListIdDisenoF, dataListIdDisenoI],
  async ([newDataListIdDisenoF, newDataListIdDisenoI]) => {
    if (newDataListIdDisenoF && newDataListIdDisenoI) {
      await treatmentsExperimentsStore.getTreatmentsExperimentsList(newDataListIdDisenoF, newDataListIdDisenoI);

      tableDataTreatmentsExperimentsF.value = tratamientosF.value.map((item: any) => ({
        id_dsno_det: item.id_dsno_det,
        trtmnto: item.trtmnto,
        id_dsno_enc: item.id_dsno_enc,
        no_crzmnto: item.no_crzmnto,
        pdgree: item.pdgree,
        orgen: item.orgen,
        nmro_clnes: item.nmro_clnes,
        plntlas_ttles: item.plntlas_ttles,
        selected: false
      }));

      tableDataTreatmentsExperimentsI.value = tratamientosI.value.map((item: any) => ({
        id_dsno_det: item.id_dsno_det,
        trtmnto: item.trtmnto,
        id_dsno_enc: item.id_dsno_enc,
        no_crzmnto: item.no_crzmnto,
        pdgree: item.pdgree,
        orgen: item.orgen,
        nmro_clnes: item.nmro_clnes,
        plntlas_ttles: item.plntlas_ttles,
        selected: false
      }));

      tableDataTestigosF.value = testigosFijosF.value.map((item: any) => ({
        id_dsno_det: item.id_dsno_det,
        nm_vrdad: item.nm_vrdad,
        pdgree: item.pdgree,
        orgen: item.orgen,
        selected: false
      }));

      tableDataTestigosM.value = testigosMovilesF.value.map((item: any) => ({
        id_dsno_det: item.id_dsno_det,
        nm_vrdad: item.nm_vrdad,
        pdgree: item.pdgree,
        orgen: item.orgen,
        selected: false
      }));

      tableDataTestigosFI.value = testigosFijosI.value.map((item: any) => ({
        id_dsno_det: item.id_dsno_det,
        nm_vrdad: item.nm_vrdad,
        pdgree: item.pdgree,
        orgen: item.orgen,
        selected: false
      }));

      tableDataTestigosMI.value = testigosMovilesI.value.map((item: any) => ({
        id_dsno_det: item.id_dsno_det,
        nm_vrdad: item.nm_vrdad,
        pdgree: item.pdgree,
        orgen: item.orgen,
        selected: false
      }));
    }
  },
  { immediate: true }
);

// Paginación y Selección Familias (F)
const currentPageTreatmentsExperimentsF = ref(1);
const pageSizeTreatmentsExperimentsF = ref(6);
const paginatedDataTreatmentsExperimentsF = computed(() => {
  const start = (currentPageTreatmentsExperimentsF.value - 1) * pageSizeTreatmentsExperimentsF.value;
  return tableDataTreatmentsExperimentsF.value.slice(start, start + pageSizeTreatmentsExperimentsF.value);
});
const totalPagesTreatmentsExperimentsF = computed(() => Math.ceil(tableDataTreatmentsExperimentsF.value.length / pageSizeTreatmentsExperimentsF.value));
const allSelectedTreatmentsExperimentsF = computed(
  () => tableDataTreatmentsExperimentsF.value.length > 0 && tableDataTreatmentsExperimentsF.value.every((row) => row.selected)
);
const toggleAllSelectionTreatmentsExperimentsF = () => {
  const newValue = !allSelectedTreatmentsExperimentsF.value;
  tableDataTreatmentsExperimentsF.value.forEach((row) => (row.selected = newValue));
};
const selectAllTreatmentsExperimentsF = () => tableDataTreatmentsExperimentsF.value.forEach((row) => (row.selected = true));
const deselectAllTreatmentsExperimentsF = () => tableDataTreatmentsExperimentsF.value.forEach((row) => (row.selected = false));
const addSelectedTreatmentsExperimentsF = async () => {
  const selectedRows = tableDataTreatmentsExperimentsF.value.filter((row) => row.selected);
  if (selectedRows.length === 0) {
    toast.error("No ha seleccionado tratamientos");
    return;
  }
};

// Paginación y Selección Individual (I)
const currentPageTreatmentsExperimentsI = ref(1);
const pageSizeTreatmentsExperimentsI = ref(6);
const paginatedDataTreatmentsExperimentsI = computed(() => {
  const start = (currentPageTreatmentsExperimentsI.value - 1) * pageSizeTreatmentsExperimentsI.value;
  return tableDataTreatmentsExperimentsI.value.slice(start, start + pageSizeTreatmentsExperimentsI.value);
});
const totalPagesTreatmentsExperimentsI = computed(() => Math.ceil(tableDataTreatmentsExperimentsI.value.length / pageSizeTreatmentsExperimentsI.value));
const allSelectedTreatmentsExperimentsI = computed(
  () => tableDataTreatmentsExperimentsI.value.length > 0 && tableDataTreatmentsExperimentsI.value.every((row) => row.selected)
);
const toggleAllSelectionTreatmentsExperimentsI = () => {
  const newValue = !allSelectedTreatmentsExperimentsI.value;
  tableDataTreatmentsExperimentsI.value.forEach((row) => (row.selected = newValue));
};
const selectAllTreatmentsExperimentsI = () => tableDataTreatmentsExperimentsI.value.forEach((row) => (row.selected = true));
const deselectAllTreatmentsExperimentsI = () => tableDataTreatmentsExperimentsI.value.forEach((row) => (row.selected = false));
const addSelectedTreatmentsExperimentsI = async () => {
  const selectedRows = tableDataTreatmentsExperimentsI.value.filter((row) => row.selected);
  if (selectedRows.length === 0) {
    toast.error("No ha seleccionado tratamientos");
    return;
  }
};

// Sub-pestañas
const activeTab = ref("tratamientos");
const activeTabI = ref("tratamientosI");
const setActiveTab = (tab: string) => (activeTab.value = tab);
const setActiveTabI = (tab: string) => (activeTabI.value = tab);

// Testigos F
const allSelectedTestigosF = computed(() => tableDataTestigosF.value.length > 0 && tableDataTestigosF.value.every((row) => row.selected));
const paginatedDataTestigosF = computed(() => tableDataTestigosF.value);
const toggleAllSelectionTestigosF = () => {
  const nv = !allSelectedTestigosF.value;
  tableDataTestigosF.value.forEach((r) => (r.selected = nv));
};

// Testigos M
const allSelectedTestigosM = computed(() => tableDataTestigosM.value.length > 0 && tableDataTestigosM.value.every((row) => row.selected));
const paginatedDataTestigosM = computed(() => tableDataTestigosM.value);
const toggleAllSelectionTestigosM = () => {
  const nv = !allSelectedTestigosM.value;
  tableDataTestigosM.value.forEach((r) => (r.selected = nv));
};

// Tratamientos Disponibles de Temporada
const tratamientosDisponibles = async () => {
  if (model.nTemporada) {
    if (model.cTipoEnsayo === "F") {
      model.nIdDiseno = dataListIdDisenoF.value || null;
    } else if (model.cTipoEnsayo === "I") {
      model.nIdDiseno = dataListIdDisenoI.value || null;
    }

    try {
      await treatmentsSeasonStore.getTreatmentsSeasonList(model.nTemporada, model.nMinimoPlantas, model.nTotalPlantas, model.nIdDiseno);

      tableData.value =
        treatmentsSeasonStore.treatmentsSeasonFilter?.tratamientos.map((item: any) => ({
          id: item.id_crzmnto,
          madre: item.vrdad_mdre || "—",
          padre: item.vrdad_pdre1 || "—",
          vivero: item.vivero || "—",
          pedigree: item.pdgree || `${item.vrdad_mdre || ''} x ${item.vrdad_pdre1 || ''}`,
          origen: item.orgen || "—",
          plantulasTotales: item.plntlas_ttles || 0,
          grupoMadre: item.grpo_crzmnto_mdre || "—",
          grupoPadre: item.grpo_crzmnto_pdre || "—",
          selected: false
        })) || [];
    } catch (error) {
      console.error("Error al obtener tratamientos:", error);
    }
  }
};

const currentPage = ref(1);
const pageSize = ref(6);
const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * pageSize.value;
  return tableData.value.slice(start, start + pageSize.value);
});
const totalPages = computed(() => Math.ceil(tableData.value.length / pageSize.value));
const allSelected = computed(() => tableData.value.length > 0 && tableData.value.every((row) => row.selected));
const toggleAllSelection = () => {
  const newValue = !allSelected.value;
  tableData.value.forEach((row) => (row.selected = newValue));
};
const selectAll = () => tableData.value.forEach((row) => (row.selected = true));
const deselectAll = () => tableData.value.forEach((row) => (row.selected = false));

const addSelected = async () => {
  const selectedRows = tableData.value.filter((row) => row.selected);
  if (selectedRows.length === 0) {
    toast.error("No ha seleccionado tratamientos");
    return;
  }

  const arrayIds = selectedRows.map((row) => ({
    id_crzmnto: row.id,
    plntlas_ttles: row.plantulasTotales
  }));

  await addTratamientosTemporada(arrayIds, "No");
  await tratamientosDisponibles();
};

const addTratamientosTemporada = async (arrayIds: Array<{ id_crzmnto: string; plntlas_ttles: number }>, testigo: string) => {
  try {
    const { nIdDiseno, nTipoParcela, nTotalPlantas } = model;
    
    if (arrayIds.length === 0) {
      toast.error("Debe marcar la casilla de al menos un tratamiento.");
      return;
    }
    if (!nTipoParcela) {
      toast.error("Por favor seleccione el 'Tipo de Parcela'.");
      return;
    }
    if (!nTotalPlantas || Number(nTotalPlantas) <= 0) {
      toast.error("Por favor ingrese un valor mayor a 0 en 'Total plantas siembra'.");
      return;
    }
    if (!nIdDiseno) {
      toast.error("Debe cargar primero el experimento usando el botón 'Buscar Experimento'.");
      return;
    }

    const data: DiseñosDetalles = {
      nIdDiseno: nIdDiseno,
      nTipoParcela: nTipoParcela,
      cTestigo: testigo,
      nTotalPlantas,
      arrayIds: arrayIds
    };

    const result = await addDesingsDetailsStore.SaveaddDesingsDetails(data);
    if (result) {
      toast.success("Tratamiento guardado con éxito");
    }
  } catch (error) {
    console.error("Error al guardar tratamiento:", error);
    toast.error("Error al guardar el tratamiento");
  }
};

// Limpiar campos
const limpiarCampos = () => {
  (["nPrograma", "nArea", "nProyecto", "nSerie", "nEstado"] as Array<keyof typeof model>).forEach((key) => {
    model[key] = null;
  });
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
