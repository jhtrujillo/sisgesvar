with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "r") as f:
    content = f.read()

import re

old_proyecto_validation = """  if (!form.value.proyecto_id) {
    activeTab.value = "generales";
    toast.error("El Proyecto es obligatorio.");
    return;
  }"""

new_proyecto_validation = """  if (!form.value.proyectos || form.value.proyectos.length === 0) {
    activeTab.value = "generales";
    toast.error("Debe vincular al menos un Proyecto.");
    return;
  }"""

content = content.replace(old_proyecto_validation, new_proyecto_validation)

with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "w") as f:
    f.write(content)
print("Submit validation fixed!")
