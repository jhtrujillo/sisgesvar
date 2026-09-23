with open("api_sivar/app/Http/Controllers/ViveroController.php", "r") as f:
    content = f.read()

old_sync = """            if ($request->has('caracteres_ids')) {
                $vivero->caracteres()->sync($request->caracteres_ids);
            }"""

new_sync = """            if ($request->has('caracteres_ids')) {
                $vivero->caracteres()->sync($request->caracteres_ids);
                if (is_array($request->caracteres_ids) && count($request->caracteres_ids) > 0) {
                    $vivero->caracter_id = $request->caracteres_ids[0];
                    $vivero->save();
                }
            }"""

content = content.replace(old_sync, new_sync)

with open("api_sivar/app/Http/Controllers/ViveroController.php", "w") as f:
    f.write(content)

print("ViveroController updated with caracter_id fallback!")
