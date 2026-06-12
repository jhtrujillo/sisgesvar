import api from "./api";
import urls from "./urls";

export const EnsayosService = {
  // --- Ensayos ---
  getEnsayos(params: any) {
    return api.get(urls.API_ENSAYOS, { params });
  },

  getDashboard() {
    return api.get(`${urls.API_ENSAYOS}/dashboard`, {});
  },

  exportEnsayos(params: any) {
    // Return base URL for direct download or fetch blob
    const query = new URLSearchParams(params).toString();
    return `${urls.API_ENSAYOS}/export?${query}`;
  },

  importEnsayos(formData: FormData) {
    return api.postWithImages(`${urls.API_ENSAYOS}/import`, formData);
  },

  confirmImport(data: any) {
    return api.post(`${urls.API_ENSAYOS}/import/confirm`, data);
  },

  updateEnsayo(id: number, data: any) {
    return api.patch(`${urls.API_ENSAYOS}/${id}`, data);
  },

  // --- Estandarización ---
  getStandardizationPreview() {
    return api.get(`${urls.API_ENSAYOS}/standardization/preview`, {});
  },

  executeStandardization(data: any) {
    return api.post(`${urls.API_ENSAYOS}/standardization/execute`, data);
  },

  // --- Catálogos ---
  getCatalogos(params: any) {
    return api.get(urls.API_CATALOGOS, { params });
  },

  storeCatalogo(data: any) {
    return api.post(urls.API_CATALOGOS, data);
  },

  updateCatalogo(id: number, data: any) {
    return api.put(`${urls.API_CATALOGOS}/${id}`, data);
  },

  deleteCatalogo(id: number) {
    return api.delete(`${urls.API_CATALOGOS}/${id}`, {});
  },

  mergeCatalogos(data: any) {
    return api.post(`${urls.API_CATALOGOS}/merge`, data);
  },

  // --- Actividades ---
  getActividades(params: any) {
    return api.get(urls.API_ACTIVIDADES, { params });
  },

  // --- Adjuntos ---
  getAdjuntos(ensayoId: number) {
    return api.get(`${urls.API_ENSAYOS}/${ensayoId}/adjuntos`, {});
  },

  uploadAdjunto(ensayoId: number, formData: FormData) {
    return api.postWithImages(`${urls.API_ENSAYOS}/${ensayoId}/adjuntos`, formData);
  },

  downloadAdjunto(adjuntoId: number) {
    return `${urls.API_URL}adjuntos/${adjuntoId}/download`;
  },

  deleteAdjunto(adjuntoId: number) {
    return api.delete(`${urls.API_URL}adjuntos/${adjuntoId}`, {});
  }
};
