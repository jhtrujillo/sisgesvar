<?php

namespace App\Imports;

use App\Models\Ensayo;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Carbon\Carbon;

class EnsayoImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithCalculatedFormulas
{
    protected $ambiente;
    protected $mappings;

    public function __construct($ambiente = null, $mappings = [])
    {
        $this->ambiente = $ambiente;
        $this->mappings = $mappings;
    }

    /**
     * Return the heading row number.
     * Row 1 is meta, Row 2 are technical headers.
     */
    public function headingRow(): int
    {
        return 2;
    }

    /**
     * Safely convert excel serial date to standard PHP date
     */
    private function transformDate($value)
    {
        if (!$value) return null;
        
        try {
            if (is_numeric($value)) {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
            }
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Clean numeric values and apply rigid schema-based integer casting if required
        $cleanNumeric = function($val, $asInt = false) {
            if (is_null($val) || $val === '') return null;
            $num = filter_var($val, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            if ($num === "" || $num === false) return null;
            return $asInt ? (int)round((float)$num) : (float)$num;
        };

        // 1. PROYECTO Mapping
        $rawProyecto = trim($row['proyecto'] ?? '');
        $proyectoFinal = $rawProyecto;
        if (isset($this->mappings['PROYECTO'][$rawProyecto]) && $this->mappings['PROYECTO'][$rawProyecto] !== '__NEW__') {
            $proyectoFinal = $this->mappings['PROYECTO'][$rawProyecto];
        }

        // 2. INGENIO Mapping
        $rawIngenio = trim($row['ingenio'] ?? '');
        $ingenioFinal = $rawIngenio;
        if (isset($this->mappings['INGENIO'][$rawIngenio]) && $this->mappings['INGENIO'][$rawIngenio] !== '__NEW__') {
            $ingenioFinal = $this->mappings['INGENIO'][$rawIngenio];
        }

        // 3. AMBIENTE Mappings (Applies to both selection and evaluation if present)
        $rawAmbSeleccion = trim($row['amb_seleccion'] ?? '');
        $ambSeleccionFinal = $rawAmbSeleccion;
        if (isset($this->mappings['AMBIENTE'][$rawAmbSeleccion]) && $this->mappings['AMBIENTE'][$rawAmbSeleccion] !== '__NEW__') {
            $ambSeleccionFinal = $this->mappings['AMBIENTE'][$rawAmbSeleccion];
        }

        $rawAmbEvaluacion = trim($row['amb_evaluacion'] ?? '');
        $ambEvaluacionFinal = $rawAmbEvaluacion;
        if (isset($this->mappings['AMBIENTE'][$rawAmbEvaluacion]) && $this->mappings['AMBIENTE'][$rawAmbEvaluacion] !== '__NEW__') {
            $ambEvaluacionFinal = $this->mappings['AMBIENTE'][$rawAmbEvaluacion];
        }

        $fsDate = $this->transformDate($row['fs'] ?? null);

        return new Ensayo([
            'nombre_ensayo'           => $row['nombre_ensayo'] ?? null,
            'nombre_env'              => $row['nombre_env'] ?? null,
            'proyecto'                => $proyectoFinal ?: null,
            'estado_seleccion'        => $row['estado_de_seleccion'] ?? null,
            'serie'                   => $row['serie'] ?? null,
            'amb_seleccion'           => $this->ambiente ?: ($ambSeleccionFinal ?: null),
            'amb_evaluacion'          => $ambEvaluacionFinal ?: null,
            'objetivo'                => $row['objetivo'] ?? null,
            'ingenio'                 => $ingenioFinal ?: null,
            'hacienda'                => $row['hacienda'] ?? null,
            'suerte'                  => $row['suerte'] ?? null,
            'zona_agroecologia'       => $row['za'] ?? null,
            'consociacion'            => $row['cs'] ?? null,
            'corte'                   => $row['corte'] ?? null,
            'entradas'                => $cleanNumeric($row['entradas'] ?? null, true),
            'testigos'                => $cleanNumeric($row['checks'] ?? null, true),
            'clones'                  => $cleanNumeric($row['noclones'] ?? null, true),
            'total_parcelas'          => $cleanNumeric($row['plots'] ?? null, true),
            'diseno'                  => $row['diseno'] ?? null,
            'surcos'                  => $cleanNumeric($row['nsurcosplot'] ?? null, true),
            'longitud_surco'          => $cleanNumeric($row['lonsurco'] ?? null),
            'longitud_callejon'       => $cleanNumeric($row['lonsurcocallejon'] ?? null),
            'distancia_surco'         => $cleanNumeric($row['distentresurco'] ?? null),
            'area_total'              => $cleanNumeric($row['areaensayo'] ?? null),
            'red_meteorologica'       => $row['rma'] ?? null,
            'fecha_siembra'           => $fsDate,
            'fecha_cosecha_final'     => $this->transformDate($row['fc'] ?? null),
            'fecha_evaluacion'        => $this->transformDate($row['feval'] ?? null),
            'meses_evaluacion'        => $cleanNumeric($row['mds'] ?? null, true),
            'fecha_cosecha_programada'=> $this->transformDate($row['fc_programada'] ?? null),
            'estado_actual'           => $row['edad_actual'] ?? null,
            'ano_siembra'             => $cleanNumeric($row['ano'] ?? null, true),
            'mes_siembra'             => $cleanNumeric($row['mes'] ?? null, true),
            'tipo_cosecha'            => $row['tipocosecha'] ?? null,
            'comentarios'             => $row['comentario'] ?? null,
            'ubicacion_fisica'        => $row['nombrearchivo'] ?? null,
            'nombre_reporte'          => $row['nombre_ensayo2'] ?? null,
            'investigador'            => $row['investigador'] ?? null,
            'user_id'                 => auth()->id(),
        ]);
    }
}
