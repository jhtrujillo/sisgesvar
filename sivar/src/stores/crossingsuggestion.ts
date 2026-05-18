import { defineStore } from "pinia";
import { ref } from "vue";
import type { SuggestionCrossings } from "@/services/types";
import CrossingsService from "@/services/crossings.services";

export const useSuggestionCrossingStore = defineStore("sueggestionCrossing", () => {
  // Estado para almacenar el el formato de superintendencia
  const suggestionCrossingsFilter = ref<SuggestionCrossings[]>([]);

  const getSuggestionCrossingList = async (proyectos: string, proyecto: string, testigo: string, megaAmbiente: string): Promise<void> => {
    try {
      const result = await CrossingsService.GetSuggestionCrossings(proyectos, proyecto, testigo, megaAmbiente);

      if (result.status === 200) {
        suggestionCrossingsFilter.value = result.data;
      }
    } catch (error) {
      console.error("Error al consultar la lista de Superintendencia", error);
    }
  };

  return {
    suggestionCrossingsFilter,
    getSuggestionCrossingList
  };
});
