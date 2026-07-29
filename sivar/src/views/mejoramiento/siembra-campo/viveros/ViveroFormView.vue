<template>
  <div class="container mx-auto p-6 max-w-3xl">
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
      <form @submit.prevent="submitForm" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Identificador Único -->
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="identificador_unico">ID Vivero</label>
            <input
              v-model="form.identificador_unico"
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-500 bg-gray-100 leading-tight focus:outline-none focus:shadow-outline cursor-not-allowed"
              id="identificador_unico"
              type="text"
              placeholder="Generado automáticamente por el sistema"
              disabled
            />
          </div>

          <!-- Nombre -->
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="nombre"> Nombre del Vivero <span class="text-red-500">*</span> </label>
            <input
              v-model="form.nombre"
              required
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              id="nombre"
              type="text"
              placeholder="Ej. Vivero Principal"
            />
          </div>

          <!-- Fecha Siembra -->
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="fecha_siembra"> Fecha de Siembra / Corte <span class="text-red-500">*</span> </label>
            <input
              v-model="form.fecha_siembra"
              required
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              id="fecha_siembra"
              type="date"
            />
          </div>

          <!-- Ingenio -->
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="ingenio">Ingenio</label>
            <select
              v-model="form.ingenio"
              @change="loadHaciendas(true)"
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              id="ingenio"
            >
              <option value="">Seleccione un Ingenio</option>
              <option v-for="ing in ingenios" :key="ing.cd_ingnio" :value="ing.cd_ingnio" v-html="ing.nm_ingnio"></option>
            </select>
          </div>

          <!-- Hacienda -->
          <div class="mb-4 md:col-span-2">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="hacienda">Hacienda</label>
            <select
              v-model="form.hacienda"
              @change="loadSuertes(true)"
              :disabled="!form.ingenio || haciendas.length === 0"
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              id="hacienda"
            >
              <option value="">Seleccione una Hacienda</option>
              <option v-for="hda in haciendas" :key="hda.cd_hcnda" :value="hda.cd_hcnda" v-html="hda.nm_hcnda"></option>
            </select>
          </div>

          <!-- Suerte -->
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="suerte">Suerte</label>
            <select
              v-model="form.suerte"
              :disabled="!form.hacienda || suertes.length === 0"
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              id="suerte"
            >
              <option value="">Seleccione una Suerte</option>
              <option v-for="ste in suertes" :key="ste.cd_srte" :value="ste.cd_srte">{{ ste.cd_srte }} (Área: {{ Number(ste.area).toFixed(2) }})</option>
            </select>
          </div>

          <!-- Temporada Floración -->
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="temporada_floracion"> Temporada de cruzamientos </label>
            <input
              v-model="form.temporada_floracion"
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              id="temporada_floracion"
              type="text"
              placeholder="Ej. Invierno 2024"
            />
          </div>

          <!-- Proyecto -->
          <div class="mb-4 relative md:col-span-2">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="proyecto_id">Proyecto (Mejoramiento)</label>
            <div class="relative">
              <textarea
                v-model="searchProyecto"
                @focus="showProyectos = true"
                @blur="hideProyectosDelay"
                placeholder="Escribe para buscar un proyecto..."
                rows="2"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline resize-none"
              ></textarea>
              <button v-if="form.proyecto_id" @click="clearProyecto" type="button" class="absolute right-2 top-2 text-gray-400 hover:text-red-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
              <div
                v-if="showProyectos"
                class="absolute z-10 w-full mt-1 bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto sm:text-sm"
              >
                <div v-if="filteredProyectos.length === 0" class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                  No se encontraron proyectos
                </div>
                <div
                  v-for="pry in filteredProyectos"
                  :key="pry.id_prycto"
                  @mousedown="selectProyecto(pry)"
                  class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-cenicana hover:text-white break-words whitespace-normal leading-tight"
                  :class="form.proyecto_id === pry.id_prycto ? 'bg-cenicana-50 text-cenicana-800 font-semibold' : 'text-gray-900'"
                  v-html="formatProjectName(pry)"
                ></div>
              </div>
            </div>
          </div>
          <!-- Carácter -->
          <div class="mb-4 relative md:col-span-1">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="caracter_id">Carácter (Opcional)</label>
            <div class="relative">
              <input
                type="text"
                v-model="searchCaracter"
                @focus="showCaracteres = true"
                @blur="hideCaracteresDelay"
                placeholder="Buscar o agregar..."
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                :disabled="!form.proyecto_id"
                :class="{ 'bg-gray-100 cursor-not-allowed': !form.proyecto_id }"
              />
              <button v-if="form.caracter_id" @click="clearCaracter" type="button" class="absolute right-2 top-2 text-gray-400 hover:text-red-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
              <div
                v-if="showCaracteres && form.proyecto_id"
                class="absolute z-10 w-full mt-1 bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto sm:text-sm"
              >
                <div
                  v-if="searchCaracter && !exactMatchCaracter"
                  @mousedown="selectNewCaracter"
                  class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-green-100 text-green-700 font-semibold border-b border-gray-100"
                >
                  + Agregar nuevo: "{{ searchCaracter }}"
                </div>
                <div v-if="filteredCaracteres.length === 0 && !searchCaracter" class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                  No hay caracteres (escribe para crear)
                </div>
                <div
                  v-for="car in filteredCaracteres"
                  :key="car.id"
                  @mousedown="selectCaracter(car)"
                  class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-cenicana hover:text-white"
                  :class="form.caracter_id === car.id ? 'bg-cenicana-50 text-cenicana-800 font-semibold' : 'text-gray-900'"
                >
                  {{ car.nombre }}
                </div>
              </div>
            </div>
          </div>
          <!-- Condición -->
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="condicion">Tipo de floración</label>
            <select
              v-model="form.condicion"
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              id="condicion"
            >
              <option value="">Seleccione un Tipo de floración</option>
              <option value="Natural">Natural</option>
              <option value="Fotoperiodo">Fotoperiodo</option>
            </select>
          </div>
          <!-- Ambiente -->
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="ambiente">Mega Ambiente</label>
            <select
              v-model="form.ambiente"
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              id="ambiente"
            >
              <option value="">Vacío (Sin Mega Ambiente)</option>
              <option v-for="amb in ambientes" :key="amb.id_ambnte" :value="amb.id_ambnte" v-html="amb.nm_ambnte"></option>
            </select>
          </div>

          <!-- Responsable -->
          <div class="mb-4 relative">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="responsable_id">Responsable</label>
            <div class="relative">
              <input
                type="text"
                v-model="searchResponsable"
                @focus="showResponsables = true"
                @blur="hideResponsablesDelay"
                placeholder="Escribe para buscar un responsable..."
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              />
              <button v-if="form.responsable_id" @click="clearResponsable" type="button" class="absolute right-2 top-2 text-gray-400 hover:text-red-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
              <div
                v-if="showResponsables"
                class="absolute z-10 w-full mt-1 bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto sm:text-sm"
              >
                <div v-if="filteredResponsables.length === 0" class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                  No se encontraron responsables
                </div>
                <div
                  v-for="usr in filteredResponsables"
                  :key="usr.id_usrio"
                  @mousedown="selectResponsable(usr)"
                  class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-cenicana hover:text-white"
                  :class="form.responsable_id === usr.id_usrio ? 'bg-cenicana-50 text-cenicana-800 font-semibold' : 'text-gray-900'"
                  v-html="usr.nmbre"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-6 mb-4 border-b pb-2">
          <h3 class="text-lg font-semibold text-gray-800">Origen de la Semilla</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-8 gap-4 mb-6">
          <!-- Origen Ingenio -->
          <div class="mb-4 md:col-span-2">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="origen_ingenio">Ingenio <span class="text-red-500">*</span></label>
            <select
              v-model="form.origen_ingenio"
              @change="loadHaciendasOrigen(true)"
              required
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              id="origen_ingenio"
            >
              <option value="">Seleccione un Ingenio</option>
              <option v-for="ing in ingenios" :key="'origen_ing_' + ing.cd_ingnio" :value="ing.cd_ingnio" v-html="ing.nm_ingnio"></option>
            </select>
          </div>
          <!-- Origen Año -->
          <div class="mb-4 md:col-span-1">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="origen_anio">Año <span class="text-red-500">*</span></label>
            <input
              v-model="form.origen_anio"
              type="number"
              required
              placeholder="Ej. 2024"
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              id="origen_anio"
            />
          </div>
          <!-- Origen Hacienda -->
          <div class="mb-4 md:col-span-2">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="origen_hacienda">Hacienda <span class="text-red-500">*</span></label>
            <select
              v-model="form.origen_hacienda"
              @change="loadSuertesOrigen(true)"
              :disabled="!form.origen_ingenio || haciendasOrigen.length === 0"
              required
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              id="origen_hacienda"
            >
              <option value="">Seleccione una Hacienda</option>
              <option v-for="hda in haciendasOrigen" :key="'origen_hda_' + hda.cd_hcnda" :value="hda.cd_hcnda" v-html="hda.nm_hcnda"></option>
            </select>
          </div>
          <!-- Origen Suerte -->
          <div class="mb-4 md:col-span-2">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="origen_suerte">Suerte <span class="text-red-500">*</span></label>
            <select
              v-model="form.origen_suerte"
              :disabled="!form.origen_hacienda || suertesOrigen.length === 0"
              required
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              id="origen_suerte"
            >
              <option value="">Seleccione una Suerte</option>
              <option v-for="ste in suertesOrigen" :key="'origen_ste_' + ste.cd_srte" :value="ste.cd_srte">
                {{ ste.cd_srte }} (Área: {{ Number(ste.area).toFixed(2) }})
              </option>
            </select>
          </div>
          <!-- Origen Parcela -->
          <div class="mb-4 md:col-span-1">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="origen_parcela">Parcela <span class="text-red-500">*</span></label>
            <input
              v-model="form.origen_parcela"
              type="text"
              required
              placeholder="Ej. A-12"
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              id="origen_parcela"
            />
          </div>
        </div>

        <div class="flex items-center justify-end">
          <button
            class="flex items-center px-6 py-2.5 text-sm font-bold text-white bg-cenicana hover:bg-cenicana-800 disabled:opacity-50 disabled:cursor-not-allowed rounded-xl shadow-md transition-all duration-200"
            type="submit"
            :disabled="isSubmitting"
          >
            <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
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
                  @focus="showVariedades = true"
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
              <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Plot Origen</label>
              <input
                v-model="parcelaForm.numero_parcela_origen"
                @input="updateIdPlotOrigen"
                type="number"
                placeholder="No."
                class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-cenicana bg-white shadow-sm"
              />
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-600 uppercase mb-1">ID Plot Origen</label>
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
                <th class="px-4 py-3 border-b border-slate-200">Plot Origen</th>
                <th class="px-4 py-3 border-b border-slate-200">ID Plot Origen</th>
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
                <tr v-for="p in paginatedParcelas" :key="p.id" class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                  <td class="px-4 py-3 font-bold text-slate-800">{{ p.numero_parcela }}</td>
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
                        @click="registrarCorteParcela(p)"
                        class="text-emerald-600 hover:text-emerald-800 transition-colors bg-emerald-50 hover:bg-emerald-100 py-1.5 px-3 rounded-lg text-xs font-bold flex items-center gap-1 shadow-sm border border-emerald-200/50"
                        title="Registrar Corte"
                      >
                        ✂️ Corte
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

      <!-- Import Wizard Modal -->
      <ViveroParcelasImportWizard
        v-if="isEditing && route.params.id"
        :show="showImportWizard"
        :variedades="variedades"
        :viveroId="route.params.id"
        :viveroIdentificador="form.identificador_unico"
        :caracterId="form.caracter_id"
        @close="showImportWizard = false"
        @imported="loadParcelas"
      />
      <!-- Drawer de Hoja de Vida de la Variedad (Quick Drawer) -->
      <VarietyProfileDrawer v-model:isOpen="isDrawerOpen" :varietyName="selectedVarietyForDrawer" />
    </template>
  </div>
