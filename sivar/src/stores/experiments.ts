import { defineStore } from "pinia";
import { ref } from "vue";
import type { Experimento } from "@/services/types";
import ExperimentsService from "@/services/experiments.services";

interface ExperimentosResponse {
  success: boolean;
  experimento: Experimento[];
}
export const useExperimentsStore = defineStore("experiments", () => {
  // Estado para almacenar el el formato de superintendencia
  const experimentsFilter = ref<ExperimentosResponse | null>(null);

  const getExperimentsList = async (idProyecto: string, Serie: string, Estado: string): Promise<void> => {
    try {
      const result = await ExperimentsService.getExperiment(idProyecto, Serie, Estado);

      if (result.status === 200) {
        experimentsFilter.value = result.data;
      }
    } catch (error) {
      console.error("Error al areas", error);
    }
  };

  return {
    experimentsFilter,
    getExperimentsList
  };
});
