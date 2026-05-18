import type { User } from "@/services/types";
import { defineStore } from "pinia";

import { ref } from "vue";
import usersService from "../services/users.services";

// Lógica de para mostrar usuarios (CRUD) de manera correcta

export const useUsersStore = defineStore("users", () => {
  const users = ref([]);

  const listUsers = async () => {
    try {
      const result = await usersService.list();

      if (result.status === 200) {
        users.value = result.data;
      }
    } catch (error) {
      console.log("error");
    }
  };

  const createUser = async (model: User) => {};
  const updateUser = async (id: number, model: User) => {};
  const deleteUser = async (id: number) => {};

  return {
    users,
    listUsers,
    createUser,
    updateUser,
    deleteUser
  };
});
