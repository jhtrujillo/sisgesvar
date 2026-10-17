import api from "@/services/api";
import urls from "@/services/urls";

export async function getVariety() {
  return await api.get(urls.API_VARIETY, {}, true);
}

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
  return await api.post(urls.API_URL + "varietys", { nm_vrdad }, {}, true);
}

const varietysService = {
  getVariety,
  getVarietysList,
  createVariety,
  getVarietyProfile,
  getVarietyCrossingsHistory
};

export default varietysService;
