<!-- Menú principal -->
<template>
  <nav class="sticky top-0 z-50 backdrop-blur-md bg-white/90 border-b border-slate-200/80 w-full">
    <div class="flex items-center justify-between py-3 px-6 md:px-12">
      <div class="flex items-center transition-transform duration-300 hover:scale-[1.01]">
        <router-link :to="{ name: 'main.show' }" class="flex items-center space-x-3 pt-1 pb-1">
          <img class="h-12 w-auto object-contain" src="@/assets/images/logo_cenicana.png" alt="Cenicaña Logo" />
        </router-link>
      </div>
      
      <span class="max-md:hidden">
        <h1 class="tracking-wide font-extrabold text-2xl bg-gradient-to-r from-cenicana-800 to-emerald-600 bg-clip-text text-transparent">
          SIVAR
        </h1>
      </span>

      <button
        type="button"
        class="p-2 text-slate-600 hover:text-cenicana-800 hover:bg-slate-100 rounded-lg transition-colors duration-200 md:hidden absolute right-6 top-3 cursor-pointer focus:outline-none"
        :class="[open]"
        @click="MenuOpen()"
      >
        <svg v-if="!open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

      <div class="relative flex items-center">
        <div class="flex items-center space-x-4">
          <Menu as="div" class="relative ml-4 flex-shrink-0">
            <div>
              <MenuButton
                class="group flex items-center space-x-2 p-1.5 rounded-full bg-slate-50 hover:bg-emerald-50 transition-all duration-200 border border-slate-200 hover:border-emerald-200 focus:outline-none focus:ring-2 focus:ring-cenicana focus:ring-offset-2"
              >
                <span class="sr-only">Open user menu</span>
                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-cenicana-800 group-hover:bg-cenicana-800 group-hover:text-white transition-all duration-300">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"
                    />
                  </svg>
                </div>
              </MenuButton>
            </div>
            <transition
              leave-active-class="transition ease-in duration-75"
              leave-from-class="transform opacity-100 scale-100"
              leave-to-class="transform opacity-0 scale-95"
            >
              <MenuItems
                class="rounded-xl absolute right-0 z-50 mt-2 w-56 origin-top-right bg-white border border-slate-100 py-2 shadow-premium focus:outline-none"
              >
                <MenuItem>
                  <div class="px-4 py-3 border-b border-slate-50">
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Usuario</p>
                    <p class="text-sm font-semibold text-slate-700 truncate mt-0.5">{{ userStore.userInfo?.nmbre }}</p>
                  </div>
                </MenuItem>
                <MenuItem v-slot="{ active }">
                  <button 
                    @click="logout" 
                    :class="[active ? 'bg-red-50 text-red-600' : 'text-slate-600', 'w-full flex items-center px-4 py-2.5 text-sm font-medium transition-colors duration-150']"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                    Cerrar sesión
                  </button>
                </MenuItem>
              </MenuItems>
            </transition>
          </Menu>
        </div>
      </div>
    </div>
  </nav>
</template>
<script setup lang="ts">
import { ref } from "vue";
import { useUserStore } from "@/stores/user";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { RouterLink } from "vue-router";

import { onMounted } from "vue";
import { useLinksStore } from "@/stores/links";

const linksStore = useLinksStore();

onMounted(async () => {
  await linksStore.getLinks();
});
onMounted(async () => {});
const userStore = useUserStore();
const logout = () => {
  userStore.logout();
};
let isVisible = ref(false);
function toggleVisbility() {
  isVisible.value = !isVisible.value;
}
let open = ref(true);
function MenuOpen() {
  open.value = !open.value;
}
</script>
