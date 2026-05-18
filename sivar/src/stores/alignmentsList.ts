import { defineStore } from "pinia";

import { ref } from "vue";
import alignmentsService from "../services/alignments.services";
import type { AlignmentsList } from "@/services/types";
// Lógica de para traer los los procesos que han sido completados de manera correcta,
//con su respectiva lógica de errores.
export const useAlignmentsListStore = defineStore(
  "alignmentsList",
  () => {
    const alignmentsList = ref<AlignmentsList[]>([]);
    // const alignmentsList = ref('');

    const getAlignments = async () => {
      try {
        const result = await alignmentsService.getAlignments();

        if (result.status === 200) {
          alignmentsList.value = result.data;
        }
      } catch (error) {
        console.log("error");
      }
    };

    return {
      getAlignments,
      alignmentsList
    };
  },
  {
    persist: true
  }
);
