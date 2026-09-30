<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm animate-fade-in">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-visible border border-slate-100 flex flex-col max-h-[90vh]">
      <!-- Header -->
      <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center shrink-0">
        <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
          <span class="p-1.5 bg-emerald-100 text-cenicana rounded-lg">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
          </span>
          Enviar a Bolsa Común
        </h3>
        <button @click="closeModal" class="text-slate-400 hover:text-slate-600 rounded-lg p-1 hover:bg-slate-200 transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
      </div>

      <!-- Body -->
      <div class="p-6 overflow-y-visible space-y-5">
        <div class="text-xs text-slate-500 mb-2">
          Seleccione las flores disponibles que desea liberar para que puedan ser utilizadas en cruzamientos de cualquier proyecto.
        </div>

        <div class="space-y-4">
          <!-- Tipo de Filtro -->
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Filtrar por:</label>
            <div class="flex flex-col gap-3">
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" v-model="form.tipo_filtro" value="variedad" class="text-cenicana focus:ring-cenicana border-slate-300">
                <span class="text-sm font-medium text-slate-700">Por Variedad</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" v-model="form.tipo_filtro" value="proyecto_caracter" class="text-cenicana focus:ring-cenicana border-slate-300">
                <span class="text-sm font-medium text-slate-700">Por Proyecto y luego por Carácter</span>
              </label>
            </div>
          </div>

          <!-- Selector de Proyecto (Headless UI Combobox) -->
          <div v-if="form.tipo_filtro === 'proyecto_caracter'">
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
              Seleccione Proyecto <span class="text-rose-500">*</span>
            </label>
            <Combobox v-model="form.proyecto_id">
              <div class="relative mt-1">
                <div class="relative w-full cursor-default overflow-hidden rounded-xl border border-slate-200 bg-white text-left focus:ring-2 focus:ring-cenicana/20 focus:border-cenicana transition-all sm:text-sm">
                  <ComboboxInput
                    class="w-full border-none py-2 pl-3 pr-10 text-sm leading-5 text-gray-900 focus:ring-0"
                    :displayValue="(optId) => projectOptions.find(o => o.id === optId)?.label || ''"
                    @change="queryProject = $event.target.value"
                    placeholder="Buscar proyecto..."
                  />
                  <ComboboxButton class="absolute inset-y-0 right-0 flex items-center pr-2">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" /></svg>
                  </ComboboxButton>
                </div>
                <TransitionRoot leave="transition ease-in duration-100" leaveFrom="opacity-100" leaveTo="opacity-0" @after-leave="queryProject = ''">
                  <ComboboxOptions class="absolute mt-1 max-h-48 w-full overflow-auto rounded-xl bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm z-[100]">
                    <div v-if="filteredProjectOptions.length === 0 && queryProject !== ''" class="relative cursor-default select-none py-2 px-4 text-gray-700">
                      Nada encontrado.
                    </div>
                    <ComboboxOption
                      v-for="opt in filteredProjectOptions"
                      as="template"
                      :key="opt.id"
                      :value="opt.id"
                      v-slot="{ selected, active }"
                    >
                      <li class="relative cursor-default select-none py-2 pl-10 pr-4" :class="{ 'bg-emerald-100 text-emerald-900': active, 'text-gray-900': !active }">
                        <span class="block truncate" :class="{ 'font-medium': selected, 'font-normal': !selected }">
                          {{ opt.label }}
                        </span>
                        <span v-if="selected" class="absolute inset-y-0 left-0 flex items-center pl-3" :class="{ 'text-emerald-600': active, 'text-emerald-600': !active }">
                          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        </span>
                      </li>
                    </ComboboxOption>
                  </ComboboxOptions>
                </TransitionRoot>
              </div>
            </Combobox>
          </div>

          <!-- Selector (Variedad o Carácter con Combobox) -->
          <div v-if="form.tipo_filtro === 'variedad' || (form.tipo_filtro === 'proyecto_caracter' && form.proyecto_id)">
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
              {{ form.tipo_filtro === 'variedad' ? 'Seleccione Variedad' : 'Seleccione Carácter' }} <span class="text-rose-500">*</span>
            </label>
            <Combobox v-model="form.valor_filtro">
              <div class="relative mt-1">
                <div class="relative w-full cursor-default overflow-hidden rounded-xl border border-slate-200 bg-white text-left focus:ring-2 focus:ring-cenicana/20 focus:border-cenicana transition-all sm:text-sm">
                  <ComboboxInput
                    class="w-full border-none py-2 pl-3 pr-10 text-sm leading-5 text-gray-900 focus:ring-0"
                    :displayValue="(optId) => filterOptions.find(o => o.id === optId)?.label || ''"
                    @change="queryFilter = $event.target.value"
                    placeholder="Buscar..."
                  />
                  <ComboboxButton class="absolute inset-y-0 right-0 flex items-center pr-2">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" /></svg>
                  </ComboboxButton>
                </div>
                <TransitionRoot leave="transition ease-in duration-100" leaveFrom="opacity-100" leaveTo="opacity-0" @after-leave="queryFilter = ''">
                  <ComboboxOptions class="absolute mt-1 max-h-48 w-full overflow-auto rounded-xl bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm z-[100]">
                    <div v-if="filteredFilterOptions.length === 0 && queryFilter !== ''" class="relative cursor-default select-none py-2 px-4 text-gray-700">
                      Nada encontrado.
                    </div>
                    <ComboboxOption
                      v-for="opt in filteredFilterOptions"
                      as="template"
                      :key="opt.id"
                      :value="opt.id"
                      v-slot="{ selected, active }"
                    >
                      <li class="relative cursor-default select-none py-2 pl-10 pr-4" :class="{ 'bg-emerald-100 text-emerald-900': active, 'text-gray-900': !active }">
                        <span class="block truncate" :class="{ 'font-medium': selected, 'font-normal': !selected }">
                          {{ opt.label }} ({{ opt.disponibles }} disponibles)
                        </span>
                        <span v-if="selected" class="absolute inset-y-0 left-0 flex items-center pl-3" :class="{ 'text-emerald-600': active, 'text-emerald-600': !active }">
                          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        </span>
                      </li>
                    </ComboboxOption>
                  </ComboboxOptions>
                </TransitionRoot>
              </div>
            </Combobox>
          </div>

          <!-- Cantidad -->
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
              Cantidad a enviar <span class="text-rose-500">*</span>
            </label>
            <input 
              type="number" 
              v-model.number="form.cantidad" 
              min="1" 
              :max="maxCantidad"
              class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-cenicana/20 focus:border-cenicana transition-all"
              placeholder="Ej: 5"
            >
            <p v-if="maxCantidad > 0" class="text-[10px] text-slate-500 mt-1">Máximo disponible: {{ maxCantidad }}</p>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-end gap-3 shrink-0">
        <button @click="closeModal" type="button" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl transition-all shadow-sm">
          Cancelar
        </button>
        <button 
          @click="submit" 
          :disabled="isSubmitting || !isValid" 
          type="button" 
          class="px-5 py-2 text-xs font-bold text-white bg-cenicana hover:bg-cenicana-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-xl transition-all shadow-sm flex items-center"
        >
          <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
          Confirmar Envío
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { useToast } from 'vue-toastification';
import { Combobox, ComboboxInput, ComboboxButton, ComboboxOptions, ComboboxOption, TransitionRoot } from '@headlessui/vue';
import CrossingsService from '@/services/crossings.services';

