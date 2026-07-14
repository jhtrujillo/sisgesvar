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
        {{ isEditing ? 'Editar Vivero' : 'Registrar Vivero' }}
      </h1>
    </div>

    <div v-if="isLoadingInfo" class="flex flex-col items-center justify-center py-20 bg-white shadow-md rounded px-8">
      <svg class="animate-spin h-10 w-10 text-cenicana mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      <p class="text-gray-600 font-medium">Cargando información del vivero...</p>
    </div>

    <form v-else @submit.prevent="submitForm" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Identificador Único -->
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="identificador_unico">
            Identificador Único (Opcional)
          </label>
          <input
            v-model="form.identificador_unico"
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            id="identificador_unico"
            type="text"
            placeholder="Autogenerado si está vacío"
            :disabled="isEditing"
          />
        </div>

        <!-- Nombre -->
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="nombre">
            Nombre del Vivero <span class="text-red-500">*</span>
          </label>
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
          <label class="block text-gray-700 text-sm font-bold mb-2" for="fecha_siembra">
            Fecha de Siembra / Corte <span class="text-red-500">*</span>
          </label>
          <input
            v-model="form.fecha_siembra"
            required
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            id="fecha_siembra"
            type="date"
          />
        </div>

        <!-- Número Corte -->
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="numero_corte">
            Número de Corte
          </label>
          <input
            v-model="form.numero_corte"
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            id="numero_corte"
            type="number"
            min="1"
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
            <option v-for="ing in ingenios" :key="ing.cd_ingnio" :value="ing.cd_ingnio">
              {{ ing.nm_ingnio }}
            </option>
          </select>
        </div>

        <!-- Hacienda -->
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="hacienda">Hacienda</label>
          <select
            v-model="form.hacienda"
            @change="loadSuertes(true)"
            :disabled="!form.ingenio || haciendas.length === 0"
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            id="hacienda"
          >
            <option value="">Seleccione una Hacienda</option>
            <option v-for="hda in haciendas" :key="hda.cd_hcnda" :value="hda.cd_hcnda">
              {{ hda.nm_hcnda }}
            </option>
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
            <option v-for="ste in suertes" :key="ste.cd_srte" :value="ste.cd_srte">
              {{ ste.cd_srte }} (Área: {{ Number(ste.area).toFixed(2) }})
            </option>
          </select>
        </div>

        <!-- Temporada Floración -->
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="temporada_floracion">
            Temporada de cruzamientos
          </label>
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
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
            </button>
            <div v-if="showProyectos" class="absolute z-10 w-full mt-1 bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto sm:text-sm">
              <div v-if="filteredProyectos.length === 0" class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                No se encontraron proyectos
              </div>
              <div
                v-for="pry in filteredProyectos"
                :key="pry.id_prycto"
                @mousedown="selectProyecto(pry)"
                class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-cenicana hover:text-white break-words whitespace-normal leading-tight"
                :class="form.proyecto_id === pry.id_prycto ? 'bg-cenicana-50 text-cenicana-800 font-semibold' : 'text-gray-900'"
              >
                {{ formatProjectName(pry) }}
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
            <option v-for="amb in ambientes" :key="amb.id_ambnte" :value="amb.id_ambnte">
              {{ amb.nm_ambnte }}
            </option>
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
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
            </button>
            <div v-if="showResponsables" class="absolute z-10 w-full mt-1 bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto sm:text-sm">
              <div v-if="filteredResponsables.length === 0" class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                No se encontraron responsables
              </div>
              <div
                v-for="usr in filteredResponsables"
                :key="usr.id_usrio"
                @mousedown="selectResponsable(usr)"
                class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-cenicana hover:text-white"
                :class="form.responsable_id === usr.id_usrio ? 'bg-cenicana-50 text-cenicana-800 font-semibold' : 'text-gray-900'"
              >
                {{ usr.nmbre }}
              </div>
            </div>
          </div>
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
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ isSubmitting ? 'Guardando...' : (isEditing ? 'Actualizar Vivero' : 'Guardar Vivero') }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import viverosServices from '@/services/viveros.services';

