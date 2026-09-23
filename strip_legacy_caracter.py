with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "r") as f:
    content = f.read()

legacy_block = """      if (form.value.proyecto_id) {
        const pry = proyectos.value.find((p) => p.id_prycto == form.value.proyecto_id);
        if (pry) searchProyecto.value = formatProjectName(pry);

        await loadCaracteres(form.value.proyecto_id);
        if (form.value.caracteres && form.value.caracteres.length > 0) {
          form.value.caracteres_ids = form.value.caracteres.map((c: any) => c.id);
        } else if (form.value.caracter_id) {
          form.value.caracteres_ids = [form.value.caracter_id];
        }
      }"""

if legacy_block in content:
    content = content.replace(legacy_block, "")
    print("Legacy block found and stripped!")
else:
    print("Legacy block not found, checking regex...")

with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "w") as f:
    f.write(content)

