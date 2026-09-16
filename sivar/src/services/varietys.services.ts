import api from "@/services/api";
import urls from "@/services/urls";

// Servicio de los alineamientos petición GET (para traer el array de cada una de las herramientas que serán embebidas)
export async function getVarietysList() {
  return await api.get(urls.API_VARIETYSLIST, {}, true);
}

export async function getVarietyProfile(varName: string) {
  return await api.get(`${urls.API_VARIETY_PROFILE}/${varName}`, {}, true);
}

export async function getVarietyCrossingsHistory(varName: string) {
  return await api.get(`${urls.API_VARIETY_CROSSINGS_HISTORY}/${varName}`, {}, true);
}

export async function createVariety(nm_vrdad: string) {
  return await api.post(urls.API_URL + 'varietys', { nm_vrdad }, {}, true);
}

const varietysService = {
  getVarietysList,
  createVariety,
  getVarietyProfile,
  getVarietyCrossingsHistory
};

export default varietysService;
