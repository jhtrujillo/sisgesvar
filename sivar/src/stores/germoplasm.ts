import { defineStore } from "pinia";

import { ref } from "vue";
import type { Germoplasma } from "@/services/types";
import germoplasmBankService from "../services/germoplasm.services";
import type { AxiosResponse } from "axios";
// Lógica de para traer las herramientas que serán embebidas en forma de array de manera correcta,
//con su respectiva lógica de errores.

export const useGermoplasmBankStore = defineStore("germoplasmBank", () => {
  const germplasm = ref<Germoplasma[]>([]);
  const currentPage = ref(1);
  const perPage = ref(10);
  const totalRecords = ref(0);
  const totalPages = ref(0);

  const getGermoplasmBank = async () => {
    try {
      const response: AxiosResponse<{ data: Germoplasma[]; total: number }> = await germoplasmBankService.getGermoplasmBankList(
        currentPage.value,
        perPage.value
      );
      const { data, total } = response.data;

      germplasm.value = data;
      console.log(germplasm.value);
      totalRecords.value = total;
      totalPages.value = Math.ceil(totalRecords.value / perPage.value);
    } catch (error) {
      console.log("error");
    }
  };

  const setCurrentPage = async (page: number) => {
    if (page >= 1 && page <= totalPages.value) {
      currentPage.value = page;
      await getGermoplasmBank();
    }
  };
  const setPerPage = async (numberPage: number) => {
    perPage.value = numberPage;
    await getGermoplasmBank();
  };

  return {
    getGermoplasmBank,
    germplasm,
    totalRecords,
    currentPage,
    totalPages,
    perPage,
    setCurrentPage,
    setPerPage
  };
});
