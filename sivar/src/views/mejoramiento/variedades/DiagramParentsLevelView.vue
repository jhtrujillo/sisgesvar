<template>
  <div class="space-y-4 pb-20">
    <div class="flex justify-start mb-4">
      <BackButton :to="{ name: 'variedades.show' }" label="Volver a Variedades" />
    </div>

    <!-- Buscador -->
    <div class="grid grid-cols-1 mb-8">
      <div class="group grid font-medium rounded-md m-2 bg-white border-none shadow-xl py-8 px-4 sm:rounded-lg sm:px-10">
        <div class="sm:mx-auto sm:w-full sm:max-w-md mb-2 flex justify-center">
          <h1 class="text-emerald-500 text-2xl font-bold">Diagrama de Parentales</h1>
        </div>
        <p class="text-center text-sm text-gray-500 mb-6">Analice el pedigrí y el origen genético de la variedad</p>
        
        <div class="relative w-full max-w-md mx-auto">
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

    <!-- Área del Diagrama -->
    <div v-if="selectedVariety" class="grid grid-cols-1 m-2 bg-white border-none shadow-xl rounded-md sm:rounded-lg overflow-hidden">
      <!-- Encabezado de información del gráfico -->
      <div class="flex border-b border-gray-100 p-6 items-center justify-between bg-gray-50 flex-wrap gap-4">
        <div>
          <h2 class="text-xl font-black text-slate-800">Pedigrí de <span class="text-violet-700">{{ selectedVariety }}</span></h2>
          <p class="text-sm text-gray-500 mt-1">Gira el ratón (scroll) para hacer zoom. Arrastra para mover.</p>
        </div>
        
        <div class="flex items-center space-x-6 flex-wrap gap-y-4">
          <div class="flex items-center space-x-4 text-xs font-bold uppercase tracking-wide">
            <div class="flex items-center"><span class="w-3 h-3 rounded-full bg-[#10b981] mr-2"></span>Madre</div>
            <div class="flex items-center"><span class="w-3 h-3 rounded-full bg-[#3b82f6] mr-2"></span>Padre</div>
            <div class="flex items-center"><span class="w-3 h-3 rounded-full bg-[#5b21b6] mr-2"></span>Base</div>
          </div>
          
          <div class="border-l border-gray-300 pl-6 flex items-center space-x-4">
            <!-- Slider de Profundidad -->
            <div class="flex items-center space-x-2 bg-white px-3 py-1.5 rounded-md border border-gray-200">
              <span class="text-xs font-bold text-gray-500">Niveles</span>
              <input type="range" min="1" max="10" step="1" v-model.lazy="maxDepth" class="w-20 accent-emerald-500" title="Generaciones a mostrar">
              <span class="text-lg font-bold text-emerald-600">{{ maxDepth }}</span>
            </div>
            <!-- Slider de Tamaño de Letra -->
            <div class="flex items-center space-x-2 bg-white px-3 py-1.5 rounded-md border border-gray-200">
              <span class="text-xs font-bold text-gray-500">A-</span>
              <input type="range" min="10" max="40" step="1" v-model.lazy="fontSize" class="w-24 accent-violet-600" title="Ajustar tamaño de texto">
              <span class="text-lg font-bold text-gray-700">A+</span>
            </div>

            <button @click="toggleOrientation" class="flex items-center text-sm font-bold text-violet-600 hover:text-violet-800 transition-colors bg-violet-50 px-3 py-1.5 rounded-md">
              <!-- Icon Horizontal -->
              <svg v-if="isHorizontal" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
              </svg>
              <!-- Icon Vertical -->
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
              </svg>
              Cambiar a {{ isHorizontal ? "Vertical" : "Horizontal" }}
            </button>
          </div>
        </div>
      </div>
      
      <!-- Contenedor del gráfico -->
      <div class="w-full h-[600px] flex justify-center items-center p-4">
        <div v-if="isLoading" class="text-violet-500 font-medium">Generando diagrama...</div>
        <div v-else-if="filteredInfo.length === 0" class="text-gray-400 font-medium">No se encontraron datos de parentales para esta variedad.</div>
        <div id="chartdiv" class="w-full h-full" :class="{ 'hidden': isLoading || filteredInfo.length === 0 }"></div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from "vue";
