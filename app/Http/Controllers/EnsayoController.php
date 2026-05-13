<?php

namespace App\Http\Controllers;

use App\Models\Ensayo;
use App\Imports\EnsayoImport;
use App\Exports\EnsayoExport;
use Illuminate\Http\Request;
use App\Http\Requests\StoreEnsayoRequest;
use App\Http\Requests\ConfirmImportEnsayoRequest;
use App\Http\Requests\UpdateCellEnsayoRequest;
use App\Http\Requests\ExecuteStandardizationRequest;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class EnsayoController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Ensayo::query()->with('user');

        // Mandatory Authorization Scoping
        if ($user->role !== 'JEFE') {
            // If Líder, restrict to their specific permitted environments
            if (is_array($user->ambiente) && count($user->ambiente) > 0) {
                $query->whereIn('amb_seleccion', $user->ambiente);
            } else {
                $query->where('user_id', $user->id);
            }
        }

        // Handle simple search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nombre_ensayo', 'ilike', '%' . $request->search . '%')
                  ->orWhere('ingenio', 'ilike', '%' . $request->search . '%')
                  ->orWhere('proyecto', 'ilike', '%' . $request->search . '%');
            });
        }

        // Handle direct Ambiente filter
        if ($request->ambiente) {
            $query->where('amb_seleccion', $request->ambiente);
        }

        // Handle direct User filter
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Handle dynamic pagination limit
        $perPage = $request->input('per_page', 10);

        // Handle Dynamic Sorting
        $sortBy = $request->input('sort_by', 'id');
        $sortDir = strtolower($request->input('sort_direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        // Safety whitelist to prevent SQL injection on ORDER BY
        if (\Illuminate\Support\Facades\Schema::hasColumn('ensayos', $sortBy)) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->orderBy('id', 'asc'); // fallback permanent anchor
        }

        // 1. Fetch Values from Static Catalogs Master
        $staticCatalogs = \App\Models\Catalogo::all()->groupBy('categoria');

        // 2. Fetch Dynamic Values present in existing Ensayos records
        $existingProy = Ensayo::distinct('proyecto')->pluck('proyecto')->filter()->toArray();
        $existingIng = Ensayo::distinct('ingenio')->pluck('ingenio')->filter()->toArray();
        $existingAmb = Ensayo::distinct('amb_seleccion')->pluck('amb_seleccion')->filter()->toArray();

        // 3. Merge and finalize Master Set sent to frontend
        $catalogos = [
            'PROYECTO' => collect($staticCatalogs->get('PROYECTO')?->pluck('valor')->toArray() ?? [])
                ->concat($existingProy)->map(fn($v) => strtoupper(trim($v)))->filter()->unique()->sort()->values()->all(),
            
            'INGENIO' => collect($staticCatalogs->get('INGENIO')?->pluck('valor')->toArray() ?? [])
                ->concat($existingIng)->map(fn($v) => strtoupper(trim($v)))->filter()->unique()->sort()->values()->all(),
            
            'AMBIENTE' => collect($staticCatalogs->get('AMBIENTE')?->pluck('valor')->toArray() ?? [])
                ->concat($existingAmb)->map(fn($v) => strtoupper(trim($v)))->filter()->unique()->sort()->values()->all(),
        ];

        $usersList = \App\Models\User::select('id', 'name')->orderBy('name')->get();

        return Inertia::render('RegistroEnsayos/Index', [
            'ensayos' => $query->withCount('adjuntos')->paginate($perPage)->withQueryString(),
            'filters' => $request->only(['search', 'per_page', 'sort_by', 'sort_direction', 'ambiente', 'user_id']),
            'catalogos' => $catalogos,
            'users_list' => $usersList
        ]);
    }

    /**
     * Process and stream a dynamic Excel file mirroring active filters and grids
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $filters = $request->only(['search', 'ambiente', 'user_id']);

        $filename = 'SIVAR_Ensayos_Export_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new EnsayoExport($filters, $user), $filename);
    }

    public function store(StoreEnsayoRequest $request)
    {
        $file = $request->file('file');
        $ambiente = $request->ambiente;

        $dups = $this->findDuplicateEnsayos($file);
        if (!empty($dups)) {
            throw \Illuminate\Validation\ValidationException::withMessages(['file' => 'NO SE PUEDE DUPLICAR INFORMACIÓN. Los siguientes ensayos ya existen en la base de datos (con ortografía similar): ' . implode(', ', $dups)]);
        }

        try {
            // Step 1: Load full array into memory for fast structural review
            $array = Excel::toArray(new EnsayoImport, $file);
            $rows = $array[0] ?? []; 

            $categories = [
                'PROYECTO' => ['proyecto'],
                'INGENIO'  => ['ingenio'],
                'AMBIENTE' => ['amb_seleccion', 'amb_evaluacion']
            ];

            $allConflicts = [];
            $fullCatalogo = [];

            foreach ($categories as $catName => $excelKeys) {
                // Gather unique trimmed values from ALL defined keys for this category
                $excelVals = collect();
                foreach ($excelKeys as $key) {
                    $excelVals = $excelVals->merge(collect($rows)->pluck($key));
                }
                $excelVals = $excelVals->filter()->unique()->map(fn($x) => trim($x))->values();

                // Fetch master Catalogo values from Database
                $dbVals = \App\Models\Catalogo::where('categoria', $catName)->pluck('valor')->toArray();

                // Enrich corrections dictionary with both Database items AND unique Excel items
                $combinedVals = collect(array_merge($dbVals, $excelVals->toArray()))
                    ->unique()
                    ->sort()
                    ->values()
                    ->toArray();
                
                $fullCatalogo[$catName] = $combinedVals;

                // Find those missing in master Catalogo
                $conflicts = $excelVals->reject(fn($v) => in_array($v, $dbVals))->values()->toArray();
                
                if (!empty($conflicts)) {
                    $allConflicts[$catName] = $conflicts;
                }
            }

            // If there are ANY conflict values in ANY category, prompt user mapping
            if (count($allConflicts) > 0) {
                $tempPath = $file->store('temp_imports');
                
                return Inertia::render('RegistroEnsayos/Homologacion', [
                    'conflicts' => $allConflicts,
                    'catalogo' => $fullCatalogo,
                    'tempPath' => $tempPath,
                    'ambiente' => $ambiente,
                ]);
            }

            // Clean execution if no conflicts
            Excel::import(new EnsayoImport($ambiente), $file);
            
            return redirect()->route('ensayos.index')
                ->with('success', 'Registros del Ensayo importados y validados exitosamente.');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Endpoint to process the resolved mappings and final insertion
     */
    public function confirmImport(ConfirmImportEnsayoRequest $request)
    {
        try {
            // Update Catalogo for any '__NEW__' designations nested by category
            foreach ($request->mappings as $category => $subMappings) {
                if (!is_array($subMappings)) continue;
                
                foreach ($subMappings as $oldVal => $target) {
                    if ($target === '__NEW__') {
                        \App\Models\Catalogo::firstOrCreate([
                            'categoria' => $category,
                            'valor' => $oldVal
                        ]);
                    }
                }
            }

            $realPath = \Illuminate\Support\Facades\Storage::path($request->tempPath);

            // FINAL GATE: Ensure no one concurrently pushed duplicates during homologation wait time
            $dups = $this->findDuplicateEnsayos($realPath);
            if (!empty($dups)) {
                throw \Illuminate\Validation\ValidationException::withMessages(['file' => 'BLOQUEO CRÍTICO: Mientras homologabas, estos ensayos ya fueron subidos al sistema. Elimínalos del archivo: ' . implode(', ', $dups)]);
            }
            
            // Custom Importer that applies final mappings
            Excel::import(new EnsayoImport($request->ambiente, $request->mappings), $realPath);

            // Cleanup temp file
            \Illuminate\Support\Facades\Storage::delete($request->tempPath);

            // Log activity audit trail
            \App\Models\Actividad::registrar(
                'IMPORTACION',
                "Importó exitosamente un lote de Ensayos desde Excel para el ambiente '{$request->ambiente}'.",
                ['ambiente' => $request->ambiente]
            );

            return redirect()->route('ensayos.index')
                ->with('success', 'Registros homologados e insertados correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('ensayos.index')->withErrors(['file' => 'Falla en paso final: ' . $e->getMessage()]);
        }
    }

    /**
     * Handle single field inline editing from the UI data grid.
     */
    public function update(UpdateCellEnsayoRequest $request, Ensayo $ensayo)
    {
        // DEBUG LOGGING to track what user passes
        file_put_contents(base_path('storage/logs/debug_save.log'), "[" . date('Y-m-d H:i:s') . "] Request: " . json_encode($request->all()) . " Ensayo ID: " . $ensayo->id . "\n", FILE_APPEND);

        try {
            $field = $request->field;
            // Sanitize empty inputs to NULL to prevent SQL strict typing failures (especially on Postgres numbers/dates)
            $value = ($request->value === '' || $request->value === null) ? null : $request->value;

            // Define Catalog Mappings for auto-registration
            $catalogMap = [
                'proyecto' => 'PROYECTO',
                'ingenio' => 'INGENIO',
                'amb_seleccion' => 'AMBIENTE',
                'amb_evaluacion' => 'AMBIENTE',
            ];

            // --- 🧠 INTELIGENCIA DE DATOS EN TIEMPO REAL: PROTECCIÓN DE CATÁLOGOS ---
            if (array_key_exists($field, $catalogMap) && !empty(trim((string)$value))) {
                $catName = $catalogMap[$field];
                $trimmedVal = trim((string)$value);

                // 🔍 Paso A: Búsqueda Exacta en el Catálogo Oficial
                $exactMatch = \App\Models\Catalogo::where('categoria', $catName)
                    ->where('valor', $trimmedVal)
                    ->first();

                if ($exactMatch) {
                    // Es exactamente oficial. Alineamos mayúsculas/minúsculas de forma segura.
                    $value = $exactMatch->valor;
                } else {
                    // El término no es idéntico. Revisamos si viene una instrucción explícita del frontend.
                    $decision = $request->input('decision_catalogo'); // Puede ser: 'USE_SUGGESTED' o 'CREATE_NEW'
                    $sugerencia = $request->input('suggested_value');

                    if ($decision === 'USE_SUGGESTED' && !empty($sugerencia)) {
                        // El usuario decidió no agregar nada y en su lugar usar la sugerencia existente.
                        $value = $sugerencia;
                    } elseif ($decision === 'CREATE_NEW') {
                        // El usuario confirmó explícitamente que desea crear un término totalmente nuevo.
                        \App\Models\Catalogo::firstOrCreate([
                            'categoria' => $catName,
                            'valor' => $trimmedVal
                        ]);
                        $value = $trimmedVal;
                    } else {
                        // --- 🛑 INTERROGATORIO DE SEGURIDAD (Pausa de Transacción) ---
                        // Buscamos coincidencias fonéticas/fuzzy para aconsejar al usuario.
                        $normInput = $this->normalizeString($trimmedVal);
                        $allCatItems = \App\Models\Catalogo::where('categoria', $catName)->get();
                        
                        $fuzzyMatch = null;
                        foreach ($allCatItems as $item) {
                            if ($this->normalizeString($item->valor) === $normInput) {
                                $fuzzyMatch = $item;
                                break;
                            }
                        }

                        if ($fuzzyMatch) {
                            // 🔔 CASO 1: Encontramos algo parecido! Ofrecemos tres caminos al usuario.
                            return response()->json([
                                'dialog_needed' => true,
                                'dialog_type' => 'FUZZY_MATCH',
                                'valor_propuesto' => $trimmedVal,
                                'valor_sugerido' => $fuzzyMatch->valor,
                                'categoria' => $catName,
                                'field' => $field
                            ]);
                        } else {
                            // 🔔 CASO 2: No se parece a nada. Ofrecemos crear o abortar.
                            return response()->json([
                                'dialog_needed' => true,
                                'dialog_type' => 'NEW_TERM',
                                'valor_propuesto' => $trimmedVal,
                                'categoria' => $catName,
                                'field' => $field
                            ]);
                        }
                    }
                }
            }

            $oldValue = $ensayo->$field;

            // Update the model dynamically
            $ensayo->update([
                $field => $value
            ]);

            // Determinamos un nombre amigable e identificable para la bitácora
            $nombreAMostrar = !empty(trim((string)$ensayo->nombre_ensayo)) 
                ? $ensayo->nombre_ensayo 
                : "Serie: " . ($ensayo->serie ?? 'S/N') . " (ID: #{$ensayo->id})";

            // Log detailed activity
            \App\Models\Actividad::registrar(
                'EDICION_CELDA',
                "Modificó el campo '{$field}' en el ensayo '{$nombreAMostrar}' (cambió '{$oldValue}' por '{$value}').",
                [
                    'ensayo_id' => $ensayo->id,
                    'campo' => $field,
                    'antes' => $oldValue,
                    'ahora' => $value
                ]
            );

            file_put_contents(base_path('storage/logs/debug_save.log'), "[" . date('Y-m-d H:i:s') . "] SUCCESS Saved\n", FILE_APPEND);

            return redirect()->back()->with('success', 'Registro actualizado con éxito.');
        } catch (\Exception $e) {
            file_put_contents(base_path('storage/logs/debug_save.log'), "[" . date('Y-m-d H:i:s') . "] ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
            throw $e; // Re-throw to let Laravel handle redirect, but we logged it!
        }
    }

    /**
     * Core Logic for Normalizing Strings (Accents out, lowercase, strip symbols)
     * to support robust duplicate detection.
     */
    private function normalizeString($str)
    {
        $str = mb_strtolower(trim((string)$str), 'UTF-8');
        $search  = ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ä', 'ë', 'ï', 'ö', 'ü'];
        $replace = ['a', 'e', 'i', 'o', 'u', 'n', 'a', 'e', 'i', 'o', 'u'];
        $str = str_replace($search, $replace, $str);
        return preg_replace('/[^a-z0-9]/', '', $str);
    }

    /**
     * Scans a file and cross-references existing DB records using fuzzy normalization
     * logic to identify potential duplicates before persistence attempts.
     */
    private function findDuplicateEnsayos($file)
    {
        // Handle path string or real object
        $array = \Maatwebsite\Excel\Facades\Excel::toArray(new \App\Imports\EnsayoImport, $file);
        $rows = $array[0] ?? [];
        
        // DEBUG LOG: Dump what keys come out of toArray
        file_put_contents(base_path('storage/logs/debug_excel_keys.log'), "[" . date('Y-m-d H:i:s') . "] Count: " . count($rows) . " | Sample[nombre_ensayo]: " . json_encode($rows[0]['nombre_ensayo'] ?? 'NULL') . " | Keys: " . json_encode(array_keys((array)($rows[0] ?? []))) . "\n", FILE_APPEND);

        if (empty($rows)) return [];

        // Read and dynamically construct name if blank
        $excelEnsayos = collect($rows)
            ->map(function($r) {
                // 1. Check standard mapped key
                $original = trim($r['nombre_ensayo'] ?? '');
                
                // Fallback: Check other key names
                if (!$original) {
                    $keys = array_keys((array)$r);
                    foreach($keys as $k) {
                        if (str_contains(strtolower($k), 'ensayo') && !str_contains(strtolower($k), 'reporte')) {
                            $original = trim($r[$k] ?? '');
                            if ($original) break;
                        }
                    }
                }

                // 2. If STILL BLANK, fully reconstruct it from parts to match reality
                if (!$original) {
                    $ing = trim($r['ingenio'] ?? '');
                    $hac = trim($r['hacienda'] ?? '');
                    $sur = trim($r['suerte'] ?? '');
                    
                    // Try to get year from row or from FS date
                    $yr = trim($r['ano'] ?? '');
                    if (!$yr) {
                        $fs = $r['fs'] ?? null;
                        if (is_numeric($fs)) {
                            $yr = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fs)->format('Y');
                        } elseif ($fs && !is_numeric($fs)) {
                            $d = date_parse($fs);
                            if (!empty($d['year'])) $yr = $d['year'];
                        }
                    }

                    if (!empty($ing) && !empty($hac)) {
                        return "{$ing}{$yr}-{$hac}{$sur}";
                    }
                }

                return $original;
            })
            ->filter()
            ->unique()
            ->map(fn($x) => trim($x));
        
        if ($excelEnsayos->isEmpty()) return [];

        // Preload existing DB state and build matching hashes by mirroring the reconstruction logic
        $allDb = \App\Models\Ensayo::get(['nombre_ensayo', 'ingenio', 'hacienda', 'suerte', 'ano_siembra', 'fecha_siembra']);
        
        $normalizedDb = [];
        foreach ($allDb as $ens) {
            // Priority 1: Use existing string from DB if populated
            $token = trim($ens->nombre_ensayo ?? '');
            
            // Priority 2: Reconstruct runtime canonical signature just like Excel validator
            if (!$token) {
                $yr = $ens->ano_siembra ?: ($ens->fecha_siembra ? \Carbon\Carbon::parse($ens->fecha_siembra)->format('Y') : '');
                if (!empty($ens->ingenio) && !empty($ens->hacienda)) {
                    $token = "{$ens->ingenio}{$yr}-{$ens->hacienda}{$ens->suerte}";
                }
            }
            
            $clean = $this->normalizeString($token);
            if (!empty($clean)) {
                $normalizedDb[$clean] = $token; // Link to raw token for report display
            }
        }

        $hits = [];
        foreach ($excelEnsayos as $candidate) {
            $normCandidate = $this->normalizeString($candidate);
            if (isset($normalizedDb[$normCandidate])) {
                $hits[] = "'{$candidate}' (ya existe como '{$normalizedDb[$normCandidate]}')";
            }
        }

        return array_unique($hits);
    }

    /**
     * 🧠 SMART DATA STANDARDIZATION: Scans and groups historical trial inconsistencies
     * using the existing high-confidence fuzzy string normalizer.
     */
    public function standardizationPreview(\Illuminate\Http\Request $request)
    {
        // 1. Map and Cache all Master Catalogs with pre-normalized lookups
        $allMasters = \App\Models\Catalogo::select('id', 'categoria', 'valor')->get()->groupBy('categoria');
        
        $masterMap = []; // categoria => [ normalized_key => [ 'id' => X, 'valor' => 'Official' ] ]
        $officialVals = []; // categoria => [ Array of clean exact casing official values ]
        
        foreach ($allMasters as $cat => $items) {
            $masterMap[$cat] = [];
            $officialVals[$cat] = [];
            foreach ($items as $item) {
                $val = trim($item->valor);
                $officialVals[$cat][] = $val;
                
                $norm = $this->normalizeString($val);
                if (!empty($norm)) {
                    $masterMap[$cat][$norm] = [
                        'id' => $item->id,
                        'valor' => $val
                    ];
                }
            }
        }

        // 2. Target Fields loop to hunt for database orphans
        $cols = [
            'proyecto' => 'PROYECTO',
            'ingenio' => 'INGENIO',
            'amb_seleccion' => 'AMBIENTE',
            'amb_evaluacion' => 'AMBIENTE'
        ];

        $autoFixes = [];
        $manualFixes = [];

        foreach ($cols as $field => $cat) {
            $distincts = \App\Models\Ensayo::select($field, \Illuminate\Support\Facades\DB::raw('count(*) as total'))
                ->whereNotNull($field)
                ->where($field, '<>', '')
                ->groupBy($field)
                ->get();

            foreach ($distincts as $row) {
                $valRaw = trim($row->$field);
                $total = $row->total;

                // Filter 1: If the entry exactly matches case-sensitive official, Skip!
                if (in_array($valRaw, $officialVals[$cat] ?? [])) {
                    continue;
                }

                // Run fuzzy matching
                $norm = $this->normalizeString($valRaw);

                if (isset($masterMap[$cat][$norm])) {
                    // Confirmed Intelligent Auto-Match (E.g. Humedo -> Húmedo, Piedemonte -> Pie de monte)
                    $target = $masterMap[$cat][$norm];
                    $autoFixes[] = [
                        'field' => $field,
                        'categoria' => $cat,
                        'valor_origen' => $valRaw,
                        'valor_destino' => $target['valor'],
                        'total' => $total,
                        'confidence' => 'ALTA'
                    ];
                } else {
                    // Manual discrepancy: System doesn't have a high confidence guess
                    $manualFixes[] = [
                        'field' => $field,
                        'categoria' => $cat,
                        'valor_origen' => $valRaw,
                        'total' => $total,
                        'opciones' => array_values($masterMap[$cat] ?? [])
                    ];
                }
            }
        }

        return response()->json([
            'auto_fixes' => $autoFixes,
            'manual_fixes' => $manualFixes
        ]);
    }

    /**
     * Executes a bulk transaction to re-map standardized items across all historical records.
     */
    public function standardizationExecute(ExecuteStandardizationRequest $request)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $totalAfectado = 0;
            $logDetalle = [];

            foreach ($request->correcciones as $c) {
                $field = $c['field'];
                $from = $c['valor_origen'];
                $to = $c['valor_destino'];

                // Security Guard: Whitelist of mutating columns
                if (!in_array($field, ['proyecto', 'ingenio', 'amb_seleccion', 'amb_evaluacion'])) {
                    continue;
                }

                $afectados = \App\Models\Ensayo::where($field, $from)->update([$field => $to]);
                $totalAfectado += $afectados;
                
                $logDetalle[] = "[Columna: {$field}] Se corrigió '{$from}' a '{$to}' en {$afectados} fila(s).";
            }

            if ($totalAfectado > 0) {
                \App\Models\Actividad::registrar(
                    'ESTANDARIZACION_MASIVA',
                    "Estandarización masiva completada. Se corrigieron exitosamente {$totalAfectado} celdas históricas.",
                    ['detalle_cambios' => $logDetalle]
                );
            }

            \Illuminate\Support\Facades\DB::commit();
            return response()->json([
                'success' => true,
                'message' => "¡Proceso terminado! Se han estandarizado exitosamente {$totalAfectado} celdas en la base de datos.",
                'total_afectado' => $totalAfectado
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Falla crítica en el motor de estandarización: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display analytical dashboard specifically for Ensayo data.
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Strict Access Restriction Gate
        if (!in_array($user->role, ['JEFE', 'LIDER'])) {
            abort(403, 'Acceso restringido al panel de control.');
        }
        
        // Apply baseline Authorization Scoping
        $baseQuery = \App\Models\Ensayo::query();
        if ($user->role !== 'JEFE') {
            if (is_array($user->ambiente) && count($user->ambiente) > 0) {
                $baseQuery->whereIn('amb_seleccion', $user->ambiente);
            } else {
                $baseQuery->where('user_id', $user->id);
            }
        }

        $stats = [
            'total_ensayos' => (clone $baseQuery)->count(),
            'total_ingenios' => (clone $baseQuery)->distinct('ingenio')->count('ingenio'),
            
            'por_ambiente' => (function() use ($baseQuery, $user) {
                $counts = (clone $baseQuery)->select('amb_seleccion', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
                    ->whereNotNull('amb_seleccion')
                    ->groupBy('amb_seleccion')
                    ->pluck('total', 'amb_seleccion')->toArray();
                
                if ($user->role === 'JEFE') {
                    $masterAmb = \App\Models\Catalogo::where('categoria', 'AMBIENTE')->pluck('valor')->toArray();
                } else {
                    $masterAmb = is_array($user->ambiente) ? $user->ambiente : [];
                }
                
                $allAmb = array_unique(array_merge($masterAmb, array_keys($counts)));
                sort($allAmb);

                $result = [];
                foreach ($allAmb as $amb) {
                    $result[] = [
                        'amb_seleccion' => $amb,
                        'total' => $counts[$amb] ?? 0
                    ];
                }
                usort($result, fn($a, $b) => $b['total'] <=> $a['total']);
                return $result;
            })(),

            'por_ingenio' => (clone $baseQuery)->select('ingenio', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
                ->groupBy('ingenio')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get(),
            'por_ano' => (clone $baseQuery)->select('ano_siembra', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
                ->whereNotNull('ano_siembra')
                ->groupBy('ano_siembra')
                ->orderBy('ano_siembra', 'asc')
                ->get(),
            'recent_uploads' => (clone $baseQuery)->with('user')->latest()->limit(5)->get(['id', 'nombre_ensayo', 'proyecto', 'created_at', 'user_id'])
        ];

        return \Inertia\Inertia::render('RegistroEnsayos/Dashboard', [
            'stats' => $stats
        ]);
    }
}
