import api from "@/services/api";
import urls from "@/services/urls";

/**
 * Obtiene los parámetros de búsqueda (programas, series, estados) para el filtro del libro.
 */
async function getSearchParameters(): Promise<any> {
  return await api.get(urls.API_SEARCH_PARAMETERS, {}, true);
}

/**
 * Obtiene las áreas asociadas a un programa/servicio.
 */
async function getAreasProgram(idPrograma: string): Promise<any> {
  const url = `${urls.API_AREAS_PROGRAM}/${idPrograma}`;
  return await api.get(url, {}, true);
}

/**
 * Obtiene los proyectos asociados a un área de trabajo.
 */
async function getProjectsArea(idArea: string): Promise<any> {
  const url = `${urls.API_PROJECTS_AREA}/${idArea}`;
  return await api.get(url, {}, true);
}

/**
 * Obtiene el libro de campo para un proyecto, serie y estado dados.
 */
async function getLibroCampo(idPr: string, srie: string, estdo: string): Promise<any> {
  const url = `${urls.API_LIBRO_CAMPO}/${idPr}/${srie}/${estdo}`;
  return await api.get(url, {}, true);
}

/**
 * Crea el libro de campo con los campos/variables seleccionados.
 */
async function crearLibroCampo(libro: any[]): Promise<any> {
  return await api.post(urls.API_CREAR_LIBRO_CAMPO, { libro }, true);
}

const LibroCampoService = {
  getSearchParameters,
  getAreasProgram,
  getProjectsArea,
  getLibroCampo,
  crearLibroCampo
};

export default LibroCampoService;
