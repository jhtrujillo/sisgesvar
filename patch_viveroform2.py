import re

with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "r") as f:
    content = f.read()

# Replace UI part
start_marker = '<label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" for="proyecto_id">Proyecto (Mejoramiento)</label>'
end_marker = '<!-- Caracter -->'
# Find the exact block
match = re.search(f"({re.escape(start_marker)}.*?)(?={re.escape(end_marker)})", content, re.DOTALL)
if match:
    old_ui = match.group(1)
    new_ui = """<label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" for="proyectos">Proyectos (Mejoramiento)</label>
                <ComboBoxMultiple
                  id="proyectos"
                  :options="proyectos.map(p => ({ keyName: p.id_prycto, text: formatProjectName(p) }))"
                  :selected="form.proyectos"
                  @update:selected="
                    (val) => {
                      form.proyectos = val;
                      loadCaracteresForMultipleProyectos(val);
                    }
                  "
                  placeholder="Seleccionar proyectos..."
                />
              </div>
              """
    content = content.replace(old_ui, new_ui)

with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "w") as f:
    f.write(content)
print("UI fixed")
