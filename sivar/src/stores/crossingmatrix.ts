import { defineStore } from "pinia";
import { ref } from "vue";
import type { Matrix, CruzamientoSeleccionado } from "@/services/types";
import CrossingsService from "@/services/crossings.services";

export const useMatrixCrossingStore = defineStore(
  "matrixCrossing",
  () => {
    // Estado para almacenar el el formato de superintendencia
    const matrixCrossingsFilter = ref<Matrix[]>([]);
    const cruzamientosSeleccionados = ref<CruzamientoSeleccionado[]>([]);
    const getMatrixCrossingList = async (proyectos: string, proyecto: string, testigo: string, ambiente: string): Promise<void> => {
      try {
        const result = await CrossingsService.getMatrix(proyectos, proyecto, testigo, ambiente);

        if (result.status === 200) {
          matrixCrossingsFilter.value = result.data;
        }
      } catch (error) {
        console.error("Error al consultar", error);
      }
    };

    return {
      matrixCrossingsFilter,
      getMatrixCrossingList,
      cruzamientosSeleccionados
    };
  },
  {
    persist: true
  }
);
