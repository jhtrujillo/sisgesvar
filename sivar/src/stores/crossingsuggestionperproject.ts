import { defineStore } from "pinia";
import { ref } from "vue";
import type { SuggestionCrossingsPerProject } from "@/services/types";
import CrossingsService from "@/services/crossings.services";

export const useSuggestionCrossingPerProjectStore = defineStore("sueggestionCrossingPerProject", () => {
  // Estado para almacenar el el formato de superintendencia
  const suggestionCrossingsPerProjectFilter = ref<SuggestionCrossingsPerProject[]>([]);

  const getSuggestionCrossingPerProjectList = async (proyectos: string, proyecto: string, testigo: string, megaAmbiente: string): Promise<void> => {
    try {
      const result = await CrossingsService.GetSuggestionCrossingsPerProject(proyectos, proyecto, testigo, megaAmbiente);

      if (result.status === 200) {
        suggestionCrossingsPerProjectFilter.value = result.data;
      }
    } catch (error) {
      console.error("Error al consultar la lista de Superintendencia", error);
    }
  };

  return {
    suggestionCrossingsPerProjectFilter,
    getSuggestionCrossingPerProjectList
  };
});
