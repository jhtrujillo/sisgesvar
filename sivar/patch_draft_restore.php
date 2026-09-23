<?php
$file = 'src/views/mejoramiento/cruzamientos/CrossingSuggestionPerProjectView.vue';
$content = file_get_contents($file);

$oldCode = <<<EOD
        rows.forEach((row: any) => {
          row.forEach((car: any) => {
            if (car && car.varA && car.varB) {
              const match = savedState.find((s: any) => s.varA === car.varA.trim() && s.varB === car.varB.trim());
              if (match) {
                car.viabilidad = match.viabilidad;
                if (car.viabilidad) {
                  car.flores_madre = 1;
                  car.flores_padre = 1;
                }
              }
            }
          });
        });
EOD;

$newCode = <<<EOD
        rows.forEach((row: any) => {
          row.forEach((car: any) => {
            if (car && car.varA && car.varB) {
              const match = savedState.find((s: any) => s.varA === car.varA.trim() && s.varB === car.varB.trim());
              if (match) {
                car.viabilidad = match.viabilidad;
                if (match.emasculado) {
                  car.emasculado = match.emasculado;
                }
                if (car.viabilidad) {
                  car.flores_madre = 1;
                  car.flores_padre = 1;
                }
              }
            }
          });
        });
EOD;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
