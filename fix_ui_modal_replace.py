import re

with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "r") as f:
    content = f.read()

start_marker = '<!-- Proyecto -->'
end_marker = '<!-- UBICACIÓN FÍSICA EN CAMPO -->'
match = re.search(f"({re.escape(start_marker)}.*?)(?=\s+{re.escape(end_marker)}|(?i)<!-- Ubicación)", content, re.DOTALL | re.IGNORECASE)

new_ui = """<!-- Proyectos y Caracteres Vinculados -->
              <div class="relative md:col-span-2">
                <div class="flex items-center justify-between mb-3">
                  <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Proyectos y Ambientes Asignados</label>
                  <button type="button" @click="showProyectoModal = true" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Vincular Proyecto
                  </button>
                </div>

                <div class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-sm">
                  <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                      <tr>
                        <th scope="col" class="px-4 py-3 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">Proyecto</th>
                        <th scope="col" class="px-4 py-3 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">Caracteres (Ambientes)</th>
                        <th scope="col" class="px-4 py-3 text-right text-[10px] font-bold text-slate-500 uppercase tracking-wider w-16">Acción</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                      <tr v-if="!form.proyectos || form.proyectos.length === 0">
                        <td colspan="3" class="px-4 py-6 text-center text-xs text-slate-400 font-medium">
                          No hay proyectos vinculados a este vivero.
                        </td>
                      </tr>
                      <tr v-for="pry_id in form.proyectos" :key="pry_id" class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-4 py-3 text-xs font-semibold text-slate-700 align-top">
                          {{ getProyectoName(pry_id) }}
                        </td>
                        <td class="px-4 py-3 align-top">
                          <div class="flex flex-wrap gap-1.5">
                            <span v-for="c_id in getCaracteresByProyecto(pry_id)" :key="c_id" class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                              {{ getCaracterNameLocal(c_id) }}
                            </span>
                            <span v-if="getCaracteresByProyecto(pry_id).length === 0" class="text-[10px] text-slate-400 font-medium italic">Sin caracteres</span>
                          </div>
                        </td>
                        <td class="px-4 py-3 text-right align-top">
                          <button type="button" @click="removeProyectoVinculado(pry_id)" class="text-slate-400 hover:text-red-500 transition-colors" title="Desvincular Proyecto">
                            <svg class="w-4 h-4 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>"""

if match:
    content = content.replace(match.group(1), new_ui)
    with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "w") as f:
        f.write(content)
    print("Match found and replaced!")
else:
    print("STILL NOT FOUND")
