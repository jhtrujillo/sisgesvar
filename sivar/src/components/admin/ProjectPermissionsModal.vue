<template>
  <Transition name="fade">
    <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
      <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl overflow-hidden border border-slate-100 transform transition-all flex flex-col max-h-[90vh]">
        <!-- Header -->
        <div class="p-6 bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 text-white flex items-center justify-between">
          <div class="space-y-1">
            <div class="flex items-center space-x-2">
              <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                Código: {{ proyecto?.cd_cntble || 'S/N' }}
              </span>
              <span class="text-xs text-slate-300 font-medium">ID: {{ proyecto?.id_prycto }}</span>
            </div>
            <h2 class="text-xl font-bold text-white tracking-tight line-clamp-1">
              {{ proyecto?.nm_prycto }}
            </h2>
            <p class="text-xs text-slate-300">
              Programa: {{ proyecto?.nombre_programa }} | Área: {{ proyecto?.nombre_area_trbjo }}
            </p>
          </div>
          <button
            @click="closeModal"
            class="p-2 text-slate-400 hover:text-white rounded-full hover:bg-white/10 transition-colors"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-6 overflow-y-auto flex-1">
          <!-- Formulario para agregar usuario -->
          <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 space-y-3">
            <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Asignar Nuevo Permiso</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
              <!-- Autocompletado de Usuario -->
              <div class="sm:col-span-7 relative">
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Buscar Usuario</label>
                <input
                  v-model="userQuery"
                  @input="searchUsers"
                  type="text"
                  placeholder="Nombre, correo o login de Cenicaña..."
                  class="w-full px-3.5 py-2 text-xs font-semibold rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white shadow-sm"
                />
                
                <!-- Dropdown de Sugerencias -->
                <div
                  v-if="userSearchResults.length > 0 && isSearching"
                  class="absolute z-20 left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-xl max-h-48 overflow-y-auto divide-y divide-slate-100"
                >
                  <div
                    v-for="u in userSearchResults"
                    :key="u.usuario_id"
                    @click="selectUser(u)"
                    class="p-2.5 hover:bg-emerald-50 cursor-pointer transition-colors"
                  >
                    <div class="font-bold text-xs text-slate-800">{{ u.nombre }}</div>
                    <div class="text-[10px] text-slate-500">{{ u.email || u.login }} {{ u.cargo ? `• ${u.cargo}` : '' }}</div>
                  </div>
                </div>
              </div>

              <!-- Selector de Rol -->
              <div class="sm:col-span-3">
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Rol / Permiso</label>
                <select
                  v-model="selectedRole"
                  class="w-full px-3 py-2 text-xs font-bold rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white"
                >
                  <option value="ADMIN">Administrador 👑</option>
                  <option value="EDITOR">Editor ✏️</option>
                  <option value="VIEWER">Lector 👁️</option>
                </select>
              </div>

              <!-- Botón Asignar -->
              <div class="sm:col-span-2">
                <button
                  @click="addPermission"
                  :disabled="!selectedUser || isSaving"
                  class="w-full py-2 text-xs font-bold text-white bg-cenicana hover:bg-cenicana-800 rounded-xl shadow-sm transition-all disabled:opacity-50 flex items-center justify-center space-x-1"
                >
                  <svg v-if="isSaving" class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                  </svg>
                  <span>Asignar</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Tabla de Usuarios Asignados -->
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-500">
                Usuarios con Permisos ({{ assignedUsers.length }})
              </h3>
              <span class="text-[11px] text-slate-400 font-semibold">
                👑 Admin • ✏️ Editor • 👁️ Lector
              </span>
            </div>

            <div v-if="isLoadingUsers" class="py-10 text-center text-slate-400 font-semibold text-xs">
              Cargando usuarios asignados...
            </div>

            <div v-else-if="assignedUsers.length === 0" class="py-10 text-center text-slate-400 space-y-1 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
              <p class="text-xs font-bold text-slate-600">Aún no hay usuarios asignados explícitamente a este proyecto.</p>
              <p class="text-[11px] text-slate-400">Usa el buscador superior para agregar administradores o editores.</p>
            </div>

            <div v-else class="border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
              <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-100/80 text-slate-600 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-200">
                  <tr>
                    <th class="px-4 py-3">Usuario</th>
                    <th class="px-4 py-3">Cargo</th>
                    <th class="px-4 py-3 text-center">Rol Asignado</th>
                    <th class="px-4 py-3 text-center">Acción</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                  <tr v-for="u in assignedUsers" :key="u.usuario_id" class="hover:bg-slate-50/70 transition-colors">
                    <td class="px-4 py-3">
                      <div class="font-bold text-slate-800">{{ u.nombre }}</div>
                      <div class="text-[10px] text-slate-400">{{ u.email || u.login }}</div>
                    </td>
                    <td class="px-4 py-3 text-slate-500 text-[11px]">
                      {{ u.cargo || 'Investigador / Usuario' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                      <select
                        :value="u.rol"
                        @change="changeUserRole(u.usuario_id, ($event.target as HTMLSelectElement).value)"
                        class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold border bg-white cursor-pointer focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        :class="{
                          'bg-amber-50 text-amber-800 border-amber-200': u.rol === 'ADMIN',
                          'bg-blue-50 text-blue-800 border-blue-200': u.rol === 'EDITOR',
                          'bg-slate-100 text-slate-700 border-slate-200': u.rol === 'VIEWER'
                        }"
                      >
                        <option value="ADMIN">👑 Administrador</option>
                        <option value="EDITOR">✏️ Editor</option>
                        <option value="VIEWER">👁️ Lector</option>
                      </select>
                    </td>
                    <td class="px-4 py-3 text-center">
                      <button
                        @click="revokeUserAccess(u.usuario_id, u.nombre)"
                        class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors cursor-pointer"
                        title="Revocar Acceso"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="p-4 bg-slate-50 border-t border-slate-200/80 flex items-center justify-end">
          <button
            @click="closeModal"
            class="px-5 py-2 text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 rounded-xl border border-slate-200 transition-all cursor-pointer shadow-sm"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup lang="ts">
import { ref, watch } from "vue";
import projectManagementService from "@/services/projectManagement.services";
import { useToast } from "vue-toastification";

const props = defineProps<{
  isOpen: boolean;
  proyecto: any;
}>();

const emit = defineEmits(["close", "updated"]);

const toast = useToast();

const assignedUsers = ref<any[]>([]);
const isLoadingUsers = ref(false);

const userQuery = ref("");
const userSearchResults = ref<any[]>([]);
const isSearching = ref(false);
const selectedUser = ref<any>(null);
const selectedRole = ref("ADMIN");
const isSaving = ref(false);

const closeModal = () => {
  emit("close");
};

const loadAssignedUsers = async () => {
  if (!props.proyecto?.id_prycto) return;
  isLoadingUsers.value = true;
  try {
    const res = await projectManagementService.getUsuariosProyecto(props.proyecto.id_prycto);
    assignedUsers.value = res.data || [];
  } catch (err) {
    console.error("Error al cargar usuarios asignados:", err);
    toast.error("Error al consultar usuarios asignados al proyecto.");
  } finally {
    isLoadingUsers.value = false;
  }
};

watch(
  () => props.isOpen,
  (newVal) => {
    if (newVal && props.proyecto) {
      userQuery.value = "";
      selectedUser.value = null;
      userSearchResults.value = [];
      loadAssignedUsers();
    }
  }
);

let searchTimeout: any = null;
const searchUsers = () => {
  clearTimeout(searchTimeout);
  if (!userQuery.value.trim()) {
    userSearchResults.value = [];
    isSearching.value = false;
    return;
  }
  searchTimeout = setTimeout(async () => {
    try {
      const res = await projectManagementService.getUsuariosDisponibles(userQuery.value);
      userSearchResults.value = res.data || [];
      isSearching.value = true;
    } catch (err) {
      console.error("Error al buscar usuarios:", err);
    }
  }, 300);
};

const selectUser = (u: any) => {
  selectedUser.value = u;
  userQuery.value = `${u.nombre} (${u.email || u.login})`;
  userSearchResults.value = [];
  isSearching.value = false;
};

const addPermission = async () => {
  if (!selectedUser.value || !props.proyecto?.id_prycto) return;
  isSaving.value = true;
  try {
    await projectManagementService.assignUsuarioProyecto(
      props.proyecto.id_prycto,
      selectedUser.value.usuario_id,
      selectedRole.value
    );
    toast.success(`Permiso asignado a ${selectedUser.value.nombre} con éxito.`);
    userQuery.value = "";
    selectedUser.value = null;
    await loadAssignedUsers();
    emit("updated");
  } catch (err: any) {
    console.error("Error al asignar permiso:", err);
    toast.error(err.response?.data?.error || "Error al asignar permiso al usuario.");
  } finally {
    isSaving.value = false;
  }
};

const changeUserRole = async (usuarioId: number, newRole: string) => {
  if (!props.proyecto?.id_prycto) return;
  try {
    await projectManagementService.assignUsuarioProyecto(
      props.proyecto.id_prycto,
      usuarioId,
      newRole
    );
    toast.success("Rol actualizado correctamente.");
    await loadAssignedUsers();
    emit("updated");
  } catch (err: any) {
    console.error("Error al actualizar rol:", err);
    toast.error(err.response?.data?.error || "Error al actualizar rol del usuario.");
  }
};

const revokeUserAccess = async (usuarioId: number, nombre: string) => {
  if (!props.proyecto?.id_prycto) return;
  if (!confirm(`¿Estás seguro de revocar el permiso al usuario ${nombre}?`)) return;

  try {
    await projectManagementService.removeUsuarioProyecto(props.proyecto.id_prycto, usuarioId);
    toast.info(`Acceso revocado para ${nombre}.`);
    await loadAssignedUsers();
    emit("updated");
  } catch (err: any) {
    console.error("Error al revocar acceso:", err);
    toast.error(err.response?.data?.error || "Error al revocar el acceso del usuario.");
  }
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
