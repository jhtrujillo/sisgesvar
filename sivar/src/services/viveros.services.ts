import api from "./api";
import urls from "./urls";

export default {
  getViveros() {
    return api.get(urls.API_VIVEROS);
  },
  getVivero(id: number | string) {
    return api.get(`${urls.API_VIVEROS}/${id}`);
  },
  getNextCorteConsecutivo(origen_parcela: string) {
    return api.get(`${urls.API_VIVEROS}/next-corte-consecutivo`, { params: { origen_parcela } });
  },
  getEstructura(id: number | string) {
    return api.get(`${urls.API_VIVEROS}/${id}/estructura`);
  },
  createVivero(data: any) {
    return api.post(urls.API_VIVEROS, data);
  },
  updateVivero(id: number | string, data: any) {
    return api.put(`${urls.API_VIVEROS}/${id}`, data);
  },
  deleteVivero(id: number | string) {
    return api.delete(`${urls.API_VIVEROS}/${id}`);
  },
  async getIngenios() {
    const response = await api.get(urls.API_INGENIOS);
    return response;
  },

  async getHaciendas(ingenio: string) {
    const response = await api.get(`${urls.API_HACIENDAS}/${ingenio}`);
    return response;
  },

  async getSuertes(hacienda: string) {
    const response = await api.get(`${urls.API_SUERTES}/${hacienda}`);
    return response;
  },

  registrarCosecha(id: number | string, payload: any) {
    return api.post(`${urls.API_VIVEROS}/${id}/cosechar`, payload);
  },

  getHistorialCosechas(id: number | string) {
    return api.get(`${urls.API_VIVEROS}/${id}/cosechas`);
  },

  getParcelas(vivero_id: string | number) {
    return api.get(`${urls.API_VIVEROS}/${vivero_id}/parcelas`);
  },

  addParcela(vivero_id: string | number, data: any) {
    return api.post(`${urls.API_VIVEROS}/${vivero_id}/parcelas`, data);
  },

  importBatchParcelas(vivero_id: string | number, parcelas: any[]) {
    return api.post(`${urls.API_VIVEROS}/${vivero_id}/parcelas/import-batch`, { parcelas });
  },

  deleteParcela(vivero_id: string | number, parcela_id: string | number) {
    return api.delete(`${urls.API_VIVEROS}/${vivero_id}/parcelas/${parcela_id}`);
  },

  deleteAllParcelas(vivero_id: string | number) {
    return api.delete(`${urls.API_VIVEROS}/${vivero_id}/parcelas`);
  },

  getProyectos() {
    return api.get(urls.API_PROYECTOS);
  },

  getResponsables() {
    return api.get(urls.API_RESPONSABLES);
  },

  getAmbientes() {
    return api.get(urls.API_AMBIENTES);
  },

  getCaracteresPorProyecto(id: string | number) {
    return api.get(`${urls.API_PROYECTOS}/${id}/caracteres`);
  },

  createCaracter(proyecto_id: string | number, payload: any) {
    return api.post(`${urls.API_PROYECTOS}/${proyecto_id}/caracteres`, payload);
  },

  getLotes(params?: any) {
    return api.get(urls.API_LOTES, { params });
  },

  createLote(data: any) {
    return api.post(urls.API_LOTES, data);
  },

  updateLote(id: number | string, data: any) {
    return api.put(`${urls.API_LOTES}/${id}`, data);
  },

  deleteLote(id: number | string) {
    return api.delete(`${urls.API_LOTES}/${id}`);
  },

  trasladarLote(id: number | string, data: any) {
    return api.post(`${urls.API_VIVEROS}/${id}/trasladar-lote`, data);
  }
};
