import api from "@/services/api";
import urls from "@/services/urls";
import type { DiseñosDetalles } from "./types";

// Servicio de los alineamientos petición GET (para traer el array de cada una de las herramientas que serán embebidas)
export async function getSearchParameters() {
  return await api.get(urls.API_SEARCH_PARAMETERS, {}, true);
}

async function getAreasProgram(idPrograma: string): Promise<any> {
  const url = `${urls.API_AREAS_PROGRAM}/${idPrograma}`;
  return await api.get(url, {}, true);
}
async function getProjectsArea(idArea: string): Promise<any> {
  const url = `${urls.API_PROJECTS_AREA}/${idArea}`;
  return await api.get(url, {}, true);
}
async function getExperiment(idProyecto: string, Serie: string, Estado: string): Promise<any> {
  const url = `${urls.API_EXPERIMENT}/${idProyecto}/${Serie}/${Estado}`;
  return await api.get(url, {}, true);
}
async function getTreatmentsSeason(año: string, idDiseñoEncabezado: string, minPlantulas: number, plantulasTotales: number): Promise<any> {
  const url = `${urls.API_TREATMENTS_SEASON}/${año}/${idDiseñoEncabezado}/${minPlantulas}/${plantulasTotales}`;
  return await api.get(url, {}, true);
}
async function getTreatmentsExperiments(idDiseñoEncabezadoInicial: string, idDiseñoEncabezadoFinal: string): Promise<any> {
  const url = `${urls.API_TREATMENTS_EXPERIMENTS}/${idDiseñoEncabezadoInicial}/${idDiseñoEncabezadoFinal}`;
  return await api.get(url, {}, true);
}
async function addDesingsDetails(model: DiseñosDetalles) {
  return await api.post(urls.API_ADD_DESIGNS_DETAILS, model, false);
}

async function GetSuggestionCrossings(proyectos: string, proyecto: string, testigo: string, megaAmbiente: string): Promise<any> {
  const url = `${urls.API_SUGGESTION_CROSSING}/${proyectos}/${proyecto}/${testigo}/${megaAmbiente}`;
  return await api.get(url, {}, true);
}

async function GetSuggestionCrossingsPerProject(proyectos: string, proyecto: string, testigo: string, megaAmbiente: string): Promise<any> {
  const url = `${urls.API_SUGGESTION_CROSSING_PER_PROJECT}/${proyectos}/${proyecto}/${testigo}/${megaAmbiente}`;
  return await api.get(url, {}, true);
}

const ExperimentsService = {
  getSearchParameters,
  getAreasProgram,
  getProjectsArea,
  getExperiment,
  getTreatmentsSeason,
  getTreatmentsExperiments,
  addDesingsDetails,
  GetSuggestionCrossings,
  GetSuggestionCrossingsPerProject
};

export default ExperimentsService;
