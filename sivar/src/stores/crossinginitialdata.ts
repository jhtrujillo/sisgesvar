import { defineStore } from "pinia";

import { ref } from "vue";
import CrossingsService from "../services/crossings.services";
import type { CrossingInitialData } from "@/services/types";
// Lógica de para traer los los procesos que han sido completados de manera correcta,
//con su respectiva lógica de errores.
export const useCrossingInitialDataStore = defineStore(
  "crossingInitialData",
  () => {
    const crossingInitialDataList = ref<CrossingInitialData[]>([]);
    // const alignmentsList = ref('');

    const getCrossingInitialDataList = async () => {
      try {
        const result = await CrossingsService.getCrossingInitialData();

        if (result.status === 200) {
          crossingInitialDataList.value = result.data;
        }
      } catch (error) {
        console.log("error");
      }
    };

    return {
      getCrossingInitialDataList,
      crossingInitialDataList
    };
  },
  {
    persist: true
  }
);
