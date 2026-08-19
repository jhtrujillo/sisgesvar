import router from "@/router";
import { ROUTE_LOGIN, ROUTE_HOME } from "../router/routes";
import userService from "../services/user.services";

import { defineStore } from "pinia";
import { computed, ref } from "vue";
import { useMainStore } from "./main";
import type { Login, User } from "../services/types";
// Lógica de para que la autenticación de usuario funcione de manera correcta
//y con sus respectivos permisos.
export const useUserStore = defineStore(
  "user",
  () => {
    const token = ref("");
    const refresh = ref("");
    const userInfo = ref<User>();

    const mainStore = useMainStore();

    const login = async (model: Login) => {
      try {
        mainStore.isBusy = true;
        mainStore.error = "";
        mainStore.responseMessage = "";

        const result = await userService.login(model);

        if (result.data.access_token) {
          token.value = result.data.access_token;
          refresh.value = result.data.refresh;

          const resultInfo = await userService.getUserInfo();
          if (resultInfo.status === 200) {
            userInfo.value = resultInfo.data;
          }
          router.push(ROUTE_HOME);
        } else if (result.data) {
          mainStore.error = result.data;
        } else {
          mainStore.error = "Authentication Failed";
        }
      } catch (error) {
        mainStore.error = "Usuario o contraseña incorrectos";
      } finally {
        mainStore.isBusy = false;
      }
    };

    const logout = () => {
      token.value = "";
      refresh.value = "";
      userInfo.value = {} as User;

      router.push(ROUTE_LOGIN);
    };

    const isAuthenticated = computed(() => {
      return Boolean(token.value) && token.value.length > 0;
    });

    return {
      token,
      refresh,
      userInfo,
      login,
      logout,
      isAuthenticated
    };
  },
  {
    persist: true
  }
);
