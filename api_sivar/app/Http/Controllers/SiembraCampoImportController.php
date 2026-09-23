<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SiembraCampoImportController extends Controller
{
    /**
     * Importa masivamente registros desde un Excel a la tabla siembra_campo.
     */
    public function importar(Request $request)
    {
        set_time_limit(300);

        $request->validate([
            'file'       => 'required|file|mimes:xlsx,xls',
            'sheet_name' => 'required|string',
            'mapping'    => 'required|string',
        ]);

        $mapping   = json_decode($request->input('mapping'), true);
        $sheetName = $request->input('sheet_name');
        $file      = $request->file('file');

        // --- Parsear el Excel con PhpSpreadsheet ---
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());

        $worksheet = $spreadsheet->getSheetByName($sheetName);
        if (!$worksheet) {
            $worksheet = $spreadsheet->getActiveSheet();
        }

        // Convertir hoja a array (sin formato, sin calcular fórmulas)
        $rows   = $worksheet->toArray(null, false, false, false);
        $header = array_map('trim', $rows[0] ?? []);

        if (empty($header)) {
            return response()->json([
                'success' => false,
                'message' => 'La hoja seleccionada está vacía o no tiene encabezados.',
            ], 422);
        }

        // Construir índice: campo_sistema => índice_columna_excel
        $colIndex = [];
        foreach ($mapping as $fieldKey => $excelColName) {
            if ($excelColName !== '' && $excelColName !== null) {
                $idx = array_search(trim($excelColName), $header);
                $colIndex[$fieldKey] = ($idx !== false) ? $idx : null;
            }
        }

        $now     = Carbon::now();
        $inserts = [];
        $userId  = auth()->id() ?? 1;

        // Iterar filas de datos (desde la fila 1, saltando el header 0)
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];

            // Omitir filas completamente vacías
            if (empty(array_filter($row, fn($v) => $v !== null && $v !== ''))) {
                continue;
            }

            $get = function (string $key) use ($colIndex, $row) {
                if (!isset($colIndex[$key]) || $colIndex[$key] === null) {
                    return null;
                }
                $val = $row[$colIndex[$key]] ?? null;
                return is_string($val) ? trim($val) : $val;
            };

            // Parsear fechas (pueden venir como número serie Excel o string)
            $parseFecha = function ($val): ?string {
                if ($val === null || $val === '') return null;
                if (is_numeric($val)) {
                    try {
                        return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$val)
                            ->format('Y-m-d');
                    } catch (\Exception $e) {
                        return null;
                    }
                }
                try {
                    return Carbon::parse(str_replace('/', '-', (string)$val))->format('Y-m-d');
                } catch (\Exception $e) {
                    return null;
                }
            };

            // Booleano vivero_en_campo
            $viveroEnCampoRaw = $get('vivero_en_campo');
            $viveroEnCampo    = null;
            if ($viveroEnCampoRaw !== null) {
                $lower = strtolower((string)$viveroEnCampoRaw);
                $viveroEnCampo = in_array($lower, ['1', 'true', 'si', 'sí', 'yes', 's', 'y']) ? true : false;
            }

            $inserts[] = [
                'vrdad'               => $get('vrdad'),
                'id_pr'               => $get('id_pr') !== null ? (int)$get('id_pr') : null,
                'id_crcter'           => $get('id_crcter') !== null ? (int)$get('id_crcter') : null,
                'ingnio'              => $get('ingnio'),
                'hcnda'               => $get('hcnda'),
                'lte'                 => $get('lte'),
                'plot'                => $get('plot'),
                'fcha_smbra'          => $parseFecha($get('fcha_smbra')),
                'fcha_crte'           => $parseFecha($get('fcha_crte')),
                'edad_actual'         => $get('edad_actual') !== null ? (float)$get('edad_actual') : null,
                'crte'                => $get('crte') !== null ? (int)$get('crte') : null,
                'orgen_bnco_grmplsma' => $get('orgen_bnco_grmplsma'),
                'tpo_flrcion'         => $get('tpo_flrcion'),
                'vivero'              => $get('vivero'),
                'grpo'                => $get('grpo'),
                'vivero_en_campo'     => $viveroEnCampo,
                'observacuines'       => $get('observacuines'),
                'id_carga'            => $get('id_carga') !== null ? (int)$get('id_carga') : null,
                'temporada'           => $get('temporada') !== null ? (int)$get('temporada') : null,
            ];
        }

        if (empty($inserts)) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron filas con datos para importar.',
            ], 422);
        }

        // Insertar en lotes de 500 para evitar timeouts en archivos grandes
        $chunks = array_chunk($inserts, 500);
        foreach ($chunks as $chunk) {
            DB::connection('sivar')->table('siembra_campo')->insert($chunk);
        }

        return response()->json([
            'success' => true,
            'count'   => count($inserts),
            'message' => count($inserts) . ' registros importados correctamente a Siembra Campo.',
        ]);
    }
}

