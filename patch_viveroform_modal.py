import re

with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "r") as f:
    content = f.read()

# 1. Imports
if "AddProyectoCaracterModal.vue" not in content:
    content = content.replace(
        'import ComboBoxMultiple from "@/components/ComboBoxMultiple.vue";',
        'import AddProyectoCaracterModal from "@/components/viveros/AddProyectoCaracterModal.vue";'
    )

# 2. State
state_insert = """
const showProyectoModal = ref(false);

const getProyectoName = (id_prycto: number) => {
  const p = proyectos.value.find(x => x.id_prycto === id_prycto);
  return p ? formatProjectName(p) : id_prycto;
};

const getCaracterNameLocal = (c_id: number) => {
  const c = caracteres.value.find(x => x.id === c_id);
  return c ? c.nombre : c_id;
};

const getCaracteresByProyecto = (id_prycto: number) => {
  return form.value.caracteres_ids.filter(c_id => {
    const c = caracteres.value.find(x => x.id === c_id);
    return c && c.proyecto_id === id_prycto;
  });
};

const handleConfirmProyecto = (data: any) => {
  const pry_id = data.proyecto.id_prycto;
  if (!form.value.proyectos) form.value.proyectos = [];
  if (!form.value.proyectos.includes(pry_id)) {
    form.value.proyectos.push(pry_id);
  }
  
  // Merge caracteres_ids
  if (!form.value.caracteres_ids) form.value.caracteres_ids = [];
  data.caracteres_ids.forEach((c_id: number) => {
    if (!form.value.caracteres_ids.includes(c_id)) {
      form.value.caracteres_ids.push(c_id);
    }
  });
  
  // We need to fetch the character objects so we can display their names and know their proyecto_id
  loadCaracteresForMultipleProyectos(form.value.proyectos);
  showProyectoModal.value = false;
};

const removeProyectoVinculado = (id_prycto: number) => {
  form.value.proyectos = form.value.proyectos.filter((id: number) => id !== id_prycto);
  
  // Remove caracteres that belong to this project
  const caracteresToKeep = form.value.caracteres_ids.filter((c_id: number) => {
    const c = caracteres.value.find(x => x.id === c_id);
    return c && c.proyecto_id !== id_prycto;
  });
  form.value.caracteres_ids = caracteresToKeep;
};
"""

content = content.replace("const showProyectos = ref(false);", "const showProyectos = ref(false);" + state_insert)

# 3. Replace the UI Block
start_marker = '<!-- Proyecto -->'
end_marker = '<!-- Carácter -->'
match = re.search(f"({re.escape(start_marker)}.*?)(?=\s+<!-- Ubicaci)", content, re.DOTALL)

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
              </div>
"""

if match:
    content = content.replace(match.group(1), new_ui)
else:
    print("MATCH NOT FOUND FOR UI REPLACE")

# 4. Add the Modal to the template
if "<AddProyectoCaracterModal" not in content:
    content = content.replace(
        "</form>",
        '</form>\n\n    <AddProyectoCaracterModal\n      :is-open="showProyectoModal"\n      :proyectos="proyectos"\n      @close="showProyectoModal = false"\n      @confirm="handleConfirmProyecto"\n    />'
    )


with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "w") as f:
    f.write(content)
print("ViveroFormView modal logic patched!")
