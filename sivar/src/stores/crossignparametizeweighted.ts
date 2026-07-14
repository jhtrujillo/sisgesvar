import { defineStore } from "pinia";
import { ref } from "vue";
import type { ParametizeWeighted } from "@/services/types";
import CrossingsService from "@/services/crossings.services";

export const useParametizeWeightedCrossingStore = defineStore("parametizeweightedCrossing", () => {
  // Estado para almacenar el el formato de superintendencia
  const parametizeWeightedCrossingFilter = ref<ParametizeWeighted>({} as ParametizeWeighted);

  const getParametizeWeightedCrossingList = async (proyecto: string, megaAmbiente: string): Promise<void> => {
    try {
      const result = await CrossingsService.getParametizeWeightedCrossing(proyecto, megaAmbiente);

      if (result.status === 200) {
        parametizeWeightedCrossingFilter.value = result.data;
      }
    } catch (error) {
      console.error("Error al consultar la lista de Superintendencia", error);
    }
  };

  return {
    parametizeWeightedCrossingFilter,
    getParametizeWeightedCrossingList
  };
});
