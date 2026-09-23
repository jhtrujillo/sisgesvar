<?php
$file = 'src/views/mejoramiento/cruzamientos/CrossingSuggestionPerProjectView.vue';
$content = file_get_contents($file);

$oldCodeToggle = <<<EOD
function toggleCruzamiento(car: any) {
  if (!car.viabilidad) {
    car.viabilidad = true;
    car.flores_madre = 1;
    car.flores_padre = 1;
  } else {
    car.viabilidad = false;
    car.flores_madre = 0;
    car.flores_padre = 0;
  }
EOD;

$newCodeToggle = <<<EOD
function toggleCruzamiento(car: any) {
  if (!car.viabilidad && getCausaInviabilidad(car).includes("Ambos son Macho")) {
    const confirmEmasculate = confirm("Incompatibilidad de Sexo: Ambos parentales son Macho.\\n\\n¿Deseas Emascular a la Madre (" + car.varA + ") para forzar y permitir este cruce?");
    if (!confirmEmasculate) {
      return; // Abortar
    }
    car.emasculado = true;
  } else if (car.viabilidad) {
    car.emasculado = false;
  }

  if (!car.viabilidad) {
    car.viabilidad = true;
    car.flores_madre = 1;
    car.flores_padre = 1;
  } else {
    car.viabilidad = false;
    car.flores_madre = 0;
    car.flores_padre = 0;
  }
EOD;

$oldCodeSave = <<<EOD
      if (c && c.varA && c.varB) {
        savedState.push({ varA: c.varA.trim(), varB: c.varB.trim(), viabilidad: !!c.viabilidad });
      }
EOD;

$newCodeSave = <<<EOD
      if (c && c.varA && c.varB) {
        savedState.push({ varA: c.varA.trim(), varB: c.varB.trim(), viabilidad: !!c.viabilidad, emasculado: !!c.emasculado });
      }
EOD;

$content = str_replace($oldCodeToggle, $newCodeToggle, $content);
$content = str_replace($oldCodeSave, $newCodeSave, $content);
file_put_contents($file, $content);
