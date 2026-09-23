import re

with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "r") as f:
    content = f.read()

start_marker = '<!-- Proyecto -->'
end_marker = '<!-- Carácter -->'
match = re.search(f"({re.escape(start_marker)}.*?)(?=\s+{re.escape(end_marker)})", content, re.DOTALL)

new_ui = """<!-- Proyecto -->
              <div class="relative md:col-span-2">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" for="proyectos">Proyectos (Mejoramiento)</label>
                
                <div class="flex flex-wrap gap-2 mb-2" v-if="form.proyectos && form.proyectos.length > 0">
                  <div v-for="p_id in form.proyectos" :key="p_id" class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-1 rounded-full flex items-center gap-1.5 border border-blue-200">
                    {{ (() => { const p = proyectos.find(x => x.id_prycto === p_id); return p ? formatProjectName(p) : p_id; })() }}
                    <button type="button" @click="
                      form.proyectos = form.proyectos.filter(id => id !== p_id);
                      loadCaracteresForMultipleProyectos(form.proyectos);
                    " class="text-blue-600 hover:text-blue-900 focus:outline-none">
                      <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    </button>
                  </div>
                </div>

                <div class="relative">
                  <input
                    type="text"
                    v-model="searchProyecto"
                    @focus="showProyectos = true"
                    @blur="hideProyectosDelay"
                    placeholder="Buscar y agregar proyectos..."
                    class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-3 focus:bg-white focus:ring-4 focus:ring-cenicana/10 focus:border-cenicana transition-all outline-none shadow-sm"
                  />
                  <div
                    v-if="showProyectos && filteredProyectos.length > 0"
                    class="absolute z-20 w-full mt-1 bg-white shadow-xl max-h-60 rounded-xl py-1 text-xs ring-1 ring-black/5 overflow-auto border border-slate-100"
                  >
                    <div
                      v-for="pry in filteredProyectos"
                      :key="pry.id_prycto"
                      @mousedown.prevent="
                        if (!form.proyectos) form.proyectos = [];
                        if (!form.proyectos.includes(pry.id_prycto)) {
                          form.proyectos.push(pry.id_prycto);
                          loadCaracteresForMultipleProyectos(form.proyectos);
                        }
                        searchProyecto = '';
                        showProyectos = false;
                      "
                      class="cursor-pointer select-none py-2.5 px-3.5 hover:bg-slate-50 text-slate-700 font-medium transition-colors"
                      :class="form.proyectos && form.proyectos.includes(pry.id_prycto) ? 'bg-blue-50 text-blue-700 font-bold' : ''"
                    >
                      {{ formatProjectName(pry) }}
                    </div>
                  </div>
                </div>
              </div>"""

if match:
    content = content.replace(match.group(1), new_ui)
    with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "w") as f:
        f.write(content)
    print("Fixed custom multi select!")
else:
    print("Not found match")
