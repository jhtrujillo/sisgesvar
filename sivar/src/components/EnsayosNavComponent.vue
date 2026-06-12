<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { useUserStore } from "@/stores/user";

const route = useRoute();
const userStore = useUserStore();
const authUser = computed(() => userStore.userInfo);
const activeRouteName = computed(() => route.name);
</script>

<template>
  <div class="space-y-4 w-full">
    <!-- Back Button & Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <router-link
        class="group inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-xl shadow-sm hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 transition duration-200 w-fit cursor-pointer"
        :to="{ name: 'mejoramiento.show' }"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 transform transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        Volver a Mejoramiento
      </router-link>

      <!-- Breadcrumbs or simple route status -->
      <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest hidden md:block select-none">
        Mejoramiento / <span class="text-slate-600">Registro de Ensayos</span>
      </div>
    </div>

    <!-- Navigation Tabs (Sleek styled tabs) -->
    <div class="bg-white p-1.5 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap gap-1.5 w-full">
      <!-- Tab 1: Dashboard -->
      <router-link
        :to="{ name: 'mejoramiento.ensayos.dashboard' }"
        class="flex-1 sm:flex-initial min-w-[120px] flex items-center justify-center gap-2 px-5 py-3 text-xs md:text-sm font-black rounded-xl transition duration-200 cursor-pointer text-center"
        :class="activeRouteName === 'mejoramiento.ensayos.dashboard'
          ? 'bg-emerald-600 text-white shadow-md'
          : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50'"
      >
        <span>📊</span>
        <span>Dashboard</span>
      </router-link>

      <!-- Tab 2: Base de Datos (Index) -->
      <router-link
        :to="{ name: 'mejoramiento.ensayos.index' }"
        class="flex-1 sm:flex-initial min-w-[120px] flex items-center justify-center gap-2 px-5 py-3 text-xs md:text-sm font-black rounded-xl transition duration-200 cursor-pointer text-center"
        :class="activeRouteName === 'mejoramiento.ensayos.index'
          ? 'bg-emerald-600 text-white shadow-md'
          : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50'"
      >
        <span>🗂️</span>
        <span>Base de Datos</span>
      </router-link>

      <!-- Tab 3: Catalogos (Tablas Maestras) - Only for JEFE -->
      <router-link
        v-if="authUser?.role === 'JEFE'"
        :to="{ name: 'mejoramiento.ensayos.catalogos' }"
        class="flex-1 sm:flex-initial min-w-[120px] flex items-center justify-center gap-2 px-5 py-3 text-xs md:text-sm font-black rounded-xl transition duration-200 cursor-pointer text-center"
        :class="activeRouteName === 'mejoramiento.ensayos.catalogos'
          ? 'bg-emerald-600 text-white shadow-md'
          : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50'"
      >
        <span>📚</span>
        <span>Tablas Maestras</span>
      </router-link>

      <!-- Tab 4: Actividades (Historial) - Only for JEFE -->
      <router-link
        v-if="authUser?.role === 'JEFE'"
        :to="{ name: 'mejoramiento.ensayos.actividades' }"
        class="flex-1 sm:flex-initial min-w-[120px] flex items-center justify-center gap-2 px-5 py-3 text-xs md:text-sm font-black rounded-xl transition duration-200 cursor-pointer text-center"
        :class="activeRouteName === 'mejoramiento.ensayos.actividades'
          ? 'bg-emerald-600 text-white shadow-md'
          : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50'"
      >
        <span>⏳</span>
        <span>Historial / Bitácora</span>
      </router-link>
    </div>
  </div>
</template>
