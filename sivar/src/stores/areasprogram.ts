import { defineStore } from "pinia";
import { ref } from "vue";
import type { listAreas } from "@/services/types";
import ExperimentsService from "@/services/experiments.services";

interface AreasProgramResponse {
  success: boolean;
  listAreas: listAreas[];
}
export const useAreasProgramStore = defineStore("areasProgram", () => {
  // Estado para almacenar el el formato de superintendencia
  const areasProgramFilter = ref<AreasProgramResponse | null>(null);

  const getAreasProgramList = async (idProgram: string): Promise<void> => {
    try {
      const result = await ExperimentsService.getAreasProgram(idProgram);

      if (result.status === 200) {
        areasProgramFilter.value = result.data;
      }
    } catch (error) {
      console.error("Error al areas", error);
    }
  };

  return {
    areasProgramFilter,
    getAreasProgramList
  };
});
