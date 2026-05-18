import api from "@/services/api";
import urls from "@/services/urls";

// Servicio de los alineamientos petición GET (para traer el array de cada una de las herramientas que serán embebidas)
export async function getGermoplasmBankList(currentPage: number, perPage: number) {
  const url = `${urls.API_GERMOPLASM_BANK}?page=${currentPage}`;
  return await api.get(url, { params: { perPage, currentPage } }, true);
}

const germoplasmBankService = {
  getGermoplasmBankList
};

export default germoplasmBankService;
