import api from "@/services/api";
import urls from "@/services/urls";
import type { Login } from "./types";

//Servicio para autenticación de usuario y para trae su información
async function login(model: Login) {
  return await api.post(urls.API_AUTH_LOGIN, model, false); //unsecured
}

async function getUserInfo() {
  return await api.post(urls.API_AUTH_USER_INFO, {}, true);
}

const userService = {
  login,
  getUserInfo
};

export default userService;
