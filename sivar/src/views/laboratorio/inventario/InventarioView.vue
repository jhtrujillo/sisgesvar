<template>
  <div class="space-y-6 w-full max-w-[98%] mx-auto px-4 pt-6 pb-12">
    
    <div class="flex justify-between items-center bg-white border border-slate-100 rounded-xl p-4 shadow-sm">
      <div class="flex items-center space-x-3">
        <router-link
          :to="{ name: 'laboratorio.show' }"
          class="flex items-center text-slate-500 hover:text-fuchsia-700 transition-colors group"
        >
          <div class="p-2 bg-slate-50 group-hover:bg-fuchsia-50 rounded-lg mr-2 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
          </div>
          <span class="font-semibold text-sm">Volver al Laboratorio</span>
        </router-link>
      </div>
      <div>
        <h1 class="text-xl font-bold text-slate-800 flex items-center">Inventario de Laboratorio</h1>
      </div>
    </div>

    <!-- Filtros y Botón Crear -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col md:flex-row justify-between items-center gap-4">
      <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
        <select v-model="selectedArea" class="border-slate-200 rounded-lg text-sm shadow-sm focus:ring-fuchsia-500 focus:border-fuchsia-500 px-4 py-2">
          <option value="">Todas las Áreas</option>
          <option value="Equipo agua">Equipo agua</option>
          <option value="Cultivo in vitro">Cultivo in vitro</option>
          <option value="Molecular">Molecular</option>
          <option value="Genómica">Genómica</option>
          <option value="Transformación">Transformación</option>
          <option value="Edición">Edición</option>
          <option value="Otra">Otra</option>
        </select>
        <div class="relative w-full sm:w-64">
          <input v-model="searchQuery" type="text" placeholder="Buscar reactivo, código..." class="w-full border-slate-200 rounded-lg text-sm shadow-sm pl-10 focus:ring-fuchsia-500 focus:border-fuchsia-500 px-4 py-2">
          <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
        <button @click="openAlertsModal" class="bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-4 py-2 rounded-lg font-medium shadow-sm transition-colors text-sm flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
          </svg>
          Alertas
        </button>
        <button @click="openModal()" class="bg-fuchsia-600 hover:bg-fuchsia-700 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition-colors text-sm">
          + Nuevo Registro
        </button>
      </div>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
          <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200 select-none">
            <tr>
              <th class="py-3 px-4 cursor-pointer hover:bg-slate-100" @click="invToggleSort('descripcion_item')">Descripción <span v-if="invSortKey === 'descripcion_item'">{{ invSortAsc ? '↑' : '↓' }}</span></th>
              <th class="py-3 px-4 cursor-pointer hover:bg-slate-100" @click="invToggleSort('area')">Área <span v-if="invSortKey === 'area'">{{ invSortAsc ? '↑' : '↓' }}</span></th>
              <th class="py-3 px-4 text-right cursor-pointer hover:bg-slate-100" @click="invToggleSort('cantidad_en_stock')">Stock Actual <span v-if="invSortKey === 'cantidad_en_stock'">{{ invSortAsc ? '↑' : '↓' }}</span></th>
              <th class="py-3 px-4 text-right cursor-pointer hover:bg-slate-100" @click="invToggleSort('cantidad_critica')">Crítica <span v-if="invSortKey === 'cantidad_critica'">{{ invSortAsc ? '↑' : '↓' }}</span></th>
              <th class="py-3 px-4 text-center cursor-pointer hover:bg-slate-100" @click="invToggleSort('condicion')">Estado <span v-if="invSortKey === 'condicion'">{{ invSortAsc ? '↑' : '↓' }}</span></th>
              <th class="py-3 px-4 cursor-pointer hover:bg-slate-100" @click="invToggleSort('ubicacion')">Ubicación <span v-if="invSortKey === 'ubicacion'">{{ invSortAsc ? '↑' : '↓' }}</span></th>
              <th class="py-3 px-4 cursor-pointer hover:bg-slate-100" @click="invToggleSort('fecha_ultima_revision')">Revisión <span v-if="invSortKey === 'fecha_ultima_revision'">{{ invSortAsc ? '↑' : '↓' }}</span></th>
              <th class="py-3 px-4 text-right">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="isLoading" class="border-b border-slate-100">
              <td colspan="8" class="py-8 text-center text-slate-500">Cargando inventario...</td>
            </tr>
            <tr v-else-if="invPaginatedData.length === 0" class="border-b border-slate-100">
              <td colspan="8" class="py-8 text-center text-slate-500">No se encontraron resultados.</td>
            </tr>
            <tr v-for="item in invPaginatedData" :key="item.id" 
                class="border-b border-slate-100 transition-colors"
                :class="(item.condicion ?? 1) <= 0 ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-slate-50'">
              <td class="py-3 px-4 font-medium text-slate-800">{{ item.descripcion_item }}</td>
              <td class="py-3 px-4">
                <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded text-xs">{{ item.area }}</span>
              </td>
              <td class="py-3 px-4 text-right font-mono font-bold" :class="(item.condicion ?? 1) <= 0 ? 'text-red-600' : 'text-slate-700'">
                {{ item.cantidad_en_stock }} <span class="text-xs font-normal text-slate-400">{{ item.unidad }}</span>
              </td>
              <td class="py-3 px-4 font-mono text-slate-400">{{ item.cantidad_critica }}</td>
              <td class="py-3 px-4 text-center">
                <span class="px-2 py-1 rounded-full text-xs font-bold" 
                      :class="item.condicion && item.condicion <= 0 ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700'">
                  {{ item.condicion }}
                </span>
              </td>
              <td class="py-3 px-4 truncate max-w-xs">{{ item.ubicacion || '-' }}</td>
              <td class="py-3 px-4">{{ item.fecha_ultima_revision || '-' }}</td>
              <td class="py-3 px-4 text-right space-x-2">
                <button @click="openMovementModal(item, 'INGRESO')" class="text-emerald-600 hover:text-emerald-800 bg-emerald-50 hover:bg-emerald-100 p-2 rounded-lg transition-colors" title="Ingresar Stock">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                </button>
                <button @click="openMovementModal(item, 'EGRESO')" class="text-orange-600 hover:text-orange-800 bg-orange-50 hover:bg-orange-100 p-2 rounded-lg transition-colors" title="Retirar Stock">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" /></svg>
                </button>
                <button @click="openHistoryModal(item)" class="text-fuchsia-600 hover:text-fuchsia-800 bg-fuchsia-50 hover:bg-fuchsia-100 p-2 rounded-lg transition-colors" title="Historial">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </button>
                <button @click="openModal(item)" class="text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition-colors">Editar</button>
                <button @click="deleteItem(item.id)" class="text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition-colors">Borrar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Paginación Inventario -->
      <div class="px-6 py-4 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-50">
        <div class="text-sm text-slate-500">
          Mostrando {{ (invCurrentPage - 1) * invItemsPerPage + 1 }} a {{ Math.min(invCurrentPage * invItemsPerPage, invTotalItems) }} de {{ invTotalItems }} registros
        </div>
        <div class="flex items-center space-x-4">
          <select v-model="invItemsPerPage" class="text-sm border-slate-200 rounded-md py-1 px-2 focus:ring-fuchsia-500">
            <option :value="10">10 por página</option>
            <option :value="20">20 por página</option>
            <option :value="50">50 por página</option>
          </select>
          <div class="flex space-x-1">
            <button @click="invPrevPage" :disabled="invCurrentPage === 1" class="px-3 py-1 rounded border border-slate-200 bg-white hover:bg-slate-50 disabled:opacity-50 transition-colors">Anterior</button>
            <span class="px-3 py-1 text-sm font-medium text-slate-700 bg-slate-200 rounded">{{ invCurrentPage }} / {{ invTotalPages }}</span>
            <button @click="invNextPage" :disabled="invCurrentPage === invTotalPages" class="px-3 py-1 rounded border border-slate-200 bg-white hover:bg-slate-50 disabled:opacity-50 transition-colors">Siguiente</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Formulario -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900 bg-opacity-50 backdrop-blur-sm p-4 overflow-y-auto pt-24 pb-24">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl overflow-hidden my-auto">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50 sticky top-0 z-10">
          <h2 class="text-lg font-bold text-slate-800">{{ isEditing ? 'Editar' : 'Nuevo' }} Registro</h2>
          <button @click="closeModal" class="text-slate-400 hover:text-slate-600">Cerrar</button>
        </div>
        
        <form @submit.prevent="saveItem" class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            <div class="md:col-span-1">
              <label class="block text-sm font-medium text-slate-700 mb-1">Área *</label>
              <select v-model="form.area" required class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-fuchsia-500 focus:border-fuchsia-500">
                <option value="Equipo agua">Equipo agua</option>
                <option value="Cultivo in vitro">Cultivo in vitro</option>
                <option value="Molecular">Molecular</option>
                <option value="Genómica">Genómica</option>
                <option value="Transformación">Transformación</option>
                <option value="Edición">Edición</option>
                <option value="Otra">Otra</option>
              </select>
            </div>
            
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-slate-700 mb-1">Descripción del Item *</label>
              <input v-model="form.descripcion_item" type="text" required class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-fuchsia-500">
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Consumible / Tipo</label>
              <input v-model="form.consumible" type="text" class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-fuchsia-500">
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Actividad</label>
              <input v-model="form.actividad" type="text" class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-fuchsia-500">
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Código CG1</label>
              <input v-model="form.codigo_cg1" type="text" class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-fuchsia-500">
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Marca</label>
              <input v-model="form.marca" type="text" class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-fuchsia-500">
            </div>

            <div class="md:col-span-2 flex space-x-4">
              <div class="w-1/3">
                <label class="block text-sm font-medium text-slate-700 mb-1">Stock *</label>
                <input v-model="form.cantidad_en_stock" type="number" step="0.01" required class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-fuchsia-500">
              </div>
              <div class="w-1/3">
                <label class="block text-sm font-medium text-slate-700 mb-1">C. Crítica *</label>
                <input v-model="form.cantidad_critica" type="number" step="0.01" required class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-fuchsia-500">
              </div>
              <div class="w-1/3">
                <label class="block text-sm font-medium text-slate-700 mb-1">Unidad</label>
                <input v-model="form.unidad" type="text" class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-fuchsia-500">
              </div>
            </div>

            <div class="md:col-span-3">
              <label class="block text-sm font-medium text-slate-700 mb-1">Ubicación</label>
              <input v-model="form.ubicacion" type="text" class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-fuchsia-500">
            </div>

            <div class="md:col-span-1">
              <label class="block text-sm font-medium text-slate-700 mb-1">Solicitante</label>
              <input v-model="form.solicitante" type="text" class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-fuchsia-500">
            </div>

            <div class="md:col-span-1">
              <label class="block text-sm font-medium text-slate-700 mb-1">Fecha Solicitud</label>
              <input v-model="form.fecha_solicitud" type="date" class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-fuchsia-500">
            </div>

            <div class="md:col-span-1">
              <label class="block text-sm font-medium text-slate-700 mb-1">Última Revisión</label>
              <input v-model="form.fecha_ultima_revision" type="date" class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-fuchsia-500">
            </div>

            <div class="md:col-span-3">
              <label class="block text-sm font-medium text-slate-700 mb-1">Observaciones</label>
              <textarea v-model="form.observaciones" rows="2" class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-fuchsia-500"></textarea>
            </div>
          </div>

          <div class="pt-4 flex justify-end space-x-3 border-t border-slate-100 mt-6 sticky bottom-0 bg-white">
            <button type="button" @click="closeModal" class="px-4 py-2 text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg font-medium transition-colors">Cancelar</button>
            <button type="submit" :disabled="isSaving" class="px-6 py-2 bg-fuchsia-600 text-white rounded-lg font-medium hover:bg-fuchsia-700 shadow-sm transition-colors disabled:opacity-50">
              {{ isSaving ? 'Guardando...' : 'Guardar' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Registro Movimiento -->
    <div v-if="showMovementModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900 bg-opacity-50 backdrop-blur-sm p-4">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
          <h2 class="text-lg font-bold text-slate-800">
            {{ movementForm.tipo_movimiento === 'INGRESO' ? 'Ingresar Stock' : 'Retirar Stock' }}
          </h2>
          <button @click="closeMovementModal" class="text-slate-400 hover:text-slate-600">Cerrar</button>
        </div>
        
        <form @submit.prevent="saveMovement" class="p-6 space-y-4">
          <div>
            <p class="text-sm text-slate-600 mb-4">
              Item: <span class="font-bold text-slate-800">{{ selectedItem?.descripcion_item }}</span><br>
              Stock actual: <span class="font-bold text-blue-600">{{ selectedItem?.cantidad_en_stock }} {{ selectedItem?.unidad }}</span>
            </p>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Cantidad a {{ movementForm.tipo_movimiento === 'INGRESO' ? 'ingresar' : 'retirar' }} *</label>
            <input v-model="movementForm.cantidad" type="number" step="0.01" min="0.01" required class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-fuchsia-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Observaciones</label>
            <textarea v-model="movementForm.observaciones" rows="2" class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-fuchsia-500" placeholder="Opcional..."></textarea>
          </div>

          <div class="pt-4 flex justify-end space-x-3 border-t border-slate-100">
            <button type="button" @click="closeMovementModal" class="px-4 py-2 text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg font-medium transition-colors">Cancelar</button>
            <button type="submit" :disabled="isSavingMovement" 
              class="px-6 py-2 text-white rounded-lg font-medium shadow-sm transition-colors disabled:opacity-50"
              :class="movementForm.tipo_movimiento === 'INGRESO' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-orange-600 hover:bg-orange-700'"
            >
              {{ isSavingMovement ? 'Guardando...' : (movementForm.tipo_movimiento === 'INGRESO' ? 'Ingresar' : 'Retirar') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Historial Movimientos -->
    <div v-if="showHistoryModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900 bg-opacity-50 backdrop-blur-sm p-4 overflow-y-auto">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl overflow-hidden my-auto">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
          <h2 class="text-lg font-bold text-slate-800">Historial de Movimientos</h2>
          <div class="space-x-3">
            <button @click="clearHistory" class="text-red-500 hover:text-red-700 font-medium text-sm">Borrar Historial</button>
            <button @click="closeHistoryModal" class="text-slate-400 hover:text-slate-600">Cerrar</button>
          </div>
        </div>
        
        <div class="p-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-slate-800">{{ selectedItem?.descripcion_item }}</h3>
            <div class="relative w-64">
              <input v-model="histSearchQuery" type="text" placeholder="Buscar historial..." class="w-full border-slate-200 rounded-lg text-sm shadow-sm px-3 py-1">
            </div>
          </div>
          
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
              <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200 select-none">
                <tr>
                  <th class="py-3 px-4 cursor-pointer hover:bg-slate-100" @click="histToggleSort('created_at')">Fecha y Hora <span v-if="histSortKey === 'created_at'">{{ histSortAsc ? '↑' : '↓' }}</span></th>
                  <th class="py-3 px-4 cursor-pointer hover:bg-slate-100" @click="histToggleSort('user_name')">Usuario <span v-if="histSortKey === 'user_name'">{{ histSortAsc ? '↑' : '↓' }}</span></th>
                  <th class="py-3 px-4 cursor-pointer hover:bg-slate-100" @click="histToggleSort('tipo_movimiento')">Tipo <span v-if="histSortKey === 'tipo_movimiento'">{{ histSortAsc ? '↑' : '↓' }}</span></th>
                  <th class="py-3 px-4 text-right cursor-pointer hover:bg-slate-100" @click="histToggleSort('cantidad')">Cantidad <span v-if="histSortKey === 'cantidad'">{{ histSortAsc ? '↑' : '↓' }}</span></th>
                  <th class="py-3 px-4 text-right cursor-pointer hover:bg-slate-100" @click="histToggleSort('stock_nuevo')">Stock Final <span v-if="histSortKey === 'stock_nuevo'">{{ histSortAsc ? '↑' : '↓' }}</span></th>
                  <th class="py-3 px-4">Observaciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="isLoadingHistory" class="border-b border-slate-100">
                  <td colspan="6" class="py-8 text-center text-slate-500">Cargando historial...</td>
                </tr>
                <tr v-else-if="histPaginatedData.length === 0" class="border-b border-slate-100">
                  <td colspan="6" class="py-8 text-center text-slate-500">No hay movimientos registrados.</td>
                </tr>
                <tr v-for="mov in histPaginatedData" :key="mov.id" class="border-b border-slate-100">
                  <td class="py-3 px-4">{{ new Date(mov.created_at).toLocaleString() }}</td>
                  <td class="py-3 px-4">{{ mov.user_name || 'N/A' }}</td>
                  <td class="py-3 px-4">
                    <span class="px-2 py-1 rounded-full text-xs font-bold"
                          :class="mov.tipo_movimiento === 'INGRESO' ? 'bg-emerald-100 text-emerald-700' : 'bg-orange-100 text-orange-700'">
                      {{ mov.tipo_movimiento }}
                    </span>
                  </td>
                  <td class="py-3 px-4 text-right font-mono font-bold"
                      :class="mov.tipo_movimiento === 'INGRESO' ? 'text-emerald-600' : 'text-orange-600'">
                    {{ mov.tipo_movimiento === 'INGRESO' ? '+' : '-' }}{{ mov.cantidad }}
                  </td>
                  <td class="py-3 px-4 text-right font-mono">{{ mov.stock_nuevo }}</td>
                  <td class="py-3 px-4 truncate max-w-xs" :title="mov.observaciones">{{ mov.observaciones || '-' }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Paginación Historial -->
          <div class="mt-4 flex justify-between items-center text-sm">
            <div class="text-slate-500">
              Total: {{ histTotalItems }}
            </div>
            <div class="flex items-center space-x-2">
              <select v-model="histItemsPerPage" class="border-slate-200 rounded py-1 px-2 text-xs">
                <option :value="5">5</option>
                <option :value="10">10</option>
                <option :value="20">20</option>
              </select>
              <button @click="histPrevPage" :disabled="histCurrentPage === 1" class="px-2 py-1 rounded bg-slate-100 disabled:opacity-50">‹</button>
              <span class="px-2">{{ histCurrentPage }} / {{ histTotalPages }}</span>
              <button @click="histNextPage" :disabled="histCurrentPage === histTotalPages" class="px-2 py-1 rounded bg-slate-100 disabled:opacity-50">›</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Configuración Alertas -->
    <div v-if="showAlertsModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900 bg-opacity-50 backdrop-blur-sm p-4 overflow-y-auto">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden my-auto">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
          <h2 class="text-lg font-bold text-slate-800">Destinatarios de Alertas Críticas</h2>
          <button @click="closeAlertsModal" class="text-slate-400 hover:text-slate-600">Cerrar</button>
        </div>
        
        <div class="p-6">
          <p class="text-sm text-slate-600 mb-6">Agrega los correos electrónicos que recibirán una notificación cuando el stock de un ítem llegue a su nivel crítico.</p>
          
          <form @submit.prevent="saveAlertEmail" class="flex flex-col sm:flex-row gap-3 mb-6">
            <input v-model="alertForm.nombre" type="text" placeholder="Nombre (Ej. Juan Perez)" required class="flex-1 border-slate-300 rounded-lg shadow-sm focus:ring-fuchsia-500">
            <input v-model="alertForm.email" type="email" placeholder="Correo electrónico" required class="flex-1 border-slate-300 rounded-lg shadow-sm focus:ring-fuchsia-500">
            <button type="submit" :disabled="isSavingAlert" class="px-4 py-2 bg-fuchsia-600 text-white rounded-lg font-medium hover:bg-fuchsia-700 shadow-sm transition-colors disabled:opacity-50">
              {{ isSavingAlert ? 'Agregando...' : 'Agregar' }}
            </button>
          </form>
          
          <div class="flex justify-between items-end mb-2">
            <h3 class="font-bold text-slate-700 text-sm">Correos Registrados</h3>
            <div class="relative w-48">
              <input v-model="alertSearchQuery" type="text" placeholder="Buscar correo..." class="w-full border-slate-200 rounded-lg text-xs shadow-sm px-2 py-1">
            </div>
          </div>
          <div class="overflow-x-auto border border-slate-200 rounded-lg">
            <table class="w-full text-left text-sm text-slate-600">
              <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200 select-none">
                <tr>
                  <th class="py-2 px-3 cursor-pointer hover:bg-slate-100" @click="alertToggleSort('nombre')">Nombre <span v-if="alertSortKey === 'nombre'">{{ alertSortAsc ? '↑' : '↓' }}</span></th>
                  <th class="py-2 px-3 cursor-pointer hover:bg-slate-100" @click="alertToggleSort('email')">Correo <span v-if="alertSortKey === 'email'">{{ alertSortAsc ? '↑' : '↓' }}</span></th>
                  <th class="py-2 px-3 text-center cursor-pointer hover:bg-slate-100" @click="alertToggleSort('activo')">Activo <span v-if="alertSortKey === 'activo'">{{ alertSortAsc ? '↑' : '↓' }}</span></th>
                  <th class="py-2 px-3 text-right">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="isLoadingAlerts" class="border-b border-slate-100">
                  <td colspan="4" class="py-4 text-center text-slate-500">Cargando correos...</td>
                </tr>
                <tr v-else-if="alertPaginatedData.length === 0" class="border-b border-slate-100">
                  <td colspan="4" class="py-4 text-center text-slate-500">No hay correos.</td>
                </tr>
                <tr v-for="email in alertPaginatedData" :key="email.id" class="border-b border-slate-100">
                  <td class="py-2 px-3 font-medium">{{ email.nombre }}</td>
                  <td class="py-2 px-3">{{ email.email }}</td>
                  <td class="py-2 px-3 text-center">
                    <input type="checkbox" :checked="email.activo" @change="toggleAlertEmailStatus(email)" class="rounded text-fuchsia-600 focus:ring-fuchsia-500 cursor-pointer">
                  </td>
                  <td class="py-2 px-3 text-right">
                    <button @click="deleteAlertEmail(email.id)" class="text-red-500 hover:text-red-700 font-medium text-xs">Eliminar</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Paginación Alertas -->
          <div class="mt-3 flex justify-between items-center text-xs">
            <div class="text-slate-500">
              Total: {{ alertTotalItems }}
            </div>
            <div class="flex items-center space-x-2">
              <button @click="alertPrevPage" :disabled="alertCurrentPage === 1" class="px-2 py-1 rounded border disabled:opacity-50">‹</button>
              <span>{{ alertCurrentPage }} / {{ alertTotalPages }}</span>
              <button @click="alertNextPage" :disabled="alertCurrentPage === alertTotalPages" class="px-2 py-1 rounded border disabled:opacity-50">›</button>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import axios from 'axios';

const API_URL = import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000/api';

interface InventoryItem {
  id?: number;
  area: string;
  consumible: string;
  actividad: string;
  codigo_cg1: string;
  descripcion_item: string;
  marca: string;
  unidad: string;
  cantidad_en_stock: number;
  cantidad_critica: number;
  condicion?: number;
  ubicacion: string;
  solicitante: string;
  fecha_solicitud: string | null;
  fecha_ultima_revision: string | null;
  observaciones: string;
}

const items = ref<InventoryItem[]>([]);
const isLoading = ref(false);
const isSaving = ref(false);
const showModal = ref(false);
const isEditing = ref(false);
const searchQuery = ref('');
const selectedArea = ref('');

const showMovementModal = ref(false);
const showHistoryModal = ref(false);
const showAlertsModal = ref(false);
const isSavingMovement = ref(false);
const isLoadingHistory = ref(false);
const isLoadingAlerts = ref(false);
const isSavingAlert = ref(false);
const selectedItem = ref<InventoryItem | null>(null);
const itemHistory = ref<any[]>([]);
const alertEmails = ref<any[]>([]);
const movementForm = ref({
  tipo_movimiento: 'INGRESO',
  cantidad: 0,
  observaciones: ''
});
const alertForm = ref({
  nombre: '',
  email: ''
});

// Configure Axios with Auth Token
import { useUserStore } from '@/stores/user';
const setAxiosHeaders = () => {
  const userStore = useUserStore();
  if (userStore.token) {
    axios.defaults.headers.common['Authorization'] = 'Bearer ' + userStore.token;
  }
};

const form = ref<InventoryItem>({
  area: 'Molecular',
  consumible: '',
  actividad: '',
  codigo_cg1: '',
  descripcion_item: '',
  marca: '',
  unidad: 'Unidad',
  cantidad_en_stock: 0,
  cantidad_critica: 0,
  ubicacion: '',
  solicitante: '',
  fecha_solicitud: null,
  fecha_ultima_revision: null,
  observaciones: ''
});

import { useDataTable } from '@/composables/useDataTable';

// Búsqueda y Filtros pre-DataTable para Inventario
const baseFilteredItems = computed(() => {
  let result = items.value;
  if (selectedArea.value) {
    result = result.filter(item => item.area === selectedArea.value);
  }
  return result;
});

// DataTable: Inventario
const {
  searchQuery: invSearchQuery,
  sortKey: invSortKey,
  sortAsc: invSortAsc,
  currentPage: invCurrentPage,
  itemsPerPage: invItemsPerPage,
  paginatedData: invPaginatedData,
  totalPages: invTotalPages,
  totalItems: invTotalItems,
  toggleSort: invToggleSort,
  nextPage: invNextPage,
  prevPage: invPrevPage,
  setPage: invSetPage
} = useDataTable(baseFilteredItems, ['descripcion_item', 'consumible', 'codigo_cg1', 'marca', 'ubicacion', 'area']);

// Sincronizar el input de búsqueda existente con el del composable
watch(searchQuery, (newVal) => {
  invSearchQuery.value = newVal;
});

// DataTable: Historial
const {
  searchQuery: histSearchQuery,
  sortKey: histSortKey,
  sortAsc: histSortAsc,
  currentPage: histCurrentPage,
  itemsPerPage: histItemsPerPage,
  paginatedData: histPaginatedData,
  totalPages: histTotalPages,
  totalItems: histTotalItems,
  toggleSort: histToggleSort,
  nextPage: histNextPage,
  prevPage: histPrevPage,
  setPage: histSetPage
} = useDataTable(itemHistory, ['user_name', 'tipo_movimiento', 'observaciones']);

// DataTable: Alertas
const {
  searchQuery: alertSearchQuery,
  sortKey: alertSortKey,
  sortAsc: alertSortAsc,
  currentPage: alertCurrentPage,
  itemsPerPage: alertItemsPerPage,
  paginatedData: alertPaginatedData,
  totalPages: alertTotalPages,
  totalItems: alertTotalItems,
  toggleSort: alertToggleSort,
  nextPage: alertNextPage,
  prevPage: alertPrevPage,
  setPage: alertSetPage
} = useDataTable(alertEmails, ['nombre', 'email']);

const fetchItems = async () => {
  isLoading.value = true;
  try {
    setAxiosHeaders();
    const response = await axios.get(`${API_URL}/lab/inventory`);
    items.value = response.data;
  } catch (error) {
    console.error("Error loading inventory", error);
  } finally {
    isLoading.value = false;
  }
};

const openModal = (item?: InventoryItem) => {
  if (item) {
    isEditing.value = true;
    form.value = { ...item };
  } else {
    isEditing.value = false;
    form.value = {
      area: selectedArea.value || 'Molecular',
      consumible: '',
      actividad: '',
      codigo_cg1: '',
      descripcion_item: '',
      marca: '',
      unidad: 'Unidad',
      cantidad_en_stock: 0,
      cantidad_critica: 0,
      ubicacion: '',
      solicitante: '',
      fecha_solicitud: null,
      fecha_ultima_revision: null,
      observaciones: ''
    };
  }
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const saveItem = async () => {
  isSaving.value = true;
  try {
    setAxiosHeaders();
    if (isEditing.value && form.value.id) {
      await axios.put(`${API_URL}/lab/inventory/${form.value.id}`, form.value);
    } else {
      await axios.post(`${API_URL}/lab/inventory`, form.value);
    }
    await fetchItems();
    closeModal();
  } catch (error) {
    console.error("Error saving item", error);
    alert("Hubo un error al guardar el registro.");
  } finally {
    isSaving.value = false;
  }
};

const deleteItem = async (id: number | undefined) => {
  if (!id) return;
  if (confirm('¿Eliminar registro?')) {
    try {
      setAxiosHeaders();
      await axios.delete(`${API_URL}/lab/inventory/${id}`);
      await fetchItems();
    } catch (error) {
      console.error("Error deleting item", error);
    }
  }
};

const openMovementModal = (item: InventoryItem, tipo: string) => {
  selectedItem.value = item;
  movementForm.value = {
    tipo_movimiento: tipo,
    cantidad: 0,
    observaciones: ''
  };
  showMovementModal.value = true;
};

const closeMovementModal = () => {
  showMovementModal.value = false;
  selectedItem.value = null;
};

const saveMovement = async () => {
  if (!selectedItem.value?.id) return;
  
  isSavingMovement.value = true;
  try {
    setAxiosHeaders();
    await axios.post(`${API_URL}/lab/inventory/${selectedItem.value.id}/movements`, movementForm.value);
    await fetchItems();
    closeMovementModal();
  } catch (error: any) {
    console.error("Error saving movement", error);
    if (error.response?.data?.message) {
      alert(error.response.data.message);
    } else {
      alert("Hubo un error al registrar el movimiento.");
    }
  } finally {
    isSavingMovement.value = false;
  }
};

const openHistoryModal = async (item: InventoryItem) => {
  selectedItem.value = item;
  showHistoryModal.value = true;
  isLoadingHistory.value = true;
  itemHistory.value = [];
  
  try {
    setAxiosHeaders();
    const response = await axios.get(`${API_URL}/lab/inventory/${item.id}/movements`);
    itemHistory.value = response.data;
  } catch (error) {
    console.error("Error loading history", error);
  } finally {
    isLoadingHistory.value = false;
  }
};

const closeHistoryModal = () => {
  showHistoryModal.value = false;
  selectedItem.value = null;
};

const clearHistory = async () => {
  if (!selectedItem.value?.id) return;
  if (confirm('¿Estás seguro de que deseas eliminar permanentemente TODO el historial de este ítem? Esta acción no se puede deshacer.')) {
    try {
      setAxiosHeaders();
      await axios.delete(`${API_URL}/lab/inventory/${selectedItem.value.id}/movements`);
      itemHistory.value = [];
      alert("Historial eliminado correctamente.");
    } catch (error) {
      console.error("Error clearing history", error);
      alert("Hubo un error al eliminar el historial.");
    }
  }
};

const openAlertsModal = async () => {
  showAlertsModal.value = true;
  await fetchAlertEmails();
};

const closeAlertsModal = () => {
  showAlertsModal.value = false;
};

const fetchAlertEmails = async () => {
  isLoadingAlerts.value = true;
  try {
    setAxiosHeaders();
    const response = await axios.get(`${API_URL}/lab/inventory-alerts`);
    alertEmails.value = response.data;
  } catch (error) {
    console.error("Error loading alert emails", error);
  } finally {
    isLoadingAlerts.value = false;
  }
};

const saveAlertEmail = async () => {
  isSavingAlert.value = true;
  try {
    setAxiosHeaders();
    await axios.post(`${API_URL}/lab/inventory-alerts`, {
      nombre: alertForm.value.nombre,
      email: alertForm.value.email,
      activo: true
    });
    alertForm.value = { nombre: '', email: '' };
    await fetchAlertEmails();
  } catch (error: any) {
    console.error("Error saving alert email", error);
    if (error.response?.data?.message) {
      alert(error.response.data.message);
    } else {
      alert("Hubo un error al guardar el correo. Asegúrate de que no esté repetido.");
    }
  } finally {
    isSavingAlert.value = false;
  }
};

const toggleAlertEmailStatus = async (emailItem: any) => {
  try {
    setAxiosHeaders();
    await axios.put(`${API_URL}/lab/inventory-alerts/${emailItem.id}`, {
      nombre: emailItem.nombre,
      email: emailItem.email,
      activo: !emailItem.activo
    });
    emailItem.activo = !emailItem.activo;
  } catch (error) {
    console.error("Error updating email status", error);
    alert("Error al actualizar el estado.");
  }
};

const deleteAlertEmail = async (id: number) => {
  if (confirm("¿Estás seguro de eliminar este correo de la lista de alertas?")) {
    try {
      setAxiosHeaders();
      await axios.delete(`${API_URL}/lab/inventory-alerts/${id}`);
      await fetchAlertEmails();
    } catch (error) {
      console.error("Error deleting alert email", error);
    }
  }
};

onMounted(() => {
  fetchItems();
});
</script>
