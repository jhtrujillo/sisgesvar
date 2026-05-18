import { defineStore } from "pinia";

import { ref } from "vue";
import linksService from "../services/links.services";
import type { Links } from "@/services/types";
// Lógica de para traer las herramientas que serán embebidas en forma de array de manera correcta,
//con su respectiva lógica de errores.
export const useLinksStore = defineStore(
  "links",
  () => {
    const linkList = ref<Links[]>([]);

    const getLinks = async () => {
      try {
        const result = await linksService.getLinks();

        if (result.status === 200) {
          linkList.value = result.data;
        }
      } catch (error) {
        console.log("error");
      }
    };

    return {
      getLinks,
      linkList
    };
  },
  {
    persist: true
  }
);
