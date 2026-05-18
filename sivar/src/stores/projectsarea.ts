import { defineStore } from "pinia";
import { ref } from "vue";
import type { listProyectos } from "@/services/types";
import ExperimentsService from "@/services/experiments.services";
interface ProjectsAreaResponse {
  success: boolean;
  listProyectos: listProyectos[];
}
export const useProjectsAreaStore = defineStore("projectsArea", () => {
  // Estado para almacenar el el formato de superintendencia
  const projectsAreaFilter = ref<ProjectsAreaResponse | null>(null);

  const getProjectsAreaList = async (idArea: string): Promise<void> => {
    try {
      const result = await ExperimentsService.getProjectsArea(idArea);

      if (result.status === 200) {
        projectsAreaFilter.value = result.data;
      }
    } catch (error) {
      console.error("Error al areas", error);
    }
  };

  return {
    getProjectsAreaList,
    projectsAreaFilter
  };
});
