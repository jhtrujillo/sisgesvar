import { defineStore } from "pinia";

import { ref } from "vue";
// Lógica para el mostrar si los datos han sido guardados correctamente o ocurrió un error
export const useMainStore = defineStore("main", () => {
  const isBusy = ref(false);
  const error = ref("");
  const responseMessage = ref("");

  return {
    isBusy,
    error,
    responseMessage
  };
});