const props = defineProps({
  isOpen: Boolean,
  floresDisponibles: {
    type: Array as () => any[],
    default: () => []
  }
});

const emit = defineEmits(['close', 'success']);
const toast = useToast();

const isSubmitting = ref(false);
const queryProject = ref('');
const queryFilter = ref('');

const form = ref({
  tipo_filtro: 'variedad',
  proyecto_id: '',
  valor_filtro: '',
  cantidad: 1
});

// Limpiar valor al cambiar tipo
watch(() => form.value.tipo_filtro, () => {
  form.value.proyecto_id = '';
  form.value.valor_filtro = '';
  form.value.cantidad = 1;
  queryProject.value = '';
  queryFilter.value = '';
});
watch(() => form.value.proyecto_id, () => {
  form.value.valor_filtro = '';
  form.value.cantidad = 1;
  queryFilter.value = '';
});

// Options logic
const projectOptions = computed(() => {
  const optionsMap = new Map();
  const validFlowers = props.floresDisponibles.filter(f => f.bolsa_comun === 0 && (f.estado === 0 || f.estado === '0'));
  
  validFlowers.forEach(f => {
    if (f.id_pr !== null && f.id_pr !== undefined && f.id_pr !== '') {
      if (!optionsMap.has(f.id_pr)) {
        const projectName = f.nm_prycto ? `${f.id_pr} - ${f.nm_prycto}` : `Proyecto ${f.id_pr}`;
        optionsMap.set(f.id_pr, { id: f.id_pr, label: projectName });
      }
    }
  });
  return Array.from(optionsMap.values()).sort((a, b) => String(a.label).localeCompare(String(b.label)));
});

