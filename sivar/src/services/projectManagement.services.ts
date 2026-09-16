import api from "@/services/api";
import urls from "@/services/urls";

export async function getProyectosAdmin(): Promise<any> {
  const url = `${urls.API_URL}admin/proyectos`;
  return await api.get(url, {}, true);
}

export async function getDetalleProyecto(proyectoId: number): Promise<any> {
  const url = `${urls.API_URL}admin/proyectos/${proyectoId}/detalles`;
  return await api.get(url, {}, true);
}

export async function getUsuariosProyecto(proyectoId: number): Promise<any> {
  const url = `${urls.API_URL}admin/proyectos/${proyectoId}/usuarios`;
  return await api.get(url, {}, true);
}

export async function assignUsuarioProyecto(proyectoId: number, usuarioId: number, rol: string): Promise<any> {
  const url = `${urls.API_URL}admin/proyectos/${proyectoId}/usuarios`;
  return await api.post(url, { usuario_id: usuarioId, rol }, {}, true);
}

export async function removeUsuarioProyecto(proyectoId: number, usuarioId: number): Promise<any> {
  const url = `${urls.API_URL}admin/proyectos/${proyectoId}/usuarios/${usuarioId}`;
  return await api.delete(url, {}, true);
}

export async function getUsuariosDisponibles(query: string = ""): Promise<any> {
  const url = `${urls.API_URL}admin/usuarios-disponibles?q=${encodeURIComponent(query)}`;
  return await api.get(url, {}, true);
}

export async function getEstabilidadAgronomica(proyectoId: number, variable: string = "tsh"): Promise<any> {
  const url = `${urls.API_URL}admin/proyectos/${proyectoId}/estabilidad-agronomica?variable=${variable}`;
  return await api.get(url, {}, true);
}

const projectManagementService = {
  getProyectosAdmin,
  getDetalleProyecto,
  getUsuariosProyecto,
  assignUsuarioProyecto,
  removeUsuarioProyecto,
  getUsuariosDisponibles,
  getEstabilidadAgronomica
};

export default projectManagementService;
