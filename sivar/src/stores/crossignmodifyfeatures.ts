import { defineStore } from "pinia";
import { ref } from "vue";
import type { ModifyWeighted } from "@/services/types";
import { useMainStore } from "./main";
import CrossingsService from "@/services/crossings.services";

export const useModifyFeaturesCrossingStore = defineStore("modifyFeaturesCrossing", () => {
  // Estado para almacenar el el formato de superintendencia
  const modifyFeaturesCrossingFilter = ref<ModifyWeighted[]>([]);
  const refresh = ref("");
  const mainStore = useMainStore();

  const getModifyFeaturesCrossingList = async (caracteristica: string, proyecto: string, nivel: string, ponderado: string, nuevo: number) => {
    try {
      mainStore.isBusy = true;
      mainStore.error = "";
      mainStore.responseMessage = "";

      const result = await CrossingsService.modifyFeatures(caracteristica, proyecto, nivel, ponderado, nuevo);

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
    modifyFeaturesCrossingFilter,
    getModifyFeaturesCrossingList,
    refresh
  };
});
