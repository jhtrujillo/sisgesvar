import { defineStore } from "pinia";
import { ref } from "vue";
import type { TratamientosTemporada } from "@/services/types";
import ExperimentsService from "@/services/experiments.services";

// interface AreasProgramResponse {
//   success: boolean;
//   listAreas: listAreas[];
// }
export const useTreatmentsSeasonStore = defineStore("treatments", () => {
  // Estado para almacenar el el formato de superintendencia
  const treatmentsSeasonFilter = ref<TratamientosTemporada[]>([]);

  const getTreatmentsSeasonList = async (año: string, idDiseñoEncabezado: string, minPlantulas: number, plantulasTotales: number): Promise<void> => {
    try {
      const result = await ExperimentsService.getTreatmentsSeason(año, idDiseñoEncabezado, minPlantulas, plantulasTotales);

      if (result.status === 200) {
        treatmentsSeasonFilter.value = result.data;
      }
    } catch (error) {
      console.error("Error al areas", error);
    }
  };

  return {
    treatmentsSeasonFilter,
    getTreatmentsSeasonList
  };
});
