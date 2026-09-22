with open("sivar/src/components/viveros/ViveroParcelasImportWizard.vue", "r") as f:
    content = f.read()

# Add Columna Proyecto
old_html = """                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Columna Carácter (Opcional)</label>
                    <select
                      v-model="mapping.caracter"
                      class="w-full pl-3 pr-10 py-2 border-slate-300 rounded-md border text-sm focus:outline-none focus:ring-1 focus:ring-cenicana bg-slate-50"
                    >
                      <option value="">-- No incluir --</option>
                      <option v-for="col in headers" :key="col" :value="col">{{ col }}</option>
                    </select>
                  </div>"""

new_html = """                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Columna Carácter (Opcional)</label>
                    <select
                      v-model="mapping.caracter"
                      class="w-full pl-3 pr-10 py-2 border-slate-300 rounded-md border text-sm focus:outline-none focus:ring-1 focus:ring-cenicana bg-slate-50"
                    >
                      <option value="">-- No incluir --</option>
                      <option v-for="col in headers" :key="col" :value="col">{{ col }}</option>
                    </select>
                  </div>
                  <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm border-l-4 border-l-blue-400">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Columna Proyecto (Opcional)</label>
                    <select
                      v-model="mapping.proyecto"
                      class="w-full pl-3 pr-10 py-2 border-slate-300 rounded-md border text-sm focus:outline-none focus:ring-1 focus:ring-cenicana bg-slate-50"
                    >
                      <option value="">-- No incluir --</option>
                      <option v-for="col in headers" :key="col" :value="col">{{ col }}</option>
                    </select>
                  </div>"""
content = content.replace(old_html, new_html)

# mapping ref
old_mapping = """const mapping = ref({ plot: "", variedad: "", caracter: "" });"""
new_mapping = """const mapping = ref({ plot: "", variedad: "", caracter: "", proyecto: "" });"""
content = content.replace(old_mapping, new_mapping)

# auto guess
old_guess = """  const carCol = headers.value.find((h) => h.toLowerCase().includes("caracter") || h.toLowerCase().includes("carácter"));
  if (carCol) mapping.value.caracter = carCol;"""
new_guess = """  const carCol = headers.value.find((h) => h.toLowerCase().includes("caracter") || h.toLowerCase().includes("carácter"));
  if (carCol) mapping.value.caracter = carCol;

  const proyCol = headers.value.find((h) => h.toLowerCase().includes("proyecto"));
  if (proyCol) mapping.value.proyecto = proyCol;"""
content = content.replace(old_guess, new_guess)

# Resolve payload - readyToImport
old_payload1 = """    let resolvedCaracterId = props.caracterId || null;
    if (mapping.value.caracter && row[mapping.value.caracter]) {
      const carText = String(row[mapping.value.caracter]).trim().toLowerCase();
      const carMatch = props.caracteres.find(c => c.nombre.toLowerCase() === carText || c.nombre.toLowerCase().includes(carText));
      if (carMatch) {
        resolvedCaracterId = carMatch.id;
      }
    }"""
new_payload1 = """    let resolvedCaracterId = props.caracterId || null;
    let caracterNombre = null;
    let proyectoNombre = null;

    if (mapping.value.caracter && row[mapping.value.caracter]) {
      const carText = String(row[mapping.value.caracter]).trim().toLowerCase();
      caracterNombre = String(row[mapping.value.caracter]).trim();
      const carMatch = props.caracteres.find(c => c.nombre.toLowerCase() === carText || c.nombre.toLowerCase().includes(carText));
      if (carMatch) {
        resolvedCaracterId = carMatch.id;
      }
    }
    
    if (mapping.value.proyecto && row[mapping.value.proyecto]) {
      proyectoNombre = String(row[mapping.value.proyecto]).trim();
    }"""
content = content.replace(old_payload1, new_payload1)

old_push1 = """      readyToImport.value.push({
        numero_parcela: plotVal,
        variedad_id: exactMatch.id_nm_vrdad,
        numero_parcela_origen: null,
        id_plot_origen: `${props.viveroIdentificador}-${plotVal}`,
        caracter_id: resolvedCaracterId
      });"""
new_push1 = """      readyToImport.value.push({
        numero_parcela: plotVal,
        variedad_id: exactMatch.id_nm_vrdad,
        numero_parcela_origen: null,
        id_plot_origen: `${props.viveroIdentificador}-${plotVal}`,
        caracter_id: resolvedCaracterId,
        caracter_nombre: caracterNombre,
        proyecto_nombre: proyectoNombre
      });"""
content = content.replace(old_push1, new_push1)

# Resolve payload - conflicts
old_payload2 = """      let resolvedCaracterId = props.caracterId || null;
      if (mapping.value.caracter && c.row[mapping.value.caracter]) {
        const carText = String(c.row[mapping.value.caracter]).trim().toLowerCase();
        const carMatch = props.caracteres.find(car => car.nombre.toLowerCase() === carText || car.nombre.toLowerCase().includes(carText));
        if (carMatch) {
          resolvedCaracterId = carMatch.id;
        }
      }

      return {
        numero_parcela: plotVal,
        variedad_id: c.resolvedId,
        numero_parcela_origen: null,
        id_plot_origen: `${props.viveroIdentificador}-${plotVal}`,
        caracter_id: resolvedCaracterId
      };"""
new_payload2 = """      let resolvedCaracterId = props.caracterId || null;
      let caracterNombre = null;
      let proyectoNombre = null;
      
      if (mapping.value.caracter && c.row[mapping.value.caracter]) {
        const carText = String(c.row[mapping.value.caracter]).trim().toLowerCase();
        caracterNombre = String(c.row[mapping.value.caracter]).trim();
        const carMatch = props.caracteres.find(car => car.nombre.toLowerCase() === carText || car.nombre.toLowerCase().includes(carText));
        if (carMatch) {
          resolvedCaracterId = carMatch.id;
        }
      }
      
      if (mapping.value.proyecto && c.row[mapping.value.proyecto]) {
        proyectoNombre = String(c.row[mapping.value.proyecto]).trim();
      }

      return {
        numero_parcela: plotVal,
        variedad_id: c.resolvedId,
        numero_parcela_origen: null,
        id_plot_origen: `${props.viveroIdentificador}-${plotVal}`,
        caracter_id: resolvedCaracterId,
        caracter_nombre: caracterNombre,
        proyecto_nombre: proyectoNombre
      };"""
content = content.replace(old_payload2, new_payload2)

with open("sivar/src/components/viveros/ViveroParcelasImportWizard.vue", "w") as f:
    f.write(content)
print("ViveroParcelasImportWizard patched")
