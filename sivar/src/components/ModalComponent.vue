<!-- Elemento que traeun modal para dar una respuesta -->
<template>
  <!-- Transicion  -->
  <TransitionRoot as="template" :show="props.isOpenModal">
    <Dialog as="div" class="relative z-50" @close="closeModal()">
      <TransitionChild
        as="template"
        enter="ease-out duration-300"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="ease-in duration-200"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 overflow-y-auto bg-gray-500 bg-opacity-75 transition-opacity" />
      </TransitionChild>
      <div class="fixed z-10 inset-0 overflow-y-auto">
        <div class="flex items-end sm:items-center justify-center p-6 text-center sm:p-0">
          <TransitionChild
            as="template"
            enter="ease-out duration-300"
            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            enter-to="opacity-100 translate-y-0 sm:scale-100"
            leave="ease-in duration-200"
            leave-from="opacity-100 translate-y-0 sm:scale-100"
            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          >
            <DialogPanel
              class="relative bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-1 md:my-1 md:p-5 sm:w-full sm:p-9"
              :class="props.modalClass"
            >
              <div class="absolute top-0 right-0 pt-4 pr-4 sm:block">
                <button
                  type="button"
                  class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                  @click="closeModal()"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
              <!-- Crear -->
              <div>
                <div class="mt-4 sm:mt-6">
                  <template v-if="props.modalType === 'create'">
                    <DialogTitle as="h1" class="text-center text-2xl leading-6 font-medium text-gray-900"
                      ><span class="text-gray-400">{{ props.modalName }}</span></DialogTitle
                    >
                    <!-- <createIrrigationUnit></createIrrigationUnit> -->
                  </template>
                </div>
              </div>
              <!-- Editar -->
              <div>
                <div class="mt-4 sm:mt-6">
                  <template v-if="props.modalType === 'edit'">
                    <DialogTitle as="h1" class="text-center text-2xl leading-6 font-medium text-gray-900"
                      ><span class="text-gray-400">{{ props.modalName }}</span>
                    </DialogTitle>
                    <!-- <createIrrigationUnit"> </createIrrigationUnit> -->
                  </template>
                </div>
              </div>
              <!-- Eliminar -->
              <template v-if="props.modalType === 'delete'">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                  <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"
                        />
                      </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                      <DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900">{{ props.modalName }}</DialogTitle>
                      <div class="mt-2">
                        <p class="text-sm text-gray-500">Esta seguro de eliminar el registro?</p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                  <button
                    type="button"
                    class="inline-flex w-full justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"
                  >
                    Eliminar
                  </button>
                  <button
                    type="button"
                    class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                    @click="closeModal()"
                    ref="cancelButtonRef"
                  >
                    Cancelar
                  </button>
                </div>
              </template>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
<script lang="ts" setup>
import { TransitionRoot, Dialog, TransitionChild, DialogPanel, DialogTitle } from "@headlessui/vue";

const props = defineProps({
  isOpenModal: { type: Boolean, default: false },
  modalType: { type: String, default: "" },
  modalClass: { type: String, default: "" },
  modalName: { type: String, default: "" },
  infoRow: { type: Object, default: () => {} }
});

const emit = defineEmits(["closeModal"]);

const closeModal = () => emit("closeModal");
</script>
