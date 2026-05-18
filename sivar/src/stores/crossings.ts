import { defineStore } from "pinia";

import { ref } from "vue";
import type { Crossings } from "@/services/types";
import CrossingsService from "../services/crossings.services";
import type { AxiosResponse } from "axios";
// Lógica de para traer las herramientas que serán embebidas en forma de array de manera correcta,
//con su respectiva lógica de errores

export const useCrossingsStore = defineStore("crossings", () => {
  const crossing = ref<Crossings[]>([]);
  const currentPage = ref(1);
  const perPage = ref(10);
  const totalRecords = ref(0);
  const totalPages = ref(0);

  const getCrossings = async () => {
    try {
      const response: AxiosResponse<{ data: Crossings[]; total: number }> = await CrossingsService.getCrossingsList(currentPage.value, perPage.value);
      const { data, total } = response.data;

      crossing.value = data;
      console.log(crossing.value);
      totalRecords.value = total;
      totalPages.value = Math.ceil(totalRecords.value / perPage.value);
    } catch (error) {
      console.error("Error fetching crossings:", error);
    }
  };

  const setCurrentPage = async (page: number) => {
    if (page >= 1 && page <= totalPages.value) {
      currentPage.value = page;
      await getCrossings();
    }
  };
  const setPerPage = async (numberPage: number) => {
    perPage.value = numberPage;
    await getCrossings();
  };

  return {
    getCrossings,
    crossing,
    totalRecords,
    currentPage,
    totalPages,
    perPage,
    setCurrentPage,
    setPerPage
  };
});
