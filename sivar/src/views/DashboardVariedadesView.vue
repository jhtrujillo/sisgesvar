<template>
  <div class="space-y-8 w-full max-w-6xl mx-auto px-4">
    <!-- Botón Volver -->
    <div class="flex justify-start">
      <router-link
        class="group flex items-center px-4 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl shadow-sm hover:bg-emerald-50 hover:text-cenicana-800 hover:border-emerald-200 transition-all duration-200"
        :to="{ name: 'main.show' }"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 transform transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        Volver al Inicio
      </router-link>
    </div>

    <!-- Title Block -->
    <div class="border-b border-slate-100 pb-5">
      <h1 class="text-3xl font-extrabold text-slate-800 flex items-center">
        <div class="p-2 bg-emerald-50 text-cenicana rounded-lg mr-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
          </svg>
        </div>
        Módulo de Mejoramiento
      </h1>
      <p class="text-slate-500 mt-2 ml-11 text-sm">Acceda a los registros y herramientas de floración, cruzamientos y gestión varietal.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Siembra Campo Card -->
      <router-link
        class="group relative flex flex-col bg-white border border-slate-100 hover:border-emerald-100 rounded-2xl p-6 shadow-premium hover:shadow-premium-hover hover:-translate-y-1.5 transition-all duration-300 overflow-hidden"
        :to="{ name: 'siembra_campo_viveros.show' }"
      >
        <div class="absolute top-0 left-0 w-full h-1 bg-emerald-400 group-hover:bg-cenicana transition-colors duration-300"></div>
        
        <div class="flex flex-col h-full items-center text-center">
          <div class="p-3.5 bg-emerald-50 rounded-xl text-cenicana group-hover:bg-cenicana group-hover:text-white transition-all duration-300 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" class="transition-transform duration-500 group-hover:scale-105">
              <path fill="currentColor" d="M12 3L2 12h3v8h6v-6h2v6h6v-8h3L12 3zm0 2.83l7 7V18h-4v-6H9v6H5v-5.17l7-7z"/>
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-800 mb-2 group-hover:text-cenicana transition-colors">Siembra Campo</h3>
          <p class="text-slate-500 text-xs leading-relaxed mb-4">Administración de viveros, siembras, parcelas, polen y floración en campo.</p>
          
          <span class="inline-flex items-center text-xs font-semibold text-emerald-600 mt-auto">
            Abrir Módulo
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-1 transform transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </span>
        </div>
      </router-link>

      <!-- Floración Card -->
      <router-link
        class="group relative flex flex-col bg-white border border-slate-100 hover:border-emerald-100 rounded-2xl p-6 shadow-premium hover:shadow-premium-hover hover:-translate-y-1.5 transition-all duration-300 overflow-hidden"
        :to="{ name: 'flowering_list.show' }"
      >
        <div class="absolute top-0 left-0 w-full h-1 bg-emerald-400 group-hover:bg-cenicana transition-colors duration-300"></div>
        
        <div class="flex flex-col h-full items-center text-center">
          <div class="p-3.5 bg-emerald-50 rounded-xl text-cenicana group-hover:bg-cenicana group-hover:text-white transition-all duration-300 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" class="transition-transform duration-500 group-hover:rotate-45">
              <path
                fill="currentColor"
                d="M12 22q0-3.425 2.225-5.95t5.525-2.975q.5-.075.875.3t.3.875q-.45 3.3-2.975 5.525T12 22Zm0 0q0-3.425-2.225-5.95T4.25 13.075q-.5-.075-.875.3t-.3.875q.45 3.3 2.975 5.525T12 22Zm0-11.5q1.05 0 1.775-.725T14.5 8q0-1.05-.725-1.775T12 5.5q-1.05 0-1.775.725T9.5 8q0 1.05.725 1.775T12 10.5Zm0 4.5q-.975 0-1.763-.537T9.1 13.05q-.125 0-.225.013t-.225.012q-1.3 0-2.225-.925T5.5 9.925q0-.525.175-1.013T6.2 8q-.325-.425-.5-.913t-.175-1.012q0-1.3.913-2.225t2.212-.925q.125 0 .225.013t.225.012q.35-.875 1.138-1.413T12 1q.975 0 1.763.537T14.9 2.95q.125 0 .225-.013t.225-.012q1.3 0 2.212.925t.913 2.225q0 .525-.163 1.012T17.8 8q.325.425.5.913t.175 1.012q0 1.3-.913 2.225t-2.212.925q-.125 0-.225-.012t-.225-.013q-.35.875-1.137 1.413T12 15Z"
              />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-800 mb-2 group-hover:text-cenicana transition-colors">Floración</h3>
          <p class="text-slate-500 text-xs leading-relaxed mb-4">Seguimiento y registros históricos de estados de floración en los cultivos.</p>
          
          <span class="inline-flex items-center text-xs font-semibold text-emerald-600 mt-auto">
            Ver Registros
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-1 transform transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </span>
        </div>
      </router-link>

      <!-- Cruzamientos Card -->
      <router-link
        class="group relative flex flex-col bg-white border border-slate-100 hover:border-emerald-100 rounded-2xl p-6 shadow-premium hover:shadow-premium-hover hover:-translate-y-1.5 transition-all duration-300 overflow-hidden"
        :to="{ name: 'cruzamientos.show' }"
      >
        <div class="absolute top-0 left-0 w-full h-1 bg-emerald-400 group-hover:bg-cenicana transition-colors duration-300"></div>
        
        <div class="flex flex-col h-full items-center text-center">
          <div class="p-3.5 bg-emerald-50 rounded-xl text-cenicana group-hover:bg-cenicana group-hover:text-white transition-all duration-300 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 640 512" class="transition-transform duration-500 group-hover:scale-105">
              <path
                fill="currentColor"
                d="M176 288a112 112 0 1 0 0-224a112 112 0 1 0 0 224zm176-112c0 86.3-62.1 158.1-144 173.1V384h32c17.7 0 32 14.3 32 32s-14.3 32-32 32h-32v32c0 17.7-14.3 32-32 32s-32-14.3-32-32v-32h-32c-17.7 0-32-14.3-32-32s14.3-32 32-32h32v-34.9C62.1 334.1 0 262.3 0 176C0 78.8 78.8 0 176 0s176 78.8 176 176zm-80.1 184.6c19.3-10.1 36.9-23.1 52.1-38.4c20 18.5 46.7 29.8 76.1 29.8c61.9 0 112-50.1 112-112s-50.1-112-112-112c-7.2 0-14.3.7-21.1 2c-4.9-21.5-13-41.7-24-60.2c14.3-3.8 29.4-5.8 45-5.8c37 0 71.4 11.4 99.8 31l20.6-20.6L487 41c-6.9-6.9-8.9-17.2-5.2-26.2S494.3 0 504 0h112c13.3 0 24 10.7 24 24v112c0 9.7-5.8 18.5-14.8 22.2s-19.3 1.7-26.2-5.2l-33.4-33.4l-20.6 20.6c19.5 28.4 31 62.7 31 99.8c0 97.2-78.8 176-176 176c-50.5 0-96-21.3-128.1-55.4z"
              />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-800 mb-2 group-hover:text-cenicana transition-colors">Cruzamientos</h3>
          <p class="text-slate-500 text-xs leading-relaxed mb-4">Planificación y registro de nuevos cruzamientos y análisis de compatibilidad genotípica.</p>
          
          <span class="inline-flex items-center text-xs font-semibold text-emerald-600 mt-auto">
            Configurar
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-1 transform transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </span>
        </div>
      </router-link>

      <!-- Variedades Card -->
      <router-link
        class="group relative flex flex-col bg-white border border-slate-100 hover:border-emerald-100 rounded-2xl p-6 shadow-premium hover:shadow-premium-hover hover:-translate-y-1.5 transition-all duration-300 overflow-hidden"
        :to="{ name: 'variedades.show' }"
      >
        <div class="absolute top-0 left-0 w-full h-1 bg-emerald-400 group-hover:bg-cenicana transition-colors duration-300"></div>
        
        <div class="flex flex-col h-full items-center text-center">
          <div class="p-3.5 bg-emerald-50 rounded-xl text-cenicana group-hover:bg-cenicana group-hover:text-white transition-all duration-300 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 512 512" class="transition-transform duration-500 group-hover:scale-110">
              <path
                fill="currentColor"
                d="M264.34 21.275c-61.1 44.835-108.23 124.229-103.512 198.29c6.36-7.25 13.422-13.883 21.139-19.713c11.146-65.623 49.022-131.354 82.373-178.577zM57.383 22.758c24.174 34.235 50.712 78.19 67.728 124.773c7.978 6.219 15.304 13.215 21.76 20.94a236.975 236.975 0 0 1 3.951-15.287c-16.31-48.832-51.666-104.548-93.44-130.426zm405.053 10.744c-47.361 40.863-69.363 100.554-74.997 158.209a202.35 202.35 0 0 1 8.16 11.328a201.113 201.113 0 0 1 16.737-29.867c9.403-51.405 27.53-101.544 50.1-139.67zm-92.069 3.707c-64.832 14.582-107.75 78.8-116.562 136.773c2.973 1.672 6.73 5.1 9.265 8.455c22.607-66.53 61.275-110.735 107.297-145.228zm-353.123 91.37c51.778 24.363 105.825 56.494 127.035 103.939c-2.272-12.34-2.629-25.452-1.396-38.944c-28.597-42.532-78.956-63.451-125.639-64.996zm476.056 27.49c-34.406-.802-76.934 23.426-86.128 66.726c1.139 4.58 13.022 13.846 21.013 16.969c.773-43.834 37.177-62.218 65.115-83.695zm-174.29 2.181c21.04 21.461 48.917 45.511 61.953 86.13c4.24-2.486 8.63-4.729 13.18-6.693c-15.79-31.463-44.049-67.846-75.133-79.437zm-73.265 30.155c-37.362 3.63-64.025 29.123-83.225 58.677c-14.63 22.519-23.115 49.683-24.555 76.266c25.986-53.821 62.46-108.757 112.652-127.534zm31.927 13.365c-2.223 2.752-5.084 4.713-7.885 6.818c31.072 26.356 47.903 58.752 56.068 93.111a205.85 205.85 0 0 1 9.504-14.467c-9.09-33.826-32.122-70.188-57.687-85.462zM248.41 215.8a128.121 128.121 0 0 0-9.748 4.63l-1.48 27.127c14.5 11.416 25.42 23.037 34.015 37.332l2.711-49.662c-6.944-6.998-15.319-13.482-25.498-19.426zm-120.312 24.09C71.5 256.71 37.71 308.58 28.604 361.046c29.726-43.263 59.439-87.436 104.86-106.18c-1.603-5.314-3.395-10.297-5.366-14.974zm74.738 9.506c-3.896 5.005-7.323 9.1-10.813 14.153c43.782 20.683 60.177 57.427 80.86 93.969c-2.173-48.16-27.882-92.375-70.047-108.121zm205.033 2.33c-29.13 10.175-52.395 33.868-69.392 64.065c-11.124 19.76-19.43 42.164-24.94 64.672c19.94-27.481 34.02-48.154 47.813-65.518c16.755-21.093 33.426-37.283 58.636-53.603c-2.898-5.063-7.514-9.234-12.117-9.615zm22.572 24.243c-5.147 3.297-9.868 6.548-14.26 9.812c23.392 42.081 38.88 88.57 55.426 133.594c-1.502-49.435-13.906-99.519-41.166-143.406zM174.115 293.63c-10.184 19.084-19.878 39.48-32.46 59.031l1.687 27.598c12.051 1.164 23.366-1.86 35.72-5.682zm227.905 3.7c-12.309 11.669-21.565 22.225-30.59 33.958c8.607 6.083 19.627 8.03 32.529 8.234l6.086-26.554a405.944 405.944 0 0 0-8.025-15.639zm-169.012 26.702l-2.383 43.643l.541-.807c9.337 6.267 18.402 6.229 28.932 4.631c-10.136-17.217-18.219-33.414-27.09-47.467zm131.951 24.508l-12.9 56.295c11.853 5.06 22.705 9.387 34.753 9.504l13.045-56.922c-12.158-.504-24.254-2.66-34.898-8.877zm-135.355 37.85l-2.575 47.148c12.128 4.554 23.916 6.054 35.803 4.572l2.688-49.242c-11.093 1.813-23.479 2.755-35.916-2.478zm-49.413 6.656c-10.937 3.273-22.755 6.1-35.74 5.361l3.897 63.762c13.989.1 26.73-.425 35.804-4.287zm167.772 29.656l-12.32 53.764c11.403 4.048 23.029 6.993 34.884 8.937l12.192-53.197c-12.962-.822-24.314-5.066-34.756-9.504zM226.01 452.21L223.729 494h36.052l2.059-37.703c-11.976 1.028-23.997-.38-35.83-4.088zm-40.705 24.514c-11.492 3.648-23.852 3.84-35.85 3.588l.836 13.689h36.07z"
              />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-800 mb-2 group-hover:text-cenicana transition-colors">Variedades</h3>
          <p class="text-slate-500 text-xs leading-relaxed mb-4">Gestión del catálogo varietal, genealogía e historial agronómico de selecciones.</p>
          
          <span class="inline-flex items-center text-xs font-semibold text-emerald-600 mt-auto">
            Ver Catálogo
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-1 transform transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </span>
        </div>
      </router-link>



      <!-- Experimentos Card -->
      <router-link
        class="group relative flex flex-col bg-white border border-slate-100 hover:border-emerald-100 rounded-2xl p-6 shadow-premium hover:shadow-premium-hover hover:-translate-y-1.5 transition-all duration-300 overflow-hidden"
        :to="{ name: 'experiments.show' }"
      >
        <div class="absolute top-0 left-0 w-full h-1 bg-emerald-400 group-hover:bg-cenicana transition-colors duration-300"></div>
        
        <div class="flex flex-col h-full items-center text-center">
          <div class="p-3.5 bg-emerald-50 rounded-xl text-cenicana group-hover:bg-cenicana group-hover:text-white transition-all duration-300 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" class="transition-transform duration-500 group-hover:rotate-12">
              <path
                fill="currentColor"
                d="M12 22q-2.075 0-3.538-1.463T7 17V8q-.825 0-1.413-.588T5 6V4q0-.825.588-1.413T7 2h10q.825 0 1.413.588T19 4v2q0 .825-.588 1.413T17 8v9q0 2.075-1.463 3.538T12 22Zm0-2q.975 0 1.75-.563T14.825 18H13q-.425 0-.713-.288T12 17q0-.425.288-.713T13 16h2v-1h-2q-.425 0-.713-.288T12 14q0-.425.288-.713T13 13h2v-1h-2q-.425 0-.713-.288T12 11q0-.425.288-.713T13 10h2V8H9v9q0 1.25.875 2.125T12 20Z"
              />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-800 mb-2 group-hover:text-cenicana transition-colors">Experimentos</h3>
          <p class="text-slate-500 text-xs leading-relaxed mb-4">Diseño experimental, control de variables, parámetros ambientales e historial de ensayos.</p>
          
          <span class="inline-flex items-center text-xs font-semibold text-emerald-600 mt-auto">
            Administrar
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-1 transform transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </span>
        </div>
      </router-link>

      <!-- Libros de campo Card -->
      <router-link
        class="group relative flex flex-col bg-white border border-slate-100 hover:border-emerald-100 rounded-2xl p-6 shadow-premium hover:shadow-premium-hover hover:-translate-y-1.5 transition-all duration-300 overflow-hidden"
        :to="{ name: 'mejoramiento.show' }"
      >
        <div class="absolute top-0 left-0 w-full h-1 bg-emerald-400 group-hover:bg-cenicana transition-colors duration-300"></div>
        
        <div class="flex flex-col h-full items-center text-center">
          <div class="p-3.5 bg-emerald-50 rounded-xl text-cenicana group-hover:bg-cenicana group-hover:text-white transition-all duration-300 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 32 32" class="transition-transform duration-500 group-hover:skew-y-6">
              <path
                fill="currentColor"
                d="M26.68 7.858a.595.595 0 0 0-.607-.083l-9.66 4.183c-.42.183-.946.27-1.486.27c-.753.003-1.532-.172-2.075-.41a2.523 2.523 0 0 1-.47-.26c.04-.02.09-.042.145-.064l8.786-3.804l1.31.56V6.613a.775.775 0 0 0-.283-.612a.592.592 0 0 0-.605-.083l-9.66 4.183c-.298.12-.554.268-.77.483a1.327 1.327 0 0 0-.395.934c0 .01.003.027.003.027v14.73l-.002.02c0 .004.003.006.003.01v.016h.002c.02.515.28.843.528 1.075c.78.688 2.09 1.073 3.484 1.093c.66 0 1.33-.1 1.95-.366l9.663-4.184c.255-.11.422-.383.422-.692V8.47a.781.781 0 0 0-.283-.612zm-6.127-2.8c-.017-.22-.108-.43-.27-.556a.595.595 0 0 0-.607-.083L10.016 8.6c-.42.182-.947.27-1.486.27c-.753.002-1.532-.173-2.075-.412a2.448 2.448 0 0 1-.47-.258c.04-.02.09-.042.145-.064l8.787-3.804l1.31.56V3.257a.776.776 0 0 0-.284-.612a.594.594 0 0 0-.606-.083l-9.66 4.184c-.298.12-.553.267-.77.483a1.327 1.327 0 0 0-.394.934c0 .012.003.028.003.028v14.777h.002c.02.515.28.843.528 1.075c.78.688 2.09 1.072 3.485 1.092a5.57 5.57 0 0 0 1.127-.122V11.544c-.01-.7.27-1.372.762-1.856a3.476 3.476 0 0 1 1.19-.756l8.943-3.874z"
              />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-800 mb-2 group-hover:text-cenicana transition-colors">Libros de Campo</h3>
          <p class="text-slate-500 text-xs leading-relaxed mb-4">Anotaciones de campo, alineamientos y bitácoras detalladas por zonas de muestreo.</p>
          
          <span class="inline-flex items-center text-xs font-semibold text-emerald-600 mt-auto">
            Abrir Libros
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-1 transform transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </span>
        </div>
      </router-link>

      <!-- Registro de Ensayos Card -->
      <router-link
        class="group relative flex flex-col bg-white border border-slate-100 hover:border-emerald-100 rounded-2xl p-6 shadow-premium hover:shadow-premium-hover hover:-translate-y-1.5 transition-all duration-300 overflow-hidden"
        :to="{ name: 'mejoramiento.ensayos.dashboard' }"
      >
        <div class="absolute top-0 left-0 w-full h-1 bg-emerald-400 group-hover:bg-cenicana transition-colors duration-300"></div>
        
        <div class="flex flex-col h-full items-center text-center">
          <div class="p-3.5 bg-emerald-50 rounded-xl text-cenicana group-hover:bg-cenicana group-hover:text-white transition-all duration-300 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" class="transition-transform duration-500 group-hover:scale-105">
              <path fill="currentColor" d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 16H5v-4h7v4zm0-6H5V7h7v6zm7 6h-5v-4h5v4zm0-6h-5V7h5v6z"/>
            </svg>
          </div>
          <h3 class="text-lg font-bold text-slate-800 mb-2 group-hover:text-cenicana transition-colors">Registro de Ensayos</h3>
          <p class="text-slate-500 text-xs leading-relaxed mb-4">Base de datos histórica de ensayos, mapas, croquis e informes agronómicos.</p>
          
          <span class="inline-flex items-center text-xs font-semibold text-emerald-600 mt-auto">
            Abrir Ensayos
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-1 transform transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </span>
        </div>
      </router-link>

    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { onMounted } from "vue";
import { useLinksStore } from "@/stores/links";

const linksStore = useLinksStore();

onMounted(async () => {
  console.log("carga los links");

  await linksStore.getLinks();
});
</script>
