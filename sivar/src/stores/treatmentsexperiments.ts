import { defineStore } from "pinia";
import { ref } from "vue";
import type { TratamientosExperimentos } from "@/services/types";
import ExperimentsService from "@/services/experiments.services";

// interface AreasProgramResponse {
//   success: boolean;
//   listAreas: listAreas[];
// }
export const useTreatmentsExperimentsStore = defineStore("treatmentsExperiments", () => {
  // Estado para almacenar el el formato de superintendencia
  const treatmentsExperimentsFilter = ref<TratamientosExperimentos>({
    tratamientosF: [],
    tratamientosI: [],
    testigosFijosF: [],
    testigosFijosI: [],
    testigosMovilesF: [],
    testigosMovilesI: [],
    distTratF: [],
    distTratI: [],
    experimentoF: [],
    experimentoI: []
  });

  const getTreatmentsExperimentsList = async (idDiseñoEncabezadoInicial: string, idDiseñoEncabezadoFinal: string): Promise<void> => {
    try {
      const result = await ExperimentsService.getTreatmentsExperiments(idDiseñoEncabezadoInicial, idDiseñoEncabezadoFinal);

      if (result.status === 200) {
        treatmentsExperimentsFilter.value = result.data;
      }
    } catch (error) {
      console.error("Error al areas", error);
    }
  };

  return {
    treatmentsExperimentsFilter,
    getTreatmentsExperimentsList
  };
});
