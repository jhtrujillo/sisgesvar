<template>
  <div class="space-y-4 pb-20">
    <div class="flex justify-start mb-4">
      <BaseButton variant="violet" size="sm" :to="{ name: 'variedades.show' }"> Volver </BaseButton>
    </div>
    
    <div class="grid grid-cols-1 mb-8">
      <!-- Search Bar using SIVAR Card Style -->
      <div class="group grid font-medium rounded-md m-2 bg-white border-none shadow-xl py-8 px-4 sm:rounded-lg sm:px-10">
        <div class="sm:mx-auto sm:w-full sm:max-w-md mb-2 flex justify-center">
          <h1 class="text-emerald-500 text-2xl font-bold">Buscar Variedad</h1>
        </div>
        <div class="relative w-full max-w-md mx-auto mt-4">
          <input 
            type="text"
            v-model="searchQuery"
            @focus="showDropdown = true"
            @blur="onBlurInput"
            placeholder="Escribe el nombre de la variedad..."
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500 sm:text-sm px-4 py-3 border text-center"
          />
          <!-- Dropdown list -->
          <div 
            v-if="showDropdown && filteredVarieties.length > 0" 
            class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-2xl max-h-60 overflow-y-auto text-left"
          >
            <div 
              v-for="v in filteredVarieties" 
              :key="v.nm_vrdad" 
              @mousedown="onSelectDropdownItem(v.nm_vrdad)"
              class="px-4 py-3 hover:bg-violet-100 cursor-pointer text-sm text-gray-700 border-b border-gray-50"
            >
              {{ v.nm_vrdad }}
            </div>
          </div>
          <div 
            v-else-if="showDropdown && searchQuery && filteredVarieties.length === 0" 
            class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-2xl p-4 text-sm text-gray-500 text-center"
          >
            No se encontraron resultados
          </div>
        </div>
      </div>
    </div>

    <!-- Content Area (Tabs) -->
    <div v-if="selectedVarietyData" class="grid grid-cols-1 m-2 bg-white border-none shadow-xl rounded-md sm:rounded-lg overflow-hidden">
      <!-- Tabs Header -->
      <div class="flex border-b border-gray-100 flex-wrap">
        <button 
          v-for="tab in tabs" 
          :key="tab.id"
          @click="currentTab = tab.id"
          :class="[
            'flex-1 py-5 px-6 text-center text-sm font-bold transition-colors cursor-pointer',
            currentTab === tab.id ? 'bg-white text-emerald-500 border-b-4 border-emerald-500' : 'text-gray-400 hover:text-emerald-400 hover:bg-gray-50 border-b-4 border-transparent'
          ]"
        >
          {{ tab.name }}
        </button>
      </div>

      <!-- Tab Content -->
      <div class="p-8 sm:px-10">
        
        <!-- TAB 1: General Info -->
        <div v-if="currentTab === 'general'" class="space-y-6 animate-fade-in">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-center">
            <div class="py-6 px-4 bg-gray-50 rounded-lg">
              <h3 class="text-sm font-bold text-violet-800 mb-2 uppercase tracking-wide">Variedad</h3>
              <p class="text-3xl font-black text-gray-800">{{ selectedVarietyData.nm_vrdad }}</p>
            </div>
            <div class="py-6 px-4 bg-gray-50 rounded-lg">
              <h3 class="text-sm font-bold text-violet-800 mb-2 uppercase tracking-wide">Tipo</h3>
              <p class="text-xl font-bold text-gray-700">
                <span v-if="selectedVarietyData.tpo">{{ selectedVarietyData.tpo }}</span>
                <span v-else class="text-gray-400 italic">No especificado</span>
              </p>
            </div>
            <div class="py-6 px-4 bg-emerald-50 rounded-lg">
              <h3 class="text-sm font-bold text-emerald-600 mb-2 uppercase tracking-wide">Madre (Hembra)</h3>
              <p class="text-2xl font-bold text-emerald-900">{{ selectedVarietyData.vrdad_madre || 'Desconocida' }}</p>
            </div>
            <div class="py-6 px-4 bg-violet-50 rounded-lg">
              <h3 class="text-sm font-bold text-violet-600 mb-2 uppercase tracking-wide">Padre (Macho)</h3>
              <p class="text-2xl font-bold text-violet-900">{{ selectedVarietyData.vrdad_pdre || 'Desconocido' }}</p>
            </div>
          </div>
        </div>

        <!-- TAB 2: Crosses History -->
        <div v-if="currentTab === 'history'" class="animate-fade-in">
          <div v-if="isLoadingHistory" class="text-center py-12 text-violet-500 font-medium text-lg">Cargando historial...</div>
          <div v-else-if="crossingsHistory.length === 0" class="text-center py-12 text-gray-400 font-medium text-lg">
            No hay registros de cruzamientos para esta variedad.
          </div>
          <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
              <thead>
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-bold text-emerald-500 uppercase tracking-wider">Fecha</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-emerald-500 uppercase tracking-wider">Proyecto</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-emerald-500 uppercase tracking-wider">Rol</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-emerald-500 uppercase tracking-wider">Otro Parental</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-50">
                <tr v-for="h in crossingsHistory" :key="h.id" class="hover:bg-gray-50 transition-colors">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700">{{ h.fecha }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ h.proyecto || '-' }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <span :class="[
                      'px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full',
                      h.rol === 'Madre' ? 'bg-emerald-100 text-emerald-700' : 
                      h.rol === 'Padre' ? 'bg-violet-100 text-violet-700' : 
                      'bg-gray-100 text-gray-700'
                    ]">
                      {{ h.rol }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm font-bold text-gray-800">{{ h.otro_parental }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- TAB 3: Traits Profile -->
        <div v-if="currentTab === 'profile'" class="animate-fade-in">
          <div v-if="isLoadingProfile" class="text-center py-12 text-violet-500 font-medium text-lg">Cargando perfil...</div>
          <div v-else-if="!varietyProfile" class="text-center py-12 text-gray-400 font-medium text-lg">
            No se encontraron datos agroindustriales.
          </div>
          <div v-else class="space-y-8">
            <div class="text-center">
              <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Origen de datos: </span>
              <span class="bg-violet-50 text-violet-700 px-3 py-1 rounded-full text-xs font-bold ml-2">{{ varietyProfile.origen_datos || 'BG' }}</span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
              <div v-for="trait in agroTraits" :key="trait.key" class="bg-gray-50 p-6 rounded-lg relative overflow-hidden group">
                <div 
                  class="absolute bottom-0 left-0 h-2 transition-all duration-1000 ease-out" 
                  :class="trait.bgClass" 
                  :style="`width: ${globalAverages && globalAverages[trait.key] ? calculatePercentage(varietyProfile[trait.key], globalAverages[trait.key]) : 0}%`">
                </div>
                
                <div class="flex flex-col items-center text-center mb-4">
                  <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">{{ trait.label }}</h4>
                  <span class="text-4xl font-black" :class="trait.colorClass">{{ formatNumber(varietyProfile[trait.key]) }}</span>
                </div>
                
                <div v-if="globalAverages && globalAverages[trait.key] !== null" class="mt-4 border-t border-gray-200 pt-3 text-center">
                  <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Promedio BG</div>
                  <div class="font-bold text-gray-600 text-lg">{{ formatNumber(globalAverages[trait.key]) }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(5px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>

<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import { useVarietysStore } from "@/stores/varietys";
import varietysService from "@/services/varietys.services";
import BaseButton from "@/components/BaseButton.vue";

const varietysListsStore = useVarietysStore();
const searchQuery = ref("");
const showDropdown = ref(false);
const selectedVarietyData = ref<any>(null);

const currentTab = ref("general");
const tabs = [
  { id: "general", name: "Información General" },
  { id: "history", name: "Historial de Cruzamientos" },
  { id: "profile", name: "Perfil Agroindustrial" }
];

// Profile data
const isLoadingProfile = ref(false);
const varietyProfile = ref<any>(null);
const globalAverages = ref<any>(null);

// History data
const isLoadingHistory = ref(false);
const crossingsHistory = ref<any[]>([]);

const agroTraits = [
  { key: "sacarosa", label: "Sacarosa", colorClass: "text-indigo-600", bgClass: "bg-indigo-500" },
  { key: "tchm", label: "TCHM", colorClass: "text-emerald-600", bgClass: "bg-emerald-500" },
  { key: "pureza", label: "Pureza", colorClass: "text-blue-600", bgClass: "bg-blue-500" },
  { key: "roya_cafe_r", label: "Roya Café", colorClass: "text-red-600", bgClass: "bg-red-500" },
  { key: "mosaico_p", label: "Mosaico", colorClass: "text-orange-600", bgClass: "bg-orange-500" },
  { key: "carbon_p", label: "Carbón", colorClass: "text-yellow-600", bgClass: "bg-yellow-500" },
  { key: "diametro_tallo", label: "Diámetro Tallo", colorClass: "text-cyan-600", bgClass: "bg-cyan-500" }
];

onMounted(async () => {
  await varietysListsStore.getVarietys();
});

const filteredVarieties = computed(() => {
  const query = searchQuery.value.toLowerCase().trim();
  if (!query) return varietysListsStore.VarietysList;
  return varietysListsStore.VarietysList.filter((v: any) => 
    v.nm_vrdad.toLowerCase().includes(query)
  );
});

const onBlurInput = () => {
  // Retrasamos un poco el cierre para que permita el evento mousedown en las opciones
  setTimeout(() => {
    showDropdown.value = false;
  }, 150);
};

const onSelectDropdownItem = (varName: string) => {
  searchQuery.value = varName;
  showDropdown.value = false;
  onVarietySelected();
};

const onVarietySelected = async () => {
  const val = searchQuery.value.trim();
  if (!val) {
    selectedVarietyData.value = null;
    return;
  }
  
  // Find in store
  const found = varietysListsStore.VarietysList.find((v: any) => v.nm_vrdad === val);
  if (found) {
    selectedVarietyData.value = found;
    // Load tabs data concurrently
    fetchProfile(val);
    fetchHistory(val);
  } else {
    selectedVarietyData.value = null;
  }
};

const fetchProfile = async (varName: string) => {
  isLoadingProfile.value = true;
  varietyProfile.value = null;
  globalAverages.value = null;
  try {
    const res = await varietysService.getVarietyProfile(varName);
    if (res.data && res.data.success) {
      varietyProfile.value = res.data.traits;
      globalAverages.value = res.data.globalAverages;
    }
  } catch (error) {
    console.error("Error loading profile", error);
  } finally {
    isLoadingProfile.value = false;
  }
};

const fetchHistory = async (varName: string) => {
  isLoadingHistory.value = true;
  crossingsHistory.value = [];
  try {
    const res = await varietysService.getVarietyCrossingsHistory(varName);
    if (res.data && res.data.success) {
      crossingsHistory.value = res.data.history;
    }
  } catch (error) {
    console.error("Error loading history", error);
  } finally {
    isLoadingHistory.value = false;
  }
};

const formatNumber = (val: any) => {
  if (val === null || val === undefined) return '-';
  const num = Number(val);
  return isNaN(num) ? '-' : num.toFixed(2);
};

const calculatePercentage = (val: any, avg: any) => {
  if (!val || !avg) return 0;
  const num = Number(val);
  const avgNum = Number(avg);
  if (avgNum === 0) return 0;
  
  const pct = (num / avgNum) * 50; 
  // Limit to 100% max for the visual bar
  return Math.min(Math.max(pct, 5), 100); 
};
</script>
