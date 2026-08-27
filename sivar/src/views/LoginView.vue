<!-- Vista para mostrar el Login para que el usuario se autentique y pueda ingresar a la plataforma-->
<template>
  <div class="min-h-screen bg-slate-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative overflow-hidden antialiased">
    <!-- Background Organic Elements -->
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-emerald-100/30 rounded-full blur-3xl pointer-events-none animate-pulse"></div>
    <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-slate-200/50 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 flex flex-col sm:mx-auto sm:w-full sm:max-w-md">
      <div class="flex justify-center mb-6 transition-transform duration-300 hover:scale-105">
        <img class="h-20 w-auto object-contain" src="@/assets/images/logo_cenicana.png" alt="Cenicaña" />
      </div>

      <div
        class="bg-white/80 backdrop-blur-xl border border-white/60 shadow-premium rounded-3xl py-10 px-6 sm:px-12 mx-4 sm:mx-0 transition-all duration-300 hover:shadow-premium-hover"
      >
        <div class="text-center mb-8">
          <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight flex items-center justify-center">SIVAR</h1>
          <p class="mt-2 text-sm text-slate-500 font-medium">Plataforma de Información para el Mejoramiento</p>
        </div>

        <form class="space-y-6" @submit.prevent="Login()">
          <div>
            <label for="usuario" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Usuario</label>
            <div class="relative rounded-xl shadow-sm">
              <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                  />
                </svg>
              </div>
              <input
                id="usuario"
                name="usuario"
                type="text"
                required
                autocomplete="username"
                class="block w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm text-slate-700 placeholder-slate-400 focus:ring-2 focus:ring-cenicana focus:border-cenicana transition-all duration-200"
                placeholder="nombre.usuario"
                v-model="model.usuario"
              />
            </div>
          </div>

          <div>
            <label for="clave" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Contraseña</label>
            <div class="relative rounded-xl shadow-sm">
              <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                  />
                </svg>
              </div>
              <input
                id="clave"
                name="clave"
                type="password"
                required
                autocomplete="current-password"
                class="block w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm text-slate-700 placeholder-slate-400 focus:ring-2 focus:ring-cenicana focus:border-cenicana transition-all duration-200"
                placeholder="••••••••"
                v-model="model.clave"
              />
            </div>
          </div>

          <div>
            <BaseButton type="submit" variant="primary" size="md" rounded="lg" block :loading="isBusy"> Iniciar Sesión </BaseButton>
          </div>
        </form>
      </div>
    </div>

    <div class="relative z-10 py-4 mt-4">
      <p class="text-slate-400 text-center text-xs font-medium">Cenicaña &copy; {{ new Date().getFullYear() }}. Todos los derechos reservados.</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, computed, watch } from "vue";
import { useUserStore } from "@/stores/user";
import { useMainStore } from "@/stores/main";

import { useToast } from "vue-toastification";

const toast = useToast();
const mainStore = useMainStore();
const userStore = useUserStore();

const model = reactive({ usuario: "", clave: "" });
const error = computed(() => mainStore.error);

const Login = () => {
  userStore.login(model);
};

watch(error, () => {
  console.log(error);
  if (error.value) {
    toast.error(error.value);
  }
});

const isBusy = computed(() => mainStore.isBusy);
</script>
