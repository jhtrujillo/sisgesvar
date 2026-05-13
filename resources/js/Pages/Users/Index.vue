<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    users: Array,
    ambientes: Array
});

const updatingId = ref(null);
const showEditModal = ref(false);

const editForm = useForm({
    id: '',
    name: '',
    email: '',
    role: '',
    ambiente: [],
    password: ''
});

const openEditModal = (user) => {
    editForm.clearErrors();
    editForm.id = user.id;
    editForm.name = user.name;
    editForm.email = user.email;
    editForm.role = user.role;
    // Standardize source as dynamic array copy
    editForm.ambiente = Array.isArray(user.ambiente) ? [...user.ambiente] : (user.ambiente ? [user.ambiente] : []);
    editForm.password = '';
    showEditModal.value = true;
};

const submitEdit = () => {
    editForm.patch(route('users.update', editForm.id), {
        preserveScroll: true,
        onSuccess: () => {
            showEditModal.value = false;
            editForm.reset();
        }
    });
};

const updateUser = (user) => {
    updatingId.value = user.id;
    
    router.patch(route('users.update', user.id), {
        name: user.name,
        email: user.email,
        role: user.role,
        ambiente: user.ambiente
    }, {
        preserveScroll: true,
        onFinish: () => {
            updatingId.value = null;
        }
    });
};

const confirmDelete = (user) => {
    if (confirm(`¿Estás absolutamente seguro de ELIMINAR a "${user.name}"? Esto revocará su acceso al sistema para siempre.`)) {
        router.delete(route('users.destroy', user.id), {
            preserveScroll: true
        });
    }
};

</script>

