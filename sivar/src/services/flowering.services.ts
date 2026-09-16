import api from "@/services/api";
import urls from "@/services/urls";

// Servicio de los alineamientos petición GET (para traer el array de cada una de las herramientas que serán embebidas)
export async function getFloweringList(historico: boolean = false) {
  return await api.get(urls.API_FLOWERINGLIST + (historico ? '?historico=true' : ''), {}, true);
}

export async function getBolsaComunFlores(): Promise<any> {
  const url = `${urls.API_URL}crossing/programming/flores_otros_proyectos?proyecto_actual=`;
  return await api.get(url, {}, true);
}

const floweringService = {
  getFloweringList,
  getBolsaComunFlores
};

export default floweringService;
