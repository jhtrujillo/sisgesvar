<template>
  <div class="space-y-8 w-full max-w-6xl mx-auto px-4">
    <!-- Botón Volver -->
    <BackButton :to="{ name: 'mejoramiento.show' }" label="Volver a Mejoramiento" />

    <div class="border-b border-slate-100 pb-5">
      <h1 class="text-3xl font-extrabold text-slate-800 flex items-center">
        <div class="p-2 bg-emerald-50 text-cenicana rounded-lg mr-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 512 512" fill="currentColor">
            <path
              d="M264.34 21.275c-61.1 44.835-108.23 124.229-103.512 198.29c6.36-7.25 13.422-13.883 21.139-19.713c11.146-65.623 49.022-131.354 82.373-178.577zM57.383 22.758c24.174 34.235 50.712 78.19 67.728 124.773c7.978 6.219 15.304 13.215 21.76 20.94a236.975 236.975 0 0 1 3.951-15.287c-16.31-48.832-51.666-104.548-93.44-130.426zm405.053 10.744c-47.361 40.863-69.363 100.554-74.997 158.209a202.35 202.35 0 0 1 8.16 11.328a201.113 201.113 0 0 1 16.737-29.867c9.403-51.405 27.53-101.544 50.1-139.67zm-92.069 3.707c-64.832 14.582-107.75 78.8-116.562 136.773c2.973 1.672 6.73 5.1 9.265 8.455c22.607-66.53 61.275-110.735 107.297-145.228zm-353.123 91.37c51.778 24.363 105.825 56.494 127.035 103.939c-2.272-12.34-2.629-25.452-1.396-38.944c-28.597-42.532-78.956-63.451-125.639-64.996zm476.056 27.49c-34.406-.802-76.934 23.426-86.128 66.726c1.139 4.58 13.022 13.846 21.013 16.969c.773-43.834 37.177-62.218 65.115-83.695zm-174.29 2.181c21.04 21.461 48.917 45.511 61.953 86.13c4.24-2.486 8.63-4.729 13.18-6.693c-15.79-31.463-44.049-67.846-75.133-79.437zm-73.265 30.155c-37.362 3.63-64.025 29.123-83.225 58.677c-14.63 22.519-23.115 49.683-24.555 76.266c25.986-53.821 62.46-108.757 112.652-127.534zm31.927 13.365c-2.223 2.752-5.084 4.713-7.885 6.818c31.072 26.356 47.903 58.752 56.068 93.111a205.85 205.85 0 0 1 9.504-14.467c-9.09-33.826-32.122-70.188-57.687-85.462zM248.41 215.8a128.121 128.121 0 0 0-9.748 4.63l-1.48 27.127c14.5 11.416 25.42 23.037 34.015 37.332l2.711-49.662c-6.944-6.998-15.319-13.482-25.498-19.426zm-120.312 24.09C71.5 256.71 37.71 308.58 28.604 361.046c29.726-43.263 59.439-87.436 104.86-106.18c-1.603-5.314-3.395-10.297-5.366-14.974zm74.738 9.506c-3.896 5.005-7.323 9.1-10.813 14.153c43.782 20.683 60.177 57.427 80.86 93.969c-2.173-48.16-27.882-92.375-70.047-108.121zm205.033 2.33c-29.13 10.175-52.395 33.868-69.392 64.065c-11.124 19.76-19.43 42.164-24.94 64.672c19.94-27.481 34.02-48.154 47.813-65.518c16.755-21.093 33.426-37.283 58.636-53.603c-2.898-5.063-7.514-9.234-12.117-9.615zm22.572 24.243c-5.147 3.297-9.868 6.548-14.26 9.812c23.392 42.081 38.88 88.57 55.426 133.594c-1.502-49.435-13.906-99.519-41.166-143.406zM174.115 293.63c-10.184 19.084-19.878 39.48-32.46 59.031l1.687 27.598c12.051 1.164 23.366-1.86 35.72-5.682zm227.905 3.7c-12.309 11.669-21.565 22.225-30.59 33.958c8.607 6.083 19.627 8.03 32.529 8.234l6.086-26.554a405.944 405.944 0 0 0-8.025-15.639zm-169.012 26.702l-2.383 43.643l.541-.807c9.337 6.267 18.402 6.229 28.932 4.631c-10.136-17.217-18.219-33.414-27.09-47.467zm131.951 24.508l-12.9 56.295c11.853 5.06 22.705 9.387 34.753 9.504l13.045-56.922c-12.158-.504-24.254-2.66-34.898-8.877zm-135.355 37.85l-2.575 47.148c12.128 4.554 23.916 6.054 35.803 4.572l2.688-49.242c-11.093 1.813-23.479 2.755-35.916-2.478zm-49.413 6.656c-10.937 3.273-22.755 6.1-35.74 5.361l3.897 63.762c13.989.1 26.73-.425 35.804-4.287zm167.772 29.656l-12.32 53.764c11.403 4.048 23.029 6.993 34.884 8.937l12.192-53.197c-12.962-.822-24.314-5.066-34.756-9.504zM226.01 452.21L223.729 494h36.052l2.059-37.703c-11.976 1.028-23.997-.38-35.83-4.088zm-40.705 24.514c-11.492 3.648-23.852 3.84-35.85 3.588l.836 13.689h36.07z"
            />
          </svg>
        </div>
        Módulo de Variedades
      </h1>
      <p class="text-slate-500 mt-2 ml-11 text-sm">
        Consulte y administre el catálogo general de variedades, genealogías, diagramas de parentales e historial agronómico.
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <ModuleCardComponent
        title="Ver Variedades"
        description="Consulte el catálogo completo de variedades registradas y sus características principales."
        actionText="Ver Registros"
        :to="{ name: 'variety_list.show' }"
        variant="emerald"
      >
        <template #icon>
          <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 transition-transform duration-500 group-hover:scale-110" viewBox="0 0 512 512">
            <path
              fill="currentColor"
              d="M350.8 22.28c-2.3 13.74-4.6 27.67-6.8 41.68h.6c1.3-.1 2.7 0 4.1.1c4.5.4 8.9 1.78 12.9 3.97c2.3-14.37 4.6-28.66 7-42.75zm-171.6.32l-17.8 2.52c1.2 8.41 2.4 16.86 3.6 25.34c3.9-1.63 8-2.62 12.2-2.89c1.8-.12 3.7-.1 5.5.1c-1.2-8.37-2.3-16.73-3.5-25.03zm.4 42.89h-1.2c-12.7.8-26.1 11.9-23.7 40.61l10.2 127.7c2.3 28.6 17.7 38.2 30.4 37.4c12.7-.8 26.1-12 23.8-40.5l-10.3-127.8c-2.2-27.65-16.8-37.55-29.2-37.41zm165.6 16.4c-4.5.13-9.2 2.17-13.6 7.1c-5.1 5.65-9.6 15.31-10.9 29.71l-11.3 127.5c-1.3 14.5 1.4 24.8 5.4 31.2c4 6.5 9 9.3 14 9.7c4.9.5 10.4-1.4 15.4-7c5.1-5.7 9.6-15.3 10.9-29.8l11.4-127.5c1.3-14.4-1.4-24.71-5.4-31.17c-4-6.46-9-9.23-14-9.68c-.6-.1-1.3-.1-1.9-.1zM25.99 223.3C78.79 299.9 126.7 397.8 125 489.7h35.3c3.1-20.8 7.5-39.3 13-56.2c-10.9-35.5-28.2-73.7-49.5-108.1c-28.14-45.5-63.44-83.9-97.81-102.1zM419.3 270c-1.3 0-3.7.7-6.8 3.1c-3.6 2.7-7.8 7.3-11.9 13c-3.3 4.7-6.6 10.2-9.6 16c5-2.1 10-3.7 15-4.4c13.3-2 26.3 1 37.8 8.1c16.5 10.2 30.1 28.1 42.2 52.2c-.3-15.6-3.4-31.3-9.5-45c-10.4-23.3-28.3-40.3-56.7-43zm-210.9 16.3c-3.8 1.6-7.9 2.5-12 2.8c-2 .1-4 .1-5.9-.1c1.8 33.1 2.7 65.9 2.3 97.9c5.5-10.6 11.5-21 18.1-31.4c-.3-22.9-1.2-46-2.5-69.2zm104.5 14.1c-5.5 56.5-9.3 112.6-10.6 166.7c5.9-20.8 12.7-41.5 20.6-62.1c1.8-32.8 4.5-66.2 7.7-99.8c-1.2 0-2.3 0-3.4-.1c-5-.5-9.9-2.1-14.3-4.7zm88.3 16.9c-7 2.3-14.7 6.4-23 12.9c-30.1 52.2-49 105.1-63.4 159.5h80.5c-12.6-57.3-5-115 5.9-172.4zm-146.7 5.6c-38.5 54.6-64.6 95.2-75.8 166H237c-13-31.1-12.1-62.8-4.3-96.6c5-21.6 12.9-44.6 21.8-69.4z"
            />
          </svg>
        </template>
      </ModuleCardComponent>

      <ModuleCardComponent
        title="Diagrama de Parentales"
        description="Explore la genealogía y jerarquía ascendente de ancestros de cada variedad."
        actionText="Explorar Diagrama"
        :to="{ name: 'diagram_parents_level.show' }"
        variant="emerald"
      >
        <template #icon>
          <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 transition-transform duration-500 group-hover:scale-110" viewBox="0 0 32 32">
            <path
              fill="currentColor"
              d="m6.812 17.202l7.396-3.665v-2.164h-.834c-.414 0-.808-.084-1.167-.237v1.16L4.81 15.96v2.912h2V17.2zm19.75 1.673v-2.913l-7.397-3.666v-1.158a2.98 2.98 0 0 1-1.166.236h-.833v2.164l7.395 3.666v1.672h2zm-9.874 0v-7.5h-2v7.5h2zm11.187 1H23.25a2 2 0 0 0-2 2V26.5a2 2 0 0 0 2 2h4.625a2 2 0 0 0 2-2v-4.625a2 2 0 0 0-2-2zm-19.75 0H3.5a2 2 0 0 0-2 2V26.5a2 2 0 0 0 2 2h4.625a2 2 0 0 0 2-2v-4.625a2 2 0 0 0-2-2zm5.25-9.5H18a2 2 0 0 0 2-2V3.75a2 2 0 0 0-2-2h-4.625a2 2 0 0 0-2 2v4.625a2 2 0 0 0 2 2zm4.625 9.5h-4.625a2 2 0 0 0-2 2V26.5a2 2 0 0 0 2 2H18a2 2 0 0 0 2-2v-4.625a2 2 0 0 0-2-2z"
            />
          </svg>
        </template>
      </ModuleCardComponent>

      <ModuleCardComponent
        title="Banco de Germoplasma"
        description="Acceda al inventario y estado de conservación del banco germoplásmico."
        actionText="Ver Germoplasma"
        :to="{ name: 'germoplasm_bank.show' }"
        variant="emerald"
      >
        <template #icon>
          <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 transition-transform duration-500 group-hover:scale-110" viewBox="0 0 48 48">
            <g fill="none" stroke="currentColor" stroke-width="4">
              <path stroke-linecap="round" stroke-linejoin="round" d="m20 33l6 2s15-3 17-3s2 2 0 4s-9 8-15 8s-10-3-14-3H4" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 29c2-2 6-5 10-5s13.5 4 15 6s-3 5-3 5" />
              <circle cx="34" cy="22" r="3" />
              <circle cx="22" cy="15" r="3" />
              <circle cx="34" cy="7" r="3" />
            </g>
          </svg>
        </template>
      </ModuleCardComponent>

      <ModuleCardComponent
        title="Historial de Variedad"
        description="Consulte eventos, registros de evaluación e historial detallado agronómico."
        actionText="Ver Historial"
        :to="{ name: 'variety_history.show' }"
        variant="emerald"
      >
        <template #icon>
          <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 transition-transform duration-500 group-hover:scale-110" viewBox="0 0 48 48">
            <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="4">
              <path
                stroke-linejoin="round"
                d="M24 43.9998H10C8.89543 43.9998 8 43.1043 8 41.9998V5.99976C8 4.89519 8.89543 3.99976 10 3.99976H38C39.1046 3.99976 40 4.89519 40 5.99976V23.9998"
              />
              <path stroke-linejoin="round" d="M35.5 43.9998V30.9998" />
              <path stroke-linejoin="round" d="M31 34.4998L32.5 32.9998L35.5 29.9998L38.5 32.9998L40 34.4998" />
              <path d="M16 15.9998H32" />
              <path d="M16 23.9998H24" />
            </g>
          </svg>
        </template>
      </ModuleCardComponent>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useLinksStore } from "@/stores/links";
import BackButton from "@/components/BackButton.vue";
import ModuleCardComponent from "@/components/ModuleCardComponent.vue";

const linksStore = useLinksStore();

onMounted(async () => {
  await linksStore.getLinks();
});

const data = ref({
  puedeAcceder: false,
  puedeMostrar: true
});
</script>
