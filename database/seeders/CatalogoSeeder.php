<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CatalogoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default Initial Environments
        $ambientes = ['HUMEDO', 'PIEDEMONTE', 'SECO', 'VALLE'];
        foreach ($ambientes as $v) {
            \App\Models\Catalogo::updateOrCreate(['categoria' => 'AMBIENTE', 'valor' => $v]);
        }

        // Extracted Unique Ingenios from R analysis
        $ingenios = ['CC', 'PR', 'PC', 'RS', 'RP', 'MN', 'CB', 'CA', 'SC', 'MY', 'CCI', 'CCA', 'ML', 'RPA', 'CM'];
        foreach ($ingenios as $v) {
            \App\Models\Catalogo::updateOrCreate(['categoria' => 'INGENIO', 'valor' => $v]);
        }

        // Cleaned up Core Projects
        $proyectos = [
            'Obtención de variedades para la zona de piedemonte del Valle del Río Cauca',
            'Selección recurrente por sacarosa',
            'Maduración precoz',
            'SRR',
            'Obtención de variedades para la zona húmeda del Valle del Río Cauca',
            'Grupo elite para cruzamientos',
            'MATE'
        ];
        foreach ($proyectos as $v) {
            \App\Models\Catalogo::updateOrCreate(['categoria' => 'PROYECTO', 'valor' => $v]);
        }
    }
}
