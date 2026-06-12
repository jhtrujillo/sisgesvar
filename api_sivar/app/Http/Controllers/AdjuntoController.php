<?php

namespace App\Http\Controllers;

use App\Models\Adjunto;
use App\Models\Ensayo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AdjuntoController extends Controller
{
    /**
     * Get attachment list via JSON for the Slide-over panel (Lazy Load)
     */
    public function index(Ensayo $ensayo)
    {
        // Scope check just to be consistent (Optional but good practice)
        $user = auth('api')->user();
        if ($user->role !== 'JEFE' && is_array($user->ambiente) && count($user->ambiente) > 0) {
            if (!in_array($ensayo->amb_seleccion, $user->ambiente)) {
                return response()->json(['error' => 'Sin permisos'], 403);
            }
        }

        $adjuntos = $ensayo->adjuntos()->with('user:id,name')->latest()->get();

        return response()->json($adjuntos);
    }

    /**
     * Upload new physical file securely
     */
    public function store(Request $request, Ensayo $ensayo)
    {
        $request->validate([
            'file' => 'required|file|max:15360', // Max 15MB limits
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $mime = $file->getMimeType();
        $size = $file->getSize();

        // Store securely in private disk (storage/app/private/adjuntos)
        $path = $file->store('adjuntos'); 

        $adjunto = Adjunto::create([
            'ensayo_id' => $ensayo->id,
            'nombre_archivo' => $originalName,
            'ruta' => $path,
            'mime_type' => $mime,
            'size' => $size,
            'user_id' => Auth::id()
        ]);

        // Log auditing activity
        $nombreAMostrar = !empty(trim((string)$ensayo->nombre_ensayo)) 
            ? $ensayo->nombre_ensayo 
            : "Serie: " . ($ensayo->serie ?? 'S/N') . " (ID: #{$ensayo->id})";

        \App\Models\Actividad::registrar(
            'SUBIDA_ADJUNTO',
            "Adjuntó el archivo '{$originalName}' en el ensayo '{$nombreAMostrar}'.",
            ['ensayo_id' => $ensayo->id, 'nombre_archivo' => $originalName, 'mime' => $mime]
        );

        return response()->json([
            'message' => 'Archivo subido correctamente.',
            'adjunto' => $adjunto->load('user:id,name')
        ]);
    }

    /**
     * Stream secure file download
     */
    public function download(Adjunto $adjunto)
    {
        if (!Storage::exists($adjunto->ruta)) {
            abort(404, 'El archivo físico no existe en el servidor.');
        }

        return Storage::download($adjunto->ruta, $adjunto->nombre_archivo);
    }

    /**
     * Remove attachment physically and logically
     */
    public function destroy(Adjunto $adjunto)
    {
        $user = auth('api')->user();

        // Ensure user owns the file or is Jefe/Admin
        if ($user->role !== 'JEFE' && $adjunto->user_id !== $user->id) {
            return response()->json(['error' => 'No tienes permisos para eliminar este archivo.'], 403);
        }

        $ensayo = $adjunto->ensayo;
        $nombre = $adjunto->nombre_archivo;

        // 1. Delete from Disk
        if (Storage::exists($adjunto->ruta)) {
            Storage::delete($adjunto->ruta);
        }

        // 2. Delete DB Row
        $adjunto->delete();

        // Log auditing activity
        $nombreAMostrar = !empty(trim((string)$ensayo->nombre_ensayo)) 
            ? $ensayo->nombre_ensayo 
            : "Serie: " . ($ensayo->serie ?? 'S/N') . " (ID: #{$ensayo->id})";

        \App\Models\Actividad::registrar(
            'ELIMINACION_ADJUNTO',
            "Eliminó el archivo adjunto '{$nombre}' del ensayo '{$nombreAMostrar}'.",
            ['ensayo_id' => $ensayo->id, 'nombre_archivo' => $nombre]
        );

        return response()->json(['message' => 'Archivo eliminado con éxito.']);
    }
}
