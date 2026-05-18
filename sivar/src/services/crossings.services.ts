import api from "@/services/api";
import urls from "@/services/urls";

// Servicio de los alineamientos petición GET (para traer el array de cada una de las herramientas que serán embebidas)
export async function getCrossingsList(currentPage: number, perPage: number) {
  const url = `${urls.API_CROSSING_LIST}?page=${currentPage}`;
  return await api.get(url, { params: { perPage, currentPage } }, true);
}

async function getCrossingInitialData() {
  return await api.get(urls.API_CROSSING_INITIAL_DATA, {}, true);
}
async function getParametizeWeightedCrossing(proyecto: string, megaAmbiente: string): Promise<any> {
  const url = `${urls.API_PARAMETIZE_WEIGHTED_CROSSING}/${proyecto}/${megaAmbiente}`;
  return await api.get(url, {}, true);
}
async function modifyFeatures(caracteristica: string, proyecto: string, nivel: string, ponderado: string, nuevo: number): Promise<any> {
  const url = `${urls.API_MODIFY_FEATURES_CROSSING}/${caracteristica}/${proyecto}/${nivel}/${ponderado}/${nuevo}`;
  return await api.post(url, {}, true);
}
async function getMatrix(proyectos: string, proyecto: string, testigo: string): Promise<any> {
  const url = `${urls.API_GENERATE_MATRIX}/${proyectos}/${proyecto}/${testigo}`;
  return await api.get(url, {}, true);
}
async function GetSuggestionCrossings(proyectos: string, proyecto: string, testigo: string, megaAmbiente: string): Promise<any> {
  const url = `${urls.API_SUGGESTION_CROSSING}/${proyectos}/${proyecto}/${testigo}/${megaAmbiente}`;
  return await api.get(url, {}, true);
}
async function GetSuggestionCrossingsPerProject(proyectos: string, proyecto: string, testigo: string, megaAmbiente: string): Promise<any> {
  const url = `${urls.API_SUGGESTION_CROSSING_PER_PROJECT}/${proyectos}/${proyecto}/${testigo}/${megaAmbiente}`;
  return await api.get(url, {}, true);
}

async function saveWeight(proyecto: string): Promise<any> {
  const url = `${urls.API_URL}crossing/programming/save_weight/${proyecto}`;
  return await api.get(url, {}, true);
}

async function saveCrossing(
  madre: string,
  padres: string,
  observaciones: string,
  idPonderados: string,
  proyectos: string,
  autofecundado: number
): Promise<any> {
  const url = `${urls.API_URL}crossing/programming/save_crossing/${madre}/${padres}/${observaciones}/${idPonderados}/${proyectos}/${autofecundado}`;
  return await api.get(url, {}, true);
}

const CrossingsService = {
  getCrossingsList,
  getCrossingInitialData,
  getParametizeWeightedCrossing,
  modifyFeatures,
  getMatrix,
  GetSuggestionCrossings,
  GetSuggestionCrossingsPerProject,
  saveWeight,
  saveCrossing
};

export default CrossingsService;
