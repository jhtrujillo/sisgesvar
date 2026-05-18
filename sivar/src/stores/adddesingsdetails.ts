import { defineStore } from "pinia";
import { ref } from "vue";
import type { DiseñosDetalles } from "@/services/types";
import ExperimentsService from "@/services/experiments.services";
import { useMainStore } from "./main";

export const useAddDesingsDetailsStore = defineStore("add_desings_details", () => {
  const refresh = ref("");
  const addDesingsDetailsInfo = ref<DiseñosDetalles>();
  const mainStore = useMainStore();

  const SaveaddDesingsDetails = async (model: DiseñosDetalles) => {
    try {
      mainStore.isBusy = true;
      mainStore.error = "";
      mainStore.responseMessage = "";

      const result = await ExperimentsService.addDesingsDetails(model);

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
    refresh,
    addDesingsDetailsInfo,
    SaveaddDesingsDetails
  };
});