import { useVarietyStore } from "@/stores/variety";
import { useParentsLevelStore } from "@/stores/parentslevel";
import BackButton from "@/components/BackButton.vue";
import type { TreeItem } from "@/services/types";
import * as am5 from "@amcharts/amcharts5";
import * as am5hierarchy from "@amcharts/amcharts5/hierarchy";
import * as am5plugins_exporting from "@amcharts/amcharts5/plugins/exporting";
import am5themes_Animated from "@amcharts/amcharts5/themes/Animated";

const varietyStore = useVarietyStore();
const parentsLevelStore = useParentsLevelStore();

const searchQuery = ref("");
const showDropdown = ref(false);
const selectedVariety = ref<string | null>(null);
const isLoading = ref(false);
const filteredInfo = ref<TreeItem[]>([]);
const isHorizontal = ref(true);
const fontSize = ref(20);
const maxDepth = ref(3);

let chartRoot: am5.Root | null = null;
let currentChartData: any = null;
let currentSeries: am5hierarchy.Tree | null = null;

const filteredVarieties = computed(() => {
  const query = searchQuery.value.toLowerCase().trim();
  if (!query) return varietyStore.Variety;
  return varietyStore.Variety.filter((v: any) => 
    v.nm_vrdad.toLowerCase().includes(query)
  );
});

const onBlurInput = () => {
  setTimeout(() => {
    showDropdown.value = false;
  }, 150);
};

const onSelectDropdownItem = (varName: string) => {
  searchQuery.value = varName;
  selectedVariety.value = varName;
  showDropdown.value = false;
};

const toggleOrientation = () => {
  isHorizontal.value = !isHorizontal.value;
  if (currentChartData) {
    // Regenerar el gráfico con la nueva orientación
    generateChart(currentChartData);
  }
};

watch(maxDepth, () => {
  if (currentChartData) {
    generateChart(currentChartData);
  }
});
watch(fontSize, () => {
  if (currentChartData) {
    generateChart(currentChartData);
  }
});

onMounted(async () => {
  await varietyStore.getVariety();
});

const getColorForType = (type: string, isRoot: boolean) => {
  if (isRoot) return am5.color(0x5b21b6); // Violeta corporativo (Base)
  const t = type?.toLowerCase() || '';
  if (t.includes('madre') || t.includes('hembra')) return am5.color(0x10b981); // Emerald (Hembra)
  if (t.includes('padre') || t.includes('macho')) return am5.color(0x3b82f6); // Azul (Macho)
  return am5.color(0x9ca3af); // Gray (Desconocido)
};

const prepareTreeItemsForAmCharts = (item: any, isRoot = true): any => {
  return {
    name: item.name,
    type: item.type || (isRoot ? "Variedad Base" : ""),
    nivel: item.nivel,
    labelString: `${item.name || ""} - ${item.type || ""}`, 
    nodeColor: getColorForType(item.type, isRoot),
    children: item.children?.map((child: any) => prepareTreeItemsForAmCharts(child, false)) || []
  };
};

watch(selectedVariety, async (newVariety) => {
  if (newVariety) {
    isLoading.value = true;
    try {
      await parentsLevelStore.getParentsLevelDiagram(newVariety);
      const parentsLevel = parentsLevelStore.ParentsLevel;

      if (parentsLevel && parentsLevel[0]?.original?.length > 0) {
        const topLevel = parentsLevel[0].original[0];
        const filteredData = prepareTreeItemsForAmCharts(topLevel, true);
        filteredInfo.value = [filteredData];
        currentChartData = filteredData;
        setTimeout(() => {
          generateChart(filteredData);
        }, 50); // Pequeño retraso para que el div exista en el DOM
      } else {
        filteredInfo.value = [];
        currentChartData = null;
        if (chartRoot) {
          chartRoot.dispose();
          chartRoot = null;
        }
      }
    } finally {
      isLoading.value = false;
    }
  }
});

