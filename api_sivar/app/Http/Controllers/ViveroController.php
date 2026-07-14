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
        return response()->json($viveros);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'fecha_siembra' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Generación del ID Único
        if ($request->identificador_unico) {
            $identificador = $request->identificador_unico;
        } else {
            $ingenio = $request->ingenio ?: '00';
            $hacienda = $request->hacienda ?: '00';
            $suerte = $request->suerte ?: '00';
            $anioSiembra = $request->fecha_siembra ? date('Y', strtotime($request->fecha_siembra)) : date('Y');
            
            // Calculamos el consecutivo basándonos en la cantidad total de registros en la tabla
            // Usamos max('id') o count(). Si hay eliminados (SoftDeletes),withTrashed()->count() o max('id') es más seguro.
            $maxId = Vivero::withTrashed()->max('id') ?? 0;
            $consecutivo = $maxId + 1;
            
            $identificador = sprintf('%s-%s-%s-%s-%d', $ingenio, $anioSiembra, $hacienda, $suerte, $consecutivo);
        }

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
        ]);

        return response()->json($vivero, 201);
    }

    public function show($id)
    {
        $vivero = Vivero::with(['proyecto', 'responsable', 'caracter'])->findOrFail($id);
        return response()->json($vivero);
    }

    public function update(Request $request, $id)
    {
        $vivero = Vivero::findOrFail($id);
        
        $vivero->update($request->all());

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
        $identificadorBase = preg_replace('/-C\d+$/', '', $vivero->identificador_unico);
        $vivero->identificador_unico = $identificadorBase . '-C' . $vivero->numero_corte;

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
}