</template>

<script setup lang="ts">
import { ref, shallowRef, onMounted, computed, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useToast } from "vue-toastification";
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
  origen_anio: "" as string | number,
  origen_parcela: ""
});

const isSubmitting = ref(false);

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
    parcelaForm.value.id_plot_origen = `${form.value.identificador_unico}-${parcelaForm.value.numero_parcela_origen}`;
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

onMounted(async () => {
  if (route.params.id) {
    isEditing.value = true;
  }
  isLoadingInfo.value = true;

  try {
    // Load common data concurrently for better performance
    await Promise.all([loadIngenios(), loadProyectos(), loadResponsables(), loadAmbientes()]);

    if (isEditing.value) {
      const response = await viverosServices.getVivero(route.params.id as string);
      const vivero = response.data;
      if (vivero.fecha_siembra) {
        vivero.fecha_siembra = vivero.fecha_siembra.substring(0, 10);
      }
      form.value = { ...vivero };

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

      // Load origin cascading data
      if (form.value.origen_ingenio) {
        await loadHaciendasOrigen(false);
      }
      if (form.value.origen_hacienda) {
        await loadSuertesOrigen(false);
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
      }
      if (route.query.origen_hacienda) {
        form.value.origen_hacienda = route.query.origen_hacienda as string;
        await loadSuertesOrigen(false);
      }
      if (route.query.origen_suerte) {
        form.value.origen_suerte = route.query.origen_suerte as string;
      }
      if (route.query.origen_anio) {
        form.value.origen_anio = Number(route.query.origen_anio);
      }
      if (route.query.origen_parcela) {
        form.value.origen_parcela = route.query.origen_parcela as string;
      }
    }
  } catch (error) {
    console.error("Error in onMounted:", error);
    toast.error("Error al cargar la información inicial o del vivero");
  } finally {
    isLoadingInfo.value = false;
  }

  await loadVariedades();
});

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
  const currentVivero = form.value;
  const origenAnio = currentVivero.fecha_siembra
    ? new Date(currentVivero.fecha_siembra).getFullYear()
    : new Date().getFullYear();
  const idPlot = `${currentVivero.identificador_unico}-${p.numero_parcela}`;

  router.push({
    name: "vivero_nuevo.show",
    query: {
      origen_ingenio: currentVivero.ingenio || "",
      origen_hacienda: currentVivero.hacienda || "",
      origen_suerte: currentVivero.suerte || "",
      origen_anio: origenAnio,
      origen_parcela: idPlot
    }
  });
};

const deleteParcela = async (parcelaId: string | number) => {
  if (!confirm("¿Está seguro de eliminar esta parcela?")) return;

  try {
    await viverosServices.deleteParcela(route.params.id as string, parcelaId);
    toast.success("Parcela eliminada correctamente");
    await loadParcelas();
  } catch (error: any) {
    console.error("Error al eliminar parcela:", error);
    toast.error("Error al eliminar la parcela");
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
    toast.error("Error al vaciar el vivero");
  }
};
</script>
