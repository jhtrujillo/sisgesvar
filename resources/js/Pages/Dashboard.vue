<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    total_ensayos: Number,
    total_cruzamientos: Number,
    total_usuarios: Number
});
</script>

<template>
    <Head title="Selector de Módulos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <div class="p-2.5 bg-slate-800 text-white rounded-xl shadow-inner shadow-slate-950/40">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-slate-800 leading-tight">Portal Principal</h2>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">Seleccione un módulo de trabajo para iniciar su operación diaria.</p>
                    </div>
                </div>
                <div class="hidden sm:block px-4 py-1.5 bg-white border border-slate-200 shadow-sm rounded-xl text-xs text-slate-600 font-black">
                    {{ new Date().toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}
                </div>
            </div>
        </template>

        <div class="py-16 bg-slate-50 min-h-screen flex items-start">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 w-full space-y-12">
                
                <div class="text-center max-w-2xl mx-auto space-y-4">
                    <h1 class="text-3xl font-black text-slate-900 sm:text-4xl tracking-tight">
                        ¡Bienvenido, {{ $page.props.auth.user.name }}! ✨
                    </h1>
                    <p class="text-slate-500 font-medium text-base">
                        El sistema ha validado tus accesos. Por favor, selecciona el módulo en el que deseas trabajar el día de hoy:
                    </p>
                </div>

                <!-- 🚀 MÓDULOS DEL SISTEMA (SELECTOR PRINCIPAL DE INGRESO) -->
                <div 
                    class="grid grid-cols-1 gap-8 mx-auto"
                    :class="$page.props.auth.user.role === 'JEFE' ? 'md:grid-cols-3 max-w-6xl' : 'md:grid-cols-2 max-w-4xl'"
                >
                    
                    <!-- Tarjeta Módulo 1: Registro de Ensayos -->
                    <Link 
                        :href="route('ensayos.dashboard')"
                        class="group relative overflow-hidden bg-white rounded-3xl p-8 border border-slate-200 shadow-lg hover:shadow-xl hover:border-emerald-500 hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between min-h-[280px]"
                    >
                        <div class="absolute -top-16 -right-16 w-48 h-48 bg-emerald-50 rounded-full group-hover:scale-125 group-hover:bg-emerald-100/80 transition-transform duration-500 pointer-events-none"></div>
                        
                        <div class="relative z-10">
                            <div class="w-16 h-16 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-3xl shadow-lg shadow-emerald-600/20 group-hover:scale-110 transition-transform duration-300">
                                📊
                            </div>
                            <h3 class="text-2xl font-black text-slate-800 mt-6 group-hover:text-emerald-700 transition duration-200">Módulo de Ensayos</h3>
                            <p class="text-slate-500 font-medium text-sm mt-2 leading-relaxed">
                                Cuadro de mando integral, carga masiva de plantillas Excel, matriz histórica de clones y estandarización inteligente de ambientes.
                            </p>
                        </div>

                        <div class="relative z-10 mt-8 flex items-center justify-between">
                            <div class="flex items-center gap-1.5 text-xs font-black uppercase tracking-wider text-emerald-600 group-hover:gap-3 transition-all duration-300">
                                Ingresar al Módulo
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </div>
                            <span class="bg-emerald-50 text-emerald-700 font-black px-4 py-1.5 rounded-xl text-xs border border-emerald-100">
                                {{ total_ensayos }} Activos
                            </span>
                        </div>
                    </Link>

                    <!-- Tarjeta Módulo 2: Cruzamientos -->
                    <Link 
                        :href="route('cruzamientos.index')"
                        class="group relative overflow-hidden bg-white rounded-3xl p-8 border border-slate-200 shadow-lg hover:shadow-xl hover:border-sky-500 hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between min-h-[280px]"
                    >
                        <div class="absolute -top-16 -right-16 w-48 h-48 bg-sky-50 rounded-full group-hover:scale-125 group-hover:bg-sky-100/80 transition-transform duration-500 pointer-events-none"></div>
                        
                        <div class="relative z-10">
                            <div class="w-16 h-16 rounded-2xl bg-sky-600 text-white flex items-center justify-center text-3xl shadow-lg shadow-sky-600/20 group-hover:scale-110 transition-transform duration-300">
                                🧬
                            </div>
                            <h3 class="text-2xl font-black text-slate-800 mt-6 group-hover:text-sky-700 transition duration-200">Módulo Cruzamientos</h3>
                            <p class="text-slate-500 font-medium text-sm mt-2 leading-relaxed">
                                Seguimiento dinámico de polinización cruzada, bases de parentalidad (madre y padre) y control de genealogía vegetal.
                            </p>
                        </div>

                        <div class="relative z-10 mt-8 flex items-center justify-between">
                            <div class="flex items-center gap-1.5 text-xs font-black uppercase tracking-wider text-sky-600 group-hover:gap-3 transition-all duration-300">
                                Ingresar al Módulo
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </div>
                            <span class="bg-sky-50 text-sky-700 font-black px-4 py-1.5 rounded-xl text-xs border border-sky-100 flex items-center gap-1.5">
                                <span class="h-2.5 w-2.5 rounded-full bg-sky-400 animate-pulse"></span>
                                Activo
                            </span>
                        </div>
                    </Link>

                    <!-- Tarjeta Módulo 3: Gestión de Usuarios (SÓLO JEFE) -->
                    <Link 
                        v-if="$page.props.auth.user.role === 'JEFE'"
                        :href="route('users.index')"
                        class="group relative overflow-hidden bg-white rounded-3xl p-8 border border-slate-200 shadow-lg hover:shadow-xl hover:border-violet-500 hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between min-h-[280px]"
                    >
                        <div class="absolute -top-16 -right-16 w-48 h-48 bg-violet-50 rounded-full group-hover:scale-125 group-hover:bg-violet-100/80 transition-transform duration-500 pointer-events-none"></div>
                        
                        <div class="relative z-10">
                            <div class="w-16 h-16 rounded-2xl bg-violet-600 text-white flex items-center justify-center text-3xl shadow-lg shadow-violet-600/20 group-hover:scale-110 transition-transform duration-300">
                                👥
                            </div>
                            <h3 class="text-2xl font-black text-slate-800 mt-6 group-hover:text-violet-700 transition duration-200">Módulo de Usuarios</h3>
                            <p class="text-slate-500 font-medium text-sm mt-2 leading-relaxed">
                                Administración global de accesos, asignación de roles (Jefe, Líder), gestión de ambientes permitidos y perfiles del sistema.
                            </p>
                        </div>

                        <div class="relative z-10 mt-8 flex items-center justify-between">
                            <div class="flex items-center gap-1.5 text-xs font-black uppercase tracking-wider text-violet-600 group-hover:gap-3 transition-all duration-300">
                                Ingresar al Módulo
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </div>
                            <span class="bg-violet-50 text-violet-700 font-black px-4 py-1.5 rounded-xl text-xs border border-violet-100">
                                {{ total_usuarios }} Colaboradores
                            </span>
                        </div>
                    </Link>

                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
