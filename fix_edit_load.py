with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "r") as f:
    content = f.read()

import re

old_load = """      const vivero = response.data;
      if (vivero.fecha_siembra) {
        vivero.fecha_siembra = vivero.fecha_siembra.substring(0, 10);
      }
      form.value = { ...vivero, caracteres_ids: [] };"""

new_load = """      const vivero = response.data;
      if (vivero.fecha_siembra) {
        vivero.fecha_siembra = vivero.fecha_siembra.substring(0, 10);
      }
      
      const mappedProyectos = vivero.proyectos ? vivero.proyectos.map((p: any) => p.id_prycto || p.id) : [];
      const mappedCaracteres = vivero.caracteres ? vivero.caracteres.map((c: any) => c.id) : [];
      
      form.value = { 
        ...vivero, 
        proyectos: mappedProyectos,
        caracteres_ids: mappedCaracteres 
      };
      
      // Load the caracter objects so we can show their names in the summary table
      if (mappedProyectos.length > 0) {
        await loadCaracteresForMultipleProyectos(mappedProyectos);
      }
"""

content = content.replace(old_load, new_load)

with open("sivar/src/views/mejoramiento/siembra-campo/viveros/ViveroFormView.vue", "w") as f:
    f.write(content)

print("Edit mode loading fixed!")
