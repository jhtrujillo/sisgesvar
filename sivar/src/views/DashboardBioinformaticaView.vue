<template>
  <div class="space-y-4">
    <!-- Botón Volver -->
    <div class="flex justify-start mb-4">
      <button
        type="button"
        class="px-4 py-2 text-sm font-semibold bg-transparent rounded-lg dark-mode:bg-transparent dark-mode:hover:bg-gray-600 dark-mode:focus:bg-gray-600 dark-mode:focus:text-white dark-mode:hover:text-white dark-mode:text-gray-200 focus:outline-none focus:shadow-outline"
      >
        <router-link
          class="text-violet-800 group border border-violet-800 flex items-center px-2 py-2 font-medium rounded-md pt-1 pb-1 pr-2 pl-2 hover:text-white hover:bg-violet-800"
          :to="{ name: 'main.show' }"
        >
          Volver
        </router-link>
      </button>
    </div>
    <div class="grid grid-cols-1 text-center sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-3 gap-4">
      <router-link
        class="group grid font-medium rounded-md m-2 bg-white border-none shadow-xl py-8 px-4 sm:rounded-lg sm:px-10"
        :to="{ name: 'alignments.show' }"
      >
        <div class="flex flex-col h-full sm:px-6 lg:px-8">
          <div class="sm:mx-auto sm:w-full sm:max-w-md mb-2 flex justify-center">
            <h1 class="text-emerald-500">Alineamiento de secuencias</h1>
          </div>
          <div class="flex justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24">
              <path
                fill="#5b21b6"
                d="M5 22q0-3.475 1.45-5.638t4-4.362q-2.55-2.2-4-4.362T5 2v-.25q0-.425.288-.713T6 .75q.425 0 .713.288T7 1.75V2q0 .275.013.513T7.05 3h9.9q.025-.25.038-.488T17 2v-.25q0-.425.288-.713T18 .75q.425 0 .713.288T19 1.75V2q0 3.475-1.45 5.638t-4 4.362q2.55 2.2 4 4.362T19 22v.25q0 .425-.288.713T18 23.25q-.425 0-.713-.288T17 22.25V22q0-.275-.013-.513T16.95 21h-9.9q-.025.25-.037.488T7 22v.25q0 .425-.288.713T6 23.25q-.425 0-.713-.288T5 22.25V22ZM8.45 7h7.1q.325-.475.563-.95T16.55 5h-9.1q.2.55.437 1.038T8.45 7ZM12 10.7q.5-.425.975-.85t.9-.85h-3.75q.425.425.9.85t.975.85ZM10.125 15h3.75q-.425-.425-.9-.85T12 13.3q-.5.425-.975.85t-.9.85ZM7.45 19h9.1q-.2-.55-.438-1.038T15.55 17h-7.1q-.325.475-.562.95T7.45 19Z"
              />
            </svg>
          </div>
        </div>
      </router-link>
      <router-link
        class="group grid font-medium rounded-md m-2 bg-white border-none shadow-xl py-8 px-4 sm:rounded-lg sm:px-10"
        :to="{ name: 'sequence_server.show' }"
      >
        <div class="flex flex-col h-full sm:px-6 lg:px-8">
          <div class="sm:mx-auto sm:w-full sm:max-w-md mb-2 flex justify-center">
            <h1 class="text-emerald-500">Blastn</h1>
          </div>
          <div class="flex justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24">
              <path
                fill="none"
                stroke="#5b21b6"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 2c-1.35 1.5-2.092 3-2.5 4.5M9 22c1.35-1.5 2.092-3 2.5-4.5M2 15c3.333-3 6.667-3 10-3m10-3c-1.5 1.35-3 2.092-4.5 2.5M17 6l-2.5-2.5M14 8l-1.5-1.5M7 18l2.5 2.5m-6-6l.5.5m16-6l.5.5m-14 3l1 1m9-3l1 1M10 16l1.5 1.5M2 2l20 20"
              />
            </svg>
          </div>
        </div>
      </router-link>
      <router-link class="group grid font-medium rounded-md m-2 bg-white border-none shadow-xl py-8 px-4 sm:rounded-lg sm:px-10" :to="{ name: 'jbrowse.show' }">
        <div class="flex flex-col h-full sm:px-6 lg:px-8">
          <div class="sm:mx-auto sm:w-full sm:max-w-md mb-2 flex justify-center">
            <h1 class="text-emerald-500">JBrowse</h1>
          </div>
          <div class="flex justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 506 512">
              <path
                fill="#5b21b6"
                d="M336.742.004c-5.732-.12-12.481 2.453-19.86 9.18c-46.533 64.308-24.742 112.637.669 195.094c27.589 89.525 3.311 151.023-96.922 122.124l13.974 45.02C356.01 389.67 380.316 301.64 363.98 225.555c-22.331-104.015-54.152-129.427-9.84-196.19c5.233-11.154-2.753-29.053-17.4-29.36zm40.46 45.635c-8.08 4.781-10.91 11.338-11.192 25.867c32.625 27.345 60.36 65.991 82.18 93.208c-8.074 4.809-18.18 9.25-29.263 7.892c-10.334-14.493-37.847-47.05-58.236-66.709c1.662 17.845 6.535 31.115 8.014 46.572c31.968 35.89 33.665 54.176 33.665 54.176c41.241 2.092 76.028-15.901 99.24-36.808c12.519-10.748-4.996-39.884-27.34-19.885c-30.184-37.486-61.414-73.664-97.068-104.313zm-135.397 90.39c-64.648-.737-110.817 48.596-107.626 112.728c5.05 56.142 30.399 92.912 34.463 138.263l-27.758-31.354c-10.855-1.19-20.37-2.676-32.085 3.792l58.532 61.95c-1.583 9.517-4.242 19.559-8.264 30.318c-22.906-22.013-49.869-55.033-79.44-87.009c-17.09 3.793-26.497 13.47-26.672 13.76l93.326 100.594c-24.194 26.779 17.503 44.953 36.45 23.433c65.193-75.952 14.961-165.88-6.389-252.319l47.519 51.681c16.752 9.187 39.172 8.919 47.482 3.836l-91.225-94.87c4.713-10.523 9.684-13.146 21.044-18.759l88.982 92.74c5.634-10.584 14.017-20.687 3.397-43.3l-57.92-64.106c9.893-3.314 31.811 3.576 48.981 8.943l-14.05-46.836c-9.899-2.252-19.512-3.379-28.747-3.484zM98.745 300.054c-45.42-.472-70.368 18.213-91.777 37.05c-19.17 16.978 5.015 47.966 26.71 32.057c27.197-24.263 50.117-37.07 93.589-33.961l-7.754-33.715c-7.374-.905-14.28-1.363-20.768-1.43z"
              />
            </svg>
          </div>
        </div>
      </router-link>
      <router-link class="group grid font-medium rounded-md m-2 bg-white border-none shadow-xl py-8 px-4 sm:rounded-lg sm:px-10" :to="{ name: 'galaxy.show' }">
        <div class="flex flex-col h-full sm:px-6 lg:px-8">
          <div class="sm:mx-auto sm:w-full sm:max-w-md mb-2 flex justify-center">
            <h1 class="text-emerald-500">Galaxy</h1>
          </div>
          <div class="flex justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="-3 -3 24 24">
              <path
                fill="#5b21b6"
                d="M.694 17.306c-1.91-1.912.258-7.18 4.845-11.767S15.394-1.217 17.306.694c1.91 1.912-.258 7.18-4.845 11.767S2.606 19.217.694 17.306zM2.08 15.92c.815.816 5.102-.95 8.997-4.845c3.895-3.895 5.66-8.182 4.845-8.997c-.815-.816-5.102.95-8.997 4.845c-3.895 3.895-5.66 8.182-4.845 8.997zm3.46-3.46C.952 7.874-1.217 2.606.694.694c1.912-1.91 7.18.258 11.767 4.845s6.756 9.855 4.845 11.767c-1.912 1.91-7.18-.258-11.767-4.845zm1.385-1.385c3.895 3.895 8.182 5.66 8.997 4.845c.816-.815-.95-5.102-4.845-8.997c-3.895-3.895-8.182-5.66-8.997-4.845c-.816.815.95 5.102 4.845 8.997zM9 11a2 2 0 1 1 0-4a2 2 0 0 1 0 4z"
              />
            </svg>
          </div>
        </div>
      </router-link>
      <a
        class="group grid font-medium rounded-md m-2 bg-white border-none shadow-xl py-8 px-4 sm:rounded-lg sm:px-10"
        :href="linksStore.linkList[3]?.url"
        target="_blank"
        rel="noopener noreferrer"
      >
        <div class="flex flex-col h-full sm:px-6 lg:px-8">
          <div class="sm:mx-auto sm:w-full sm:max-w-md mb-2 flex justify-center">
            <h1 class="text-emerald-500">RStudio Server</h1>
          </div>
          <div class="flex justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 512 512">
              <path
                fill="#5b21b6"
                fill-rule="evenodd"
                d="M127.711 477.547c-170.281-98.17-170.281-344.925 0-443.094C297.992-63.716 512 59.662 512 256S297.993 575.716 127.711 477.547zm18.213-374.554c73.982 3.811 107.081-10.531 152.548 5.75c77.665 27.811 62.64 124.222-6.655 141.539c-2.015 1.54-1.096 2.906 0 4.262l69.487 99.317h32.658v28.848h-55.04l-85.199-125.67h-38.952v96.822h37.21v28.848H143.645v-28.848h37.794V135.892c-9.6-1.09-21.787-1.467-35.515-3.942zm69.008 33.33c22.828 0 45.947-4.348 66.025 1.361c22.94 6.524 33.844 22.968 33.839 40.039c-.007 21.801-9.541 38.713-35.323 45.653c-28.228 7.599-64.54 4.699-64.54 4.699z"
              />
            </svg>
          </div>
        </div>
      </a>
      <!-- class="group grid font-medium rounded-md m-2 bg-white border-none shadow-lg py-8 px-4 sm:rounded-lg sm:px-10 transition-transform transform hover:scale-105" -->
      <router-link
        class="group grid font-medium rounded-md m-2 bg-white border-none shadow-xl py-8 px-4 sm:rounded-lg sm:px-10"
        :to="{ name: 'wiki_cenicana.show' }"
      >
        <div class="flex flex-col h-full sm:px-6 lg:px-8">
          <div class="sm:mx-auto sm:w-full sm:max-w-md mb-2 flex justify-center">
            <h1 class="text-emerald-500">Wiki Cenicaña</h1>
          </div>
          <div class="flex justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 16 16">
              <path
                fill="#5b21b6"
                d="M8.835 3.003c.828-.006 2.688 0 2.688 0l.033.03v.288c0 .08-.045.12-.133.12c-.433.02-.522.063-.68.29c-.087.126-.258.393-.435.694l-1.52 2.843l-.043.089l1.858 3.801l.113.031l2.926-6.946c.102-.28.086-.478-.044-.595c-.132-.114-.224-.18-.563-.195l-.275-.014a.161.161 0 0 1-.096-.035a.1.1 0 0 1-.046-.084v-.289l.042-.03h3.306l.034.03v.29c0 .078-.045.117-.133.117c-.433.02-.754.113-.962.281a1.64 1.64 0 0 0-.488.704s-2.691 6.16-3.612 8.208c-.353.672-.7.61-1.004-.019A224.05 224.05 0 0 1 8.044 8.81c-.623 1.285-1.475 3.026-1.898 3.81c-.411.715-.75.622-1.02.019c-.45-1.065-1.131-2.519-1.817-3.982c-.735-1.569-1.475-3.149-1.943-4.272c-.167-.4-.293-.657-.412-.759c-.12-.1-.368-.16-.746-.18C.069 3.429 0 3.395 0 3.341v-.303l.034-.03c.615-.003 3.594 0 3.594 0l.034.03v.288c0 .08-.05.118-.15.118l-.375.016c-.322.013-.483.11-.483.288c0 .083.034.217.109.4c.72 1.753 3.207 6.998 3.207 6.998l.091.023l1.603-3.197l-.32-.71L6.24 5.095s-.213-.433-.286-.577l-.098-.196c-.387-.77-.411-.82-.865-.88c-.137-.017-.208-.035-.208-.102v-.304l.041-.03h2.853l.075.024v.303c0 .069-.05.104-.15.104l-.206.03c-.523.04-.438.254-.09.946l1.057 2.163l1.17-2.332c.195-.427.155-.534.074-.633c-.046-.055-.202-.144-.54-.158l-.133-.015a.159.159 0 0 1-.096-.034a.099.099 0 0 1-.045-.085v-.288l.041-.03Z"
              />
            </svg>
          </div>
        </div>
      </router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from "vue";
import { useLinksStore } from "@/stores/links";

const linksStore = useLinksStore();

linksStore.linkList;

onMounted(async () => {
  await linksStore.getLinks();
});
</script>
