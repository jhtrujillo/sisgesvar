<!-- Vista para enviar los parámetros al clúster a traves del formulario "Alineamiento de secuencias" -->
<template>
  <div class="w-full flex-col grid place-content-center">
    <div>
      <button
        type="button"
        class="block mb-4 px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark-mode:bg-transparent dark-mode:hover:bg-gray-600 dark-mode:focus:bg-gray-600 dark-mode:focus:text-white dark-mode:hover:text-white dark-mode:text-gray-200 md:mt-0 focus:outline-none focus:shadow-outline"
        href="#"
      >
        <router-link
          class="text-violet-800 group border border-violet-800 flex items-center px-2 py-2 font-medium rounded-md pt-1 pb-1 pr-2 pl-2 hover:text-white hover:bg-violet-800"
          :to="{
            name: 'bioinformatica.show'
          }"
        >
          Volver</router-link
        >
      </button>
    </div>
    <h1 class="text-center font-bold text-5xl mb-6 text-violet-800">Alineamientos de Secuencias</h1>
    <div>
      <button
        type="button"
        class="block mb-4 px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark-mode:bg-transparent dark-mode:hover:bg-gray-600 dark-mode:focus:bg-gray-600 dark-mode:focus:text-white dark-mode:hover:text-white dark-mode:text-gray-200 md:mt-0 focus:outline-none focus:shadow-outline"
        href="#"
      >
        <router-link
          class="text-violet-800 group border border-violet-800 flex items-center px-2 py-2 font-medium rounded-md pt-1 pb-1 pr-2 pl-2 hover:text-white hover:bg-violet-800"
          :to="{
            name: 'alignments_list.show'
          }"
        >
          Lista de Alineamientos</router-link
        >
      </button>
    </div>
    <form @submit.prevent="SaveAlignments()" class="w-full max-w-3xl shadow-xl bg-white rounded-xl">
      <div class="flex flex-wrap -mx-3 mb-2 w-full text-center p-4">
        <div class="w-full md:w-full px-3 mb-10 md:mb-0">
          <label class="block uppercase tracking-wide text-violet-800 text-xs font-bold mb-2" for="grid-state"> Genoma de referencia </label>
          <div class="mb-4">
            <select
              class="block w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
              id="genomaReferencia"
              name="genomaReferencia"
              v-model="model.parametros.genomaReferencia"
            >
              <option value="CC 01-1940">CC 01-1940</option>
              <option value="R 570">R 570</option>
              <option value="S. Spontaneum">S. Spontaneum</option>
            </select>
          </div>
        </div>
        <div class="w-full md:w-full px-3 mb-6 md:mb-0">
          <label class="block uppercase tracking-wide text-violet-800 text-xs font-bold mb-2" for="grid-state"> Individuo a mapear </label>
          <div class="mb-4">
            <select
              class="block w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
              id="individuo"
              v-model="model.parametros.individuo"
            >
              <option value="S1">S1</option>
              <option value="S2">S2</option>
              <option value="S3">S3</option>
              <option value="S4">S4</option>
              <option value="S5">S5</option>
              <option value="S6">S6</option>
              <option value="S7">S7</option>
              <option value="S8">S8</option>
              <option value="S9">S9</option>
              <option value="S10">S10</option>
            </select>
          </div>
        </div>
        <div class="w-full md:w-full px-3 mb-6 md:mb-0">
          <label class="block uppercase tracking-wide text-violet-800 text-xs font-bold mb-2" for="grid-state"> NGS </label>
          <div class="mb-4">
            <select
              class="block w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
              id="ngs"
              name="ngs"
              v-model="model.parametros.ngs"
            >
              <option value="GBS">GBS</option>
              <option value="RADSeq">RADSeq</option>
              <option value="WGS(EI)">WGS(EI)</option>
            </select>
          </div>
        </div>
        <div class="w-full md:w-full px-3 mb-6 md:mb-0">
          <label class="uppercase tracking-wide text-violet-800 text-xs font-bold mb-2 hidden" for="grid-state"> Comando </label>
          <div class="relative hidden">
            <input
              type="text"
              readonly
              id="comando"
              name="comando"
              v-model="model.parametros.comando"
              class="block appearance-none w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
            />
            <input
              type="text"
              readonly
              id="usuario"
              name="usuario"
              v-model="model.parametros.usuario"
              class="block appearance-none w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
            />
          </div>
        </div>
      </div>
      <div>
        <button
          type="submit"
          class="w-1/2 flex ml-40 mb-8 place-content-center justify-center py-2 px-4 text-sm font-medium text-violet-700 border border-transparent rounded-md"
          :class="[
            isBusy
              ? 'bg-violet-700 rounded opacity-50 cursor-not-allowed'
              : ' bg-transparent rounded border-1 border-violet-700 text-violet-700 shadow-sm hover:bg-violet-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500'
          ]"
        >
          Enviar
          <div class="ml-4" v-if="isBusy">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-violet-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
            </svg>
          </div>
        </button>
      </div>
    </form>
  </div>
</template>
<script setup lang="ts">
import { reactive, computed, watch } from "vue";
import { useAlignmentsStore } from "@/stores/alignments";
import { useMainStore } from "@/stores/main";
import { useUserStore } from "@/stores/user";

import { useToast } from "vue-toastification";

const userStore = useUserStore();
const toast = useToast();
const mainStore = useMainStore();
const AlignmentsStore = useAlignmentsStore();

const model = reactive({
  parametros: {
    genomaReferencia: "",
    individuo: "",
    ngs: "",
    comando: "Alinear",
    usuario: userStore.userInfo?.lgin || ""
  }
});
const error = computed(() => mainStore.error);
const resetModel = () => {
  model.parametros.genomaReferencia = "";
  model.parametros.individuo = "";
  model.parametros.ngs = "";
};
const SaveAlignments = async () => {
  try {
    const { genomaReferencia, individuo, ngs } = model.parametros;

    if (!genomaReferencia || !individuo || !ngs) {
      toast.error("Todos los campos son requeridos");
      return;
    }

    const stringModel = JSON.stringify(model);
    console.log(stringModel);

    const result = await AlignmentsStore.SaveAlignments(model);
    if (result) {
      toast.success("Guardado con éxito");
      resetModel();
    }
  } catch (error) {
    console.log(error);
    toast.error("Error al guardar");
  }
};

watch(error, () => {
  if (error.value) {
    toast.error(error.value);
  }
});

const isBusy = computed(() => mainStore.isBusy);
</script>
