import re

with open('app/Http/Controllers/VarietyController.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace specifically inside germoplasmBankList
old_str = r"\$model = DB::connection\('sivar'\)->table\('caracterizacion_banco_germoplasma'\)->paginate\(10\);"
new_str = """$query = DB::connection('sivar')->table('caracterizacion_banco_germoplasma');
            $search = $request->query('search');
            if (!empty($search)) {
                $query->where('variedad', 'ilike', '%' . $search . '%')
                      ->orWhere('ensayo', 'ilike', '%' . $search . '%')
                      ->orWhere('madre', 'ilike', '%' . $search . '%')
                      ->orWhere('padre', 'ilike', '%' . $search . '%');
            }
            $model = $query->paginate($request->query('perPage', 50));"""

content = re.sub(old_str, new_str, content, count=1)

with open('app/Http/Controllers/VarietyController.php', 'w', encoding='utf-8') as f:
    f.write(content)
