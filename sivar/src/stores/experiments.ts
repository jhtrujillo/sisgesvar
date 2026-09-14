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

  const createExperiment = async (idProyecto: string | number, Serie: string | number, Estado: string, idAmbiente: number = 1): Promise<any> => {
    try {
      const payload = {
        id_pr: idProyecto,
        srie: Serie,
        estdo: Estado,
        id_ambnte: idAmbiente
      };
      const result = await ExperimentsService.grabarEncabezado(payload);
      return result;
    } catch (error) {
      console.error("Error al crear experimento", error);
      throw error;
    }
  };

  return {
    experimentsFilter,
    getExperimentsList,
    createExperiment
  };
});
