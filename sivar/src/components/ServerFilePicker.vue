<template>
  <div>
    <!-- Botón / Input que abre el selector -->
    <div class="flex items-center space-x-2">
      <input
        type="text"
        :value="displayValue"
        readonly
        :placeholder="placeholder"
        class="w-full border-slate-300 bg-slate-50 cursor-not-allowed rounded-lg shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm font-medium text-slate-700"
      />
      <BaseButton type="button" variant="secondary" size="sm" @click="openModal" class="flex-shrink-0">
        <template #icon-left>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"
            />
          </svg>
        </template>
        Explorar
      </BaseButton>
    </div>

    <!-- Modal del Explorador -->
    <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[85vh] flex flex-col overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
          <h3 class="text-lg font-bold text-slate-800 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-teal-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"
              />
            </svg>
            Explorador del Servidor
          </h3>
          <BaseButton variant="ghost" size="sm" iconOnly @click="closeModal" class="text-slate-400 hover:text-red-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </BaseButton>
        </div>

        <!-- Breadcrumb / Ruta Actual -->
        <div class="px-6 py-3 bg-white border-b border-slate-100 flex items-center space-x-2 overflow-x-auto">
          <BaseButton variant="ghost" size="xs" rounded="md" iconOnly @click="loadDirectory(parentPath)" :disabled="!parentPath" title="Subir un nivel">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
            </svg>
          </BaseButton>

          <div class="text-sm font-mono text-slate-700 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200 flex-1 truncate">
            {{ currentPath }}
          </div>
        </div>

        <!-- Lista de Archivos -->
        <div class="flex-1 overflow-y-auto p-2 bg-white min-h-[300px]">
          <div v-if="isLoading" class="flex flex-col items-center justify-center h-full text-slate-400 space-y-3">
            <svg class="animate-spin h-8 w-8 text-teal-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
            </svg>
            <span>Cargando directorio...</span>
          </div>

          <div v-else-if="errorMsg" class="flex flex-col items-center justify-center h-full text-red-500 space-y-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
              />
            </svg>
            <span class="font-semibold">{{ errorMsg }}</span>
          </div>

          <table v-else class="w-full text-left border-collapse">
            <thead class="bg-slate-50 sticky top-0 shadow-sm z-10">
              <tr>
                <th class="px-4 py-2 text-xs font-semibold text-slate-500 uppercase">Nombre</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="file in files"
                :key="file.path"
                @click="selectItem(file)"
                @dblclick="file.isDirectory ? loadDirectory(file.path) : confirmSelection()"
                class="border-b border-slate-50 hover:bg-teal-50/50 cursor-pointer transition-colors"
                :class="{ 'bg-teal-50 border-teal-200': selectedPath === file.path }"
              >
                <td class="px-4 py-2.5 flex items-center space-x-3">
                  <!-- Icono de Carpeta -->
                  <svg v-if="file.isDirectory" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
                  </svg>
                  <!-- Icono de Archivo -->
                  <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                    />
                  </svg>

                  <span class="text-sm text-slate-700 font-medium truncate" :class="{ 'font-bold': file.isDirectory }">
                    {{ file.name }}
                  </span>
                </td>
              </tr>
              <tr v-if="files.length === 0">
                <td class="px-4 py-8 text-center text-slate-400 text-sm italic">La carpeta está vacía</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Footer / Acciones -->
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
          <div class="flex-1 mr-4">
            <span v-if="selectedPath && !selectedIsDirectory" class="text-xs text-teal-600 font-semibold truncate block">
              Seleccionado: {{ selectedName }}
            </span>
            <span v-else class="text-xs text-slate-400"> Navegue y seleccione un archivo </span>
          </div>
          <div class="flex space-x-3">
            <BaseButton variant="secondary" size="sm" @click="closeModal"> Cancelar </BaseButton>
            <BaseButton variant="primary" size="sm" @click="confirmSelection" :disabled="!selectedPath || selectedIsDirectory">
              <template #icon-left>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </template>
              Seleccionar Archivo
            </BaseButton>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, computed } from "vue";

const props = defineProps({
  modelValue: {
    type: String,
    default: ""
  },
  placeholder: {
    type: String,
    default: "Seleccione un archivo..."
  }
});

const emit = defineEmits(["update:modelValue"]);

const displayValue = computed(() => {
  if (!props.modelValue) return "";
  const parts = props.modelValue.split("/");
  return parts[parts.length - 1] || props.modelValue;
});

// Estado del Modal
const isOpen = ref(false);
const isLoading = ref(false);
const errorMsg = ref("");

// Estado del Explorador
const currentPath = ref("");
const parentPath = ref("");
const files = ref<any[]>([]);

// Selección
const selectedPath = ref("");
const selectedName = ref("");
const selectedIsDirectory = ref(false);

const openModal = () => {
  isOpen.value = true;
  // Cargar ruta actual si hay una, o cargar raíz por defecto
  let pathToLoad = props.modelValue || "";

  // Si modelValue es un archivo, intentar cargar su directorio padre
  if (pathToLoad && !pathToLoad.endsWith("/")) {
    const parts = pathToLoad.split("/");
    parts.pop();
    pathToLoad = parts.join("/");
  }

  loadDirectory(pathToLoad);
};

const closeModal = () => {
  isOpen.value = false;
  selectedPath.value = "";
};

const loadDirectory = async (path: string) => {
  isLoading.value = true;
  errorMsg.value = "";
  files.value = [];
  selectedPath.value = "";

  try {
    const url = new URL("http://localhost:3001/list-directory");
    if (path) {
      url.searchParams.append("path", path);
    }

    const response = await fetch(url.toString());
    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.error || "Error al cargar el directorio");
    }

    currentPath.value = data.currentPath;
    parentPath.value = data.parentPath;
    files.value = data.files;
  } catch (error: any) {
    errorMsg.value = error.message;
    console.error(error);
  } finally {
    isLoading.value = false;
  }
};

const selectItem = (file: any) => {
  selectedPath.value = file.path;
  selectedName.value = file.name;
  selectedIsDirectory.value = file.isDirectory;
};

const confirmSelection = () => {
  if (selectedPath.value && !selectedIsDirectory.value) {
    emit("update:modelValue", selectedPath.value);
    closeModal();
  }
};
</script>
