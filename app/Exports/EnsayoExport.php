<?php

namespace App\Exports;

use App\Models\Ensayo;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EnsayoExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    use Exportable;

    protected $filters;
    protected $user;

    public function __construct($filters, $user)
    {
        $this->filters = $filters;
        $this->user = $user;
    }

    public function query()
    {
        $query = Ensayo::query();

        // 1. Mandatory Security & Scope Protection (Replicate controller logic)
        if ($this->user->role !== 'JEFE') {
            if (is_array($this->user->ambiente) && count($this->user->ambiente) > 0) {
                $query->whereIn('amb_seleccion', $this->user->ambiente);
            } else {
                $query->where('user_id', $this->user->id);
            }
        }

        // 2. Apply user's dynamic filters
        if (!empty($this->filters['search'])) {
            $s = $this->filters['search'];
            $query->where(function($q) use ($s) {
                $q->where('nombre_ensayo', 'ilike', "%{$s}%")
                  ->orWhere('proyecto', 'ilike', "%{$s}%")
                  ->orWhere('ingenio', 'ilike', "%{$s}%")
                  ->orWhere('hacienda', 'ilike', "%{$s}%")
                  ->orWhere('serie', 'ilike', "%{$s}%");
            });
        }

        if (!empty($this->filters['ambiente'])) {
            $query->where('amb_seleccion', $this->filters['ambiente']);
        }

        if (!empty($this->filters['ingenio'])) {
            $query->where('ingenio', $this->filters['ingenio']);
        }

        if (!empty($this->filters['proyecto'])) {
            $query->where('proyecto', $this->filters['proyecto']);
        }

        if (!empty($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
        }

        return $query->latest('id');
    }

    /**
     * Define standard technical column headings mirroring the imported layout
     */
    public function headings(): array
    {
        return [
            'ID BD',
            'Nombre Ensayo',
            'Nombre Env',
            'Proyecto',
            'Estado Selección',
            'Serie',
            'Amb Seleccion',
            'Amb Evaluacion',
            'Objetivo',
            'Ingenio',
            'Hacienda',
            'Suerte',
            'ZA',
            'CS',
            'Corte',
            'Entradas',
            'Checks (Testigos)',
            'NoClones',
            'Plots (Parcelas)',
            'Diseño',
            'NSurcosPlot',
            'LonSurco',
            'LonSurcoCallejon',
            'DistEntreSurco',
            'AreaEnsayo',
            'RMA',
            'FS (Siembra)',
            'FC (Cosecha Final)',
            'FEval (Evaluación)',
            'MDS (Meses Eval)',
            'FC Programada',
            'Edad Actual',
            'Año',
            'Mes',
            'Tipo Cosecha',
            'Comentario',
            'Nombre Archivo Original',
            'Nombre Ensayo2 (Reporte)',
            'Investigador',
            'Fecha Registro Sistema'
        ];
    }

    /**
     * Safely map and format every entity row
     */
    public function map($ensayo): array
    {
        return [
            $ensayo->id,
            $ensayo->nombre_ensayo,
            $ensayo->nombre_env,
            $ensayo->proyecto,
            $ensayo->estado_seleccion,
            $ensayo->serie,
            $ensayo->amb_seleccion,
            $ensayo->amb_evaluacion,
            $ensayo->objetivo,
            $ensayo->ingenio,
            $ensayo->hacienda,
            $ensayo->suerte,
            $ensayo->zona_agroecologia,
            $ensayo->consociacion,
            $ensayo->corte,
            $ensayo->entradas,
            $ensayo->testigos,
            $ensayo->clones,
            $ensayo->total_parcelas,
            $ensayo->diseno,
            $ensayo->surcos,
            $ensayo->longitud_surco,
            $ensayo->longitud_callejon,
            $ensayo->distancia_surco,
            $ensayo->area_total,
            $ensayo->red_meteorologica,
            $ensayo->fecha_siembra ? substr($ensayo->fecha_siembra, 0, 10) : '',
            $ensayo->fecha_cosecha_final ? substr($ensayo->fecha_cosecha_final, 0, 10) : '',
            $ensayo->fecha_evaluacion ? substr($ensayo->fecha_evaluacion, 0, 10) : '',
            $ensayo->meses_evaluacion,
            $ensayo->fecha_cosecha_programada ? substr($ensayo->fecha_cosecha_programada, 0, 10) : '',
            $ensayo->estado_actual,
            $ensayo->ano_siembra,
            $ensayo->mes_siembra,
            $ensayo->tipo_cosecha,
            $ensayo->comentarios,
            $ensayo->ubicacion_fisica,
            $ensayo->nombre_reporte,
            $ensayo->investigador,
            $ensayo->created_at ? $ensayo->created_at->format('Y-m-d H:i:s') : ''
        ];
    }

    /**
     * Apply premium spreadsheet styling
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF065F46'] // Emerald 800 - Premium Green
                ],
            ],
        ];
    }
}
