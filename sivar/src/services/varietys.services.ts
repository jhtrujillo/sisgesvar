import api from "@/services/api";
import urls from "@/services/urls";

// Servicio de los alineamientos petición GET (para traer el array de cada una de las herramientas que serán embebidas)
export async function getVarietysList() {
  return await api.get(urls.API_VARIETYSLIST, {}, true);
}

const varietysService = {
  getVarietysList
};

export default varietysService;
