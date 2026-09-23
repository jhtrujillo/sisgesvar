with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "r") as f:
    content = f.read()

# Replace mappedProyectos logic in edit mode
old_mapping = """      const mappedProyectos = vivero.proyectos ? vivero.proyectos.map((p: any) => p.id_prycto || p.id) : [];
      const mappedCaracteres = vivero.caracteres ? vivero.caracteres.map((c: any) => c.id) : [];
      
      form.value = { 
        ...vivero, 
        proyectos: mappedProyectos,
        caracteres_ids: mappedCaracteres 
      };
      
      // Load the caracter objects so we can show their names in the summary table
      if (mappedProyectos.length > 0) {
        await loadCaracteresForMultipleProyectos(mappedProyectos);
      }"""

new_mapping = """      let mappedProyectos: number[] = vivero.proyectos && vivero.proyectos.length > 0 
        ? vivero.proyectos.map((p: any) => Number(p.id_prycto || p.id)) 
        : (vivero.proyecto_id ? [Number(vivero.proyecto_id)] : []);

      if (vivero.caracteres && vivero.caracteres.length > 0) {
        vivero.caracteres.forEach((c: any) => {
          if (c.proyecto_id && !mappedProyectos.includes(Number(c.proyecto_id))) {
            mappedProyectos.push(Number(c.proyecto_id));
          }
        });
      }

      const mappedCaracteres = vivero.caracteres ? vivero.caracteres.map((c: any) => Number(c.id)) : [];
      
      form.value = { 
        ...vivero, 
        proyectos: mappedProyectos,
        caracteres_ids: mappedCaracteres 
      };
      
      if (mappedProyectos.length > 0) {
        await loadCaracteresForMultipleProyectos(mappedProyectos);
      }"""

content = content.replace(old_mapping, new_mapping)

# Ensure getCaracterNameLocal, getCaracteresByProyecto, removeProyectoVinculado use Number() or ==
content = content.replace("const c = caracteres.value.find(x => x.id === c_id);", "const c = caracteres.value.find(x => Number(x.id) === Number(c_id));")
content = content.replace("const c = caracteres.value.find(x => x.id == c_id);", "const c = caracteres.value.find(x => Number(x.id) === Number(c_id));")

with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "w") as f:
    f.write(content)

print("ViveroFormView patched!")
