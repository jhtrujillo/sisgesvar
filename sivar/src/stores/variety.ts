import { defineStore } from "pinia";

import { ref } from "vue";
import type { Variety } from "@/services/types";
import varietyService from "../services/variety.services";
// Lógica de para traer las herramientas que serán embebidas en forma de array de manera correcta,
//con su respectiva lógica de errores.
export const useVarietyStore = defineStore(
  "variety",
  () => {
    const Variety = ref<Variety[]>([]);

    const getVariety = async () => {
      try {
        const result = await varietyService.getVariety();
        if (result.status === 200) {
          Variety.value = result.data;
        }
      } catch (error) {
        console.log("error");
      }
    };

    return {
      getVariety,
      Variety
    };
  },
  {
    persist: true
  }
);
