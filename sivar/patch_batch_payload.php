<?php
$file = 'src/views/mejoramiento/cruzamientos/CrossingSuggestionPerProjectView.vue';
$content = file_get_contents($file);

$oldCode = <<<EOD
    const batchPayload = selectedCrossings.map((car) => {
      const autofecundado = car.varA === car.varB ? 1 : 0;
      return {
        madre: `\${car.varA}_\${car.proyecto}_\${car.id_caracter}`,
        padres: `\${car.varB}_\${car.proyecto2}_\${car.id_caracter2}`,
        observaciones: "Programacion de Cruzamientos desde Matriz por Proyecto",
        id_ponderados: idPonderado,
        proyectos: `\${car.proyecto}`,
        autofecundado: autofecundado,
        // Variables añadidas para consumo asimétrico de flores
        flores_madre: car.flores_madre ?? 1,
        flores_padre: car.flores_padre ?? 1
      };
    });
EOD;

$newCode = <<<EOD
    const batchPayload = selectedCrossings.map((car) => {
      const autofecundado = car.varA === car.varB ? 1 : 0;
      return {
        madre: `\${car.varA}_\${car.proyecto}_\${car.id_caracter}`,
        padres: `\${car.varB}_\${car.proyecto2}_\${car.id_caracter2}`,
        observaciones: "Programacion de Cruzamientos desde Matriz por Proyecto",
        id_ponderados: idPonderado,
        proyectos: `\${car.proyecto}`,
        autofecundado: autofecundado,
        emasculado: car.emasculado ? true : false,
        // Variables añadidas para consumo asimétrico de flores
        flores_madre: car.flores_madre ?? 1,
        flores_padre: car.flores_padre ?? 1
      };
    });
EOD;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
