<template>
  <div class="w-full flex flex-col items-center">
    <!-- Botón para volver atrás -->
    <div>
      <button
        type="button"
        class="block mb-4 px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark-mode:bg-transparent dark-mode:hover:bg-gray-600 dark-mode:focus:bg-gray-600 dark-mode:focus:text-white dark-mode:text-gray-200 md:mt-0 focus:outline-none focus:shadow-outline"
      >
        <router-link
          class="text-violet-800 group border border-violet-800 flex items-center px-2 py-2 font-medium rounded-md pt-1 pb-1 pr-2 pl-2 hover:text-white hover:bg-violet-800"
          :to="{ name: 'variedades.show' }"
        >
          Volver
        </router-link>
      </button>
    </div>
    <!-- Título del diagrama -->
    <h1 class="text-center font-bold text-4xl mb-6 text-violet-800">Diagrama de Parentales</h1>
    <!-- Filtro y ComboBox -->
    <div class="flex flex-wrap justify-center items-center space-x-4 mb-4 mt-4">
      <div class="flex-none w-48 mb-5">
        <label for="variety" class="block text-sm font-medium text-gray-700">Busca una variedad</label>
        <ComboBoxMultiple
          :data-list="dataListVariedades"
          :column-value="columnValueVariedades"
          :column-to-show="columnToShowVariedades"
          v-model:selectedData="selectedVariety"
        />
      </div>
    </div>
    <!-- Área para mostrar el gráfico -->
    <div class="w-full h-[550px] flex justify-center items-center">
      <div id="chartdiv" class="w-full h-full"></div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from "vue";
import { useVarietyStore } from "@/stores/variety";
import { useParentsLevelStore } from "@/stores/parentslevel";
import ComboBoxMultiple from "@/components/ComboBoxMultiple.vue";
import type { TreeItem } from "@/services/types"; // Importar las interfaces desde el archivo types.ts
import * as am5 from "@amcharts/amcharts5";
import * as am5hierarchy from "@amcharts/amcharts5/hierarchy";
import am5themes_Animated from "@amcharts/amcharts5/themes/Animated";

const varietyStore = useVarietyStore();
const parentsLevelStore = useParentsLevelStore();

// Variables para el filtro y el ComboBox
const selectedVariety = ref<string | null>(null);
const dataListVariedades = varietyStore.Variety;
const columnValueVariedades = "nm_vrdad";
const columnToShowVariedades = "nm_vrdad";

// Datos filtrados para mostrar en la interfaz
const filteredInfo = ref<TreeItem[]>([]);

// Mantén una referencia a la instancia del gráfico
let chartRoot: am5.Root | null = null;

// Cargar variedades al montar el componente
onMounted(async () => {
  await varietyStore.getVariety();
});

const prepareTreeItemsForAmCharts = (item: any): any => {
  return {
    name: item.name,
    type: item.type,
    nivel: item.nivel,
    children: item.children?.map((child: any) => prepareTreeItemsForAmCharts(child)) || []
  };
};

watch(selectedVariety, async (selectedVariety) => {
  if (selectedVariety) {
    await parentsLevelStore.getParentsLevelDiagram(selectedVariety);
    const parentsLevel = parentsLevelStore.ParentsLevel;

    if (parentsLevel && parentsLevel[0]?.original?.length > 0) {
      const topLevel = parentsLevel[0].original[0];
      const filteredData = prepareTreeItemsForAmCharts(topLevel);
      filteredInfo.value = [filteredData];

      // Set the data for the chart
      generateChart(filteredData);
    } else {
      filteredInfo.value = [];
    }
  }
});

const generateChart = (data: any) => {
  // Destruye la instancia anterior si existe
  if (chartRoot) {
    chartRoot.dispose();
  }

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
      initialDepth: 10,
      valueField: "value",
      categoryField: "name",
      childDataField: "children",
      inversed: true
      // orientation: "horizontal"
    })
  );
  series.circles.template.setAll({
    radius: 20
  });
  series.outerCircles.template.setAll({
    radius: 20
  });
  // series.nodes.template.setAll({
  //   draggable: false,
  //   cursorOverStyle: "default"
  // });

  // Configurar tooltip para mostrar nombre y tipo
  series.labels.template.setAll({
    minScale: 0,
    fontSize: 30
  });
  series.nodes.template.set("tooltipText", "{name}: {type}");

  series.data.setAll([data]);
  series.set("selectedDataItem", series.dataItems[0]);

  series.appear(1000, 100);
};
</script>