const route = useRoute();
const router = useRouter();
const toast = useToast();

const isEditing = ref(false);
const isLoadingInfo = ref(false);
const form = ref({
  identificador_unico: '',
  nombre: '',
  ingenio: '',
  hacienda: '',
  suerte: '',
  fecha_siembra: '',
  numero_corte: 1,
  temporada_floracion: '',
  proyecto_id: '',
  ambiente: '',
  responsable_id: '',
  condicion: '',
  caracter_id: ''
});

const isSubmitting = ref(false);
const ingenios = ref<any[]>([]);
const haciendas = ref<any[]>([]);
const suertes = ref<any[]>([]);
const proyectos = ref<any[]>([]);
const responsables = ref<any[]>([]);
const ambientes = ref<any[]>([]);

const searchProyecto = ref('');
const showProyectos = ref(false);

const formatProjectName = (pry: any) => {
  let code = pry.cd_cntble;
  if (code && code.length === 6) {
    code = `${code.substring(0, 2)}.${code.substring(2, 4)}.${code.substring(4, 6)}`;
  }
  return code ? `${code} - ${pry.nm_prycto}` : pry.nm_prycto;
};

const filteredProyectos = computed(() => {
  if (searchProyecto.value === '') {
    return proyectos.value;
  }
  return proyectos.value.filter((pry) => {
    return formatProjectName(pry).toLowerCase().includes(searchProyecto.value.toLowerCase())
  })
});

const selectProyecto = (pry: any) => {
  form.value.proyecto_id = pry.id_prycto;
  searchProyecto.value = formatProjectName(pry);
  showProyectos.value = false;
  // Reset caracter and load new ones
  form.value.caracter_id = '';
  searchCaracter.value = '';
  loadCaracteres(pry.id_prycto);
};

const clearProyecto = () => {
  form.value.proyecto_id = '';
  searchProyecto.value = '';
  showProyectos.value = true;
  form.value.caracter_id = '';
  searchCaracter.value = '';
  caracteres.value = [];
};

const hideProyectosDelay = () => {
  setTimeout(() => { showProyectos.value = false; }, 200);
};

// Caracter Logic
const caracteres = ref<any[]>([]);
const searchCaracter = ref('');
const showCaracteres = ref(false);

const filteredCaracteres = computed(() => {
  if (searchCaracter.value === '') {
    return caracteres.value;
  }
  return caracteres.value.filter((car) => {
    return car.nombre.toLowerCase().includes(searchCaracter.value.toLowerCase())
  })
});

const exactMatchCaracter = computed(() => {
  return caracteres.value.some(car => car.nombre.toLowerCase() === searchCaracter.value.trim().toLowerCase());
});

const loadCaracteres = async (proyecto_id: string | number) => {
  try {
    const res = await viverosServices.getCaracteresPorProyecto(proyecto_id);
    caracteres.value = res.data;
  } catch (error) {
    console.error('Error fetching caracteres:', error);
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
    console.error('Error creating caracter:', error);
    toast.error('No se pudo crear el caracter.');
  }
};

const clearCaracter = () => {
  form.value.caracter_id = '';
  searchCaracter.value = '';
  showCaracteres.value = true;
};

const hideCaracteresDelay = () => {
  setTimeout(() => { showCaracteres.value = false; }, 200);
};

const searchResponsable = ref('');
const showResponsables = ref(false);

const filteredResponsables = computed(() => {
  if (searchResponsable.value === '') {
    return responsables.value;
  }
  return responsables.value.filter((usr) => {
    return usr.nmbre.toLowerCase().includes(searchResponsable.value.toLowerCase())
  })
});

const selectResponsable = (usr: any) => {
  form.value.responsable_id = usr.id_usrio;
  searchResponsable.value = usr.nmbre;
  showResponsables.value = false;
};

const clearResponsable = () => {
  form.value.responsable_id = '';
  searchResponsable.value = '';
  showResponsables.value = true;
};

