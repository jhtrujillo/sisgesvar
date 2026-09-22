with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "r") as f:
    content = f.read()

# Remove Proyecto mandatory validation
old_val = """  if (!form.value.proyecto_id) {
    toast.error("El Proyecto es obligatorio.");
    return;
  }"""
new_val = """  // Proyecto ya no es estrictamente obligatorio a nivel de encabezado
  // if (!form.value.proyecto_id) {
  //   toast.error("El Proyecto es obligatorio.");
  //   return;
  // }"""
content = content.replace(old_val, new_val)

with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "w") as f:
    f.write(content)
print("ViveroFormView patched")
