import api from "@/services/api";
import urls from "@/services/urls";

// Servicio de los alineamientos petición GET (para traer el array de cada una de las herramientas que serán embebidas)
export async function getVarietysList() {
  return await api.get(urls.API_VARIETYSLIST, {}, true);
}

export async function createVariety(nm_vrdad: string) {
  return await api.post('/api/varietys', { nm_vrdad }, {}, true);
}

const varietysService = {
  getVarietysList,
  createVariety
};

export default varietysService;
