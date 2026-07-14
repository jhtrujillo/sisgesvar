import api from "./api";
import urls from "./urls";

export default {
  getViveros() {
    return api.get(urls.API_VIVEROS);
  },
  getVivero(id: number | string) {
    return api.get(`${urls.API_VIVEROS}/${id}`);
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
  }
};
