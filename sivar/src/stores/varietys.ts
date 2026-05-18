import { defineStore } from "pinia";

import { ref } from "vue";
import type { Varietys } from "@/services/types";
import varietyService from "../services/varietys.services";
// Lógica de para traer las herramientas que serán embebidas en forma de array de manera correcta,
//con su respectiva lógica de errores.
export const useVarietysStore = defineStore(
  "varietys",
  () => {
    const VarietysList = ref<Varietys[]>([]);

    const getVarietys= async () => {
      try {
        const result = await varietyService.getVarietysList();
        console.log(result);
        if (result.status === 200) {
          VarietysList.value = result.data;
        }
      } catch (error) {
        console.log("error");
      }
    };

    return {
      getVarietys,
      VarietysList
    };
  },
  {
    persist: true
  }
);
