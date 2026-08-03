<template>
  <div class="w-full flex-col pt-5 grid px-4">
    <!-- Botón Volver -->
    <div class="w-full max-w-7xl mx-auto mb-4">
      <BackButton :to="{ name: 'mejoramiento.show' }" label="Volver a Mejoramiento" />
    </div>
    <div class="w-full max-w-7xl mx-auto">
      <h1 class="text-center font-bold text-4xl mb-6 text-violet-800">Parámetros</h1>
    </div>
    <div class="w-full max-w-10xl mx-auto bg-white shadow-lg rounded-lg p-6">
      <!-- Grid principal -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Programa/Servicio -->
        <div>
          <label class="block uppercase tracking-wide text-violet-800 text-xs font-bold mb-2" for="nPrograma"> Programa/Servicio: </label>
          <ComboBoxMultiple
            id="nPrograma"
            :data-list="dataListProgram"
            :column-value="columnValueProgram"
            :column-to-show="columnToShowProgram"
            placeholder="Seleccione ..."
            v-model:selectedData="model.nPrograma"
            class="w-full"
          />
        </div>

        <!-- Área -->
        <div>
          <label class="block uppercase tracking-wide text-violet-800 text-xs font-bold mb-2" for="nArea"> Área: </label>
          <ComboBoxMultiple
            :data-list="dataListAreas"
            :column-value="columnValueAreas"
            :column-to-show="columnToShowAreas"
            placeholder="Seleccione ..."
            v-model:selectedData="model.nArea"
            :disabled="!model.nPrograma"
            class="w-full"
          />
        </div>
      </div>

      <!-- Proyecto -->
      <div class="mt-6">
        <label class="block uppercase tracking-wide text-violet-800 text-xs font-bold mb-2" for="nProyecto"> Proyecto: </label>
        <ComboBoxMultiple
          :data-list="dataListProject"
          :column-value="columnValueProject"
          :column-to-show="columnToShowProject"
          placeholder="Seleccione ..."
          v-model:selectedData="model.nProyecto"
          :disabled="!model.nArea"
          class="w-full"
        />
      </div>

      <!-- Grid para Serie y Estado -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <!-- Serie -->
        <div>
          <label class="block uppercase tracking-wide text-violet-800 text-xs font-bold mb-2" for="nSerie"> Serie: </label>
          <ComboBoxMultiple
            :data-list="dataListSerie"
            :column-value="columnValueSerie"
            :column-to-show="columnToShowSerie"
            placeholder="Seleccione ..."
            v-model:selectedData="model.nSerie"
            :disabled="!model.nProyecto"
            class="w-full"
          />
        </div>

        <!-- Estado -->
        <div>
          <label class="block uppercase tracking-wide text-violet-800 text-xs font-bold mb-2" for="nEstado"> Estado: </label>
          <ComboBoxMultiple
            :data-list="dataListEstado"
            :column-value="columnToShowEstado"
            :column-to-show="columnToShowEstado"
            placeholder="Seleccione ..."
            v-model:selectedData="model.nEstado"
            :disabled="!model.nSerie"
            class="w-full"
          />
        </div>
      </div>
    </div>

    <div
      class="text-red-300 pl-2"
      v-if="experimentsStore.experimentsFilter != null && Object.keys(experimentsStore.experimentsFilter.experimento).length === 0"
    >
      * No hay experimentos disponibles para los parámetros seleccionados.
    </div>
    <!-- Botones -->
    <div class="mt-6 text-center">
      <button
        v-if="model.nPrograma || model.nArea || model.nProyecto"
        type="button"
        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring focus:ring-blue-400"
        @click.prevent="buscarExperimento"
      >
        Buscar Experimento
      </button>
      <button
        v-else
        type="button"
        class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:ring focus:ring-gray-400"
        @click.prevent="limpiarCampos"
      >
        Limpiar
      </button>
    </div>
    <div class="form-body" v-if="experimentsStore.experimentsFilter && Object.keys(experimentsStore.experimentsFilter.experimento).length > 0">
      <h3 class="text-xl font-bold mb-4 text-violet-800">Paso 1: Selección de Tratamientos</h3>
      <div class="w-full max-w-10xl mx-auto bg-white shadow-lg rounded-lg p-6 mb-6">
        <div class="border-b pb-4 mb-6">
          <h4 class="text-lg font-semibold text-gray-700">Configuración</h4>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- Selección de temporada -->
          <div>
            <label class="block text-sm font-medium text-violet-800 mb-2"> Seleccione temporada de cruzamientos:</label>
            <ComboBoxMultiple
              :data-list="dataListTemporadas"
              :column-value="columnValueTemporadas"
              :column-to-show="columnToShowTemporadas"
              placeholder="Seleccione ..."
              v-model:selectedData="model.nTemporada"
              :disabled="!model.nProyecto"
            />
          </div>

          <!-- Grupo cruzamiento Madre -->
          <div>
            <label class="block text-sm font-medium text-violet-800 mb-2">Grupo cruzamiento Madre:</label>
            <ComboBoxMultiple
              :data-list="dataListCruzamientoMadre"
              :column-value="columnValueCruzamientoMadre"
              :column-to-show="columnToShowCruzamientoMadre"
              placeholder="Seleccione ..."
              v-model:selectedData="model.nCruzMadre"
              :disabled="!model.nTemporada"
            />
          </div>
          <!-- Botón tratamientos disponibles -->
          <div class="flex items-center justify-center">
            <button
              type="button"
              class="bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-600"
              data-toggle="modal"
              data-target="#tablaTratamientos"
              @click.prevent="tratamientosDisponibles"
              @click="openModal"
            >
              Tratamientos disponibles
            </button>
          </div>
          <!-- Grupo cruzamiento Padre -->
          <div>
            <label class="block text-sm font-medium text-violet-800 mb-2">Grupo cruzamiento Padre:</label>
            <ComboBoxMultiple
              :data-list="dataListCruzamientoPadre"
              :column-value="columnValueCruzamientoPadre"
              :column-to-show="columnToShowCruzamientoPadre"
              placeholder="Seleccione ..."
              v-model:selectedData="model.nCruzPadre"
              :disabled="!model.nCruzMadre"
            />
          </div>
          <!-- Tipo de ensayo -->
          <div>
            <label class="block text-sm font-medium text-violet-800 mb-2">Tipo de ensayo:</label>
            <ComboBoxMultiple
              :data-list="dataListTipoEnsayo"
              :column-value="columnValueTipoEnsayo"
              :column-to-show="columnToShowTipoEnsayo"
              placeholder="Seleccione ..."
              v-model:selectedData="model.cTipoEnsayo"
              :disabled="!model.nCruzPadre"
            />
          </div>
          <!-- Tipo de parcela -->
          <div>
            <label class="block text-sm font-medium text-violet-800 mb-2">Tipo de parcela:</label>
            <ComboBoxMultiple
              :data-list="dataListTipoParcela"
              :column-value="columnValueTipoParcela"
              :column-to-show="columnToShowTipoParcela"
              placeholder="Seleccione ..."
              v-model:selectedData="model.nTipoParcela"
              :disabled="!model.cTipoEnsayo"
            />
          </div>
          <!-- Mínimo número de plantas -->
          <div>
            <label class="block text-sm font-medium text-violet-800 mb-2">Mínimo número de plantas:</label>
            <input
              type="number"
              min="1"
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              v-model="model.nMinimoPlantas"
              :disabled="!model.nTipoParcela"
            />
          </div>
          <!-- Total plantas -->
          <div>
            <label class="block text-sm font-medium text-violet-800 mb-2">
              <div v-if="model.cTipoEnsayo && model.cTipoEnsayo === 'F'">Total plantas siembra Familias:</div>
              <div v-else-if="model.cTipoEnsayo && model.cTipoEnsayo === 'I'">Total plantas siembra Individual:</div>
            </label>
            <input
              type="number"
              min="1"
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              v-model="model.nTotalPlantas"
              :disabled="!model.nTipoParcela"
            />
          </div>
        </div>
      </div>
    </div>
    <!-- Tabla de tratamientos -->
    <div
      v-if="isModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-50"
      id="tablaTratamientos"
      tabindex="-1"
      role="dialog"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <!-- Modal Body -->
          <div class="modal-body">
            <div class="p-4 bg-white rounded-lg shadow">
              <div class="modal-header">
                <button
                  type="button"
                  class="close bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded mr-1"
                  data-dismiss="modal"
                  aria-hidden="true"
                  @click="closeModal"
                >
                  &times;
                </button>
                <h4 class="modal-title text-center p-2"><strong>Tratamientos Disponibles</strong></h4>
              </div>
              <!-- Toolbar -->
              <div class="flex justify-center items-center mb-2">
                <div>
                  <button @click="selectAll" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-2 rounded mr-4">Seleccionar Todos</button>
                  <button @click="deselectAll" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-2 rounded mr-4">Deseleccionar Todos</button>
                </div>
                <a href="javascript:;" @click="addSelected" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-2 rounded mr-4">
                  <i class="fa fa-plus"></i> Añadir Selección
                </a>
              </div>
              <!-- Table -->
              <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                <table class="table-auto overflow-x-scroll w-min divide-y divide-gray-300">
                  <thead class="bg-gray-50">
                    <tr class="bg-gray-100">
                      <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                        <input type="checkbox" :checked="allSelected" @click="toggleAllSelection" />
                      </th>
                      <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500 hidden">ID</th>
                      <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Pedigree</th>
                      <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Origen</th>
                      <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Plántulas</th>
                      <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Grupo cruz. madre</th>
                      <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Grupo cruz. padre</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200 bg-white">
                    <tr v-for="row in paginatedData" :key="row.id" class="hover:bg-gray-50">
                      <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-800 sm:pl-6">
                        <input type="checkbox" v-model="row.selected" />
                      </td>
                      <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-800 sm:pl-6 hidden">
                        {{ row.id }}
                      </td>
                      <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-800 sm:pl-6">
                        {{ row.pedigree }}
                      </td>
                      <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-800 sm:pl-6">
                        {{ row.origen }}
                      </td>
                      <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-800 sm:pl-6">
                        {{ row.plantulasTotales }}
                      </td>
                      <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-800 sm:pl-6">
                        {{ row.grupoMadre }}
                      </td>
                      <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-800 sm:pl-6">
                        {{ row.grupoPadre }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- Controles de paginación -->
              <div class="flex justify-between items-center mt-4">
                <button
                  @click="currentPage > 1 && currentPage--"
                  class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
                  :disabled="currentPage === 1"
                >
                  Anterior
                </button>
                <span>Página {{ currentPage }} de {{ totalPages }}</span>
                <button
                  @click="currentPage < totalPages && currentPage++"
                  class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
                  :disabled="currentPage === totalPages"
                >
                  Siguiente
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Tabla de tratamientos Familias -->
    <div
      class="modal fade mb-6"
      id="tablaTratamientosFamilias"
      tabindex="-1"
      role="dialog"
      aria-hidden="true"
      v-if="experimentsStore.experimentsFilter && Object.keys(experimentsStore.experimentsFilter.experimento).length > 0"
    >
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-body">
            <div class="p-4 bg-white rounded-lg shadow">
              <!-- Modal Header -->
              <div class="modal-header">
                <h4 class="modal-title p-2 text-lg font-bold">Tratamientos Familias</h4>
              </div>
              <!-- Tabs -->
              <ul class="flex border-b mb-4">
                <li class="mr-2">
                  <a
                    href="javascript:;"
                    @click="setActiveTab('tratamientos')"
                    :class="[
                      'inline-block py-2 px-4 text-blue-500 hover:text-blue-700 border-b-2',
                      activeTab === 'tratamientos' ? 'border-blue-500' : 'border-transparent'
                    ]"
                  >
                    Tratamientos
                  </a>
                </li>
                <li class="mr-2">
                  <a
                    href="javascript:;"
                    @click="setActiveTab('subparcelas')"
                    :class="[
                      'inline-block py-2 px-4 text-blue-500 hover:text-blue-700 border-b-2',
                      activeTab === 'subparcelas' ? 'border-blue-500' : 'border-transparent'
                    ]"
                  >
                    Subparcelas
                  </a>
                </li>
                <li class="mr-2">
                  <a
                    href="javascript:;"
                    @click="setActiveTab('testigos')"
                    :class="[
                      'inline-block py-2 px-4 text-blue-500 hover:text-blue-700 border-b-2',
                      activeTab === 'testigos' ? 'border-blue-500' : 'border-transparent'
                    ]"
                  >
                    Testigos
                  </a>
                </li>
                <li class="mr-2">
                  <a
                    href="javascript:;"
                    @click="setActiveTab('testigosMoviles')"
                    :class="[
                      'inline-block py-2 px-4 text-blue-500 hover:text-blue-700 border-b-2',
                      activeTab === 'testigosMoviles' ? 'border-blue-500' : 'border-transparent'
                    ]"
                  >
                    Testigos Móviles
                  </a>
                </li>
              </ul>
              <!-- Toolbar -->
              <div v-show="activeTab === 'tratamientos'">
                <div class="flex justify-between items-center mb-4">
                  <div class="flex space-x-4">
                    <button @click="selectAllTreatmentsExperimentsF" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                      Seleccionar Todos
                    </button>
                    <button @click="deselectAllTreatmentsExperimentsF" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                      Deseleccionar Todos
                    </button>
                    <button @click="addSelectedTreatmentsExperimentsF" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                      <i class="fa fa-plus"></i> Añadir Selección
                    </button>
                  </div>
                </div>
                <!-- Table -->
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                  <table class="table-auto w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                      <tr class="bg-gray-100">
                        <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                          <input type="checkbox" :checked="allSelectedTreatmentsExperimentsF" @click="toggleAllSelectionTreatmentsExperimentsF" />
                        </th>
                        <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Familia</th>
                        <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Pedigree</th>
                        <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Origen</th>
                        <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">No. Plantas</th>
                        <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Plantas Almacenadas</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                      <tr v-for="row in paginatedDataTreatmentsExperimentsF" :key="row.id_dsno_det" class="hover:bg-gray-50">
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-800 sm:pl-6">
                          <input type="checkbox" v-model="row.selected" />
                        </td>
                        <td class="whitespace-nowrap py-4 px-3 text-sm text-gray-800">{{ row.no_crzmnto }}</td>
                        <td class="whitespace-nowrap py-4 px-3 text-sm text-gray-800">{{ row.pdgree }}</td>
                        <td class="whitespace-nowrap py-4 px-3 text-sm text-gray-800">{{ row.orgen }}</td>
                        <td class="whitespace-nowrap py-4 px-3 text-sm text-gray-800">{{ row.nmro_clnes }}</td>
                        <td class="whitespace-nowrap py-4 px-3 text-sm text-gray-800">{{ row.plntlas_ttles }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <!-- Pagination Controls -->
                <div class="flex justify-between items-center mt-4">
                  <button
                    @click="currentPageTreatmentsExperimentsF > 1 && currentPageTreatmentsExperimentsF--"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
                    :disabled="currentPageTreatmentsExperimentsF === 1"
                  >
                    Anterior
                  </button>
                  <span>Página {{ currentPageTreatmentsExperimentsF }} de {{ totalPagesTreatmentsExperimentsF }}</span>
                  <button
                    @click="currentPageTreatmentsExperimentsF < totalPagesTreatmentsExperimentsF && currentPageTreatmentsExperimentsF++"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
                    :disabled="currentPageTreatmentsExperimentsF === totalPagesTreatmentsExperimentsF"
                  >
                    Siguiente
                  </button>
                </div>
              </div>
              <div v-show="activeTab === 'subparcelas'">
                <div>
                  <!-- Estructura para Subparcelas -->
                  <p>Contenido de Subparcelas</p>
                </div>
              </div>
              <div v-show="activeTab === 'testigos'">
                <div class="flex justify-between items-center mb-4">
                  <div class="flex space-x-4">
                    <button @click="selectAllTestigosF" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Seleccionar Todos</button>
                    <button @click="deselectAllTestigosF" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                      Deseleccionar Todos
                    </button>
                    <button @click="addSelectedTestigosF" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                      <i class="fa fa-plus"></i> Añadir Selección
                    </button>
                  </div>
                </div>
                <!-- Table -->
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                  <table class="table-auto w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                      <tr class="bg-gray-100">
                        <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                          <input type="checkbox" :checked="allSelectedTestigosF" @click="toggleAllSelectionTestigosF" />
                        </th>
                        <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Variedad</th>
                        <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Pedigree</th>
                        <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Origen</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                      <tr v-for="row in paginatedDataTestigosF" :key="row.id_dsno_det" class="hover:bg-gray-50">
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-800 sm:pl-6">
                          <input type="checkbox" v-model="row.selected" />
                        </td>
                        <td class="whitespace-nowrap py-4 px-3 text-sm text-gray-800">{{ row.nm_vrdad }}</td>
                        <td class="whitespace-nowrap py-4 px-3 text-sm text-gray-800">{{ row.pdgree }}</td>
                        <td class="whitespace-nowrap py-4 px-3 text-sm text-gray-800">{{ row.orgen }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <!-- Pagination Controls -->
                <div class="flex justify-between items-center mt-4">
                  <button
                    @click="currentPageTestigosF > 1 && currentPageTestigosF--"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
                    :disabled="currentPageTestigosF === 1"
                  >
                    Anterior
                  </button>
                  <span>Página {{ currentPageTestigosF }} de {{ totalPagesTestigosF }}</span>
                  <button
                    @click="currentPageTestigosF < totalPagesTestigosF && currentPageTestigosF++"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
                    :disabled="currentPageTestigosF === totalPagesTestigosF"
                  >
                    Siguiente
                  </button>
                </div>
              </div>
              <div v-show="activeTab === 'testigosMoviles'">
                <div class="flex justify-between items-center mb-4">
                  <div class="flex space-x-4">
                    <button @click="selectAllTestigosM" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Seleccionar Todos</button>
                    <button @click="deselectAllTestigosM" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                      Deseleccionar Todos
                    </button>
                    <button @click="addSelectedTestigosM" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                      <i class="fa fa-plus"></i> Añadir Selección
                    </button>
                  </div>
                </div>
                <!-- Table -->
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                  <table class="table-auto w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                      <tr class="bg-gray-100">
                        <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                          <input type="checkbox" :checked="allSelectedTestigosM" @click="toggleAllSelectionTestigosM" />
                        </th>
                        <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Variedad</th>
                        <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Pedigree</th>
                        <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Origen</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                      <tr v-for="row in paginatedDataTestigosM" :key="row.id_dsno_det" class="hover:bg-gray-50">
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-800 sm:pl-6">
                          <input type="checkbox" v-model="row.selected" />
                        </td>
                        <td class="whitespace-nowrap py-4 px-3 text-sm text-gray-800">{{ row.nm_vrdad }}</td>
                        <td class="whitespace-nowrap py-4 px-3 text-sm text-gray-800">{{ row.pdgree }}</td>
                        <td class="whitespace-nowrap py-4 px-3 text-sm text-gray-800">{{ row.orgen }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <!-- Pagination Controls -->
                <div class="flex justify-between items-center mt-4">
                  <button
                    @click="currentPageTestigosM > 1 && currentPageTestigosM--"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
                    :disabled="currentPageTestigosM === 1"
                  >
                    Anterior
                  </button>
                  <span>Página {{ currentPageTestigosM }} de {{ totalPagesTestigosM }}</span>
                  <button
                    @click="currentPageTestigosM < totalPagesTestigosM && currentPageTestigosM++"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
                    :disabled="currentPageTestigosM === totalPagesTestigosM"
                  >
                    Siguiente
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabla de tratamientos Individual -->
    <div
      class="modal fade mb-6"
      id="tablaTratamientosIndividual"
      tabindex="-1"
      role="dialog"
      aria-hidden="true"
      v-if="experimentsStore.experimentsFilter && Object.keys(experimentsStore.experimentsFilter.experimento).length > 0"
    >
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-body">
            <div class="p-4 bg-white rounded-lg shadow">
              <!-- Modal Header -->
              <div class="modal-header">
                <h4 class="modal-title p-2 text-lg font-bold">Tratamientos Individual</h4>
              </div>
              <!-- Tabs -->
              <ul class="flex border-b mb-4">
                <li class="mr-2">
                  <a
                    href="javascript:;"
                    @click="setActiveTabI('tratamientosI')"
                    :class="[
                      'inline-block py-2 px-4 text-blue-500 hover:text-blue-700 border-b-2',
                      activeTab === 'tratamientos' ? 'border-blue-500' : 'border-transparent'
                    ]"
                  >
                    Tratamientos
                  </a>
                </li>
                <li class="mr-2">
                  <a
                    href="javascript:;"
                    @click="setActiveTabI('subparcelasI')"
                    :class="[
                      'inline-block py-2 px-4 text-blue-500 hover:text-blue-700 border-b-2',
                      activeTab === 'subparcelas' ? 'border-blue-500' : 'border-transparent'
                    ]"
                  >
                    Subparcelas
                  </a>
                </li>
                <li class="mr-2">
                  <a
                    href="javascript:;"
                    @click="setActiveTabI('testigosI')"
                    :class="[
                      'inline-block py-2 px-4 text-blue-500 hover:text-blue-700 border-b-2',
                      activeTab === 'testigos' ? 'border-blue-500' : 'border-transparent'
                    ]"
                  >
                    Testigos
                  </a>
                </li>
                <li class="mr-2">
                  <a
                    href="javascript:;"
                    @click="setActiveTabI('testigosMovilesI')"
                    :class="[
                      'inline-block py-2 px-4 text-blue-500 hover:text-blue-700 border-b-2',
                      activeTab === 'testigosMoviles' ? 'border-blue-500' : 'border-transparent'
                    ]"
                  >
                    Testigos Móviles
                  </a>
                </li>
              </ul>
              <!-- Toolbar -->
              <div v-show="activeTabI === 'tratamientosI'">
                <div class="flex justify-between items-center mb-2">
                  <div>
                    <button @click="selectAllTreatmentsExperimentsI" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-2 rounded mr-4">
                      Seleccionar Todos
                    </button>
                    <button @click="deselectAllTreatmentsExperimentsI" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-2 rounded mr-4">
                      Deseleccionar Todos
                    </button>
                  </div>
                  <a
                    href="javascript:;"
                    @click="addSelectedTreatmentsExperimentsI"
                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-2 rounded mr-4"
                  >
                    <i class="fa fa-plus"></i> Añadir Selección
                  </a>
                </div>
                <!-- Table -->
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                  <table class="table-auto overflow-x-scroll w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                      <tr class="bg-gray-100">
                        <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                          <input type="checkbox" :checked="allSelectedTreatmentsExperimentsI" @click="toggleAllSelectionTreatmentsExperimentsI" />
                        </th>
                        <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Familia</th>
                        <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Pedigree</th>
                        <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Origen</th>
                        <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">No. Plantas</th>
                        <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Plantas Almacenadas</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                      <tr v-for="row in paginatedDataTreatmentsExperimentsI" :key="row.id_dsno_det" class="hover:bg-gray-50">
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-800 sm:pl-6">
                          <input type="checkbox" v-model="row.selected" />
                        </td>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-800 sm:pl-6 hidden">
                          {{ row.id_dsno_det }}
                        </td>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-800 sm:pl-6 hidden">
                          {{ row.trtmnto }}
                        </td>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-800 sm:pl-6 hidden">
                          {{ row.id_dsno_enc }}
                        </td>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-800 sm:pl-6">
                          {{ row.no_crzmnto }}
                        </td>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-800 sm:pl-6">
                          {{ row.pdgree }}
                        </td>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-800 sm:pl-6">
                          {{ row.orgen }}
                        </td>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-800 sm:pl-6">
                          {{ row.nmro_clnes }}
                        </td>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-800 sm:pl-6">
                          {{ row.plntlas_ttles }}
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <!-- Controles de paginación -->
                <div class="flex justify-between items-center mt-4">
                  <button
                    @click="currentPageTreatmentsExperimentsI > 1 && currentPageTreatmentsExperimentsI--"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
                    :disabled="currentPageTreatmentsExperimentsI === 1"
                  >
                    Anterior
                  </button>
                  <span>Página {{ currentPageTreatmentsExperimentsI }} de {{ totalPagesTreatmentsExperimentsI }}</span>
                  <button
                    @click="currentPageTreatmentsExperimentsI < totalPagesTreatmentsExperimentsI && currentPageTreatmentsExperimentsI++"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
                    :disabled="currentPageTreatmentsExperimentsI === totalPagesTreatmentsExperimentsI"
                  >
                    Siguiente
                  </button>
                </div>
              </div>
              <div v-show="activeTabI === 'subparcelasI'">
                <div>
                  <!-- Estructura para Subparcelas -->
                  <p>Contenido de Subparcelas individuales</p>
                </div>
              </div>
              <div v-show="activeTabI === 'testigosI'">
                <div class="flex justify-between items-center mb-4">
                  <div class="flex space-x-4">
                    <button @click="selectAllTestigosFI" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Seleccionar Todos</button>
                    <button @click="deselectAllTestigosFI" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                      Deseleccionar Todos
                    </button>
                    <button @click="addSelectedTestigosFI" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                      <i class="fa fa-plus"></i> Añadir Selección
                    </button>
                  </div>
                </div>
                <!-- Table -->
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                  <table class="table-auto w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                      <tr class="bg-gray-100">
                        <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                          <input type="checkbox" :checked="allSelectedTestigosFI" @click="toggleAllSelectionTestigosFI" />
                        </th>
                        <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Variedad</th>
                        <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Pedigree</th>
                        <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Origen</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                      <tr v-for="row in paginatedDataTestigosFI" :key="row.id_dsno_det" class="hover:bg-gray-50">
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-800 sm:pl-6">
                          <input type="checkbox" v-model="row.selected" />
                        </td>
                        <td class="whitespace-nowrap py-4 px-3 text-sm text-gray-800">{{ row.nm_vrdad }}</td>
                        <td class="whitespace-nowrap py-4 px-3 text-sm text-gray-800">{{ row.pdgree }}</td>
                        <td class="whitespace-nowrap py-4 px-3 text-sm text-gray-800">{{ row.orgen }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <!-- Pagination Controls -->
                <div class="flex justify-between items-center mt-4">
                  <button
                    @click="currentPageTestigosFI > 1 && currentPageTestigosFI--"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
                    :disabled="currentPageTestigosFI === 1"
                  >
                    Anterior
                  </button>
                  <span>Página {{ currentPageTestigosFI }} de {{ totalPagesTestigosFI }}</span>
                  <button
                    @click="currentPageTestigosFI < totalPagesTestigosFI && currentPageTestigosFI++"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
                    :disabled="currentPageTestigosFI === totalPagesTestigosFI"
                  >
                    Siguiente
                  </button>
                </div>
              </div>
              <div v-show="activeTabI === 'testigosMovilesI'">
                <div class="flex justify-between items-center mb-4">
                  <div class="flex space-x-4">
                    <button @click="selectAllTestigosMI" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Seleccionar Todos</button>
                    <button @click="deselectAllTestigosMI" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                      Deseleccionar Todos
                    </button>
                    <button @click="addSelectedTestigosMI" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                      <i class="fa fa-plus"></i> Añadir Selección
                    </button>
                  </div>
                </div>
                <!-- Table -->
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                  <table class="table-auto w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                      <tr class="bg-gray-100">
                        <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                          <input type="checkbox" :checked="allSelectedTestigosMI" @click="toggleAllSelectionTestigosMI" />
                        </th>
                        <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Variedad</th>
                        <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Pedigree</th>
                        <th class="cursor-pointer px-3 py-1 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Origen</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                      <tr v-for="row in paginatedDataTestigosMI" :key="row.id_dsno_det" class="hover:bg-gray-50">
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-800 sm:pl-6">
                          <input type="checkbox" v-model="row.selected" />
                        </td>
                        <td class="whitespace-nowrap py-4 px-3 text-sm text-gray-800">{{ row.nm_vrdad }}</td>
                        <td class="whitespace-nowrap py-4 px-3 text-sm text-gray-800">{{ row.pdgree }}</td>
                        <td class="whitespace-nowrap py-4 px-3 text-sm text-gray-800">{{ row.orgen }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <!-- Pagination Controls -->
                <div class="flex justify-between items-center mt-4">
                  <button
                    @click="currentPageTestigosMI > 1 && currentPageTestigosMI--"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
                    :disabled="currentPageTestigosMI === 1"
                  >
                    Anterior
                  </button>
                  <span>Página {{ currentPageTestigosMI }} de {{ totalPagesTestigosMI }}</span>
                  <button
                    @click="currentPageTestigosMI < totalPagesTestigosMI && currentPageTestigosMI++"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
                    :disabled="currentPageTestigosMI === totalPagesTestigosMI"
                  >
                    Siguiente
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-dialog modal-lg"></div>
    </div>
    <div v-if="experimentsStore.experimentsFilter && Object.keys(experimentsStore.experimentsFilter.experimento).length > 0">
      <h3 class="text-xl font-bold mb-4 text-violet-800">Paso 2: Definición Diseño Estadístico</h3>
      <div class="w-full max-w-10xl mx-auto bg-white shadow-lg rounded-lg p-6 mb-6">
        <div class="border-b pb-4 mb-6">
          <h4 class="text-lg font-semibold text-gray-700">Ensayo Familias</h4>
        </div>

        <!-- Diseño Experimental -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-violet-800 mb-2">Diseño Experimental:</label>
          <ComboBoxMultiple
            :data-list="dataListDisenoExp"
            :column-value="columnValueTemporadas"
            :column-to-show="columnToShowTemporadas"
            placeholder="Seleccione ..."
            v-model:selectedData="model.nDisenoExpF"
            :disabled="!model.nProyecto"
            class="w-full"
          />
        </div>

        <!-- Grid para valores numéricos -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Localidades -->
          <div>
            <label class="block text-sm font-medium text-violet-800 mb-2">Localidades:</label>
            <input
              type="number"
              min="0"
              v-model="model.nLocalidadesF"
              class="w-full p-2 border rounded border-gray-300 shadow-md"
              :disabled="!model.nDisenoExpF"
            />
          </div>
          <!-- Repeticiones -->
          <div>
            <label class="block text-sm font-medium text-violet-800 mb-2">Repeticiones:</label>
            <input
              type="number"
              min="0"
              v-model="model.nRepeticionesF"
              class="w-full p-2 border rounde border-gray-300 shadow-md"
              :disabled="!model.nLocalidadesF"
            />
          </div>
        </div>

        <!-- Grid para otros valores -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
          <div>
            <label class="block text-sm font-medium text-violet-800 mb-2">Tratamientos:</label>
            <input type="number" v-model="model.nTratamientoF" class="w-full p-2 border rounded border-gray-300 shadow-md" />
          </div>
          <div>
            <label class="block text-sm font-medium text-violet-800 mb-2">Testigos:</label>
            <input type="number" v-model="model.nTestigosF" class="w-full p-2 border rounded border-gray-300 shadow-md" />
          </div>
          <div>
            <label class="block text-sm font-medium text-violet-800 mb-2">Testigos Móviles:</label>
            <input type="number" v-model="model.nTestigosMovilF" class="w-full p-2 border rounded border-gray-300 shadow-md" />
          </div>
          <div>
            <label class="block text-sm font-medium text-violet-800 mb-2">Parcela Principal:</label>
            <input type="number" v-model="model.nParcelaPpalF" class="w-full p-2 border rounded border-gray-300 shadow-md" />
          </div>
        </div>

        <!-- Descripción -->
        <div class="mt-4">
          <label class="block text-sm font-medium text-violet-800 mb-2">Descripción:</label>
          <textarea
            v-model="model.cDescripcionF"
            class="w-full p-2 border rounded border-gray-300 shadow-md"
            rows="3"
            :disabled="!model.nDisenoExpF"
          ></textarea>
        </div>

        <!-- Botón de Guardar -->
        <div class="text-center mt-6">
          <button class="bg-violet-800 text-white px-4 py-2 rounded">Actualizar Diseño</button>
        </div>
      </div>
      <div class="w-full max-w-10xl mx-auto bg-white shadow-lg rounded-lg p-6">
        <div class="border-b pb-4 mb-6">
          <h4 class="text-lg font-semibold text-gray-700">Ensayo Individual</h4>
        </div>

        <!-- Diseño Experimental -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-violet-800 mb-2">Diseño Experimental:</label>
          <ComboBoxMultiple
            :data-list="dataListDisenoExp"
            :column-value="columnValueTemporadas"
            :column-to-show="columnToShowTemporadas"
            placeholder="Seleccione ..."
            v-model:selectedData="model.nDisenoExpI"
            :disabled="!model.nProyecto"
            class="w-full"
          />
        </div>

        <!-- Grid para valores numéricos -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Localidades -->
          <div>
            <label class="block text-sm font-medium text-violet-800 mb-2">Localidades:</label>
            <input
              type="number"
              min="0"
              v-model="model.nLocalidadesI"
              class="w-full p-2 border rounded border-gray-300 shadow-md"
              :disabled="!model.nDisenoExpI"
            />
          </div>
          <!-- Repeticiones -->
          <div>
            <label class="block text-sm font-medium text-violet-800 mb-2">Repeticiones:</label>
            <input
              type="number"
              min="0"
              v-model="model.nRepeticionesI"
              class="w-full p-2 border rounded border-gray-300 shadow-md"
              :disabled="!model.nLocalidadesI"
            />
          </div>
        </div>

        <!-- Grid para otros valores -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
          <div>
            <label class="block text-sm font-medium text-violet-800 mb-2">Tratamientos:</label>
            <input type="number" v-model="model.nTratamientoI" class="w-full p-2 border rounded border-gray-300 shadow-md" />
          </div>
          <div>
            <label class="block text-sm font-medium text-violet-800 mb-2">Testigos:</label>
            <input type="number" v-model="model.nTestigosI" class="w-full p-2 border rounded border-gray-300 shadow-md" />
          </div>
          <div>
            <label class="block text-sm font-medium text-violet-800 mb-2">Testigos Móviles:</label>
            <input type="number" v-model="model.nTestigosMovilI" class="w-full p-2 border rounded border-gray-300 shadow-md" />
          </div>
          <div>
            <label class="block text-sm font-medium text-violet-800 mb-2">Parcela Principal:</label>
            <input type="number" v-model="model.nParcelaPpalI" class="w-full p-2 border rounded border-gray-300 shadow-md" />
          </div>
        </div>

        <!-- Descripción -->
        <div class="mt-4">
          <label class="block text-sm font-medium text-violet-800 mb-2">Descripción:</label>
          <textarea
            v-model="model.cDescripcionI"
            class="w-full p-2 border rounded border-gray-300 shadow-md"
            rows="3"
            :disabled="!model.nDisenoExpI"
          ></textarea>
        </div>

        <!-- Botón de Guardar -->
        <div class="text-center mt-6">
          <button class="bg-violet-800 text-white px-4 py-2 rounded">Actualizar Diseño</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, onMounted, watch, computed, ref } from "vue";
import BackButton from "@/components/BackButton.vue";
import { useSearchParametersStore } from "@/stores/parametersexperiments";
import { useAreasProgramStore } from "@/stores/areasprogram";
import { useProjectsAreaStore } from "@/stores/projectsarea";
import { useExperimentsStore } from "@/stores/experiments";
import { useTreatmentsSeasonStore } from "@/stores/treatmentsseason";
import { useTreatmentsExperimentsStore } from "@/stores/treatmentsexperiments";
import { useAddDesingsDetailsStore } from "@/stores/adddesingsdetails";
import ComboBoxMultiple from "@/components/ComboBoxMultiple.vue";
import { useToast } from "vue-toastification";
import { useMainStore } from "@/stores/main";
import type { DiseñosDetalles } from "../../../services/types";

const toast = useToast();
const mainStore = useMainStore();
const error = computed(() => mainStore.error);
const searchParametersStore = useSearchParametersStore();
const areasProgramStore = useAreasProgramStore();
const projectsAreaStore = useProjectsAreaStore();
const experimentsStore = useExperimentsStore();
const treatmentsSeasonStore = useTreatmentsSeasonStore();
const treatmentsExperimentsStore = useTreatmentsExperimentsStore();
const addDesingsDetailsStore = useAddDesingsDetailsStore();

// type TipoEnsayo = { id: string; text: string } | null;

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
// const columnValueEstado = "id";
const columnToShowEstado = "text";

// const dataListExperiments = computed(() => experimentsStore.experimentsFilter?.experimento || []);

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

const isModalOpen = ref(false);
const openModal = (tableData: any) => {
  tableData.value;
  isModalOpen.value = true;
};
// Cerrar modal
const closeModal = () => {
  isModalOpen.value = false;
};

// const dataListTreamentsExperiments = computed(() => treatmentsExperimentsStore.treatmentsExperimentsFilter || []);
// const dataListTreamentsSeason = computed(() => treatmentsSeasonStore.treatmentsSeasonFilter || []);

onMounted(async () => {
  try {
    await searchParametersStore.getSearchParametersResult();
    console.log("Datos iniciales cargados:", searchParametersStore.searchParameters);
  } catch (error) {
    console.error("Error al cargar los datos iniciales:", error);
  }
  tratamientosDisponibles();
});

// Watch para filtrar las áreas cuando cambia el programa seleccionado
watch(
  () => model.nPrograma,
  async (newProgram) => {
    if (newProgram) {
      model.nArea = null;
      model.nProyecto = null;
      await areasProgramStore.getAreasProgramList(newProgram);
      console.log("Áreas actualizadas:", areasProgramStore.areasProgramFilter);
    }
  },
  { immediate: true } // Opcional: Ejecuta el `watch` al inicio
);

// Watch para filtrar los proyectos cuando cambia el área seleccionada
watch(
  () => model.nArea,
  async (newArea) => {
    if (newArea) {
      model.nProyecto = null;
      await projectsAreaStore.getProjectsAreaList(newArea);
      console.log("Proyectos actualizados:", projectsAreaStore.projectsAreaFilter);
    }
  },
  { immediate: true } // Opcional: Ejecuta el `watch` al inicio
);

// Función que se llama cuando se presiona el botón de "Buscar Experimento"
const buscarExperimento = async () => {
  if (model.nProyecto && model.nSerie && model.nEstado) {
    // Llamada a la API para actualizar los experimentos con los parámetros seleccionados
    await experimentsStore.getExperimentsList(model.nProyecto, model.nSerie, model.nEstado);
    console.log("Experimentos actualizados:", experimentsStore.experimentsFilter);
  } else {
    console.log("Faltan parámetros para realizar la búsqueda.");
  }
};
// Watch para filtrar los TratamientosExperimentos cuando cambia el área seleccionada
const dataListIdDisenoF = computed(() => experimentsStore.experimentsFilter?.experimento[0]?.id_dsno_enc);
const dataListIdDisenoI = computed(() => experimentsStore.experimentsFilter?.experimento[1]?.id_dsno_enc);
const tratamientosF = computed(() => treatmentsExperimentsStore.treatmentsExperimentsFilter?.tratamientosF || []);
const tratamientosI = computed(() => treatmentsExperimentsStore.treatmentsExperimentsFilter?.tratamientosI || []);
const testigosFijosF = computed(() => treatmentsExperimentsStore.treatmentsExperimentsFilter?.testigosFijosF || []);
const testigosFijosI = computed(() => treatmentsExperimentsStore.treatmentsExperimentsFilter?.testigosFijosI || []);
const testigosMovilesF = computed(() => treatmentsExperimentsStore.treatmentsExperimentsFilter?.testigosMovilesF || []);
const testigosMovilesI = computed(() => treatmentsExperimentsStore.treatmentsExperimentsFilter?.testigosMovilesI || []);
// const experimentoF = computed(() => treatmentsExperimentsStore.treatmentsExperimentsFilter?.experimentoF || []);
// const experimentoI = computed(() => treatmentsExperimentsStore.treatmentsExperimentsFilter?.experimentoI || []);

// Watch para observar cambios en dataListIdDisenoF y dataListIdDisenoI
watch(
  [dataListIdDisenoF, dataListIdDisenoI],
  async ([newDataListIdDisenoF, newDataListIdDisenoI]) => {
    if (newDataListIdDisenoF && newDataListIdDisenoI) {
      // Llama al método del store para obtener los tratamientos
      await treatmentsExperimentsStore.getTreatmentsExperimentsList(newDataListIdDisenoF, newDataListIdDisenoI);
      console.log("TratamientosExperimentos:", treatmentsExperimentsStore.treatmentsExperimentsFilter);

      // Actualiza los datos para la tabla de tratamientos
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

      // Actualiza los datos para la tabla de testigos
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
  { immediate: true } // Ejecutar el watcher inmediatamente al montar
);

// // Métodos para seleccionar/deseleccionar tratamientos
// // Computed para la selección global
const currentPageTreatmentsExperimentsF = ref(1); // Página inicial
const pageSizeTreatmentsExperimentsF = ref(6); // Tamaño de página (filas por página)

// Computed para obtener los datos paginados
const paginatedDataTreatmentsExperimentsF = computed(() => {
  const start = (currentPageTreatmentsExperimentsF.value - 1) * pageSizeTreatmentsExperimentsF.value;
  const end = start + pageSizeTreatmentsExperimentsF.value;
  return tableDataTreatmentsExperimentsF.value.slice(start, end);
});

// Total de páginas
const totalPagesTreatmentsExperimentsF = computed(() => Math.ceil(tableDataTreatmentsExperimentsF.value.length / pageSizeTreatmentsExperimentsF.value));

const allSelectedTreatmentsExperimentsF = computed(() => tableDataTreatmentsExperimentsF.value.every((row) => row.selected));

// Métodos para manejo de selección
const toggleAllSelectionTreatmentsExperimentsF = () => {
  const newValue = !allSelectedTreatmentsExperimentsF.value;
  tableDataTreatmentsExperimentsF.value.forEach((row) => (row.selected = newValue));
};
const selectAllTreatmentsExperimentsF = () => {
  tableDataTreatmentsExperimentsF.value.forEach((row) => (row.selected = true));
};
const deselectAllTreatmentsExperimentsF = () => {
  tableDataTreatmentsExperimentsF.value.forEach((row) => (row.selected = false));
};
const addSelectedTreatmentsExperimentsF = async () => {
  const selectedRows = tableDataTreatmentsExperimentsF.value.filter((row) => row.selected);
  if (selectedRows.length === 0) {
    console.log("error", "No ha seleccionado tratamientos");
    return;
  }

  // const arrayIds = [];
  const arrayIdsTreatmentsExperimentsF = selectedRows.map((row) => ({
    id_detalle: row.id_dsno_det,
    id_crzmnto: row.trtmnto,
    nro_plntlas: row.nmro_clnes
  }));
};

const currentPageTreatmentsExperimentsI = ref(1); // Página inicial
const pageSizeTreatmentsExperimentsI = ref(6); // Tamaño de página (filas por página)

// Computed para obtener los datos paginados
const paginatedDataTreatmentsExperimentsI = computed(() => {
  const start = (currentPageTreatmentsExperimentsI.value - 1) * pageSizeTreatmentsExperimentsI.value;
  const end = start + pageSizeTreatmentsExperimentsI.value;
  return tableDataTreatmentsExperimentsI.value.slice(start, end);
});

// Total de páginas
const totalPagesTreatmentsExperimentsI = computed(() => Math.ceil(tableDataTreatmentsExperimentsI.value.length / pageSizeTreatmentsExperimentsI.value));

const allSelectedTreatmentsExperimentsI = computed(() => tableDataTreatmentsExperimentsI.value.every((row) => row.selected));

// Métodos para manejo de selección
const toggleAllSelectionTreatmentsExperimentsI = () => {
  const newValue = !allSelectedTreatmentsExperimentsI.value;
  tableDataTreatmentsExperimentsI.value.forEach((row) => (row.selected = newValue));
};
const selectAllTreatmentsExperimentsI = () => {
  tableDataTreatmentsExperimentsI.value.forEach((row) => (row.selected = true));
};
const deselectAllTreatmentsExperimentsI = () => {
  tableDataTreatmentsExperimentsI.value.forEach((row) => (row.selected = false));
};
const addSelectedTreatmentsExperimentsI = async () => {
  const selectedRows = tableDataTreatmentsExperimentsI.value.filter((row) => row.selected);
  if (selectedRows.length === 0) {
    console.log("error", "No ha seleccionado tratamientos");
    return;
  }

  // const arrayIds = [];
  const arrayIdsTreatmentsExperimentsI = selectedRows.map((row) => ({
    id_detalle: row.id_dsno_det,
    id_crzmnto: row.trtmnto,
    nro_plntlas: row.nmro_clnes
  }));
};

const activeTab = ref("");
const activeTabI = ref("");
const tratamientosData = ref<any[]>([]);
const subparcelasData = ref<any[]>([]);
const testigosData = ref<any[]>([]);
const testigosMovilesData = ref<any[]>([]);
const tratamientosDataI = ref<any[]>([]);
const subparcelasDataI = ref<any[]>([]);
const testigosDataI = ref<any[]>([]);
const testigosMovilesDataI = ref<any[]>([]);
// const tratamientosData = paginatedDataTreatmentsExperimentsF;

// Función para cambiar la pestaña activa
const setActiveTab = (tab: any) => {
  activeTab.value = tab;
};
const setActiveTabI = (tab: any) => {
  activeTabI.value = tab;
};
// Función para seleccionar todos los elementos
const selectAlls = (tab: any) => {
  const data = getData(tab);
  data.forEach((row) => (row.selected = true));
};
// Función para deseleccionar todos los elementos
const deselectAlls = (tab: any) => {
  const data = getData(tab);
  data.forEach((row) => (row.selected = false));
};
const selectAllsI = (tab: any) => {
  const data = getDataI(tab);
  data.forEach((row) => (row.selected = true));
};
// Función para deseleccionar todos los elementos
const deselectAllsI = (tab: any) => {
  const data = getDataI(tab);
  data.forEach((row) => (row.selected = false));
};
// Función para añadir selecciones
// Función para obtener los datos basados en la pestaña
const getData = (tab: any) => {
  switch (tab) {
    case "tratamientos":
      return tratamientosData.value;
    case "subparcelas":
      return subparcelasData.value;
    case "testigos":
      return testigosData.value;
    case "testigosMoviles":
      return testigosMovilesData.value;
    default:
      return [];
  }
};
const getDataI = (tab: any) => {
  switch (tab) {
    case "tratamientosI":
      return tratamientosDataI.value;
    case "subparcelasI":
      return subparcelasDataI.value;
    case "testigosI":
      return testigosDataI.value;
    case "testigosMovilesI":
      return testigosMovilesDataI.value;
    default:
      return [];
  }
};
const allSelectedTestigosF = computed(() => tableDataTestigosF.value.every((row) => row.selected));
const currentPageTestigosF = ref(1); // Página inicial
const pageSizeTestigosF = ref(6); // Tamaño de página (filas por página)

// Computed para obtener los datos paginados
const paginatedDataTestigosF = computed(() => {
  const start = (currentPageTestigosF.value - 1) * pageSizeTestigosF.value;
  const end = start + pageSizeTestigosF.value;
  return tableDataTestigosF.value.slice(start, end);
});

// Total de páginas
const totalPagesTestigosF = computed(() => Math.ceil(tableDataTestigosF.value.length / pageSizeTestigosF.value));
// Métodos para manejo de selección
const toggleAllSelectionTestigosF = () => {
  const newValue = !allSelectedTestigosF.value;
  tableDataTestigosF.value.forEach((row) => (row.selected = newValue));
};
const selectAllTestigosF = () => {
  tableDataTestigosF.value.forEach((row) => (row.selected = true));
};
const deselectAllTestigosF = () => {
  tableDataTestigosF.value.forEach((row) => (row.selected = false));
};
const addSelectedTestigosF = async () => {
  const selectedRows = tableDataTestigosF.value.filter((row) => row.selected);
  if (selectedRows.length === 0) {
    console.log("error", "No ha seleccionado tratamientos");
    return;
  }

  // const arrayIds = [];
  const arrayIdsTestigosF = selectedRows.map((row) => ({
    id_detalle: row.id_dsno_det
  }));
  // Realiza las acciones correspondientes con los seleccionados
  // await removeDetalle(nIdDisenoI, arrayIds);
  await tratamientosExperimentos(); // Refresca los datos
};

const allSelectedTestigosM = computed(() => tableDataTestigosM.value.every((row) => row.selected));
const currentPageTestigosM = ref(1); // Página inicial
const pageSizeTestigosM = ref(6); // Tamaño de página (filas por página)

// Computed para obtener los datos paginados
const paginatedDataTestigosM = computed(() => {
  const start = (currentPageTestigosM.value - 1) * pageSizeTestigosM.value;
  const end = start + pageSizeTestigosM.value;
  return tableDataTestigosM.value.slice(start, end);
});

// Total de páginas
const totalPagesTestigosM = computed(() => Math.ceil(tableDataTestigosM.value.length / pageSizeTestigosM.value));
// Métodos para manejo de selección
const toggleAllSelectionTestigosM = () => {
  const newValue = !allSelectedTestigosM.value;
  tableDataTestigosM.value.forEach((row) => (row.selected = newValue));
};
const selectAllTestigosM = () => {
  tableDataTestigosM.value.forEach((row) => (row.selected = true));
};
const deselectAllTestigosM = () => {
  tableDataTestigosM.value.forEach((row) => (row.selected = false));
};
const addSelectedTestigosM = async () => {
  const selectedRows = tableDataTestigosM.value.filter((row) => row.selected);
  if (selectedRows.length === 0) {
    console.log("error", "No ha seleccionado tratamientos");
    return;
  }

  // const arrayIds = [];
  const arrayIdsTestigosF = selectedRows.map((row) => ({
    id_detalle: row.id_dsno_det
  }));
  // Realiza las acciones correspondientes con los seleccionados
  // await removeDetalle(nIdDisenoI, arrayIds);
  await tratamientosExperimentos(); // Refresca los datos
};

const allSelectedTestigosFI = computed(() => tableDataTestigosFI.value.every((row) => row.selected));
const currentPageTestigosFI = ref(1); // Página inicial
const pageSizeTestigosFI = ref(6); // Tamaño de página (filas por página)

// Computed para obtener los datos paginados
const paginatedDataTestigosFI = computed(() => {
  const start = (currentPageTestigosFI.value - 1) * pageSizeTestigosFI.value;
  const end = start + pageSizeTestigosFI.value;
  return tableDataTestigosFI.value.slice(start, end);
});

// Total de páginas
const totalPagesTestigosFI = computed(() => Math.ceil(tableDataTestigosFI.value.length / pageSizeTestigosFI.value));
// Métodos para manejo de selección
const toggleAllSelectionTestigosFI = () => {
  const newValue = !allSelectedTestigosFI.value;
  tableDataTestigosFI.value.forEach((row) => (row.selected = newValue));
};
const selectAllTestigosFI = () => {
  tableDataTestigosFI.value.forEach((row) => (row.selected = true));
};
const deselectAllTestigosFI = () => {
  tableDataTestigosFI.value.forEach((row) => (row.selected = false));
};
const addSelectedTestigosFI = async () => {
  const selectedRows = tableDataTestigosFI.value.filter((row) => row.selected);
  if (selectedRows.length === 0) {
    console.log("error", "No ha seleccionado tratamientos");
    return;
  }

  // const arrayIds = [];
  const arrayIdsTestigosFI = selectedRows.map((row) => ({
    id_detalle: row.id_dsno_det
  }));
  // Realiza las acciones correspondientes con los seleccionados
  // await removeDetalle(nIdDisenoI, arrayIds);
  await tratamientosExperimentos(); // Refresca los datos
};
const allSelectedTestigosMI = computed(() => tableDataTestigosMI.value.every((row) => row.selected));
const currentPageTestigosMI = ref(1); // Página inicial
const pageSizeTestigosMI = ref(6); // Tamaño de página (filas por página)

// Computed para obtener los datos paginados
const paginatedDataTestigosMI = computed(() => {
  const start = (currentPageTestigosMI.value - 1) * pageSizeTestigosMI.value;
  const end = start + pageSizeTestigosMI.value;
  return tableDataTestigosMI.value.slice(start, end);
});

// Total de páginas
const totalPagesTestigosMI = computed(() => Math.ceil(tableDataTestigosMI.value.length / pageSizeTestigosMI.value));
// Métodos para manejo de selección
const toggleAllSelectionTestigosMI = () => {
  const newValue = !allSelectedTestigosMI.value;
  tableDataTestigosMI.value.forEach((row) => (row.selected = newValue));
};
const selectAllTestigosMI = () => {
  tableDataTestigosMI.value.forEach((row) => (row.selected = true));
};
const deselectAllTestigosMI = () => {
  tableDataTestigosMI.value.forEach((row) => (row.selected = false));
};
const addSelectedTestigosMI = async () => {
  const selectedRows = tableDataTestigosMI.value.filter((row) => row.selected);
  if (selectedRows.length === 0) {
    console.log("error", "No ha seleccionado tratamientos");
    return;
  }

  // const arrayIds = [];
  const arrayIdsTestigosMI = selectedRows.map((row) => ({
    id_detalle: row.id_dsno_det
  }));
  // Realiza las acciones correspondientes con los seleccionados
  // await removeDetalle(nIdDisenoI, arrayIds);
  await tratamientosExperimentos(); // Refresca los datos
};

// Watch para filtrar los TratamientosTemporada cuando cambia el área seleccionada

const tratamientosDisponibles = async () => {
  if (model.nTemporada) {
    if (model.cTipoEnsayo && model.cTipoEnsayo === "F") {
      model.nIdDiseno = dataListIdDisenoF.value || null;
    } else if (model.cTipoEnsayo && model.cTipoEnsayo == "I") {
      model.nIdDiseno = dataListIdDisenoI.value || null;
    }

    console.log(model.nIdDiseno);
    try {
      // Llamada a la API para actualizar los experimentos con los parámetros seleccionados
      await treatmentsSeasonStore.getTreatmentsSeasonList(model.nTemporada, model.nMinimoPlantas, model.nTotalPlantas, model.nIdDiseno);
      console.log("TratamientosTemporada:", treatmentsSeasonStore.treatmentsSeasonFilter);

      // Muestra los datos en una tabla simple utilizando Vue
      tableData.value = treatmentsSeasonStore.treatmentsSeasonFilter?.tratamientos.map((item: any) => ({
        id: item.id_crzmnto,
        pedigree: item.pdgree,
        origen: item.orgen,
        plantulasTotales: item.plntlas_ttles,
        grupoMadre: item.grpo_crzmnto_mdre,
        grupoPadre: item.grpo_crzmnto_pdre,
        selected: false
      }));
      console.log("Datos formateados para la tabla:", tableData.value);
    } catch (error) {
      console.error("Error al obtener tratamientos:", error);
    }
  } else {
    console.log("Faltan parámetros para realizar la búsqueda.");
  }
};
const currentPage = ref(1); // Página inicial
const pageSize = ref(6); // Tamaño de página (filas por página)

// Computed para obtener los datos paginados
const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * pageSize.value;
  const end = start + pageSize.value;
  return tableData.value.slice(start, end);
});

// Total de páginas
const totalPages = computed(() => Math.ceil(tableData.value.length / pageSize.value));

// Métodos para seleccionar/deseleccionar tratamientos
// Computed para la selección global
const allSelected = computed(() => tableData.value.every((row) => row.selected));

// Métodos para manejo de selección
const toggleAllSelection = () => {
  const newValue = !allSelected.value;
  tableData.value.forEach((row) => (row.selected = newValue));
};
const selectAll = () => {
  tableData.value.forEach((row) => (row.selected = true));
};
const deselectAll = () => {
  tableData.value.forEach((row) => (row.selected = false));
};
const addSelected = async () => {
  const selectedRows = tableData.value.filter((row) => row.selected);
  if (selectedRows.length === 0) {
    console.log("error", "No ha seleccionado tratamientos");
    return;
  }

  const arrayIds = selectedRows.map((row) => ({
    id_crzmnto: row.id,
    plntlas_ttles: row.plantulasTotales
  }));

  console.log("IDs seleccionados:", arrayIds);

  // Realiza las acciones correspondientes con los seleccionados
  await addTratamientosTemporada(arrayIds, "No");
  await tratamientosDisponibles(); // Refresca los datos
};

const addTratamientosTemporada = async (arrayIds: Array<{ id_crzmnto: string; plntlas_ttles: number }>, testigo: string) => {
  try {
    // Valida que los campos requeridos estén presentes
    const { nIdDiseno, nTipoParcela, nTotalPlantas } = model;

    if (!nIdDiseno || !nTipoParcela || !nTotalPlantas || arrayIds.length === 0 || !testigo) {
      toast.error("Todos los campos son requeridos");
      return;
    }

    // Crea el payload para la API
    const data: DiseñosDetalles = {
      nIdDiseno: nIdDiseno,
      nTipoParcela: nTipoParcela,
      cTestigo: testigo,
      nTotalPlantas,
      arrayIds: arrayIds
    };

    console.log("Payload a enviar:", data);

    // Llama al store para guardar
    const result = await addDesingsDetailsStore.SaveaddDesingsDetails(data);

    if (result) {
      toast.success("Tratamiento guardado con éxito");
      model.tipoTabla = "temp";

      if (model.tipoTabla === "disp") {
        model.listRegistros = [];
        model.selectedRegistro = [];
        model.nPlantulasDisponibles = 0;
        model.nPlantulasTratamiento = "";
        toast.success("Registro guardado con éxito");
      }
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
@media (max-width: 768px) {
  .w-full.md\:w-auto {
    width: 100% !important;
  }
}
</style>
