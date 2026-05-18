import { defineStore } from "pinia";
import { ref } from "vue";
import type { TreeData } from "@/services/types";
import ParentsService from "@/services/parents.services";

export const useParentsLevelStore = defineStore(
  "getParentsLevel",
  () => {
    // Estado para almacenar el historial de variedades
    const ParentsLevel = ref<TreeData[]>([]);
    /**
     * Retrieves the history of a variety.
     * @param variety - The variety to retrieve the history for.
     * @returns Promise<void>
     */
    const getParentsLevelDiagram = async (variety: string): Promise<void> => {
      try {
        const result = await ParentsService.getParentsLevel(variety);

        if (result.status === 200) {
          ParentsLevel.value = result.data;
        }
      } catch (error) {
        console.error("Error al consultar el historial de variedades:", error);
      }
    };

    return {
      ParentsLevel,
      getParentsLevelDiagram
    };
  },
  {
    persist: true
  }
);
