<script setup>
import { ref, computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);

// 🧠 Dynamic System Context Router - Separates Header menus per active Module
const currentContext = computed(() => {
    const routeName = route().current();
    if (!routeName || routeName === 'dashboard') {
        return 'hub';
    }
    
    // Ensayos & dependencies namespace
    if (
        routeName.startsWith('ensayos') || 
        routeName.startsWith('catalogos') || 
        routeName.startsWith('actividades')
    ) {
        return 'ensayos';
    }
    
    // Cruzamientos namespace
    if (routeName.startsWith('cruzamientos')) {
        return 'cruzamientos';
    }
    
    // Usuarios namespace
    if (routeName.startsWith('users')) {
        return 'usuarios';
    }
    
    return 'hub';
});
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav
                class="border-b border-gray-100 bg-white"
            >
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800"
                                    />
                                </Link>
                            </div>

                            <!-- Contextual Navigation Links: Displayed ONLY when inside a specific module -->
                            <div
                                v-if="currentContext !== 'hub'"
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <!-- Master Link to escape sub-modules back to main selector -->
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="false"
                                    class="text-slate-400 hover:text-slate-800 transition"
                                >
                                    🏠 Portal Principal
                                </NavLink>

                                <!-- Sub-Menu: Módulo Ensayos -->
                                <template v-if="currentContext === 'ensayos'">
                                    <NavLink
                                        :href="route('ensayos.dashboard')"
                                        :active="route().current('ensayos.dashboard')"
                                    >
                                        Métricas
                                    </NavLink>
                                    <NavLink
                                        :href="route('ensayos.index')"
                                        :active="route().current('ensayos.index')"
                                    >
                                        Ensayos
                                    </NavLink>
                                    <NavLink
                                        v-if="$page.props.auth.user.role === 'JEFE'"
                                        :href="route('catalogos.index')"
                                        :active="route().current('catalogos.*')"
                                    >
                                        Diccionarios Maestros
                                    </NavLink>
                                    <NavLink
                                        v-if="$page.props.auth.user.role === 'JEFE'"
                                        :href="route('actividades.index')"
                                        :active="route().current('actividades.*')"
                                    >
                                        Auditoría
                                    </NavLink>
                                </template>

                                <!-- Sub-Menu: Módulo Cruzamientos -->
                                <template v-if="currentContext === 'cruzamientos'">
                                    <NavLink
                                        :href="route('cruzamientos.index')"
                                        :active="route().current('cruzamientos.*')"
                                    >
                                        🧬 Gestión de Cruzamientos
                                    </NavLink>
                                </template>

                                <!-- Sub-Menu: Módulo Usuarios -->
                                <template v-if="currentContext === 'usuarios'">
                                    <NavLink
                                        :href="route('users.index')"
                                        :active="route().current('users.*')"
                                    >
                                        👥 Gestión de Usuarios
                                    </NavLink>
                                </template>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            🏠 Portal Principal
                        </ResponsiveNavLink>

                        <!-- Contextual Ensayos Responsive links -->
                        <template v-if="currentContext === 'ensayos'">
                            <ResponsiveNavLink
                                :href="route('ensayos.dashboard')"
                                :active="route().current('ensayos.dashboard')"
                            >
                                Métricas Ensayos
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('ensayos.index')"
                                :active="route().current('ensayos.index')"
                            >
                                Ensayos
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                v-if="$page.props.auth.user.role === 'JEFE'"
                                :href="route('catalogos.index')"
                                :active="route().current('catalogos.index')"
                            >
                                Diccionarios Maestros
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                v-if="$page.props.auth.user.role === 'JEFE'"
                                :href="route('actividades.index')"
                                :active="route().current('actividades.index')"
                            >
                                Auditoría
                            </ResponsiveNavLink>
                        </template>

                        <!-- Contextual Cruzamientos Responsive links -->
                        <template v-if="currentContext === 'cruzamientos'">
                            <ResponsiveNavLink
                                :href="route('cruzamientos.index')"
                                :active="route().current('cruzamientos.*')"
                            >
                                🧬 Gestión de Cruzamientos
                            </ResponsiveNavLink>
                        </template>

                        <!-- Contextual Usuarios Responsive links -->
                        <template v-if="currentContext === 'usuarios'">
                            <ResponsiveNavLink
                                :href="route('users.index')"
                                :active="route().current('users.*')"
                            >
                                👥 Gestión de Usuarios
                            </ResponsiveNavLink>
                        </template>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div
                        class="border-t border-gray-200 pb-1 pt-4"
                    >
                        <div class="px-4">
                            <div
                                class="text-base font-medium text-gray-800"
                            >
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                class="bg-white shadow"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