const hideResponsablesDelay = () => {
  setTimeout(() => { showResponsables.value = false; }, 200);
};

const loadIngenios = async () => {
  try {
    const res = await viverosServices.getIngenios();
    ingenios.value = res.data;
  } catch (error) {
    console.error('Error fetching ingenios:', error);
  }
};

const loadHaciendas = async (reset = false) => {
  if (reset) {
    form.value.hacienda = '';
    form.value.suerte = '';
    suertes.value = [];
  }
  haciendas.value = [];
  if (!form.value.ingenio) return;
  
  try {
    const res = await viverosServices.getHaciendas(form.value.ingenio);
    haciendas.value = res.data;
  } catch (error) {
    console.error('Error fetching haciendas:', error);
  }
};

const loadSuertes = async (reset = false) => {
  if (reset) form.value.suerte = '';
  if (!form.value.hacienda) {
    suertes.value = [];
    return;
  }
  try {
    const res = await viverosServices.getSuertes(form.value.hacienda);
    suertes.value = res.data;
  } catch (error) {
    console.error('Error fetching suertes:', error);
  }
};

const loadProyectos = async () => {
  try {
    const res = await viverosServices.getProyectos();
    proyectos.value = res.data;
  } catch (error) {
    console.error('Error fetching proyectos:', error);
  }
};

const loadResponsables = async () => {
  try {
    const res = await viverosServices.getResponsables();
    responsables.value = res.data;
  } catch (error) {
    console.error('Error fetching responsables:', error);
  }
};

const loadAmbientes = async () => {
  try {
    const res = await viverosServices.getAmbientes();
    ambientes.value = res.data;
  } catch (error) {
    console.error('Error fetching ambientes:', error);
  }
};

onMounted(async () => {
  if (route.params.id) {
    isEditing.value = true;
  }
  isLoadingInfo.value = true;

  try {
    // Load common data concurrently for better performance
    await Promise.all([
      loadIngenios(),
      loadProyectos(),
      loadResponsables(),
      loadAmbientes()
    ]);

    if (isEditing.value) {
      const response = await viverosServices.getVivero(route.params.id as string);
      const vivero = response.data;
      if (vivero.fecha_siembra) {
        vivero.fecha_siembra = vivero.fecha_siembra.substring(0, 10);
      }
      form.value = { ...vivero };
      
      if (form.value.proyecto_id) {
        const pry = proyectos.value.find(p => p.id_prycto == form.value.proyecto_id);
        if (pry) searchProyecto.value = formatProjectName(pry);
        
        await loadCaracteres(form.value.proyecto_id);
        if (form.value.caracter_id) {
            const car = caracteres.value.find(c => c.id == form.value.caracter_id);
            if (car) searchCaracter.value = car.nombre;
        }
      }
      if (form.value.responsable_id) {
        const usr = responsables.value.find(u => u.id_usrio == form.value.responsable_id);
        if (usr) searchResponsable.value = usr.nmbre;
      }

      // Load cascading data without resetting values
      if (form.value.ingenio) {
        await loadHaciendas(false);
      }
      if (form.value.hacienda) {
        await loadSuertes(false);
      }
    }
  } catch (error) {
    console.error('Error in onMounted:', error);
    toast.error('Error al cargar la información inicial o del vivero');
  } finally {
    isLoadingInfo.value = false;
  }
});

const submitForm = async () => {
  isSubmitting.value = true;
  try {
    if (isEditing.value) {
      await viverosServices.updateVivero(route.params.id as string, form.value);
      toast.success('Vivero actualizado correctamente');
    } else {
      await viverosServices.createVivero(form.value);
      toast.success('Vivero registrado correctamente');
    }
    router.push({ name: 'siembra_campo_viveros.show' });
  } catch (error) {
    console.error('Error saving vivero:', error);
    toast.error('Error al guardar el vivero');
  } finally {
    isSubmitting.value = false;
  }
};
</script>
