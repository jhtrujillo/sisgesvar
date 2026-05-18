import api from "@/services/api";
import urls from "@/services/urls";

// Servicio para obtener el historial de variedades
async function getParents(variety: string): Promise<any> {
  const url = `${urls.API_PARENTS_DIAGRAM}/${variety}`;
  return await api.get(url, {}, true);
}
async function getParentsLevel(variety: string): Promise<any> {
  const url = `${urls.API_PARENTS_DIAGRAM_LEVEL}/${variety}`;
  return await api.get(url, {}, true);
}

const ParentsService = {
  getParents,
  getParentsLevel
};

export default ParentsService;
