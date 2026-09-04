import { defineStore } from "pinia";

import { ref } from "vue";
import type { FLowering } from "@/services/types";
import floweringService from "../services/flowering.services";
// Lógica de para traer las herramientas que serán embebidas en forma de array de manera correcta,
//con su respectiva lógica de errores.
export const useFloweringStore = defineStore(
  "flowering",
  () => {
    const FloweringList = ref<FLowering[]>([]);

    const getFlowering = async (historico: boolean = false) => {
      try {
        const result = await floweringService.getFloweringList(historico);

        if (result && result.status === 200 && Array.isArray(result.data)) {
          FloweringList.value = result.data;
        } else {
          FloweringList.value = [];
        }
      } catch (error) {
        console.error("Error al obtener lista de floraciones:", error);
        FloweringList.value = [];
      }
    };

    return {
      getFlowering,
      FloweringList
    };
  },
  {
    persist: true
  }
);