const generateChart = (data: any) => {
  if (chartRoot) {
    chartRoot.dispose();
  }

  // Verificar que el div exista
  const chartDiv = document.getElementById("chartdiv");
  if (!chartDiv) return;

  chartRoot = am5.Root.new("chartdiv");
  chartRoot.setThemes([am5themes_Animated.new(chartRoot)]);

  const zoomableContainer = chartRoot.container.children.push(
    am5.ZoomableContainer.new(chartRoot, {
      width: am5.percent(100),
      height: am5.percent(100),
      wheelable: true,
      pinchZoom: true
    })
  );

  const zoomTools = zoomableContainer.children.push(
    am5.ZoomTools.new(chartRoot, {
      target: zoomableContainer
    })
  );

  const series = zoomableContainer.contents.children.push(
    am5hierarchy.Tree.new(chartRoot, {
      singleBranchOnly: false,
      downDepth: 1,
      initialDepth: maxDepth.value,
      valueField: "value",
      categoryField: "name",
      childDataField: "children",
      inversed: true,
      orientation: isHorizontal.value ? "horizontal" : "vertical"
    })
  );

  currentSeries = series;

  // Configurar nodos con colores dinámicos
  series.circles.template.setAll({
    radius: 20,
    strokeWidth: 2,
    fillOpacity: 0.9,
    cursorOverStyle: "pointer"
  });

  series.circles.template.adapters.add("fill", (fill, target) => {
    const dataItem = target.dataItem;
    if (dataItem) {
      const dataContext = dataItem.dataContext as any;
      if (dataContext && dataContext.nodeColor) {
        return dataContext.nodeColor;
      }
    }
    return fill;
  });

  series.circles.template.adapters.add("stroke", (stroke, target) => {
    const dataItem = target.dataItem;
    if (dataItem) {
      const dataContext = dataItem.dataContext as any;
      if (dataContext && dataContext.nodeColor) {
        return dataContext.nodeColor;
      }
    }
    return stroke;
  });

  // Estilos de los enlaces (conectores)
  series.links.template.setAll({
    strokeWidth: 2,
    strokeOpacity: 0.3
  });
  
  series.links.template.adapters.add("stroke", (stroke, target) => {
    const dataItem = target.dataItem;
    if (dataItem) {
      const dataContext = dataItem.dataContext as any;
      if (dataContext && dataContext.nodeColor) {
        return dataContext.nodeColor;
      }
    }
    return stroke;
  });

  // Etiquetas de los nodos (Colocadas AL LADO del nodo)
  series.labels.template.setAll({
    minScale: 0,
    oversizedBehavior: "none",
    text: "[bold]{name}[/]\n[#6b7280]{type}[/]",
    fontSize: Number(fontSize.value),
    fill: am5.color(0x111827),
    paddingLeft: 12
  });

  series.nodes.template.set("tooltipText", "[bold]{name}[/]\nRol: {type}\nNivel de parentesco: {nivel}");
  series.nodes.template.events.on("click", (e) => {
    const dataContext = e.target.dataItem?.dataContext as any;
    if (dataContext && dataContext.name) {
      let varName = dataContext.name;
      // Limpiar texto basura como "(Padre)" si se filtró, aunque prepareTreeItemsForAmCharts solo pasa el nombre limpio.
      onSelectDropdownItem(varName);
      window.scrollTo({ top: 0, behavior: "smooth" });
    }
  });

  series.data.setAll([data]);
  series.set("selectedDataItem", series.dataItems[0]);

  series.appear(1000, 100);

  // Habilitar la exportación del gráfico (PNG, SVG, etc.)
  const exporting = am5plugins_exporting.Exporting.new(chartRoot, {
    menu: am5plugins_exporting.ExportingMenu.new(chartRoot, {}),
    filePrefix: "Pedigri_" + (selectedVariety.value || "Variedad")
  });
};
</script>
