import { defineStore } from "pinia";

import { ref } from "vue";
import type { searchParameters } from "@/services/types";
import ExperimentsService from "../services/experiments.services";
// Lógica de para traer las herramientas que serán embebidas en forma de array de manera correcta,
//con su respectiva lógica de errores.
export const useSearchParametersStore = defineStore(
  "searchParameters",
  () => {
    const searchParameters = ref<searchParameters>({
      listProgramas: [],
      listAreas: [],
      listProyectos: [],
      listSeries: [],
      listEstados: [],
      listTemporadas: [],
      listCruzamientoMadre: [],
      listCruzamientoPadre: [],
      listTipoEnsayo: [],
      listTipoParcela: [],
      listDisenoExp: [],
      listVariables: []
    });

    const getSearchParametersResult = async () => {
      try {
        const result = await ExperimentsService.getSearchParameters();
        if (result.status === 200) {
          searchParameters.value = result.data;
        }
      } catch (error) {
        console.error("Error al obtener los parámetros de búsqueda:", error);
      }
    };

    return {
      getSearchParametersResult,
      searchParameters
    };
  },
  {
    persist: true
  }
);
