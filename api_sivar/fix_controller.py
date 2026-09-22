with open("app/Http/Controllers/ViveroController.php", "r") as f:
    content = f.read()

import re

# Insert in `store` method.
# In store:
#             if ($request->has('caracteres_ids')) {
#                 $vivero->caracteres()->sync($request->caracteres_ids);
#             }
# Wait, let's see if store has it.
store_caracteres_match = re.search(r'if \(\$request->has\(\'caracteres_ids\'\)\) \{\s*\$vivero->caracteres\(\)->sync\(\$request->caracteres_ids\);\s*\}', content)
if store_caracteres_match:
    print("Found caracteres_ids sync. Adding proyectos.")
    # Actually wait, let's check `store` first. I'll just append it right before `return response()->json($vivero);` 
    # BUT I need to do it in BOTH store and update.

def append_sync(content, method_name):
    # Find `return response()->json($vivero);` inside the method.
    # To do this safely, we find `public function store` and then the first `return response()->json($vivero, 201);`
    # For update, `public function update` and first `return response()->json($vivero);`
    pass

# Easiest way: just replace all `if ($request->has('caracteres_ids')) {` block with it and the new block.
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

# Also there's one with `return response()->json($vivero, 201);` in store that might not have `caracteres_ids` synced?
# Let's check if store has `caracteres_ids` sync.

with open("app/Http/Controllers/ViveroController.php", "w") as f:
    f.write(content)
print("Replaced!")
