<!-- Menú Lateral -->
<template>
  <div class="hidden md:fixed md:inset-y-0 md:flex md:w-64 md:flex-col">
    <div class="flex flex-grow flex-col overflow-y-auto border-r border-gray-200 bg-white pt-5">
      <!-- sidebar -->
      <div class="mt-5 flex flex-grow flex-col">
        <nav class="flex-1 space-y-1 px-2 pb-4 mt-20">
          <router-link
            class="text-gray-900 group flex items-center px-2 py-2 text-sm font-medium rounded-md"
            :to="{
              name: 'main.show'
            }"
            >Inicio</router-link
          >
          <router-link
            class="text-lime-800 group flex items-center px-2 py-2 text-sm font-medium rounded-md"
            :to="{
              name: 'users.show'
            }"
            >Usuarios</router-link
          >
          <router-link
            class="text-lime-800 group flex items-center px-2 py-2 text-sm font-medium rounded-md"
            :to="{
              name: 'sequence_server.show'
            }"
            >Sequence Server</router-link
          >
          <router-link
            class="text-lime-800 group flex items-center px-2 py-2 text-sm font-medium rounded-md"
            :to="{
              name: 'wiki_cenicana.show'
            }"
            >Wiki Cenicaña</router-link
          >
          <router-link
            class="text-lime-800 group flex items-center px-2 py-2 text-sm font-medium rounded-md"
            :to="{
              name: 'jbrowse.show'
            }"
            >JBrowse</router-link
          >
          <router-link
            class="text-lime-800 group flex items-center px-2 py-2 text-sm font-medium rounded-md"
            :to="{
              name: 'rstudio.show'
            }"
            >RStudio Server</router-link
          >
        </nav>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts"></script>
