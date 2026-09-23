import api from "./api";
import urls from "./urls";

export default {
  /**
   * @param formData FormData con los campos: file, sheet_name, mapping (JSON string)
   * @returns Tupla [response, error] (patrón de api.postWithImages)
   */
  importarSiembraCampo(formData: FormData) {
    return api.postWithImages(urls.API_SIEMBRA_CAMPO_IMPORT, formData);
  }
};