const filteredProjectOptions = computed(() => {
  if (queryProject.value === '') return projectOptions.value;
  return projectOptions.value.filter(opt => 
    opt.label.toLowerCase().includes(queryProject.value.toLowerCase())
  );
});

const filterOptions = computed(() => {
  const optionsMap = new Map();
  
  let validFlowers = props.floresDisponibles.filter(f => f.bolsa_comun === 0 && (f.estado === 0 || f.estado === '0'));

  if (form.value.tipo_filtro === 'proyecto_caracter' && form.value.proyecto_id) {
    validFlowers = validFlowers.filter(f => String(f.id_pr) === String(form.value.proyecto_id));
  }

  validFlowers.forEach(f => {
    let key = '';
    let label = '';
    
    if (form.value.tipo_filtro === 'variedad') {
      key = f.vrdad;
      label = f.vrdad;
    } else if (form.value.tipo_filtro === 'proyecto_caracter') {
      key = f.id_crcter;
      label = f.nmbre_crcter ? `${f.nmbre_crcter}` : `Carácter ${f.id_crcter}`; 
    }

    if (key !== null && key !== undefined && key !== '') {
      if (!optionsMap.has(key)) {
        optionsMap.set(key, { id: key, label: label, disponibles: 1 });
      } else {
        const item = optionsMap.get(key);
        item.disponibles += 1;
      }
    }
  });

  return Array.from(optionsMap.values()).sort((a, b) => String(a.label).localeCompare(String(b.label)));
});

const filteredFilterOptions = computed(() => {
  if (queryFilter.value === '') return filterOptions.value;
  return filterOptions.value.filter(opt => 
    opt.label.toLowerCase().includes(queryFilter.value.toLowerCase())
  );
});

const maxCantidad = computed(() => {
  if (!form.value.valor_filtro) return 0;
  const opt = filterOptions.value.find(o => o.id === form.value.valor_filtro);
  return opt ? opt.disponibles : 0;
});

const isValid = computed(() => {
  const baseValid = form.value.valor_filtro !== '' && form.value.cantidad > 0 && form.value.cantidad <= maxCantidad.value;
  if (form.value.tipo_filtro === 'proyecto_caracter') {
    return baseValid && form.value.proyecto_id !== '';
  }
  return baseValid;
});

const closeModal = () => {
  emit('close');
  // Reset form
  setTimeout(() => {
    form.value = { tipo_filtro: 'variedad', proyecto_id: '', valor_filtro: '', cantidad: 1 };
    queryProject.value = '';
    queryFilter.value = '';
  }, 200);
};

const submit = async () => {
  if (!isValid.value) return;
  isSubmitting.value = true;
  
  try {
    const res = await CrossingsService.sendManualToCommonBag(form.value);
    toast.success(res?.data?.message || 'Flores enviadas a bolsa común con éxito');
    emit('success');
    closeModal();
  } catch (error: any) {
    console.error('Error al enviar a bolsa común:', error);
    toast.error(error?.response?.data?.error || 'Ocurrió un error al enviar las flores.');
  } finally {
    isSubmitting.value = false;
  }
};
</script>
