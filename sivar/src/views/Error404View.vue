<!-- Vista para mostrar un error 404 por defecto en caso de que el usuario ingrese a una ruta de la plataforma que no exista -->
<template>
  <div class="flex flex-col justify-center items-center h-screen bg-slate-50">
    <div class="flex flex-col items-center space-y-4">
      <img class="h-auto w-64" src="@/assets/images/logo_cenicana.png" alt="logo-cenimol" />
      <h1 class="text-violet-800 text-3xl font-bold">ERROR 404</h1>
      <p class="text-emerald-600 text-3xl font-bold">Página no encontrada</p>
    </div>
  </div>
</template>

<script setup lang="ts"></script>
