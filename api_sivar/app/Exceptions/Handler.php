<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (\Illuminate\Database\QueryException $e, $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                $sqlState = $e->errorInfo[0] ?? $e->getCode();
                if ($sqlState == '23505' || str_contains($e->getMessage(), '23505') || str_contains($e->getMessage(), 'unique constraint')) {
                    return response()->json([
                        'message' => 'Ya existe un registro con la misma información o identificador único.'
                    ], 422);
                }
                \Illuminate\Support\Facades\Log::error('Error de BD no controlado: ' . $e->getMessage());
                return response()->json([
                    'message' => 'Ocurrió un error en el procesamiento de la base de datos. Por favor verifique los datos enviados e intente de nuevo.'
                ], 500);
            }
        });
    }
}
