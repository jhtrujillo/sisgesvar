<template>
  <div class="container mx-auto p-6 max-w-7xl">
    <div class="mb-6">
      <div class="mb-4">
        <router-link
          :to="{ name: 'siembra_campo_viveros.show' }"
          class="inline-flex items-center px-5 py-2.5 text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-800 rounded-full shadow-sm transition-all duration-200"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
          </svg>
          Volver a Viveros
        </router-link>
      </div>
      <h1 class="text-2xl font-bold text-slate-800">
        {{ isEditing ? "Editar Vivero" : "Registrar Vivero" }}
      </h1>
    </div>

    <div v-if="isLoadingInfo" class="flex flex-col items-center justify-center py-20 bg-white shadow-md rounded px-8">
      <svg class="animate-spin h-10 w-10 text-cenicana mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path
          class="opacity-75"
          fill="currentColor"
          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
        ></path>
      </svg>
      <p class="text-gray-600 font-medium">Cargando información del vivero...</p>
    </div>

    <template v-else>
      <form @submit.prevent="submitForm" class="space-y-6">
        <!-- CARD 1: IDENTIFICACIÓN Y GENERALES -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-5">
          <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
            <div class="p-1.5 bg-slate-100 text-slate-700 rounded-lg">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">Información General del Vivero</h3>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Identificador Único -->
            <div>
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" for="identificador_unico">ID Vivero</label>
              <input
                v-model="form.identificador_unico"
                class="w-full bg-slate-100 border border-slate-200 text-slate-500 text-xs font-semibold rounded-xl px-3.5 py-3 outline-none cursor-not-allowed shadow-inner"
                id="identificador_unico"
                type="text"
                placeholder="Generado automáticamente por el sistema"
                disabled
              />
            </div>

            <!-- Fecha Siembra -->
            <div>
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" for="fecha_siembra">Fecha de Siembra / Corte <span class="text-red-500">*</span></label>
              <input
                v-model="form.fecha_siembra"
                required
                class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-3 focus:bg-white focus:ring-4 focus:ring-cenicana/10 focus:border-cenicana transition-all outline-none shadow-sm"
                id="fecha_siembra"
                type="date"
              />
            </div>

            <!-- Temporada Floración -->
            <div>
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" for="temporada_floracion">Temporada de cruzamientos</label>
              <input
                v-model="form.temporada_floracion"
                class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-3 focus:bg-white focus:ring-4 focus:ring-cenicana/10 focus:border-cenicana transition-all outline-none shadow-sm"
                id="temporada_floracion"
                type="text"
                placeholder="Ej. Invierno 2024"
              />
            </div>



            <!-- Ambiente -->
            <div>
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" for="ambiente">Mega Ambiente</label>
              <select
                v-model="form.ambiente"
                class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-3 focus:bg-white focus:ring-4 focus:ring-cenicana/10 focus:border-cenicana transition-all outline-none shadow-sm"
                id="ambiente"
              >
                <option value="">Vacío (Sin Mega Ambiente)</option>
                <option v-for="amb in ambientes" :key="amb.id_ambnte" :value="amb.id_ambnte" v-html="amb.nm_ambnte"></option>
              </select>
            </div>

            <!-- Responsable -->
            <div class="relative md:col-span-2">
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" for="responsable_id">Responsable</label>
              <div class="relative">
                <input
                  type="text"
                  v-model="searchResponsable"
                  @focus="showResponsables = true"
                  @blur="hideResponsablesDelay"
                  placeholder="Escribe para buscar un responsable..."
                  class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-3 focus:bg-white focus:ring-4 focus:ring-cenicana/10 focus:border-cenicana transition-all outline-none shadow-sm"
                />
                <button v-if="form.responsable_id" @click="clearResponsable" type="button" class="absolute right-3.5 top-3 text-slate-400 hover:text-red-500 transition-colors">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                </button>
                <div
                  v-if="showResponsables"
                  class="absolute z-20 w-full mt-1 bg-white shadow-xl max-h-60 rounded-xl py-1 text-xs ring-1 ring-black/5 overflow-auto border border-slate-100"
                >
                  <div v-if="filteredResponsables.length === 0" class="cursor-default select-none py-2 px-3.5 text-slate-400 font-medium">
                    No se encontraron responsables
                  </div>
                  <div
                    v-for="usr in filteredResponsables"
                    :key="usr.id_usrio"
                    @mousedown="selectResponsable(usr)"
                    class="cursor-pointer select-none py-2.5 px-3.5 hover:bg-slate-50 text-slate-700 font-medium transition-colors"
                    :class="form.responsable_id === usr.id_usrio ? 'bg-emerald-50 text-cenicana font-bold border-l-2 border-cenicana' : ''"
                    v-html="usr.nmbre"
                  ></div>
                </div>
              </div>
            </div>

            <!-- Proyecto -->
            <div class="relative md:col-span-2">
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" for="proyecto_id">Proyecto (Mejoramiento)</label>
              <div class="relative">
                <textarea
                  v-model="searchProyecto"
                  @focus="showProyectos = true"
                  @blur="hideProyectosDelay"
                  placeholder="Escribe para buscar un proyecto..."
                  rows="2"
                  class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-3 focus:bg-white focus:ring-4 focus:ring-cenicana/10 focus:border-cenicana transition-all outline-none shadow-sm resize-none"
                ></textarea>
                <button v-if="form.proyecto_id" @click="clearProyecto" type="button" class="absolute right-3.5 top-3.5 text-slate-400 hover:text-red-500 transition-colors">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                </button>
                <div
                  v-if="showProyectos"
                  class="absolute z-20 w-full mt-1 bg-white shadow-xl max-h-60 rounded-xl py-1 text-xs ring-1 ring-black/5 overflow-auto border border-slate-100"
                >
                  <div v-if="filteredProyectos.length === 0" class="cursor-default select-none py-2 px-3.5 text-slate-400 font-medium">
                    No se encontraron proyectos
                  </div>
                  <div
                    v-for="pry in filteredProyectos"
                    :key="pry.id_prycto"
                    @mousedown="selectProyecto(pry)"
                    class="cursor-pointer select-none py-2.5 px-3.5 hover:bg-slate-50 text-slate-700 font-medium transition-colors"
                    :class="form.proyecto_id === pry.id_prycto ? 'bg-emerald-50 text-cenicana font-bold border-l-2 border-cenicana' : ''"
                    v-html="formatProjectName(pry)"
                  ></div>
                </div>
              </div>
            </div>

            <!-- Carácter -->
            <div class="relative md:col-span-2">
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" for="caracter_id">Carácter (Opcional)</label>
              <div class="relative">
                <input
                  type="text"
                  v-model="searchCaracter"
                  @focus="showCaracteres = true"
                  @blur="hideCaracteresDelay"
                  placeholder="Buscar o agregar..."
                  class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-3 focus:bg-white focus:ring-4 focus:ring-cenicana/10 focus:border-cenicana transition-all outline-none shadow-sm"
                  :disabled="!form.proyecto_id"
                  :class="{ 'bg-slate-100 border-slate-200 text-slate-400 cursor-not-allowed shadow-inner': !form.proyecto_id }"
                />
                <button v-if="form.caracter_id" @click="clearCaracter" type="button" class="absolute right-3.5 top-3 text-slate-400 hover:text-red-500 transition-colors">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                </button>
                <div
                  v-if="showCaracteres && form.proyecto_id"
                  class="absolute z-20 w-full mt-1 bg-white shadow-xl max-h-60 rounded-xl py-1 text-xs ring-1 ring-black/5 overflow-auto border border-slate-100"
                >
                  <div
                    v-if="searchCaracter && !exactMatchCaracter"
                    @mousedown="selectNewCaracter"
                    class="cursor-pointer select-none py-2 px-3.5 hover:bg-emerald-50 text-cenicana font-bold border-b border-slate-100 transition-colors"
                  >
                    + Agregar nuevo: "{{ searchCaracter }}"
                  </div>
                  <div v-if="filteredCaracteres.length === 0 && !searchCaracter" class="cursor-default select-none py-2 px-3.5 text-slate-400 font-medium">
                    No hay caracteres (escribe para crear)
                  </div>
                  <div
                    v-for="car in filteredCaracteres"
                    :key="car.id"
                    @mousedown="selectCaracter(car)"
                    class="cursor-pointer select-none py-2.5 px-3.5 hover:bg-slate-50 text-slate-700 font-medium transition-colors"
                    :class="form.caracter_id === car.id ? 'bg-emerald-50 text-cenicana font-bold border-l-2 border-cenicana' : ''"
                  >
                    {{ car.nombre }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- CARD 2: UBICACIÓN FÍSICA -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-5">
          <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
            <div class="p-1.5 bg-emerald-50 text-cenicana rounded-lg">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
            <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">Ubicación Física en Campo</h3>
          </div>

          <div class="grid grid-cols-1 gap-5">
            <!-- Ingenio -->
            <div>
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" for="ingenio">Ingenio</label>
              <select
                v-model="form.ingenio"
                @change="loadHaciendas(true)"
                :disabled="isEditing"
                class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-3 focus:bg-white focus:ring-4 focus:ring-cenicana/10 focus:border-cenicana transition-all outline-none shadow-sm"
                :class="{ 'bg-slate-100 border-slate-200 text-slate-500 cursor-not-allowed shadow-inner': isEditing }"
                id="ingenio"
              >
                <option value="">Seleccione un Ingenio</option>
                <option v-for="ing in ingenios" :key="ing.cd_ingnio" :value="ing.cd_ingnio" v-html="ing.nm_ingnio"></option>
              </select>
            </div>

            <!-- Hacienda -->
            <div>
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" for="hacienda">Hacienda</label>
              <select
                v-model="form.hacienda"
                :disabled="!form.ingenio || haciendas.length === 0"
                class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-3 focus:bg-white focus:ring-4 focus:ring-cenicana/10 focus:border-cenicana transition-all outline-none shadow-sm"
                :class="{ 'bg-slate-100 border-slate-200 text-slate-400 cursor-not-allowed shadow-inner': !form.ingenio || haciendas.length === 0 }"
                id="hacienda"
              >
                <option value="">Seleccione una Hacienda</option>
                <option v-for="hda in haciendas" :key="hda.cd_hcnda" :value="hda.cd_hcnda" v-html="hda.nm_hcnda"></option>
              </select>
            </div>

            <!-- Lote -->
            <div>
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" for="lote_id">Lote <span class="text-red-500">*</span></label>
              <div class="flex gap-2">
                <select
                  v-model="form.lote_id"
                  :disabled="!form.ingenio || isEditing"
                  required
                  class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-3 focus:bg-white focus:ring-4 focus:ring-cenicana/10 focus:border-cenicana transition-all outline-none shadow-sm"
                  :class="{ 'bg-slate-100 border-slate-200 text-slate-400 cursor-not-allowed shadow-inner': !form.ingenio || isEditing }"
                  id="lote_id"
                >
                  <option value="">Seleccione un Lote</option>
                  <option v-for="lote in lotes" :key="lote.id" :value="lote.id">
                    {{ lote.nombre_lote }} (Viveros: {{ lote.viveros_activos_count }}/{{ lote.capacidad_maxima }})
                  </option>
                </select>
                <button
                  v-if="isEditing"
                  type="button"
                  @click="openTrasladoModal"
                  class="inline-flex items-center justify-center px-4 py-2.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-all shadow-md shadow-blue-950/10 cursor-pointer whitespace-nowrap"
                >
                  Trasladar Lote
                </button>
              </div>
            </div>

            <!-- Vivero slot selection -->
            <div>
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" for="consecutivo_vivero_id">Vivero <span class="text-red-500">*</span></label>
              <select
                v-model="form.consecutivo_vivero_ingenio"
                :disabled="!form.lote_id || isEditing"
                required
                class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-3 focus:bg-white focus:ring-4 focus:ring-cenicana/10 focus:border-cenicana transition-all outline-none shadow-sm"
                :class="{ 'bg-slate-100 border-slate-200 text-slate-400 cursor-not-allowed shadow-inner': !form.lote_id || isEditing }"
                id="consecutivo_vivero_id"
              >
                <option value="">Seleccione Número de Vivero</option>
                <option v-for="num in availableViveroNumbers" :key="'v_num_' + num" :value="num">
                  Vivero {{ num }}
                </option>
              </select>
            </div>
          </div>

          <!-- Bitácora de Lotes/Traslados (Solo en edición) -->
          <div class="bg-slate-50/50 rounded-2xl p-4 border border-slate-100 mt-4 space-y-3" v-if="isEditing && form.historial_lotes && form.historial_lotes.length > 0">
            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Bitácora / Historial de Lotes</h4>
            <div class="overflow-x-auto rounded-xl border border-slate-100 bg-white">
              <table class="min-w-full text-xs">
                <thead class="bg-slate-55 border-b border-slate-100 text-slate-500 uppercase font-bold text-[9px] tracking-wider">
                  <tr>
                    <th class="py-2.5 px-4 text-left">Lote</th>
                    <th class="py-2.5 px-4 text-left">Fecha Ingreso</th>
                    <th class="py-2.5 px-4 text-left">Fecha Salida</th>
                    <th class="py-2.5 px-4 text-center">Estado</th>
                  </tr>
                </thead>
                <tbody class="text-slate-600 font-medium">
                  <tr v-for="hist in form.historial_lotes" :key="hist.id" class="border-b border-slate-100 last:border-0 hover:bg-slate-50/30">
                    <td class="py-2.5 px-4 font-bold text-slate-800">{{ hist.lote?.nombre_lote || 'N/A' }}</td>
                    <td class="py-2.5 px-4">{{ formatDateTime(hist.fecha_inicio) }}</td>
                    <td class="py-2.5 px-4">{{ hist.fecha_fin ? formatDateTime(hist.fecha_fin) : '-' }}</td>
                    <td class="py-2.5 px-4 text-center">
                      <span
                        class="px-2 py-0.5 rounded-full text-[9px] font-bold"
                        :class="[hist.activo ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-slate-100 text-slate-500 border border-slate-200']"
                      >
                        {{ hist.activo ? 'Actual' : 'Pasado' }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- CARD 3: ORIGEN DE SEMILLA -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-5">
          <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
            <div class="p-1.5 bg-blue-50 text-blue-600 rounded-lg">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
            </div>
            <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">Procedencia (Origen de Semilla)</h3>
          </div>

          <!-- Copiar Origen desde Vivero Existente -->
          <div class="relative">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" for="origen_vivero_select">
              Buscar Origen desde Vivero existente (Autocompleta los campos de abajo)
            </label>
            <input
              v-model="searchOrigenVivero"
              @focus="showOrigenViveros = true"
              @blur="hideOrigenViverosDelay"
              @input="showOrigenViveros = true"
              class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-3 focus:bg-white focus:ring-4 focus:ring-cenicana/10 focus:border-cenicana transition-all outline-none shadow-sm"
              id="origen_vivero_select"
              type="text"
              placeholder="Escribe para buscar viveros por ID, hacienda o suerte..."
              autocomplete="off"
            />
            <div
              v-if="showOrigenViveros"
              class="absolute z-20 w-full mt-1 bg-white shadow-xl max-h-60 rounded-xl py-1 text-xs ring-1 ring-black/5 overflow-auto border border-slate-100"
            >
              <div v-if="filteredOrigenViveros.length === 0" class="cursor-default select-none py-2 px-3.5 text-slate-400 font-medium">
                No se encontraron viveros coincidentes
              </div>
              <div
                v-for="v in filteredOrigenViveros"
                :key="v.id"
                @mousedown="selectOrigenVivero(v)"
                class="cursor-pointer select-none py-2.5 px-3.5 hover:bg-slate-50 border-b border-slate-100 last:border-0 transition-colors"
              >
                <div class="font-bold font-mono text-xs text-slate-800">{{ v.identificador_unico }}</div>
                <div class="text-[10px] text-slate-400 mt-0.5">
                  {{ getIngenioName(v.ingenio) }} - {{ v.hacienda || 'N/A' }} - {{ v.suerte || 'N/A' }}
                </div>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2">
            <!-- Origen Ingenio -->
            <div>
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" for="origen_ingenio">Ingenio <span class="text-red-500">*</span></label>
              <select
                v-model="form.origen_ingenio"
                @change="loadHaciendasOrigen(true)"
                required
                class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-3 focus:bg-white focus:ring-4 focus:ring-cenicana/10 focus:border-cenicana transition-all outline-none shadow-sm"
                id="origen_ingenio"
              >
                <option value="">Seleccione un Ingenio</option>
                <option v-for="ing in ingenios" :key="'origen_ing_' + ing.cd_ingnio" :value="ing.cd_ingnio" v-html="ing.nm_ingnio"></option>
              </select>
            </div>

            <!-- Origen Año -->
            <div>
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" for="origen_anio">Año <span class="text-red-500">*</span></label>
              <input
                v-model="form.origen_anio"
                type="number"
                required
                placeholder="Ej. 2024"
                class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-3 focus:bg-white focus:ring-4 focus:ring-cenicana/10 focus:border-cenicana transition-all outline-none shadow-sm"
                id="origen_anio"
              />
            </div>

            <!-- Origen Hacienda -->
            <div>
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" for="origen_hacienda">Hacienda <span class="text-red-500">*</span></label>
              <select
                v-model="form.origen_hacienda"
                :disabled="!form.origen_ingenio || haciendasOrigen.length === 0"
                required
                class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-3 focus:bg-white focus:ring-4 focus:ring-cenicana/10 focus:border-cenicana transition-all outline-none shadow-sm"
                :class="{ 'bg-slate-100 border-slate-200 text-slate-400 cursor-not-allowed shadow-inner': !form.origen_ingenio || haciendasOrigen.length === 0 }"
                id="origen_hacienda"
              >
                <option value="">Seleccione una Hacienda</option>
                <option v-for="hda in haciendasOrigen" :key="'origen_hda_' + hda.cd_hcnda" :value="hda.cd_hcnda" v-html="hda.nm_hcnda"></option>
              </select>
            </div>

            <!-- Origen Lote -->
            <div>
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" for="origen_lote">Lote <span class="text-red-500">*</span></label>
              <select
                v-model="form.origen_lote_id"
                :disabled="!form.origen_ingenio || lotesOrigen.length === 0"
                required
                class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-3 focus:bg-white focus:ring-4 focus:ring-cenicana/10 focus:border-cenicana transition-all outline-none shadow-sm"
                :class="{ 'bg-slate-100 border-slate-200 text-slate-400 cursor-not-allowed shadow-inner': !form.origen_ingenio || lotesOrigen.length === 0 }"
                id="origen_lote"
              >
                <option value="">Seleccione un Lote</option>
                <option v-for="l in lotesOrigen" :key="'origen_lote_' + l.id" :value="l.id">
                  {{ l.nombre_lote }}
                </option>
              </select>
            </div>

            <!-- Origen Vivero -->
            <div>
              <div class="flex justify-between items-center mb-1.5">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider" for="origen_vivero_id">
                  Vivero <span class="text-red-500">*</span>
                </label>
                <button
                  type="button"
                  @click="origenViveroManual = !origenViveroManual"
                  class="text-[9px] font-bold text-cenicana hover:text-emerald-700 transition-colors"
                >
                  {{ origenViveroManual ? 'Seleccionar de lista' : 'Escribir manualmente' }}
                </button>
              </div>

              <!-- List Select -->
              <select
                v-if="!origenViveroManual"
                v-model="form.origen_vivero_id"
                :disabled="!form.origen_lote_id || viverosOrigenOptions.length === 0"
                required
                class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-3 focus:bg-white focus:ring-4 focus:ring-cenicana/10 focus:border-cenicana transition-all outline-none shadow-sm"
                :class="{ 'bg-slate-100 border-slate-200 text-slate-400 cursor-not-allowed shadow-inner': !form.origen_lote_id || viverosOrigenOptions.length === 0 }"
                id="origen_vivero_id"
              >
                <option value="">Seleccione un Vivero</option>
                <option v-for="v in viverosOrigenOptions" :key="'origen_vivero_' + v.id" :value="v.id">
                  {{ v.nombre }} ({{ v.identificador_unico }})
                </option>
              </select>

              <!-- Manual Input -->
              <input
                v-else
                v-model="origenViveroInput"
                type="text"
                placeholder="Escriba el identificador del vivero..."
                required
                class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-3 focus:bg-white focus:ring-4 focus:ring-cenicana/10 focus:border-cenicana transition-all outline-none shadow-sm"
                id="origen_vivero_id"
              />
            </div>

            <!-- Origen Parcela -->
            <div>
              <div class="flex justify-between items-center mb-1.5">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider" for="origen_parcela_text">
                  Parcela (Opcional)
                </label>
                <button
                  type="button"
                  @click="origenParcelaManual = !origenParcelaManual"
                  class="text-[9px] font-bold text-cenicana hover:text-emerald-700 transition-colors"
                >
                  {{ origenParcelaManual ? 'Seleccionar de lista' : 'Escribir manualmente' }}
                </button>
              </div>

              <!-- List Select -->
              <select
                v-if="!origenParcelaManual"
                v-model="origenParcelaText"
                :disabled="!form.origen_vivero_id || origenParcelasOptions.length === 0"
                class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-3 focus:bg-white focus:ring-4 focus:ring-cenicana/10 focus:border-cenicana transition-all outline-none shadow-sm"
                :class="{ 'bg-slate-100 border-slate-200 text-slate-400 cursor-not-allowed shadow-inner': !form.origen_vivero_id || origenParcelasOptions.length === 0 }"
                id="origen_parcela_text"
              >
                <option value="">Seleccione una Parcela (Opcional)</option>
                <option 
                  v-for="p in origenParcelasOptions" 
                  :key="'orig_p_' + p.id" 
                  :value="p.numero_parcela"
                >
                  Parcela {{ p.numero_parcela }} ({{ p.variedad?.nm_vrdad || 'Sin variedad' }})
                </option>
              </select>

              <!-- Manual Input -->
              <input
                v-else
                v-model="origenParcelaText"
                type="text"
                placeholder="Escriba el número de parcela..."
                class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-3 focus:bg-white focus:ring-4 focus:ring-cenicana/10 focus:border-cenicana transition-all outline-none shadow-sm"
                id="origen_parcela_text"
              />
            </div>
          </div>
        </div>

        <!-- ACCIONES FORMULARIO -->
        <div class="flex items-center justify-end gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-100">
          <router-link
            :to="{ name: 'siembra_campo_viveros.show' }"
            class="px-5 py-2.5 text-xs font-bold text-slate-500 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl transition-all"
          >
            Cancelar
          </router-link>
          <button
            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 disabled:opacity-50 disabled:cursor-not-allowed rounded-xl shadow-md shadow-emerald-950/10 transition-all duration-200 cursor-pointer"
            type="submit"
            :disabled="isSubmitting"
          >
            <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ isSubmitting ? "Guardando..." : isEditing ? "Actualizar Vivero" : "Guardar Vivero" }}
          </button>
        </div>
      </form>

      <!-- Administrar Parcelas (Solo visible en edición) -->
      <div v-if="isEditing" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 border-t-4 border-cenicana">
        <h3 class="text-lg font-bold text-gray-800 mb-4 uppercase tracking-wide">Administrar Parcelas</h3>

        <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 mb-6 shadow-sm">
          <h4 class="text-sm font-bold text-slate-700 mb-4 border-b pb-2">Agregar Nueva Parcela</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 items-start">
            <div>
              <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Plot (No.)</label>
              <input
                v-model="parcelaForm.numero_parcela"
                type="number"
                class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-cenicana bg-white shadow-sm"
              />
            </div>
            <div class="relative">
              <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Variedad</label>
              <div class="relative">
                <input
                  type="text"
                  v-model="searchVariedad"
                  @focus="showVariedades = true; loadVariedadesIfNeeded()"
                  @blur="hideVariedadesDelay"
                  placeholder="Buscar variedad..."
                  class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-cenicana bg-white shadow-sm"
                />
                <button
                  v-if="parcelaForm.variedad_id"
                  @click="clearVariedad"
                  class="absolute right-2 top-2 text-gray-400 hover:text-red-500 font-bold"
                  type="button"
                  title="Limpiar selección"
                >
                  ✕
                </button>
              </div>
              <div
                v-if="showVariedades"
                class="absolute z-10 w-full mt-1 bg-white shadow-xl max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto sm:text-sm border border-slate-100"
              >
                <div v-if="filteredVariedades.length === 0" class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                  No se encontraron variedades
                </div>
                <div
                  v-for="v in filteredVariedades"
                  :key="v.id_nm_vrdad"
                  @mousedown="selectVariedad(v)"
                  class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-slate-100 transition-colors"
                  :class="parcelaForm.variedad_id === v.id_nm_vrdad ? 'bg-cenicana-50 text-cenicana-800 font-semibold' : 'text-slate-700'"
                >
                  {{ v.nm_vrdad }}
                </div>
              </div>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Carácter</label>
              <select
                v-model="parcelaForm.caracter_id"
                class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-cenicana bg-white shadow-sm"
              >
                <option value="">Seleccione...</option>
                <option v-for="c in caracteres" :key="'parc_car_' + c.id" :value="c.id">{{ c.nombre }}</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Parcela</label>
              <input
                v-model="parcelaForm.numero_parcela_origen"
                @input="updateIdPlotOrigen"
                type="number"
                placeholder="No."
                class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-cenicana bg-white shadow-sm"
              />
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-600 uppercase mb-1">ID Plot</label>
              <input
                v-model="parcelaForm.id_plot_origen"
                type="text"
                class="w-full border rounded-lg px-3 py-2 text-sm bg-slate-100 text-slate-500 cursor-not-allowed shadow-inner font-mono"
                readonly
              />
            </div>
          </div>

          <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-100">
            <button
              v-if="parcelas.length > 0"
              type="button"
              @click="deleteAllParcelas"
              class="bg-white border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 font-bold py-2.5 px-6 rounded-lg text-sm transition-colors shadow-sm flex items-center justify-center gap-2 mr-auto"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
              </svg>
              Eliminar Todas
            </button>

            <button
              type="button"
              @click="showImportWizard = true"
              class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-bold py-2.5 px-6 rounded-lg text-sm transition-colors shadow-sm flex items-center justify-center gap-2"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-emerald-600"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="17 8 12 3 7 8"></polyline>
                <line x1="12" y1="3" x2="12" y2="15"></line>
              </svg>
              Importar Excel
            </button>

            <button
              type="button"
              @click="submitParcela"
              :disabled="isSubmittingParcela || !parcelaForm.variedad_id"
              class="bg-cenicana hover:bg-cenicana-800 text-white font-bold py-2.5 px-6 rounded-lg text-sm transition-colors shadow-md disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 min-w-[180px]"
            >
              <svg v-if="!isSubmittingParcela" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
              </svg>
              {{ isSubmittingParcela ? "Agregando..." : "Agregar Parcela" }}
            </button>
          </div>
        </div>

        <!-- Tabla de parcelas -->
        <div class="flex justify-between items-center mb-3 mt-4">
          <h4 class="text-sm font-bold text-slate-700">
            Parcelas Agregadas <span class="bg-slate-200 text-slate-600 px-2 py-0.5 rounded-full text-xs ml-1">{{ parcelas.length }}</span>
          </h4>
          <div class="relative w-full max-w-xs">
            <input
              v-model="searchParcela"
              type="text"
              placeholder="Buscar parcela, variedad..."
              class="w-full pl-9 pr-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-cenicana bg-white shadow-sm"
            />
            <svg
              class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </div>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg border border-slate-200 shadow-sm">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-100 text-slate-600 uppercase text-xs">
                <th class="px-4 py-3 border-b border-slate-200">Plot</th>
                <th class="px-4 py-3 border-b border-slate-200">Variedad</th>
                <th class="px-4 py-3 border-b border-slate-200">Pedigree</th>
                <th class="px-4 py-3 border-b border-slate-200">Carácter</th>
                <th class="px-4 py-3 border-b border-slate-200">Parcela</th>
                <th class="px-4 py-3 border-b border-slate-200">ID Plot</th>
                <th class="px-4 py-3 border-b border-slate-200 text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loadingParcelas">
                <td colspan="7" class="text-center py-8 text-slate-500">
                  <div class="flex items-center justify-center space-x-2">
                    <div class="w-4 h-4 border-2 border-cenicana border-t-transparent rounded-full animate-spin"></div>
                    <span>Cargando parcelas...</span>
                  </div>
                </td>
              </tr>
              <tr v-else-if="parcelas.length === 0">
                <td colspan="7" class="text-center py-8 text-slate-500 bg-slate-50">No hay parcelas registradas en este vivero.</td>
              </tr>
              <tr v-else-if="filteredParcelas.length === 0">
                <td colspan="7" class="text-center py-8 text-slate-500 bg-slate-50">No se encontraron parcelas que coincidan con la búsqueda.</td>
              </tr>
              <template v-else>
                <tr v-for="p in paginatedParcelas" :key="p.id" class="border-b border-slate-100 transition-colors" :class="{ 'bg-cenicana/5': editingPlotId === p.id, 'hover:bg-slate-50': editingPlotId !== p.id }">
                  <td class="px-4 py-3 font-bold text-slate-800">
                    <div>{{ p.numero_parcela }}</div>
                    <div v-if="p.cortes && p.cortes.length > 0 && editingPlotId !== p.id" class="mt-1 flex flex-col gap-1 font-normal">
                      <span class="text-[9px] text-slate-400 uppercase font-bold">Cortes:</span>
                      <div
                        v-for="c in p.cortes"
                        :key="'cut_' + c.id"
                        class="inline-flex items-center gap-1 w-fit px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-50 text-blue-700 border border-blue-100 hover:bg-blue-100 transition-colors cursor-pointer"
                        title="Ver/Editar vivero hijo"
                      >
                        <router-link :to="{ name: 'vivero_editar.show', params: { id: c.id } }" class="hover:underline">
                          Corte {{ c.consecutivo_corte }}: {{ c.identificador_unico }}
                        </router-link>
                        <button
                          type="button"
                          @click.stop.prevent="confirmDeleteCorte(c.id, c.identificador_unico)"
                          class="text-red-400 hover:text-red-600 ml-0.5 transition-colors font-bold text-[10px]"
                          title="Eliminar este Corte"
                        >
                          &times;
                        </button>
                      </div>
                    </div>
                  </td>

                  <template v-if="editingPlotId === p.id">
                    <td class="px-4 py-3 min-w-[200px]">
                      <div class="relative">
                        <input
                          type="text"
                          v-model="editingPlotForm.variedad_name"
                          @focus="showEditingVariedades = true; loadVariedadesIfNeeded()"
                          @blur="hideEditingVariedadesDelay"
                          placeholder="Buscar variedad..."
                          class="w-full border border-slate-300 rounded px-2 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-cenicana bg-white shadow-sm"
                        />
                        <div
                          v-if="showEditingVariedades"
                          class="absolute z-50 w-full mt-1 bg-white shadow-xl max-h-40 rounded border border-slate-200 overflow-y-auto text-xs py-1"
                        >
                          <div v-if="filteredEditingVariedades.length === 0" class="p-2 text-slate-500">
                            No se encontraron variedades
                          </div>
                          <div
                            v-for="v in filteredEditingVariedades"
                            :key="v.id_nm_vrdad"
                            @mousedown="selectEditingVariedad(v)"
                            class="cursor-pointer p-2 hover:bg-slate-100 transition-colors"
                          >
                            {{ v.nm_vrdad }}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="px-4 py-3 text-slate-400 text-xs italic">
                      {{ variedades.find(v => v.id_nm_vrdad === editingPlotForm.variedad_id)?.pdgree || 'N/A' }}
                    </td>
                    <td class="px-4 py-3 min-w-[150px]">
                      <select
                        v-model="editingPlotForm.caracter_id"
                        class="w-full border border-slate-300 rounded px-2 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-cenicana bg-white shadow-sm"
                      >
                        <option value="">Seleccione...</option>
                        <option v-for="c in caracteres" :key="'edit_c_' + c.id" :value="c.id">{{ c.nombre }}</option>
                      </select>
                    </td>
                    <td class="px-4 py-3 min-w-[100px]">
                      <input
                        v-model="editingPlotForm.numero_parcela_origen"
                        @input="updateEditingPlotIdOrigen"
                        type="number"
                        placeholder="No."
                        class="w-full border border-slate-300 rounded px-2 py-1.5 text-xs font-bold focus:outline-none focus:ring-1 focus:ring-cenicana bg-white shadow-sm"
                      />
                    </td>
                    <td class="px-4 py-3 text-slate-600 font-mono text-xs">
                      {{ editingPlotForm.id_plot_origen || 'N/A' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                      <div class="flex items-center justify-center gap-2">
                        <button
                          type="button"
                          @click="saveEditingPlot"
                          :disabled="isSubmittingEditingPlot"
                          class="bg-cenicana text-white hover:bg-cenicana-800 p-2 rounded-lg transition-colors disabled:opacity-50"
                          title="Guardar Cambios"
                        >
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                          </svg>
                        </button>
                        <button
                          type="button"
                          @click="cancelEditingPlot"
                          class="bg-slate-100 text-slate-600 hover:bg-slate-200 p-2 rounded-lg transition-colors"
                          title="Cancelar"
                        >
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                          </svg>
                        </button>
                      </div>
                    </td>
                  </template>

                  <template v-else>
                    <td
                      class="px-4 py-3 font-bold text-cenicana hover:text-emerald-800 cursor-pointer hover:underline transition-colors"
                      @click="openVarietyProfile(p.variedad?.nm_vrdad)"
                      title="Ver hoja de vida de la variedad"
                    >
                      {{ p.variedad?.nm_vrdad }}
                    </td>
                    <td class="px-4 py-3 text-slate-600 text-xs">{{ p.variedad?.pdgree || "N/A" }}</td>
                    <td class="px-4 py-3 text-slate-600 text-xs">
                      <span v-if="p.caracter?.nombre">{{ p.caracter.nombre }}</span>
                      <span v-else-if="form.caracter_id && getCaracterGlobalNombre()" class="text-slate-400 italic" title="Heredado del Vivero">{{
                        getCaracterGlobalNombre()
                      }}</span>
                      <span v-else>N/A</span>
                    </td>
                    <td class="px-4 py-3 text-slate-600 font-bold">{{ p.numero_parcela_origen || "N/A" }}</td>
                    <td class="px-4 py-3 text-slate-600 font-mono text-xs">{{ p.id_plot_origen || "N/A" }}</td>
                    <td class="px-4 py-3 text-center">
                      <div class="flex items-center justify-center gap-2">
                        <button
                          type="button"
                          @click="startEditingPlot(p)"
                          class="text-amber-600 hover:text-amber-800 transition-colors bg-amber-50 hover:bg-amber-100 p-2 rounded-lg border border-amber-200/50"
                          title="Editar Parcela"
                        >
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                          </svg>
                        </button>

                        <button
                          type="button"
                          @click="deleteParcela(p.id)"
                          class="text-red-400 hover:text-red-600 transition-colors bg-red-50 hover:bg-red-100 p-2 rounded-lg"
                          title="Eliminar Parcela"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                            ></path>
                          </svg>
                        </button>
                      </div>
                    </td>
                  </template>
                </tr>
              </template>
            </tbody>
          </table>

          <!-- Pagination controls -->
          <div v-if="parcelas.length > 0" class="flex flex-col sm:flex-row items-center justify-between border-t border-slate-200 px-4 py-3 bg-slate-50">
            <div class="flex items-center gap-2 mb-2 sm:mb-0">
              <span class="text-sm text-slate-600">Filas por página:</span>
              <select
                v-model="rowsPerPage"
                @change="currentPage = 1"
                class="text-sm border border-slate-300 rounded px-2 py-1 bg-white focus:outline-none focus:ring-1 focus:ring-cenicana"
              >
                <option :value="10">10</option>
                <option :value="25">25</option>
                <option :value="50">50</option>
                <option :value="100">100</option>
                <option :value="-1">Todas</option>
              </select>
            </div>
            <div class="flex items-center gap-4">
              <span class="text-sm text-slate-600">
                <template v-if="Number(rowsPerPage) === -1"> 1 - {{ filteredParcelas.length }} de {{ filteredParcelas.length }} </template>
                <template v-else>
                  {{ filteredParcelas.length === 0 ? 0 : (currentPage - 1) * Number(rowsPerPage) + 1 }} -
                  {{ Math.min(currentPage * Number(rowsPerPage), filteredParcelas.length) }} de {{ filteredParcelas.length }}
                </template>
              </span>
              <div class="flex items-center gap-1">
                <button
                  @click="currentPage--"
                  :disabled="currentPage === 1"
                  class="p-1.5 rounded text-slate-500 hover:bg-slate-200 disabled:opacity-50 transition-colors"
                  title="Página Anterior"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                  </svg>
                </button>
                <button
                  @click="currentPage++"
                  :disabled="currentPage >= totalPages"
                  class="p-1.5 rounded text-slate-500 hover:bg-slate-200 disabled:opacity-50 transition-colors"
                  title="Siguiente Página"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <ViveroParcelasImportWizard
        v-if="isEditing && route.params.id"
        :show="showImportWizard"
        :variedades="variedades"
        :viveroId="route.params.id"
        :viveroIdentificador="form.identificador_unico"
        :origenParcela="form.origen_parcela"
        :consecutivoCorte="form.consecutivo_corte"
        :caracterId="form.caracter_id"
        @close="showImportWizard = false"
        @imported="loadParcelas"
      />
      <!-- Drawer de Hoja de Vida de la Variedad (Quick Drawer) -->
      <VarietyProfileDrawer v-model:isOpen="isDrawerOpen" :varietyName="selectedVarietyForDrawer" />

      <!-- Modal para Trasladar Lote -->
      <div v-if="isTrasladoModalOpen" class="fixed inset-0 flex items-center justify-center bg-slate-900/60 z-50 transition-opacity duration-300">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden border border-slate-100">
          <div class="flex justify-between items-center border-b border-slate-100 p-5 bg-slate-50">
            <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wide">Trasladar Vivero de Lote</h4>
            <button type="button" @click="closeTrasladoModal" class="text-slate-400 hover:text-slate-600 transition-colors text-2xl">&times;</button>
          </div>

          <form @submit.prevent="submitTraslado">
            <div class="p-6 space-y-4">
              <div class="p-3 bg-blue-50 text-blue-900 text-xs font-semibold rounded-lg border border-blue-100">
                Estás trasladando el vivero <strong class="font-black">{{ form.identificador_unico }}</strong> a un nuevo lote físico. El lote actual quedará liberado.
              </div>

               <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Ingenio Destino</label>
                <select
                  v-model="trasladoIngenio"
                  required
                  @change="handleTrasladoIngenioChange"
                  class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-cenicana/20 focus:border-cenicana transition-all outline-none mb-3"
                >
                  <option value="" disabled>Seleccione el ingenio...</option>
                  <option v-for="ing in ingenios" :key="'traslado_ing_' + ing.cd_ingnio" :value="ing.cd_ingnio" v-html="ing.nm_ingnio"></option>
                </select>
              </div>

              <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Hacienda Destina</label>
                <select
                  v-model="trasladoHacienda"
                  required
                  @change="handleTrasladoHaciendaChange"
                  :disabled="!trasladoIngenio"
                  class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-cenicana/20 focus:border-cenicana transition-all outline-none mb-3 disabled:opacity-50"
                >
                  <option value="" disabled>Seleccione la hacienda...</option>
                  <option v-for="hac in trasladoHaciendas" :key="'traslado_hac_' + hac.cd_hcnda" :value="hac.cd_hcnda">
                    {{ hac.nm_hcnda }}
                  </option>
                </select>
              </div>

              <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Seleccionar Lote Destino</label>
                <select
                  v-model="trasladoLoteId"
                  required
                  :disabled="!trasladoHacienda"
                  class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-cenicana/20 focus:border-cenicana transition-all outline-none disabled:opacity-50"
                >
                  <option value="" disabled>Seleccione el lote destino...</option>
                  <option v-for="lote in trasladoLotes" :key="lote.id" :value="lote.id" :disabled="lote.id === form.lote_id">
                    {{ lote.nombre_lote }} (Viveros: {{ lote.viveros_activos_count }}/{{ lote.capacidad_maxima }})
                  </option>
                </select>
              </div>
            </div>

            <div class="border-t border-slate-100 p-5 bg-slate-50 flex justify-end gap-2">
              <button
                type="button"
                @click="closeTrasladoModal"
                class="px-4 py-2.5 text-xs font-bold text-slate-500 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl transition-all"
              >
                Cancelar
              </button>
              <button
                type="submit"
                :disabled="savingTraslado"
                class="px-5 py-2.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 rounded-xl transition-all shadow-md shadow-blue-950/10 cursor-pointer"
              >
                {{ savingTraslado ? 'Trasladando...' : 'Confirmar Traslado' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
import { ref, shallowRef, onMounted, computed, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useToast } from "vue-toastification";
import dayjs from "dayjs";
import viverosServices from "@/services/viveros.services";
import varietysServices from "@/services/varietys.services";
import ViveroParcelasImportWizard from "@/components/viveros/ViveroParcelasImportWizard.vue";
import VarietyProfileDrawer from "@/components/VarietyProfileDrawer.vue";

const route = useRoute();
const router = useRouter();
const toast = useToast();

const isEditing = ref(false);
const showImportWizard = ref(false);
const isLoadingInfo = ref(false);
const form = ref({
  identificador_unico: "",
  nombre: "",
  ingenio: "",
  hacienda: "",
  suerte: "",
  fecha_siembra: "",
  numero_corte: 1,
  temporada_floracion: "",
  proyecto_id: "",
  ambiente: "",
  responsable_id: "",
  condicion: "",
  caracter_id: "",
  origen_ingenio: "",
  origen_hacienda: "",
  origen_suerte: "",
  origen_lote_id: "" as string | number,
  origen_vivero_id: "" as string | number,
  origen_anio: "" as string | number,
  origen_parcela: "",
  consecutivo_corte: null as number | null,
  es_corte: false,
  lote_id: "",
  consecutivo_vivero_ingenio: null as number | null,
  historial_lotes: [] as any[]
});

const isSubmitting = ref(false);

const searchOrigenVivero = ref("");
const showOrigenViveros = ref(false);
const allViverosList = ref<any[]>([]);
const origenParcelasOptions = ref<any[]>([]);
const viveroSeleccionadoOrigen = ref<any>(null);
const origenViveroInput = ref("");
const origenViveroManual = ref(false);
const origenParcelaManual = ref(false);
const origenParcelaText = ref("");
const origenCorteText = ref("");

const lotes = ref<any[]>([]);
const isTrasladoModalOpen = ref(false);
const trasladoLoteId = ref('');
const trasladoIngenio = ref('');
const trasladoHacienda = ref('');
const trasladoHaciendas = ref<any[]>([]);
const trasladoLotes = ref<any[]>([]);
const savingTraslado = ref(false);

const parseViveroIdToFields = (viveroId: string) => {
  const parts = viveroId.split("-");
  if (parts.length >= 4) {
    const p0 = parts[0];
    const ingenioCd = p0.slice(0, -4);
    const anio = p0.slice(-4);
    const hacienda = parts[1];
    const suerte = parts[2];
    const consecutivo = parts[3];
    const parcela = parts[4] || "";
    const corte = parts[5] || "";
    return {
      ingenio: ingenioCd,
      anio: Number(anio),
      hacienda: hacienda,
      suerte: suerte,
      consecutivo: consecutivo,
      parcel: parcela,
      cut: corte
    };
  }
  return null;
};

const availableViveroNumbers = computed(() => {
  if (!form.value.lote_id) return [];
  const selectedLote = lotes.value.find(l => l.id === form.value.lote_id);
  if (!selectedLote) return [];
  
  const capacity = selectedLote.capacidad_maxima || 5;
  const activeNumbers = allViverosList.value
    .filter(v => v.lote_id === form.value.lote_id && v.proyecto_id && (!isEditing.value || v.id !== form.value.id))
    .map(v => v.consecutivo_vivero_ingenio);
    
  const options = [];
  if (isEditing.value && form.value.consecutivo_vivero_ingenio) {
    options.push(form.value.consecutivo_vivero_ingenio);
  }
  for (let i = 1; i <= capacity; i++) {
    if (!activeNumbers.includes(i) && !options.includes(i)) {
      options.push(i);
    }
  }
  return options.sort((a, b) => a - b);
});

watch(() => form.value.lote_id, (newLoteId) => {
  const selectedLote = lotes.value.find(l => l.id === newLoteId);
  if (selectedLote) {
    form.value.suerte = selectedLote.nombre_lote;
  } else {
    form.value.suerte = "";
  }
  if (!isEditing.value) {
    form.value.consecutivo_vivero_ingenio = null;
  }
});

const lotesOrigen = ref<any[]>([]);
const loadLotesOrigen = async () => {
  if (!form.value.origen_ingenio || !form.value.origen_hacienda) {
    lotesOrigen.value = [];
    return;
  }
  try {
    const res = await viverosServices.getLotes({ 
      ingenio_codigo: form.value.origen_ingenio,
      hacienda_codigo: form.value.origen_hacienda 
    });
    lotesOrigen.value = res.data;
  } catch (error) {
    console.error("Error loading origin lotes:", error);
  }
};

watch([() => form.value.origen_ingenio, () => form.value.origen_hacienda], () => {
  if (!isEditing.value) {
    form.value.origen_lote_id = "";
  }
  loadLotesOrigen();
});

const viverosOrigenOptions = computed(() => {
  if (!form.value.origen_lote_id) return [];
  const filtered = allViverosList.value.filter(v => v.lote_id == form.value.origen_lote_id);
  return filtered.sort((a, b) => {
    const nameA = (a.nombre || "").toString().toLowerCase();
    const nameB = (b.nombre || "").toString().toLowerCase();
    return nameA.localeCompare(nameB, undefined, { numeric: true, sensitivity: "base" });
  });
});

watch(() => form.value.origen_vivero_id, (newVal) => {
  if (!origenViveroManual.value) {
    const parent = allViverosList.value.find(v => v.id == newVal);
    if (parent) {
      origenViveroInput.value = parent.identificador_unico || "";
      origenParcelasOptions.value = parent.parcelas || [];
    } else {
      origenViveroInput.value = "";
      origenParcelasOptions.value = [];
    }
  }
});

watch(origenViveroInput, (newVal) => {
  if (origenViveroManual.value) {
    const parent = allViverosList.value.find(v => v.identificador_unico === newVal);
    if (parent) {
      form.value.origen_vivero_id = parent.id;
      origenParcelasOptions.value = parent.parcelas || [];
    } else {
      form.value.origen_vivero_id = "";
      origenParcelasOptions.value = [];
    }
  }
});

watch(origenViveroManual, (isManual) => {
  if (isManual) {
    form.value.origen_vivero_id = "";
    origenParcelasOptions.value = [];
  } else {
    origenViveroInput.value = "";
  }
});

watch([origenParcelaText, origenViveroInput, () => form.value.origen_vivero_id], () => {
  if (!origenViveroManual.value && form.value.origen_vivero_id) {
    const parent = allViverosList.value.find(v => v.id == form.value.origen_vivero_id);
    if (parent) {
      if (origenParcelaText.value) {
        form.value.origen_parcela = `${parent.identificador_unico}-${origenParcelaText.value}`;
      } else {
        form.value.origen_parcela = parent.identificador_unico;
      }
      return;
    }
  }
  
  if (origenViveroInput.value) {
    if (origenParcelaText.value) {
      form.value.origen_parcela = `${origenViveroInput.value}-${origenParcelaText.value}`;
    } else {
      form.value.origen_parcela = origenViveroInput.value;
    }
  } else {
    form.value.origen_parcela = "";
  }
});

const filteredOrigenViveros = computed(() => {
  if (!searchOrigenVivero.value) return allViverosList.value;
  const q = searchOrigenVivero.value.toLowerCase();
  return allViverosList.value.filter(v =>
    (v.identificador_unico && v.identificador_unico.toLowerCase().includes(q)) ||
    (v.hacienda && v.hacienda.toLowerCase().includes(q)) ||
    (v.lote?.nombre_lote && v.lote.nombre_lote.toLowerCase().includes(q))
  );
});

const selectOrigenVivero = async (v: any) => {
  viveroSeleccionadoOrigen.value = v;
  form.value.origen_ingenio = v.ingenio || "";
  form.value.origen_anio = v.fecha_siembra ? new Date(v.fecha_siembra).getFullYear() : null;
  
  await loadHaciendasOrigen(false);
  form.value.origen_hacienda = v.hacienda || "";
  
  await loadLotesOrigen();
  form.value.origen_lote_id = v.lote_id || "";
  
  origenViveroManual.value = false;
  origenParcelaManual.value = false;
  
  // Wait a tick for computed viverosOrigenOptions to resolve
  form.value.origen_vivero_id = v.id || "";
  origenViveroInput.value = v.identificador_unico || "";
  
  origenParcelasOptions.value = v.parcelas || [];
  origenParcelaText.value = ""; // Default optional
  
  searchOrigenVivero.value = v.identificador_unico;
  showOrigenViveros.value = false;
};

const hideOrigenViverosDelay = () => {
  setTimeout(() => {
    showOrigenViveros.value = false;
  }, 200);
};

const loadAllViveros = async () => {
  try {
    const res = await viverosServices.getViveros();
    allViverosList.value = res.data;
  } catch (error) {
    console.error("Error loading viveros for selection:", error);
  }
};

const getIngenioName = (cd: string) => {
  const ing = ingenios.value.find(i => i.cd_ingnio === cd);
  return ing ? ing.nm_ingnio : cd;
};

// Drawer de variedades
const isDrawerOpen = ref(false);
const selectedVarietyForDrawer = ref("");

const openVarietyProfile = (name: any) => {
  const nameStr = String(name || "").trim();
  if (nameStr && nameStr !== "null" && nameStr !== "?") {
    selectedVarietyForDrawer.value = nameStr;
    isDrawerOpen.value = true;
  }
};

// Parcelas State
const parcelas = ref<any[]>([]);
const variedades = shallowRef<any[]>([]);
const loadingParcelas = ref(false);
const isSubmittingParcela = ref(false);
const searchVariedad = ref("");
const showVariedades = ref(false);
const editingPlotId = ref<number | null>(null);
const editingPlotForm = ref({
  id: null as number | null,
  numero_parcela: 1,
  variedad_id: "",
  variedad_name: "",
  numero_parcela_origen: "" as string | number,
  id_plot_origen: "",
  caracter_id: "" as string | number
});
const showEditingVariedades = ref(false);
const searchParcela = ref("");
const parcelaForm = ref({
  numero_parcela: 1,
  variedad_id: "",
  numero_parcela_origen: "" as string | number,
  id_plot_origen: "",
  caracter_id: "" as string | number
});

const filteredParcelas = computed(() => {
  if (!searchParcela.value) return parcelas.value;
  const q = searchParcela.value.toLowerCase();
  return parcelas.value.filter((p) => {
    const inheritedCaracter = form.value.caracter_id ? getCaracterGlobalNombre() : "";
    const activeCaracter = p.caracter?.nombre || inheritedCaracter || "N/A";

    return (
      p.numero_parcela?.toString().includes(q) ||
      p.variedad?.nm_vrdad?.toLowerCase().includes(q) ||
      p.variedad?.pdgree?.toLowerCase().includes(q) ||
      p.numero_parcela_origen?.toString().includes(q) ||
      p.id_plot_origen?.toLowerCase().includes(q) ||
      activeCaracter.toLowerCase().includes(q)
    );
  });
});

const currentPage = ref(1);
const rowsPerPage = ref(10);

watch(searchParcela, () => {
  currentPage.value = 1;
});

const paginatedParcelas = computed(() => {
  const rows = Number(rowsPerPage.value);
  if (rows === -1) return filteredParcelas.value;
  const start = (currentPage.value - 1) * rows;
  return filteredParcelas.value.slice(start, start + rows);
});

const totalPages = computed(() => {
  const rows = Number(rowsPerPage.value);
  if (rows === -1) return 1;
  return Math.ceil(filteredParcelas.value.length / rows);
});

const filteredVariedades = computed(() => {
  if (!searchVariedad.value) {
    return variedades.value.slice(0, 100);
  }
  return variedades.value.filter((v) => v.nm_vrdad.toLowerCase().includes(searchVariedad.value.toLowerCase())).slice(0, 100);
});

const getCaracterGlobalNombre = () => {
  if (!form.value.caracter_id) return "";
  const c = caracteres.value.find((car) => car.id == form.value.caracter_id);
  return c ? c.nombre : "";
};

const selectVariedad = (v: any) => {
  parcelaForm.value.variedad_id = v.id_nm_vrdad;
  searchVariedad.value = v.nm_vrdad;
  showVariedades.value = false;
};

const clearVariedad = () => {
  parcelaForm.value.variedad_id = "";
  searchVariedad.value = "";
  showVariedades.value = true;
};

const hideVariedadesDelay = () => {
  setTimeout(() => {
    showVariedades.value = false;
  }, 200);
};

const updateIdPlotOrigen = () => {
  if (parcelaForm.value.numero_parcela_origen) {
    const parts = (form.value.identificador_unico || "").split("-");
    const baseId = parts.slice(0, 4).join("-");
    parcelaForm.value.id_plot_origen = `${baseId}-${parcelaForm.value.numero_parcela_origen}`;
  } else {
    parcelaForm.value.id_plot_origen = "";
  }
};
const ingenios = ref<any[]>([]);
const haciendas = ref<any[]>([]);
const suertes = ref<any[]>([]);
const haciendasOrigen = ref<any[]>([]);
const suertesOrigen = ref<any[]>([]);
const proyectos = ref<any[]>([]);
const responsables = ref<any[]>([]);
const ambientes = ref<any[]>([]);

const searchProyecto = ref("");
const showProyectos = ref(false);

const formatProjectName = (pry: any) => {
  let code = pry.cd_cntble;
  if (code && code.length === 6) {
    code = `${code.substring(0, 2)}.${code.substring(2, 4)}.${code.substring(4, 6)}`;
  }
  return code ? `${code} - ${pry.nm_prycto}` : pry.nm_prycto;
};

const filteredProyectos = computed(() => {
  if (searchProyecto.value === "") {
    return proyectos.value;
  }
  return proyectos.value.filter((pry) => {
    return formatProjectName(pry).toLowerCase().includes(searchProyecto.value.toLowerCase());
  });
});

const selectProyecto = (pry: any) => {
  form.value.proyecto_id = pry.id_prycto;
  searchProyecto.value = formatProjectName(pry);
  showProyectos.value = false;
  // Reset caracter and load new ones
  form.value.caracter_id = "";
  searchCaracter.value = "";
  loadCaracteres(pry.id_prycto);
};

const clearProyecto = () => {
  form.value.proyecto_id = "";
  searchProyecto.value = "";
  showProyectos.value = true;
  form.value.caracter_id = "";
  searchCaracter.value = "";
  caracteres.value = [];
};

const hideProyectosDelay = () => {
  setTimeout(() => {
    showProyectos.value = false;
  }, 200);
};

// Caracter Logic
const caracteres = ref<any[]>([]);
const searchCaracter = ref("");
const showCaracteres = ref(false);

const filteredCaracteres = computed(() => {
  if (searchCaracter.value === "") {
    return caracteres.value;
  }
  return caracteres.value.filter((car) => {
    return car.nombre.toLowerCase().includes(searchCaracter.value.toLowerCase());
  });
});

const exactMatchCaracter = computed(() => {
  return caracteres.value.some((car) => car.nombre.toLowerCase() === searchCaracter.value.trim().toLowerCase());
});

const loadCaracteres = async (proyecto_id: string | number) => {
  try {
    const res = await viverosServices.getCaracteresPorProyecto(proyecto_id);
    caracteres.value = res.data;
  } catch (error) {
    console.error("Error fetching caracteres:", error);
  }
};

const selectCaracter = (car: any) => {
  form.value.caracter_id = car.id;
  searchCaracter.value = car.nombre;
  showCaracteres.value = false;
};

const selectNewCaracter = async () => {
  if (!searchCaracter.value || !form.value.proyecto_id) return;
  try {
    const res = await viverosServices.createCaracter(form.value.proyecto_id, {
      nombre: searchCaracter.value
    });
    const newCar = res.data;
    caracteres.value.push(newCar);
    selectCaracter(newCar);
  } catch (error) {
    console.error("Error creating caracter:", error);
    toast.error("No se pudo crear el caracter.");
  }
};

const clearCaracter = () => {
  form.value.caracter_id = "";
  searchCaracter.value = "";
  showCaracteres.value = true;
};

const hideCaracteresDelay = () => {
  setTimeout(() => {
    showCaracteres.value = false;
  }, 200);
};

const searchResponsable = ref("");
const showResponsables = ref(false);

const filteredResponsables = computed(() => {
  if (searchResponsable.value === "") {
    return responsables.value;
  }
  return responsables.value.filter((usr) => {
    return usr.nmbre.toLowerCase().includes(searchResponsable.value.toLowerCase());
  });
});

const selectResponsable = (usr: any) => {
  form.value.responsable_id = usr.id_usrio;
  searchResponsable.value = usr.nmbre;
  showResponsables.value = false;
};

const clearResponsable = () => {
  form.value.responsable_id = "";
  searchResponsable.value = "";
  showResponsables.value = true;
};

const hideResponsablesDelay = () => {
  setTimeout(() => {
    showResponsables.value = false;
  }, 200);
};

const loadIngenios = async () => {
  try {
    const res = await viverosServices.getIngenios();
    ingenios.value = res.data;
  } catch (error) {
    console.error("Error fetching ingenios:", error);
  }
};

const loadHaciendas = async (reset = false) => {
  if (reset) {
    form.value.hacienda = "";
    form.value.suerte = "";
    suertes.value = [];
  }
  haciendas.value = [];
  if (!form.value.ingenio) return;

  try {
    const res = await viverosServices.getHaciendas(form.value.ingenio);
    haciendas.value = res.data;
  } catch (error) {
    console.error("Error fetching haciendas:", error);
  }
};

const loadSuertes = async (reset = false) => {
  if (reset) form.value.suerte = "";
  if (!form.value.hacienda) {
    suertes.value = [];
    return;
  }
  try {
    const res = await viverosServices.getSuertes(form.value.hacienda);
    suertes.value = res.data;
  } catch (error) {
    console.error("Error fetching suertes:", error);
  }
};

const loadHaciendasOrigen = async (reset = false) => {
  if (reset) {
    form.value.origen_hacienda = "";
    form.value.origen_suerte = "";
    suertesOrigen.value = [];
  }
  haciendasOrigen.value = [];
  if (!form.value.origen_ingenio) return;

  try {
    const res = await viverosServices.getHaciendas(form.value.origen_ingenio);
    haciendasOrigen.value = res.data;
  } catch (error) {
    console.error("Error fetching haciendas de origen:", error);
  }
};

const loadSuertesOrigen = async (reset = false) => {
  if (reset) form.value.origen_suerte = "";
  if (!form.value.origen_hacienda) {
    suertesOrigen.value = [];
    return;
  }
  try {
    const res = await viverosServices.getSuertes(form.value.origen_hacienda);
    suertesOrigen.value = res.data;
  } catch (error) {
    console.error("Error fetching suertes de origen:", error);
  }
};

const loadProyectos = async () => {
  try {
    const res = await viverosServices.getProyectos();
    proyectos.value = res.data;
  } catch (error) {
    console.error("Error fetching proyectos:", error);
  }
};

const loadResponsables = async () => {
  try {
    const res = await viverosServices.getResponsables();
    responsables.value = res.data;
  } catch (error) {
    console.error("Error fetching responsables:", error);
  }
};

const loadAmbientes = async () => {
  try {
    const res = await viverosServices.getAmbientes();
    ambientes.value = res.data;
  } catch (error) {
    console.error("Error fetching ambientes:", error);
  }
};


const submitForm = async () => {
  isSubmitting.value = true;
  try {
    if (isEditing.value) {
      await viverosServices.updateVivero(route.params.id as string, form.value);
      toast.success("Vivero actualizado correctamente");
    } else {
      await viverosServices.createVivero(form.value);
      toast.success("Vivero registrado correctamente");
    }
    router.push({ name: "siembra_campo_viveros.show" });
  } catch (error) {
    console.error("Error saving vivero:", error);
    toast.error("Error al guardar el vivero");
  } finally {
    isSubmitting.value = false;
  }
};

// Parcelas Logic
const loadVariedades = async () => {
  try {
    const res = await varietysServices.getVarietysList();
    variedades.value = res.data;
  } catch (error) {
    console.error("Error fetching variedades:", error);
  }
};

const loadVariedadesIfNeeded = async () => {
  if (variedades.value.length === 0) {
    await loadVariedades();
  }
};

const filteredEditingVariedades = computed(() => {
  if (!editingPlotForm.value.variedad_name) return variedades.value;
  return variedades.value.filter((v) =>
    v.nm_vrdad.toLowerCase().includes(editingPlotForm.value.variedad_name.toLowerCase())
  );
});

const hideEditingVariedadesDelay = () => {
  setTimeout(() => {
    showEditingVariedades.value = false;
  }, 200);
};

const selectEditingVariedad = (v: any) => {
  editingPlotForm.value.variedad_id = v.id_nm_vrdad;
  editingPlotForm.value.variedad_name = v.nm_vrdad;
  showEditingVariedades.value = false;
};

const startEditingPlot = (p: any) => {
  editingPlotId.value = p.id;
  editingPlotForm.value = {
    id: p.id,
    numero_parcela: p.numero_parcela,
    variedad_id: p.variedad_id || "",
    variedad_name: p.variedad?.nm_vrdad || "",
    numero_parcela_origen: p.numero_parcela_origen || "",
    id_plot_origen: p.id_plot_origen || "",
    caracter_id: p.caracter_id || ""
  };
  updateEditingPlotIdOrigen();
};

const cancelEditingPlot = () => {
  editingPlotId.value = null;
};

const updateEditingPlotIdOrigen = () => {
  if (editingPlotForm.value.numero_parcela_origen) {
    const parts = (form.value.identificador_unico || "").split("-");
    const baseId = parts.slice(0, 4).join("-");
    editingPlotForm.value.id_plot_origen = `${baseId}-${editingPlotForm.value.numero_parcela_origen}`;
  } else {
    editingPlotForm.value.id_plot_origen = "";
  }
};

const isSubmittingEditingPlot = ref(false);
const saveEditingPlot = async () => {
  if (!editingPlotForm.value.variedad_id) {
    toast.error("Debe seleccionar una variedad");
    return;
  }
  isSubmittingEditingPlot.value = true;
  try {
    const payload = {
      numero_parcela: editingPlotForm.value.numero_parcela,
      variedad_id: editingPlotForm.value.variedad_id,
      numero_parcela_origen: editingPlotForm.value.numero_parcela_origen || null,
      id_plot_origen: editingPlotForm.value.id_plot_origen || null,
      caracter_id: editingPlotForm.value.caracter_id || null
    };
    await viverosServices.updateParcela(route.params.id as string, editingPlotForm.value.id as any, payload);
    toast.success("Parcela actualizada correctamente");
    editingPlotId.value = null;
    await loadParcelas();
  } catch (error: any) {
    console.error("Error updating parcela:", error);
    const msg = error.response?.data?.message || "Error al actualizar la parcela";
    toast.error(msg);
  } finally {
    isSubmittingEditingPlot.value = false;
  }
};

const loadParcelas = async () => {
  if (!isEditing.value) return;
  loadingParcelas.value = true;
  try {
    const res = await viverosServices.getParcelas(route.params.id as string);
    parcelas.value = res.data;
    // Auto-calculate next plot number
    if (parcelas.value.length > 0) {
      const maxPlot = Math.max(...parcelas.value.map((p) => p.numero_parcela));
      parcelaForm.value.numero_parcela = maxPlot + 1;
    } else {
      parcelaForm.value.numero_parcela = 1;
    }

    // Auto-extract origin parcel number and calculate id_plot_origen if it's a cut nursery
    if (form.value.origen_parcela && form.value.origen_parcela.split("-").length > 3) {
      const parts = form.value.origen_parcela.split("-");
      const lastPart = parts[parts.length - 1];
      if (lastPart && !isNaN(Number(lastPart))) {
        parcelaForm.value.numero_parcela_origen = Number(lastPart);
      }
      updateIdPlotOrigen();
    }
  } catch (error) {
    console.error("Error fetching parcelas:", error);
    toast.error("Error al cargar parcelas");
  } finally {
    loadingParcelas.value = false;
  }
};

const submitParcela = async () => {
  if (!parcelaForm.value.variedad_id) {
    toast.warning("Debe seleccionar una variedad");
    return;
  }
  isSubmittingParcela.value = true;
  try {
    await viverosServices.addParcela(route.params.id as string, parcelaForm.value);
    toast.success("Parcela agregada exitosamente");
    parcelaForm.value.variedad_id = "";
    searchVariedad.value = "";
    parcelaForm.value.numero_parcela_origen = "";
    parcelaForm.value.id_plot_origen = "";
    parcelaForm.value.caracter_id = "";
    await loadParcelas();
  } catch (error: any) {
    console.error("Error al agregar parcela:", error);
    toast.error(error.response?.data?.message || "Error al agregar la parcela");
  } finally {
    isSubmittingParcela.value = false;
  }
};

const registrarCorteParcela = (p: any) => {
  console.log("registrarCorteParcela clicked for parcel:", p);
  try {
    const currentVivero = form.value;
    
    // Extraer año de forma segura sin depender de new Date()
    let origenAnio = new Date().getFullYear();
    if (currentVivero.fecha_siembra) {
      const dateStr = String(currentVivero.fecha_siembra);
      const yearMatch = dateStr.match(/^(\d{4})/);
      if (yearMatch && yearMatch[1]) {
        origenAnio = parseInt(yearMatch[1], 10);
      }
    }

    const parcelLabel = p.numero_parcela_origen || p.numero_parcela;
    const idPlot = `${currentVivero.identificador_unico}-${parcelLabel}`;
    
    router.push({
      name: "vivero_nuevo.show",
      query: {
        origen_ingenio: currentVivero.ingenio || "",
        origen_hacienda: currentVivero.hacienda || "",
        origen_suerte: currentVivero.suerte || "",
        origen_anio: origenAnio,
        origen_parcela: idPlot,
        ingenio: currentVivero.ingenio || "",
        hacienda: currentVivero.hacienda || "",
        suerte: currentVivero.suerte || "",
        proyecto_id: currentVivero.proyecto_id || "",
        caracter_id: currentVivero.caracter_id || "",
        es_corte: "true"
      }
    });
  } catch (error: any) {
    console.error("Error in registrarCorteParcela:", error);
    toast.error("Error al redirigir al corte: " + error.message);
  }
};

const loadLotesForLocation = async () => {
  if (!form.value.ingenio || !form.value.hacienda) {
    lotes.value = [];
    return;
  }
  try {
    const res = await viverosServices.getLotes({ 
      ingenio_codigo: form.value.ingenio,
      hacienda_codigo: form.value.hacienda 
    });
    lotes.value = res.data;
  } catch (error) {
    console.error("Error loading lotes:", error);
    toast.error("Error al cargar los lotes");
  }
};

watch([() => form.value.ingenio, () => form.value.hacienda], () => {
  if (!isEditing.value) {
    form.value.lote_id = "";
  }
  loadLotesForLocation();
});

const formatDateTime = (dateString: string) => {
  if (!dateString) return '';
  return dayjs(dateString).format('YYYY-MM-DD HH:mm');
};

const openTrasladoModal = async () => {
  trasladoIngenio.value = form.value.ingenio || "";
  trasladoHacienda.value = form.value.hacienda || "";
  trasladoLoteId.value = "";
  trasladoHaciendas.value = [];
  trasladoLotes.value = [];
  
  if (trasladoIngenio.value) {
    try {
      const resHac = await viverosServices.getHaciendas(trasladoIngenio.value);
      trasladoHaciendas.value = resHac.data;
    } catch (err) {
      console.error(err);
    }
  }
  if (trasladoIngenio.value && trasladoHacienda.value) {
    try {
      const resLot = await viverosServices.getLotes({
        ingenio_codigo: trasladoIngenio.value,
        hacienda_codigo: trasladoHacienda.value
      });
      trasladoLotes.value = resLot.data;
    } catch (err) {
      console.error(err);
    }
  }
  
  isTrasladoModalOpen.value = true;
};

const closeTrasladoModal = () => {
  isTrasladoModalOpen.value = false;
};

const handleTrasladoIngenioChange = async () => {
  trasladoHacienda.value = "";
  trasladoLoteId.value = "";
  trasladoHaciendas.value = [];
  trasladoLotes.value = [];
  if (trasladoIngenio.value) {
    try {
      const res = await viverosServices.getHaciendas(trasladoIngenio.value);
      trasladoHaciendas.value = res.data;
    } catch (err) {
      console.error(err);
      toast.error("Error al cargar haciendas de traslado");
    }
  }
};

const handleTrasladoHaciendaChange = async () => {
  trasladoLoteId.value = "";
  trasladoLotes.value = [];
  if (trasladoIngenio.value && trasladoHacienda.value) {
    try {
      const res = await viverosServices.getLotes({
        ingenio_codigo: trasladoIngenio.value,
        hacienda_codigo: trasladoHacienda.value
      });
      trasladoLotes.value = res.data;
    } catch (err) {
      console.error(err);
      toast.error("Error al cargar lotes de traslado");
    }
  }
};

const submitTraslado = async () => {
  if (!trasladoLoteId.value) return;
  savingTraslado.value = true;
  try {
    const response = await viverosServices.trasladarLote(route.params.id as string, {
      lote_id: trasladoLoteId.value
    });
    toast.success("Vivero trasladado de lote correctamente");
    const updatedVivero = response.data;
    form.value.lote_id = updatedVivero.lote_id;
    form.value.ingenio = updatedVivero.ingenio;
    form.value.hacienda = updatedVivero.hacienda;
    form.value.suerte = updatedVivero.suerte;
    form.value.identificador_unico = updatedVivero.identificador_unico;
    form.value.nombre = updatedVivero.nombre;
    form.value.historial_lotes = updatedVivero.historial_lotes || [];
    
    // Refresh lotes to update their current active counts
    if (form.value.ingenio) {
      await loadLotesForLocation();
    }
    
    isTrasladoModalOpen.value = false;
  } catch (error: any) {
    console.error("Error in submitTraslado:", error);
    const msg = error.response?.data?.message || "Error al trasladar el vivero";
    toast.error(msg);
  } finally {
    savingTraslado.value = false;
  }
};

const confirmDeleteCorte = async (id: number, uniqueId: string) => {
  if (confirm(`¿Está seguro de que desea eliminar el corte ${uniqueId}?`)) {
    try {
      await viverosServices.deleteVivero(id);
      toast.success('Corte eliminado correctamente');
      await loadParcelas();
    } catch (error: any) {
      console.error('Error deleting corte:', error);
      const msg = error.response?.data?.message || 'Error al eliminar el corte';
      toast.error(msg);
    }
  }
};

const deleteParcela = async (parcelaId: string | number) => {
  if (!confirm("¿Está seguro de eliminar esta parcela?")) return;

  try {
    await viverosServices.deleteParcela(route.params.id as string, parcelaId);
    toast.success("Parcela eliminada correctamente");
    await loadParcelas();
  } catch (error: any) {
    console.error("Error al eliminar parcela:", error);
    const msg = error.response?.data?.message || "Error al eliminar la parcela";
    toast.error(msg);
  }
};

const deleteAllParcelas = async () => {
  if (!confirm("¿Está seguro de que desea eliminar TODAS las parcelas de este vivero? Esta acción no se puede deshacer.")) return;

  try {
    await viverosServices.deleteAllParcelas(route.params.id as string);
    toast.success("Todas las parcelas fueron eliminadas");
    await loadParcelas();
  } catch (error: any) {
    console.error("Error al eliminar parcelas:", error);
    const msg = error.response?.data?.message || "Error al vaciar el vivero";
    toast.error(msg);
  }
};

const resetAndLoad = async () => {
  // Reset form and variables to default empty state
  form.value = {
    identificador_unico: "",
    nombre: "",
    ingenio: "",
    hacienda: "",
    suerte: "",
    proyecto_id: "",
    ambiente: "",
    responsable_id: "",
    fecha_siembra: "",
    numero_corte: 1,
    temporada_floracion: "",
    condicion: "",
    caracter_id: "",
    origen_ingenio: "",
    origen_hacienda: "",
    origen_suerte: "",
    origen_anio: null,
    origen_parcela: "",
    lote_id: "",
    consecutivo_vivero_ingenio: null,
    historial_lotes: []
  };
  searchProyecto.value = "";
  searchCaracter.value = "";
  searchResponsable.value = "";
  searchOrigenVivero.value = "";
  origenParcelasOptions.value = [];
  viveroSeleccionadoOrigen.value = null;
  parcelas.value = [];

  if (route.params.id) {
    isEditing.value = true;
  } else {
    isEditing.value = false;
  }
  
  isLoadingInfo.value = true;

  try {
    // Load common data concurrently for better performance
    await Promise.all([loadIngenios(), loadProyectos(), loadResponsables(), loadAmbientes(), loadAllViveros()]);

    if (isEditing.value) {
      const response = await viverosServices.getVivero(route.params.id as string);
      const vivero = response.data;
      if (vivero.fecha_siembra) {
        vivero.fecha_siembra = vivero.fecha_siembra.substring(0, 10);
      }
      form.value = { ...vivero };

      if (form.value.ingenio && form.value.hacienda) {
        await loadLotesForLocation();
      }

      if (form.value.origen_vivero_id) {
        const parentVivero = allViverosList.value.find(v => v.id == form.value.origen_vivero_id);
        if (parentVivero) {
          viveroSeleccionadoOrigen.value = parentVivero;
          origenParcelasOptions.value = parentVivero.parcelas || [];
          searchOrigenVivero.value = parentVivero.identificador_unico;
          origenViveroManual.value = false;
          
          if (form.value.origen_parcela && form.value.origen_parcela.startsWith(parentVivero.identificador_unico)) {
            const suffix = form.value.origen_parcela.substring(parentVivero.identificador_unico.length + 1);
            if (suffix) {
              origenParcelaText.value = suffix;
              origenParcelaManual.value = false;
            }
          }
        }
      } else if (form.value.origen_parcela) {
        origenViveroManual.value = true;
        origenParcelaManual.value = true;
        
        const parts = form.value.origen_parcela.split("-");
        if (parts.length >= 5) {
          origenViveroInput.value = parts.slice(0, 4).join("-");
          origenParcelaText.value = parts.slice(4).join("-");
        } else {
          origenViveroInput.value = form.value.origen_parcela;
          origenParcelaText.value = "";
        }
      }

      if (form.value.proyecto_id) {
        const pry = proyectos.value.find((p) => p.id_prycto == form.value.proyecto_id);
        if (pry) searchProyecto.value = formatProjectName(pry);

        await loadCaracteres(form.value.proyecto_id);
        if (form.value.caracter_id) {
          const car = caracteres.value.find((c) => c.id == form.value.caracter_id);
          if (car) searchCaracter.value = car.nombre;
        }
      }
      if (form.value.responsable_id) {
        const usr = responsables.value.find((u) => u.id_usrio == form.value.responsable_id);
        if (usr) searchResponsable.value = usr.nmbre;
      }

      // Load cascading data without resetting values
      if (form.value.ingenio) {
        await loadHaciendas(false);
      }
      if (form.value.hacienda) {
        await loadSuertes(false);
      }
      await loadLotesForLocation();

      // Load origin cascading data
      if (form.value.origen_ingenio) {
        await loadHaciendasOrigen(false);
        await loadLotesOrigen();
      }

      // Load Parcelas
      await loadParcelas();
    } else {
      const currentYear = new Date().getFullYear();
      if (!form.value.anio) {
        form.value.anio = currentYear;
      }

      if (route.query.origen_ingenio) {
        form.value.origen_ingenio = route.query.origen_ingenio as string;
        await loadHaciendasOrigen(false);
        await loadLotesOrigen();
      }
      if (route.query.origen_hacienda) {
        form.value.origen_hacienda = route.query.origen_hacienda as string;
      }
      if (route.query.origen_lote_id) {
        form.value.origen_lote_id = Number(route.query.origen_lote_id);
      }
      if (route.query.origen_vivero_id) {
        form.value.origen_vivero_id = Number(route.query.origen_vivero_id);
      }
      if (route.query.origen_anio) {
        form.value.origen_anio = Number(route.query.origen_anio);
      }
      if (route.query.es_corte) {
        form.value.es_corte = route.query.es_corte === 'true';
      }
      if (route.query.origen_parcela) {
        form.value.origen_parcela = route.query.origen_parcela as string;
        
        const parts = form.value.origen_parcela.split("-");
        if (parts.length >= 4) {
          const baseId = parts.slice(0, 4).join("-");
          const parentVivero = allViverosList.value.find(v => v.identificador_unico === baseId);
          if (parentVivero) {
            viveroSeleccionadoOrigen.value = parentVivero;
            form.value.origen_vivero_id = parentVivero.id;
            form.value.origen_lote_id = parentVivero.lote_id || "";
            origenParcelasOptions.value = parentVivero.parcelas || [];
            searchOrigenVivero.value = parentVivero.identificador_unico;
            
            const suffix = parts[4];
            if (suffix) {
              origenParcelaText.value = suffix;
            }
          }
        }

        try {
          const res = await viverosServices.getNextCorteConsecutivo(form.value.origen_parcela);
          form.value.consecutivo_corte = res.data.consecutivo;
          if (form.value.es_corte) {
            form.value.identificador_unico = `${form.value.origen_parcela}-${form.value.consecutivo_corte}`;
          }
        } catch (err) {
          console.error("Error fetching next consecutivo_corte:", err);
        }
      }

      // Pre-fill nursery fields from query parameters
      if (route.query.ingenio) {
        form.value.ingenio = route.query.ingenio as string;
        await loadHaciendas(false);
      }
      if (route.query.hacienda) {
        form.value.hacienda = route.query.hacienda as string;
      }
      if (route.query.proyecto_id) {
        form.value.proyecto_id = Number(route.query.proyecto_id);
        const pry = proyectos.value.find((p) => p.id_prycto == form.value.proyecto_id);
        if (pry) searchProyecto.value = formatProjectName(pry);
        await loadCaracteres(form.value.proyecto_id);
      }
      if (route.query.caracter_id) {
        form.value.caracter_id = Number(route.query.caracter_id);
        const car = caracteres.value.find((c) => c.id == form.value.caracter_id);
        if (car) searchCaracter.value = car.nombre;
      }
    }
  } catch (error) {
    console.error("Error in resetAndLoad:", error);
    toast.error("Error al cargar la información del vivero");
  } finally {
    isLoadingInfo.value = false;
  }


};

onMounted(resetAndLoad);

watch(() => route.path, resetAndLoad);
</script>
