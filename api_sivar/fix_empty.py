import re

with open('app/Http/Controllers/VarietyController.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_str = """        // Check if records are found
        if ($model->isNotEmpty()) {
            return response()->json($model);
        }

        // Return response if no records are found
        return response("No hay registros", 400);"""

new_str = """        // Always return the paginated model, even if empty. Frontend expects { data: [], total: 0 }
        return response()->json($model);"""

content = content.replace(old_str, new_str)

with open('app/Http/Controllers/VarietyController.php', 'w', encoding='utf-8') as f:
    f.write(content)
