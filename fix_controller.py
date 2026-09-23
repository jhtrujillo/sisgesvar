with open("api_sivar/app/Http/Controllers/ViveroController.php", "r") as f:
    content = f.read()

import re

# Update method sync
block_to_find = """            if ($request->has('caracteres_ids')) {
                $vivero->caracteres()->sync($request->caracteres_ids);
            }"""

block_to_replace = """            if ($request->has('caracteres_ids')) {
                $vivero->caracteres()->sync($request->caracteres_ids);
            }

            if ($request->has('proyectos') && is_array($request->proyectos)) {
                $vivero->proyectos()->sync($request->proyectos);
            } else {
                $vivero->proyectos()->sync([]);
            }"""

content = content.replace(block_to_find, block_to_replace)

# Store method sync
store_return = """            return response()->json($vivero, 201);"""
store_replace = """            if ($request->has('caracteres_ids')) {
                $vivero->caracteres()->sync($request->caracteres_ids);
            }

            if ($request->has('proyectos') && is_array($request->proyectos)) {
                $vivero->proyectos()->sync($request->proyectos);
            }

            return response()->json($vivero, 201);"""

content = content.replace(store_return, store_replace)

with open("api_sivar/app/Http/Controllers/ViveroController.php", "w") as f:
    f.write(content)
print("Replaced successfully!")
