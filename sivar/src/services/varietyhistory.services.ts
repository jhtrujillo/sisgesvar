import api from "@/services/api";
import urls from "@/services/urls";

// Servicio para obtener el historial de variedades
export async function getVarietyHistory(variety: string, state: number, type: number): Promise<any> {
  const url = `${urls.API_VARIETY_HISTORY}/${variety}/${state}/${type}`;
  return await api.get(url, {}, true);
}

const varietyHistoryService = {
  getVarietyHistory
};

export default varietyHistoryService;
