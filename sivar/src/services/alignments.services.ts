import api from "@/services/api";
import urls from "@/services/urls";
import type { Alignments } from "./types";

// Servicios de alineamientos, petición POST (evniar parámetros
// del formulario) y GET(traer procesos completados)

async function alignments(model: Alignments) {
  return await api.post(urls.API_ALIGNMENTS, model, false); //unsecured
}

async function getAlignments() {
  return await api.get(urls.API_ALIGNMENTSLIST, {}, true);
}

const alignmentsService = {
  alignments,
  getAlignments
};

export default alignmentsService;
