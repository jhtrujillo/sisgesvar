with open("api_sivar/app/Http/Controllers/ViveroController.php", "r") as f:
    content = f.read()

old_sync = """            if ($request->has('proyectos') && is_array($request->proyectos)) {
                $vivero->proyectos()->sync($request->proyectos);
            } else {
                $vivero->proyectos()->sync([]);
            }"""

new_sync = """            if ($request->has('proyectos') && is_array($request->proyectos)) {
                $vivero->proyectos()->sync($request->proyectos);
                if (count($request->proyectos) > 0) {
                    $vivero->proyecto_id = $request->proyectos[0];
                    $vivero->save();
                }
            } else {
                $vivero->proyectos()->sync([]);
            }"""

content = content.replace(old_sync, new_sync)

with open("api_sivar/app/Http/Controllers/ViveroController.php", "w") as f:
    f.write(content)

print("ViveroController sync updated!")
