import router from "@/router";
import { ROUTE_ALIGNMENTS } from "../router/routes";
import alignmentsService from "@/services/alignments.services";

import { defineStore } from "pinia";
import { computed, ref } from "vue";
import { useMainStore } from "./main";
import type { Alignments } from "../services/types";
// Lógica de para guardar los parametros del Formulario "Alineamientos de secuencias" de manera correcta,
//con su respectiva lógica de errores.
export const useAlignmentsStore = defineStore(
  "alignments",
  () => {
    const parametros = ref("");
    const comando = ref("");
    const refresh = ref("");
    const alignmentsInfo = ref<Alignments>();

    const mainStore = useMainStore();

    const SaveAlignments = async (model: Alignments) => {
      try {
        mainStore.isBusy = true;
        mainStore.error = "";
        mainStore.responseMessage = "";

        const result = await alignmentsService.alignments(model);

        if (result.data) {
          return true;
        } else {
          return false;
        }
      } catch (error) {
        mainStore.error = String("Error al guardar");
      } finally {
        mainStore.isBusy = false;
      }
    };

    return {
      parametros,
      refresh,
      comando,
      alignmentsInfo,
      SaveAlignments
    };
  },
  {
    persist: true
  }
);