<template>
    <Head title="Gestión de Usuarios" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">Control de Personal y Accesos</h2>
        </template>

        <div class="py-12 bg-slate-50 min-h-[calc(100vh-64px)]">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Overview Message -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 mb-8 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="bg-indigo-100 p-3 rounded-lg text-indigo-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-xl text-slate-800">Panel de Permisos</h3>
                            <p class="text-slate-500 text-sm">Define quién es Jefe (acceso total) y a qué Ambiente están restringidos los Líderes.</p>
                        </div>
                    </div>
                    <div class="bg-slate-100 px-4 py-2 rounded-lg">
                        <span class="text-2xl font-bold text-slate-800">{{ users.length }}</span>
                        <span class="text-slate-500 text-sm ml-2 uppercase tracking-wider font-medium">Cuentas</span>
                    </div>
                </div>

                <!-- Table listing -->
                <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-800 text-white uppercase tracking-wider font-semibold text-xs">
                            <tr>
                                <th class="px-6 py-4 text-left">Nombre y Correo</th>
                                <th class="px-6 py-4 text-center">Rol / Jerarquía</th>
                                <th class="px-6 py-4 text-center">Ambiente Asignado</th>
                                <th class="px-6 py-4 text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <tr v-for="user in users" :key="user.id" class="hover:bg-slate-50/80 transition-colors group">
                                <!-- Identity -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 font-bold text-lg uppercase">
                                            {{ user.name.charAt(0) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="font-bold text-slate-800">{{ user.name }}</div>
                                            <div class="text-slate-500">{{ user.email }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Role Indicator -->
                                <td class="px-6 py-4 text-center">
                                    <span 
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider"
                                        :class="user.role === 'JEFE' ? 'bg-indigo-100 text-indigo-800 border border-indigo-200' : 'bg-slate-100 text-slate-700'"
                                    >
                                        {{ user.role === 'JEFE' ? '👑 JEFE' : '💼 LÍDER' }}
                                    </span>
                                </td>

                                <!-- Ambiente Display Badges -->
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1 justify-center">
                                        <template v-if="user.role === 'JEFE'">
                                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-600 bg-indigo-50 border border-indigo-100 px-2 py-0.5 rounded">
                                                Acceso Total
                                            </span>
                                        </template>
                                        <template v-else-if="Array.isArray(user.ambiente) && user.ambiente.length > 0">
                                            <span v-for="amb in user.ambiente" :key="amb" class="text-[10px] font-bold uppercase text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded">
                                                {{ amb }}
                                            </span>
                                        </template>
                                        <template v-else-if="typeof user.ambiente === 'string' && user.ambiente.trim() !== ''">
                                            <span class="text-[10px] font-bold uppercase text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded">
                                                {{ user.ambiente }}
                                            </span>
                                        </template>
                                        <template v-else>
                                            <span class="text-xs text-slate-400 italic">
                                                -- Sin Áreas --
                                            </span>
                                        </template>
                                    </div>
                                </td>

                                <!-- Danger Zone actions -->
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-3">
                                        <div v-if="updatingId === user.id" class="animate-spin h-5 w-5 border-2 border-indigo-600 border-t-transparent rounded-full"></div>
                                        <template v-else>
                                            <button 
                                                @click="openEditModal(user)"
                                                class="text-slate-400 hover:text-emerald-600 p-2 rounded hover:bg-emerald-50 transition"
                                                title="Editar Datos Completos"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </button>
                                            
                                            <button 
                                                v-if="user.id !== $page.props.auth.user.id"
                                                @click="confirmDelete(user)"
                                                class="text-slate-400 hover:text-red-600 p-2 rounded hover:bg-red-50 transition"
                                                title="Eliminar Usuario"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                            <span v-else class="bg-slate-100 text-slate-500 text-xs px-2 py-1 rounded italic cursor-default">Tú</span>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <!-- Modal de Edición de Usuario -->
        <div v-if="showEditModal" class="fixed inset-0 z-[9999] overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" @click="showEditModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-200">
                    <form @submit.prevent="submitEdit">
                        <div class="bg-emerald-700 px-6 py-4 text-white flex justify-between items-center">
                            <h3 class="text-lg font-bold flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Editar Usuario
                            </h3>
                            <button type="button" @click="showEditModal = false" class="hover:text-emerald-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        
                        <div class="p-6 bg-white space-y-4 text-sm">
                            <!-- Name -->
                            <div>
                                <label class="block text-slate-700 font-bold mb-1">Nombre Completo</label>
                                <input v-model="editForm.name" type="text" required class="w-full border-slate-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 shadow-sm"/>
                                <div v-if="editForm.errors.name" class="text-red-600 text-xs mt-1">{{ editForm.errors.name }}</div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-slate-700 font-bold mb-1">Correo Electrónico</label>
                                <input v-model="editForm.email" type="email" required class="w-full border-slate-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 shadow-sm"/>
                                <div v-if="editForm.errors.email" class="text-red-600 text-xs mt-1">{{ editForm.errors.email }}</div>
                            </div>

                            <!-- Password Optional -->
                            <div>
                                <label class="block text-slate-700 font-bold mb-1">Nueva Contraseña (Opcional)</label>
                                <input v-model="editForm.password" type="password" placeholder="Dejar en blanco para no cambiar" class="w-full border-slate-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 shadow-sm placeholder-slate-400"/>
                                <div v-if="editForm.errors.password" class="text-red-600 text-xs mt-1">{{ editForm.errors.password }}</div>
                            </div>

                            <!-- Access Control Section -->
                            <div class="pt-2 border-t border-slate-100 space-y-4">
                                <!-- Role -->
                                <div>
                                    <label class="block text-slate-700 font-bold mb-1">Rol de Acceso</label>
                                    <select v-model="editForm.role" class="w-full border-slate-300 rounded-lg shadow-sm text-sm">
                                        <option value="LIDER">LIDER (Acceso limitado)</option>
                                        <option value="JEFE">JEFE (Acceso global administrativo)</option>
                                    </select>
                                </div>
                                
                                <!-- Multiple Ambientes Checkboxes -->
                                <div class="bg-slate-50 p-3 rounded-lg border border-slate-200">
                                    <div class="flex justify-between items-center mb-2">
                                        <label class="block text-slate-700 font-bold">Ambientes Permitidos</label>
                                        <span v-if="editForm.role !== 'JEFE'" class="text-[10px] bg-emerald-100 text-emerald-700 font-bold px-2 py-0.5 rounded-full">
                                            {{ editForm.ambiente.length }} seleccionados
                                        </span>
                                    </div>
                                    
                                    <template v-if="editForm.role === 'JEFE'">
                                        <p class="text-xs text-emerald-700 font-medium flex items-center gap-2 py-2">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 4.925-3.567 9.223-8 10.944-4.433-1.721-8-6.02-8-10.944 0-.68.056-1.35.166-2.001zm8 2.001a1 1 0 00-1 1v2a1 1 0 002 0v-2a1 1 0 00-1-1zM9 13a1 1 0 112 0 1 1 0 01-2 0z" clip-rule="evenodd"></path></svg>
                                            Los administradores ven todos los datos automáticamente.
                                        </p>
                                    </template>
                                    <template v-else>
                                        <p class="text-xs text-slate-500 mb-3">Selecciona todos los ambientes a los que el usuario puede acceder:</p>
                                        <div class="grid grid-cols-2 gap-2 max-h-36 overflow-y-auto pr-1">
                                            <label v-for="amb in ambientes" :key="amb" class="flex items-center gap-2 p-2 bg-white border border-slate-200 hover:border-emerald-300 rounded-md shadow-sm cursor-pointer transition-colors group">
                                                <input 
                                                    type="checkbox" 
                                                    v-model="editForm.ambiente" 
                                                    :value="amb"
                                                    class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer"
                                                />
                                                <span class="text-xs font-bold text-slate-700 group-hover:text-emerald-700 transition-colors uppercase truncate" :title="amb">
                                                    {{ amb }}
                                                </span>
                                            </label>
                                        </div>
                                        <div v-if="ambientes.length === 0" class="text-xs italic text-slate-400 py-2 text-center">
                                            No hay ambientes creados en el diccionario aún.
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3 border-t border-slate-200">
                            <button type="button" @click="showEditModal = false" class="px-4 py-2 text-slate-600 hover:bg-slate-200 rounded-lg font-medium transition">Cancelar</button>
                            <button type="submit" :disabled="editForm.processing" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold shadow-md disabled:opacity-50 flex items-center gap-2">
                                <span v-if="editForm.processing" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
