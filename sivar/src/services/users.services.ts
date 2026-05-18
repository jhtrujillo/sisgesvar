import api from "@/services/api";
import urls from "@/services/urls";
import type { User } from "./types";

//Servicio para CRUD de usuarios
export async function list() {
  return await api.get(urls.API_USERS, {}, true);
}

export async function get(id: number) {
  return await api.get(urls.API_USERS + id, {}, true);
}

export async function create(model: User) {
  return await api.post(urls.API_USERS, model, true);
}

export async function update(id: number, model: User) {
  return await api.put(urls.API_USERS + id, model, true);
}

export async function deleteItem(id: number) {
  return await api.delete(urls.API_USERS + id, {}, true);
}

const usersService = {
  list,
  get,
  create,
  update,
  deleteItem
};

export default usersService;
