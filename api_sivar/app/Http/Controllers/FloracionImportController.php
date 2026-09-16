<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class FloracionImportController extends Controller
{
    public function validateImport(Request $request)
    {
        set_time_limit(300);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
            'vivero_id' => 'required|integer',
            'sheet_name' => 'required|string',
            'mapping' => 'required|string'
        ]);

        $viveroId = $request->vivero_id;
        $sheetName = $request->sheet_name;
        $mapping = json_decode($request->mapping, true);

        // Fetch vivero and project details
        $vivero = DB::connection('sivar')->table('viveros')->where('id', $viveroId)->first();
        if (!$vivero) {
            return response()->json(['errors' => [['row' => '-', 'message' => 'Vivero no encontrado en el sistema.']]]);
        }

        $proyecto = DB::connection('sivar')->table('actividads')->where('id', $vivero->proyecto_id)->first();
        $proyectoNombre = $proyecto ? $proyecto->nm_prycto : null;

        // Fetch valid parcelas for this vivero
        $parcelas = DB::connection('sivar')->table('vivero_parcelas')->where('vivero_id', $viveroId)->get();
        $parcelasMap = []; // parcel_number => variedad_name
        $plotOrigenMap = []; // parcel_number => id_plot_origen
        foreach ($parcelas as $p) {
            $variedad = DB::connection('sivar')->table('maestro_V_VIC_BG')->where('id_nm_vrdad', $p->variedad_id)->first();
            $parcelasMap[$p->numero_parcela] = $variedad ? trim($variedad->nm_vrdad) : null;
            $plotOrigenMap[$p->numero_parcela] = $p->id_plot_origen ? trim($p->id_plot_origen) : null;
        }

        $file = $request->file('file');
        
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
        $sheetName = $request->input('sheet_name');
        
        $worksheet = $spreadsheet->getSheetByName($sheetName);
        if (!$worksheet) {
            $worksheet = $spreadsheet->getActiveSheet();
        }
        
        $rows = $worksheet->toArray(null, false, false, false); 

        $header = array_map('trim', $rows[0]);
        $errors = [];
        $validCount = 0;

        // Get column indices based on mapping
        $colIndex = [];
        foreach ($mapping as $key => $colName) {
            $colIndex[$key] = array_search($colName, $header);
        }

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            
            // Skip empty rows (check if parcela and variedad are empty)
            $parcelaIdx = $colIndex['parcela'] ?? false;
            $variedadIdx = $colIndex['variedad'] ?? false;
            
            if ($parcelaIdx === false || $variedadIdx === false || !isset($row[$parcelaIdx]) || !isset($row[$variedadIdx])) {
                continue;
            }

            $excelVivero = $colIndex['vivero'] !== false ? trim($row[$colIndex['vivero']]) : null;
            $excelParcela = trim($row[$parcelaIdx]);
            $excelVariedad = trim($row[$variedadIdx]);
            $excelProyecto = $colIndex['proyecto'] !== false ? trim($row[$colIndex['proyecto']]) : null;

            // Rule 1: Check Vivero ID (can be the vivero ID itself, or the vivero ID + "-" + parcela, or the id_plot_origen of the parcela)
            $plotIdComputed = $vivero->identificador_unico . '-' . $excelParcela;
            $plotIdOrigen = $plotOrigenMap[$excelParcela] ?? null;
            
            $viveroMatch = false;
            if (!$excelVivero) {
                $viveroMatch = true;
            } else {
                $evLower = strtolower($excelVivero);
                if ($evLower === strtolower($vivero->identificador_unico) || 
                    $evLower === strtolower($plotIdComputed) || 
                    ($plotIdOrigen && $evLower === strtolower($plotIdOrigen))) {
                    $viveroMatch = true;
                }
            }
            
            if (!$viveroMatch) {
                $filename = $request->file('file')->getClientOriginalName();
$errors[] = ['row' => $i + 1, 'message' => "El Origen '{$excelVivero}' en el archivo '{$filename}' no coincide con el vivero '{$vivero->identificador_unico}' ni con el ID Plot de la parcela '{$excelParcela}'."];
                continue;
            }

            // Rule 2 & 3: Check Parcela existence and Variedad match
            if (!isset($parcelasMap[$excelParcela])) {
                $errors[] = ['row' => $i + 1, 'message' => "La parcela {$excelParcela} no existe registrada en el vivero {$vivero->identificador_unico} en SIVAR."];
                continue;
            } else {
                $dbVariedad = $parcelasMap[$excelParcela];
                if (strtolower($dbVariedad) !== strtolower($excelVariedad)) {
                    $errors[] = ['row' => $i + 1, 'message' => "Inconsistencia de Variedad en Parcela {$excelParcela}: El Excel dice '{$excelVariedad}' pero SIVAR tiene registrada '{$dbVariedad}'."];
                    continue;
                }
            }

            // Rule 4: Check Project
            if ($proyectoNombre && $excelProyecto && strtolower($excelProyecto) !== strtolower($proyectoNombre) && trim($excelProyecto) !== '') {
                $errors[] = ['row' => $i + 1, 'message' => "El proyecto '{$excelProyecto}' no coincide con el proyecto asignado al vivero '{$proyectoNombre}'."];
                continue;
            }
            
            $validCount++;
        }

        if (count($errors) > 0) {
            return response()->json(['errors' => $errors]);
        }

        return response()->json(['validCount' => $validCount]);
    }

    public function executeImport(Request $request)
    {
        set_time_limit(300);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
            'vivero_id' => 'required|integer',
            'sheet_name' => 'required|string',
            'mapping' => 'required|string'
        ]);

        $viveroId = $request->vivero_id;
        $mapping = json_decode($request->mapping, true);

        $vivero = DB::connection('sivar')->table('viveros')->where('id', $viveroId)->first();
        
        $file = $request->file('file');
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
        $sheetName = $request->input('sheet_name');
        
        $worksheet = $spreadsheet->getSheetByName($sheetName);
        if (!$worksheet) {
            $worksheet = $spreadsheet->getActiveSheet();
        }
        
        $rows = $worksheet->toArray(null, false, false, false); 
        $header = array_map('trim', $rows[0]);

        $colIndex = [];
        foreach ($mapping as $key => $colName) {
            $colIndex[$key] = array_search($colName, $header);
        }

        $inserts = [];
        $now = Carbon::now();

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            
            $parcelaIdx = $colIndex['parcela'] ?? false;
            $variedadIdx = $colIndex['variedad'] ?? false;
            
            if ($parcelaIdx === false || $variedadIdx === false || !isset($row[$parcelaIdx]) || !isset($row[$variedadIdx])) {
                continue;
            }

            $excelParcela = trim($row[$parcelaIdx]);
            $excelVariedad = trim($row[$variedadIdx]);
            
            $sexo = $colIndex['sexo'] !== false ? trim($row[$colIndex['sexo']]) : null;
            $polen = $colIndex['polen'] !== false ? trim($row[$colIndex['polen']]) : null;
            $floracionTipo = $colIndex['floracion'] !== false ? trim($row[$colIndex['floracion']]) : null;
            $fecha = $colIndex['fecha'] !== false ? trim($row[$colIndex['fecha']]) : null;

            // Clean data
            $mappedSexo = null;
            if (stripos($sexo, 'macho') !== false || strtoupper($sexo) === 'M') $mappedSexo = 'Macho';
            else if (stripos($sexo, 'hembra') !== false || strtoupper($sexo) === 'H') $mappedSexo = 'Hembra';
            else $mappedSexo = $sexo;

            // Format Date (Excel sometimes gives numbers or DD/MM/YYYY)
            // Just saving as string or parsing based on Sivar logic
            if (is_numeric($fecha)) {
                $fechaParsed = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fecha)->format('Y-m-d');
            } else {
                try {
                    $fechaParsed = Carbon::parse(str_replace('/', '-', $fecha))->format('Y-m-d');
                } catch (\Exception $e) {
                    $fechaParsed = $now->format('Y-m-d');
                }
            }

            $inserts[] = [
                'hcnda' => $vivero->hacienda,
                'fcha' => $fechaParsed,
                'lte' => $vivero->suerte,
                'prcla' => $excelParcela,
                'vrdad' => $excelVariedad,
                'flrcion' => $floracionTipo ?? 'Natural',
                'sxo' => $mappedSexo,
                'polen' => is_numeric($polen) ? (int)$polen : null,
                'vivero' => $vivero->identificador_unico,
                'id_smbra_cmpo' => $vivero->id, // Store reference
                'estado' => '0',
                'id_pr' => $vivero->proyecto_id,
                'usrio_edto' => auth()->id() ?? 1,
                'fcha_edto' => $now
            ];
        }

        DB::connection('sivar')->table('floracion')->insert($inserts);

        return response()->json(['success' => true, 'count' => count($inserts)]);
    }
}
