<?php

namespace App\Http\Controllers;

use App\Models\Vivero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ViveroController extends Controller
{
    public function index()
    {
        $viveros = Vivero::with(['proyecto', 'responsable', 'caracter'])->orderBy('created_at', 'desc')->get();
        foreach ($viveros as $vivero) {
            $vivero->id_vivero_origen_formateado = $this->formatIdViveroOrigen($vivero);
        }
        return response()->json($viveros);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'fecha_siembra' => 'required|date',
            'origen_ingenio' => 'nullable|string',
            'origen_hacienda' => 'nullable|string',
            'origen_suerte' => 'nullable|string',
            'origen_anio' => 'nullable|integer',
            'origen_parcela' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Calculamos el consecutivo basándonos en la cantidad total de registros en la tabla
        $maxId = Vivero::withTrashed()->max('id') ?? 0;
        $consecutivo = $maxId + 1;

        $identificador = $this->generarIdentificadorUnico(
            $request->ingenio,
            $request->hacienda,
            $request->suerte,
            $request->fecha_siembra,
            $consecutivo
        );

        $vivero = Vivero::create([
            'identificador_unico' => $identificador,
            'nombre' => $request->nombre,
            'ingenio' => $request->ingenio,
            'hacienda' => $request->hacienda,
            'suerte' => $request->suerte,
            'proyecto_id' => $request->proyecto_id,
            'ambiente' => $request->ambiente,
            'responsable_id' => $request->responsable_id,
            'fecha_siembra' => $request->fecha_siembra,
            'numero_corte' => $request->numero_corte ?? 1,
            'temporada_floracion' => $request->temporada_floracion,
            'condicion' => $request->condicion,
            'caracter_id' => $request->caracter_id,
            'origen_ingenio' => $request->origen_ingenio,
            'origen_hacienda' => $request->origen_hacienda,
            'origen_suerte' => $request->origen_suerte,
            'origen_anio' => $request->origen_anio,
            'origen_parcela' => $request->origen_parcela,
        ]);

        return response()->json($vivero, 201);
    }

    public function show($id)
    {
        $vivero = Vivero::with(['proyecto', 'responsable', 'caracter'])->findOrFail($id);
        $vivero->id_vivero_origen_formateado = $this->formatIdViveroOrigen($vivero);
        return response()->json($vivero);
    }

    public function update(Request $request, $id)
    {
        $vivero = Vivero::findOrFail($id);
        
        $vivero->fill($request->except('identificador_unico'));
        
        // Always regenerate the identificador_unico using the helper method on update
        $parts = explode('-', $vivero->identificador_unico);
        $consecutivo = end($parts);
        if (!is_numeric($consecutivo) || intval($consecutivo) <= 0) {
            $consecutivo = $vivero->id;
        }
        $vivero->identificador_unico = $this->generarIdentificadorUnico(
            $vivero->ingenio,
            $vivero->hacienda,
            $vivero->suerte,
            $vivero->fecha_siembra,
            $consecutivo
        );
        
        $vivero->save();

        return response()->json($vivero);
    }

    public function destroy($id)
    {
        $vivero = Vivero::findOrFail($id);
        $vivero->delete();
        return response()->json(null, 204);
    }

    public function registrarCosecha(Request $request, $id)
    {
        $request->validate([
            'fecha_cosecha' => 'required|date',
            'nueva_fecha_siembra' => 'required|date',
        ]);

        $vivero = Vivero::findOrFail($id);

        // Guardar el registro histórico de la cosecha
        $vivero->cosechas()->create([
            'fecha_cosecha' => $request->fecha_cosecha,
            'nueva_fecha_siembra' => $request->nueva_fecha_siembra,
            'numero_corte_anterior' => $vivero->numero_corte,
        ]);

        // Actualizar el vivero para el nuevo ciclo
        $vivero->numero_corte += 1;
        $vivero->fecha_siembra = $request->nueva_fecha_siembra;

        // HU-003: Actualizar identificador_unico con el sufijo de corte
        $identificadorBase = preg_replace('/-C?\d+$/', '', $vivero->identificador_unico);
        $vivero->identificador_unico = $identificadorBase . '-' . $vivero->numero_corte;

        $vivero->save();

        return response()->json([
            'message' => 'Cosecha registrada y vivero actualizado correctamente',
            'vivero' => $vivero
        ]);
    }

    public function getHistorialCosechas($id)
    {
        $vivero = Vivero::findOrFail($id);
        $historial = $vivero->cosechas()->orderBy('created_at', 'desc')->get();
        return response()->json($historial);
    }

    public function getIngenios()
    {
        $ingenios = DB::connection('sivar')->table('remote_pg_ingenios')->get();
        return response()->json($ingenios);
    }

    public function getHaciendas($ingenio)
    {
        $haciendas = DB::connection('sivar')->table('remote_pg_hacienda')
            ->where('cd_ingnio', $ingenio)
            ->get();
        return response()->json($haciendas);
    }

    public function getSuertes($hacienda)
    {
        $suertes = DB::connection('sivar')->table('remote_pg_suerte')
            ->where('cd_hcnda', $hacienda)
            ->get();
        return response()->json($suertes);
    }

    public function getProyectos()
    {
        $proyectos = \App\Models\Proyecto::where('id_area_trbjo', 5)->get();
        return response()->json($proyectos);
    }

    public function getResponsables()
    {
        // 1 = VARIEDADES, 5 = Mejoramiento Genético
        $usuarios = \App\Models\User::where('id_area', 1)
                                      ->where('id_area_trbjo', 5)
                                      ->where('estdo', '0') // Asumiendo que 0 es activo, u omitir si todos valen
                                      ->get();

        // Si la tabla usuario no tiene id_area_trbjo poblado correctamente en todos, 
        // y quieres asegurar traerlos:
        if ($usuarios->isEmpty()) {
             $usuarios = \App\Models\User::where('id_area', 1)->get();
        }

        return response()->json($usuarios);
    }

    public function getAmbientes()
    {
        $ambientes = DB::connection('sivar')->table('mega_ambiente')->get();
        return response()->json($ambientes);
    }

    public function getCaracteresPorProyecto($id)
    {
        $caracteres = DB::table('proyecto_caracteres')->where('proyecto_id', $id)->get();
        return response()->json($caracteres);
    }

    public function storeCaracter(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $exists = DB::table('proyecto_caracteres')
            ->where('proyecto_id', $id)
            ->whereRaw('LOWER(nombre) = ?', [strtolower($request->nombre)])
            ->first();

        if ($exists) {
            return response()->json($exists, 200);
        }

        $newId = DB::table('proyecto_caracteres')->insertGetId([
            'proyecto_id' => $id,
            'nombre' => $request->nombre,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $caracter = DB::table('proyecto_caracteres')->where('id', $newId)->first();
        return response()->json($caracter, 201);
    }

    private function generarIdentificadorUnico($ingenioCd, $haciendaCd, $suerteCd, $fechaSiembra, $consecutivo)
    {
        $ingenio = $ingenioCd ?: '00';
        $hacienda = $haciendaCd ?: '00';
        $suerte = $suerteCd ?: '00';
        $anioSiembra = $fechaSiembra ? date('Y', strtotime($fechaSiembra)) : date('Y');

        return sprintf('%s%s-%s-%s-%d', $ingenio, $anioSiembra, $hacienda, $suerte, $consecutivo);
    }

    private function formatIdViveroOrigen($vivero)
    {
        // Si tiene origen_parcela con formato de ID de parcela de Vivero (ej: CN2026-00EESA-00023D-2-4)
        if ($vivero->origen_parcela && count(explode('-', $vivero->origen_parcela)) > 3) {
            $parts = explode('-', $vivero->origen_parcela);
            $lastPart = end($parts);
            if (is_numeric($lastPart)) {
                array_pop($parts); // Remover número de parcela
                return implode('-', $parts); // Retorna el Vivero ID padre
            }
        }

        // Si no (origen manual/externo), construimos el ID usando los códigos de la base de datos
        $info = [];
        $ingenioAnio = '';
        if ($vivero->origen_ingenio) {
            $ingenioAnio .= $vivero->origen_ingenio;
        }
        if ($vivero->origen_anio) {
            $ingenioAnio .= $vivero->origen_anio;
        }
        if ($ingenioAnio !== '') {
            $info[] = $ingenioAnio;
        }
        if ($vivero->origen_hacienda) $info[] = $vivero->origen_hacienda;
        if ($vivero->origen_suerte) $info[] = $vivero->origen_suerte;
        if ($vivero->origen_parcela) $info[] = $vivero->origen_parcela;

        return count($info) > 0 ? implode('-', $info) : 'N/A';
    }
}
