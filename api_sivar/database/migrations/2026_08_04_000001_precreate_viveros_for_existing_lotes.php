<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Lote;
use App\Models\Vivero;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $lotes = Lote::all();
        foreach ($lotes as $lote) {
            $capacidad = $lote->capacidad_maxima;
            $totalParcelas = $lote->total_parcelas_vivero ?: 10;

            // Get existing vivero numbers in this lote
            $existingNumbers = Vivero::withTrashed()
                ->where('lote_id', $lote->id)
                ->pluck('consecutivo_vivero_ingenio')
                ->toArray();

            for ($i = 1; $i <= $capacidad; $i++) {
                if (!in_array($i, $existingNumbers)) {
                    // Generate unique identifier
                    $ingenio = $lote->ingenio_codigo ?: '00';
                    $hacienda = $lote->hacienda_codigo ?: '00';
                    $suerte = $lote->nombre_lote ?: '00';
                    $anio = date('Y');
                    $identificador = sprintf('%s%s-%s-%s-%d', $ingenio, $anio, $hacienda, $suerte, $i);

                    $vivero = Vivero::create([
                        'identificador_unico' => $identificador,
                        'nombre' => "Vivero {$i}",
                        'ingenio' => $lote->ingenio_codigo,
                        'hacienda' => $lote->hacienda_codigo,
                        'suerte' => $lote->nombre_lote,
                        'lote_id' => $lote->id,
                        'fecha_siembra' => now()->format('Y-m-d'),
                        'consecutivo_vivero_ingenio' => $i,
                        'total_parcelas' => $totalParcelas
                    ]);

                    // Create default parcelas
                    for ($p = 1; $p <= $totalParcelas; $p++) {
                        DB::connection('sivar')
                            ->table('vivero_parcelas')
                            ->insert([
                                'vivero_id' => $vivero->id,
                                'numero_parcela' => $p,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
