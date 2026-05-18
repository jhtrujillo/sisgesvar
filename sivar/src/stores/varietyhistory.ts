import { defineStore } from "pinia";
import { ref } from "vue";
import type { HistoryVariety } from "@/services/types";
import varietyHistoryService from "@/services/varietyhistory.services";

export const useVarietyHistoryStore = defineStore(
  "varietyHistory",
  () => {
    // Estado para almacenar el historial de variedades
    const varietyHistory = ref<HistoryVariety[]>([]);
    /**
     * Retrieves the history of a variety.
     * @param variety - The variety to retrieve the history for.
     * @param state - The state to filter the history by.
     * @param type - The type to filter the history by.
     * @returns Promise<void>
     */
    const getVarietyHistory = async (variety: string, state: number, type: number): Promise<void> => {
      try {
        const result = await varietyHistoryService.getVarietyHistory(variety, state, type);

        if (result.status === 200) {
          varietyHistory.value = result.data.data;
        }
      } catch (error) {
        console.error("Error al consultar el historial de variedades:", error);
      }
    };

    return {
      varietyHistory,
      getVarietyHistory
    };
  },
  {
    persist: true
  }
);
