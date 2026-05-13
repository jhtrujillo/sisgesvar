<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    cruzamientos: Object
});
</script>

<template>
    <Head title="Gestión de Cruzamientos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-3">
                <div class="p-2.5 bg-emerald-800 text-white rounded-xl shadow-inner shadow-emerald-950/50">
                    <!-- Crossed genetic style icon -->
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12a7.5 7.5 0 0015 0m-15 0A7.5 7.5 0 1118 16.65M12 19.5a7.5 7.5 0 100-15 7.5 7.5 0 000 15z"></path></svg>
                </div>
                <div>
                    <h2 class="text-2xl font-black leading-tight text-slate-800">Módulo de Cruzamientos</h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Estructura base creada con éxito para el seguimiento de parentalidades y cruzamientos.</p>
                </div>
            </div>
        </template>

        <div class="py-12 bg-slate-50 min-h-screen">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Banner de Bienvenida a la Construcción del Módulo -->
                <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-3xl shadow-2xl border border-slate-700/50 overflow-hidden relative">
                    
                    <!-- Ambient Backdrop Glow -->
                    <div class="absolute -top-24 -right-24 w-64 h-64 bg-emerald-500/10 blur-[80px] rounded-full pointer-events-none"></div>
                    <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-sky-500/10 blur-[80px] rounded-full pointer-events-none"></div>

                    <div class="p-10 sm:p-16 text-center relative z-10">
                        <div class="w-20 h-20 bg-white/10 backdrop-blur-md rounded-3xl flex items-center justify-center mx-auto shadow-xl border border-white/10">
                            <span class="text-4xl">🏗️</span>
                        </div>
                        
                        <h1 class="text-3xl font-black text-white mt-8">¡Lienzo en Blanco Preparado!</h1>
                        <p class="text-slate-400 max-w-lg mx-auto mt-3 font-medium leading-relaxed">
                            He materializado la arquitectura del backend (Migración, Modelo y Controlador) y este espacio visual. El sistema está listo para que comencemos a añadir las tablas, formularios de carga y registros de cruzamientos en el momento en que lo desees.
                        </p>

                        <div class="mt-10 flex flex-wrap justify-center gap-3">
                            <div class="bg-slate-800/80 border border-slate-700/60 px-4 py-2.5 rounded-2xl text-left flex items-center gap-3 shrink-0">
                                <div class="w-8 h-8 bg-emerald-900/50 rounded-xl flex items-center justify-center font-bold text-emerald-400 text-xs border border-emerald-800/30">DB</div>
                                <div>
                                    <p class="text-[10px] font-bold text-slate-500 uppercase">Migración</p>
                                    <p class="text-xs font-extrabold text-slate-200">cruzamientos materializado</p>
                                </div>
                            </div>

                            <div class="bg-slate-800/80 border border-slate-700/60 px-4 py-2.5 rounded-2xl text-left flex items-center gap-3 shrink-0">
                                <div class="w-8 h-8 bg-sky-900/50 rounded-xl flex items-center justify-center font-bold text-sky-400 text-xs border border-sky-800/30">API</div>
                                <div>
                                    <p class="text-[10px] font-bold text-slate-500 uppercase">Enrutador</p>
                                    <p class="text-xs font-extrabold text-slate-200">cruzamientos.index expuesto</p>
                                </div>
                            </div>
                        </div>

                        <p class="mt-12 text-xs font-black text-emerald-400 uppercase tracking-widest animate-pulse">Arquitectura de Software Construida Satisfactoriamente</p>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
