import re

with open("api_sivar/app/Http/Controllers/ViveroParcelaController.php", "r") as f:
    content = f.read()

# Replace validation to accept caracter_nombre and proyecto_nombre
old_validation = """            'parcelas.*.caracter_id' => 'nullable|numeric',"""
new_validation = """            'parcelas.*.caracter_id' => 'nullable|numeric',
            'parcelas.*.caracter_nombre' => 'nullable|string',
            'parcelas.*.proyecto_nombre' => 'nullable|string',"""
content = content.replace(old_validation, new_validation)

# Replace processing logic inside the loop
old_logic = """                $numero_parcela_origen = (isset($data['numero_parcela_origen']) && $data['numero_parcela_origen'] !== '') ? $data['numero_parcela_origen'] : null;
                $id_plot_origen = (isset($data['id_plot_origen']) && $data['id_plot_origen'] !== '') ? $data['id_plot_origen'] : null;
                $caracter_id = (isset($data['caracter_id']) && $data['caracter_id'] !== '') ? $data['caracter_id'] : null;"""

new_logic = """                $numero_parcela_origen = (isset($data['numero_parcela_origen']) && $data['numero_parcela_origen'] !== '') ? $data['numero_parcela_origen'] : null;
                $id_plot_origen = (isset($data['id_plot_origen']) && $data['id_plot_origen'] !== '') ? $data['id_plot_origen'] : null;
                $caracter_id = (isset($data['caracter_id']) && $data['caracter_id'] !== '') ? $data['caracter_id'] : null;

                // Resolve caracter_id dynamically if names are provided
                if (!$caracter_id && isset($data['caracter_nombre']) && $data['caracter_nombre'] !== '' && isset($data['proyecto_nombre']) && $data['proyecto_nombre'] !== '') {
                    $proyecto_nombre = trim($data['proyecto_nombre']);
                    $caracter_nombre = trim($data['caracter_nombre']);
                    
                    // Buscar proyecto por nombre
                    $proyecto = \App\Models\Proyecto::where('nm_prycto', $proyecto_nombre)->first();
                    if ($proyecto) {
                        // Buscar o crear caracter
                        $caracter = \App\Models\ProyectoCaracter::firstOrCreate(
                            ['proyecto_id' => $proyecto->id_prycto, 'nombre' => $caracter_nombre]
                        );
                        $caracter_id = $caracter->id;
                    }
                }"""
content = content.replace(old_logic, new_logic)

with open("api_sivar/app/Http/Controllers/ViveroParcelaController.php", "w") as f:
    f.write(content)
print("ViveroParcelaController patched")
