with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "r") as f:
    content = f.read()

import re

# Add ComboBoxMultiple import
if "ComboBoxMultiple.vue" not in content:
    content = content.replace('import ComboBox from "@/components/admin/ComboBox.vue";', 'import ComboBox from "@/components/admin/ComboBox.vue";\nimport ComboBoxMultiple from "@/components/admin/ComboBoxMultiple.vue";')

# form value
content = content.replace("proyecto_id: \"\",", "proyecto_id: \"\",\n  proyectos: [],")

# UI replacement
ui_old = """                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" for="proyecto_id">Proyecto (Mejoramiento)</label>
                <div class="relative">
                  <textarea
                    v-model="searchProyecto"
                    @focus="showProyectos = true"
                    @blur="hideProyectosDelay"
                    placeholder="Escribe para buscar un proyecto..."
                    rows="2"
                    class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-3 focus:bg-white focus:ring-4 focus:ring-cenicana/10 focus:border-cenicana transition-all outline-none shadow-sm resize-none"
                  ></textarea>
                  <button
                    v-if="form.proyecto_id"
                    @click.prevent="clearProyecto"
                    class="absolute right-3 top-3 text-slate-400 hover:text-red-500"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                  </button>
                  <ul
                    v-if="showProyectos && filteredProyectos.length > 0"
                    class="absolute z-10 w-full bg-white border border-slate-200 rounded-xl mt-1 max-h-48 overflow-y-auto shadow-lg"
                  >
                    <li
                      v-for="pry in filteredProyectos"
                      :key="pry.id_prycto"
                      @click="selectProyecto(pry)"
                      class="px-3 py-2 text-xs hover:bg-slate-50 cursor-pointer"
                      :class="form.proyecto_id === pry.id_prycto ? 'bg-emerald-50 text-cenicana font-bold border-l-2 border-cenicana' : ''"
                    >
                      <div class="font-semibold text-slate-700">{{ pry.id_prycto }}</div>
                      <div class="text-[10px] text-slate-500 line-clamp-1">{{ pry.nm_prycto }}</div>
                    </li>
                  </ul>
                </div>"""

ui_new = """                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" for="proyectos">Proyectos (Mejoramiento)</label>
                <ComboBoxMultiple
                  id="proyectos"
                  :options="proyectos.map(p => ({ keyName: p.id_prycto, text: p.id_prycto + ' - ' + p.nm_prycto }))"
                  :selected="form.proyectos"
                  @update:selected="
                    (val) => {
                      form.proyectos = val;
                      loadCaracteresForMultipleProyectos(val);
                    }
                  "
                  placeholder="Seleccionar proyectos..."
                />"""

content = content.replace(ui_old, ui_new)

# Edit mode load
edit_load_old = """      if (form.value.proyecto_id) {
        const pry = proyectos.value.find((p) => p.id_prycto == form.value.proyecto_id);
        if (pry) {
          searchProyecto.value = pry.id_prycto + ' - ' + pry.nm_prycto;
        }
        await loadCaracteres(form.value.proyecto_id);
      }"""
edit_load_new = """      if (v.proyectos && v.proyectos.length > 0) {
        form.value.proyectos = v.proyectos.map(p => p.id_prycto);
        await loadCaracteresForMultipleProyectos(form.value.proyectos);
      } else if (form.value.proyecto_id) { // fallback legacy
        form.value.proyectos = [form.value.proyecto_id];
        await loadCaracteresForMultipleProyectos(form.value.proyectos);
      }"""
content = content.replace(edit_load_old, edit_load_new)

# Add loadCaracteresForMultipleProyectos
multi_load = """const loadCaracteresForMultipleProyectos = async (proyectosIds: any[]) => {
  try {
    const promises = proyectosIds.map(id => viverosServices.getCaracteresPorProyecto(id));
    const results = await Promise.all(promises);
    caracteres.value = results.flat();
  } catch (error) {
    console.error("Error al cargar caracteres para multiples proyectos", error);
  }
};"""
content = content.replace("const loadCaracteres =", multi_load + "\n\nconst loadCaracteres =")

# When duplicating or routing
route_old = """      if (route.query.proyecto_id) {
        form.value.proyecto_id = Number(route.query.proyecto_id);
        const pry = proyectos.value.find((p) => p.id_prycto == form.value.proyecto_id);
        if (pry) {
          searchProyecto.value = pry.id_prycto + ' - ' + pry.nm_prycto;
        }
        await loadCaracteres(form.value.proyecto_id);
      }"""
route_new = """      if (route.query.proyecto_id) {
        form.value.proyectos = [Number(route.query.proyecto_id)];
        await loadCaracteresForMultipleProyectos(form.value.proyectos);
      }"""
content = content.replace(route_old, route_new)

# Validation logic adjustments inside createCaracter
create_car_old = """  if (!searchCaracter.value || !form.value.proyecto_id) return;
  try {
    const res = await viverosServices.createCaracter(form.value.proyecto_id, {"""
create_car_new = """  if (!searchCaracter.value || form.value.proyectos.length === 0) return;
  const targetProyecto = form.value.proyectos[0]; // create on the first one
  try {
    const res = await viverosServices.createCaracter(targetProyecto, {"""
content = content.replace(create_car_old, create_car_new)


with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "w") as f:
    f.write(content)
print("ViveroFormView frontend patched")
