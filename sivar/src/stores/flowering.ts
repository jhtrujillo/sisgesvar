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

    const getFlowering = async () => {
      try {
        const result = await floweringService.getFloweringList();

        if (result.status === 200) {
          FloweringList.value = result.data;
        }
      } catch (error) {
        console.log("error");
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
