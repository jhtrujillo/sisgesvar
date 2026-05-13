<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});
</script>

<template>
    <Head title="Bienvenidos a Sivar" />
    <div class="relative min-h-screen overflow-hidden bg-gradient-to-br from-[#f8fafc] to-[#e2e8f0]">
        <!-- Decorative Ambient Background Blurs -->
        <div class="absolute top-[-10%] right-[-10%] w-96 h-96 bg-[#22c55e] rounded-full mix-blend-multiply filter blur-[128px] opacity-20 animate-pulse"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[32rem] h-[32rem] bg-[#0ea5e9] rounded-full mix-blend-multiply filter blur-[128px] opacity-10"></div>

        <div class="relative flex flex-col min-h-screen">
            <!-- Transparent Header -->
            <header class="flex items-center justify-between px-8 py-6 bg-white/50 backdrop-blur-md border-b border-white/20">
                <div class="flex items-center space-x-3">
                    <div class="p-2 rounded-xl bg-gradient-to-tr from-green-600 to-emerald-400 shadow-md">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <span class="text-3xl font-extrabold tracking-tight text-slate-900 font-sans">SIVAR</span>
                </div>

                <nav v-if="canLogin" class="flex items-center space-x-4">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="px-6 py-2 font-semibold text-white transition duration-300 transform shadow-lg bg-slate-800 rounded-xl hover:bg-slate-700 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-800"
                    >
                        Ir al Panel
                    </Link>

                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="font-medium transition text-slate-600 hover:text-slate-900"
                        >
                            Iniciar Sesión
                        </Link>

                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="px-6 py-2 font-semibold text-white transition duration-300 transform shadow-lg bg-gradient-to-r from-green-600 to-emerald-500 rounded-xl hover:from-green-700 hover:to-emerald-600 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        >
                            Comenzar Registro
                        </Link>
                    </template>
                </nav>
            </header>

            <!-- Hero Section -->
            <main class="flex flex-col items-center justify-center flex-grow px-6 py-12 text-center lg:py-24">
                <div class="inline-flex items-center px-3 py-1 mb-8 space-x-2 rounded-full bg-green-100/80 backdrop-blur border border-green-200 text-green-800 font-medium text-sm shadow-sm">
                    <span class="flex w-2 h-2 bg-green-500 rounded-full animate-ping"></span>
                    <span>Plataforma de Mejoramiento Genético Activa</span>
                </div>

                <h1 class="max-w-4xl text-5xl font-black tracking-tight text-slate-900 sm:text-7xl font-sans">
                    Gestión Integral de
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-600 to-teal-500">
                        Registros de Ensayos
                    </span>
                </h1>

                <p class="max-w-2xl mt-8 text-xl text-slate-600/90 font-light leading-relaxed">
                    SIVAR permite centralizar el levantamiento de información técnica, monitorear ambientes, y agilizar la toma de decisiones a través de cargas inteligentes de datos.
                </p>

                <div class="flex flex-col items-center mt-12 space-y-4 sm:flex-row sm:space-y-0 sm:space-x-6">
                    <Link
                        v-if="!$page.props.auth.user"
                        :href="route('register')"
                        class="px-10 py-4 text-lg font-bold text-white transition-all duration-300 bg-slate-900 rounded-2xl shadow-xl hover:bg-slate-800 hover:-translate-y-1 hover:shadow-2xl"
                    >
                        Crear Cuenta de Acceso
                    </Link>
                    <Link
                        v-else
                        :href="route('dashboard')"
                        class="px-10 py-4 text-lg font-bold text-white transition-all duration-300 bg-slate-900 rounded-2xl shadow-xl hover:bg-slate-800 hover:-translate-y-1 hover:shadow-2xl"
                    >
                        Acceder al Dashboard
                    </Link>
                    
                    <a href="#" class="flex items-center font-semibold transition text-slate-700 hover:text-green-700 group">
                        Conocer más del módulo
                        <svg class="w-5 h-5 ml-2 transition group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                </div>

                <!-- Feature Cards Container -->
                <div class="grid max-w-6xl grid-cols-1 gap-8 mt-24 md:grid-cols-3">
                    <!-- Card 1 -->
                    <div class="group relative p-8 bg-white/70 backdrop-blur border border-white shadow-xl rounded-3xl transition duration-500 hover:-translate-y-2 hover:shadow-2xl hover:bg-white">
                        <div class="absolute inset-0 bg-gradient-to-tr from-green-50 to-transparent opacity-0 group-hover:opacity-100 rounded-3xl transition duration-500"></div>
                        <div class="relative">
                            <div class="w-14 h-14 flex items-center justify-center bg-green-100 text-green-600 rounded-2xl mb-6 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-3">Registro por Ambientes</h3>
                            <p class="text-slate-600 text-sm leading-relaxed">
                                Acceso focalizado por líder de ambiente para agilizar la captura y auditoría de lotes.
                            </p>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="group relative p-8 bg-white/70 backdrop-blur border border-white shadow-xl rounded-3xl transition duration-500 hover:-translate-y-2 hover:shadow-2xl hover:bg-white">
                        <div class="absolute inset-0 bg-gradient-to-tr from-blue-50 to-transparent opacity-0 group-hover:opacity-100 rounded-3xl transition duration-500"></div>
                        <div class="relative">
                            <div class="w-14 h-14 flex items-center justify-center bg-blue-100 text-blue-600 rounded-2xl mb-6 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-3">Carga Masiva Excel</h3>
                            <p class="text-slate-600 text-sm leading-relaxed">
                                Sube plantillas técnicas existentes y sincroniza instantáneamente toda la base histórica.
                            </p>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="group relative p-8 bg-white/70 backdrop-blur border border-white shadow-xl rounded-3xl transition duration-500 hover:-translate-y-2 hover:shadow-2xl hover:bg-white">
                        <div class="absolute inset-0 bg-gradient-to-tr from-amber-50 to-transparent opacity-0 group-hover:opacity-100 rounded-3xl transition duration-500"></div>
                        <div class="relative">
                            <div class="w-14 h-14 flex items-center justify-center bg-amber-100 text-amber-600 rounded-2xl mb-6 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-3">Seguridad Modular</h3>
                            <p class="text-slate-600 text-sm leading-relaxed">
                                Control de roles granulares: Los Jefes tienen visión global mientras se blinda la información.
                            </p>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Subdued Footer -->
            <footer class="py-8 border-t border-white/20 bg-white/30 text-center backdrop-blur">
                <p class="text-slate-500 text-sm">
                    &copy; 2026 SIVAR - Sistema Informático de Variedades. Todos los derechos reservados.
                </p>
            </footer>
        </div>
    </div>
</template>

<style>
body {
    font-family: 'Inter', sans-serif;
}
</style>
