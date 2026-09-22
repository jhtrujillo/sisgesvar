with open("app/Http/Controllers/ViveroController.php", "r") as f:
    content = f.read()

import re

# Add to Store
store_hook = """                if ($request->has('caracter_id') && is_array($request->caracter_id)) {
                    $vivero->caracteres()->sync($request->caracter_id);
                }"""
new_store_hook = """                if ($request->has('caracter_id') && is_array($request->caracter_id)) {
                    $vivero->caracteres()->sync($request->caracter_id);
                }
                
                if ($request->has('proyectos') && is_array($request->proyectos)) {
                    $vivero->proyectos()->sync($request->proyectos);
                }"""
content = content.replace(store_hook, new_store_hook)

# Add to Update
update_hook = """            if ($request->has('caracter_id') && is_array($request->caracter_id)) {
                $vivero->caracteres()->sync($request->caracter_id);
            } else {
                $vivero->caracteres()->sync([]);
            }"""
new_update_hook = """            if ($request->has('caracter_id') && is_array($request->caracter_id)) {
                $vivero->caracteres()->sync($request->caracter_id);
            } else {
                $vivero->caracteres()->sync([]);
            }
            
            if ($request->has('proyectos') && is_array($request->proyectos)) {
                $vivero->proyectos()->sync($request->proyectos);
            } else {
                $vivero->proyectos()->sync([]);
            }"""
content = content.replace(update_hook, new_update_hook)

# Add to index eager load
content = content.replace("'caracteres',", "'caracteres', 'proyectos',")
content = content.replace("'caracteres'", "'caracteres', 'proyectos'")

with open("app/Http/Controllers/ViveroController.php", "w") as f:
    f.write(content)
print("ViveroController patched")
