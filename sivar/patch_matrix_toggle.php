<?php
$file = 'src/views/mejoramiento/cruzamientos/CrossingMatrixView.vue';
$content = file_get_contents($file);

$oldCode = <<<EOD
// Función para alternar el cruzamiento cuando se hace click
const toggleCruzamiento = (car: any) => {
  // Mutar la viabilidad localmente
  car.viabilidad = !car.viabilidad;
EOD;

$newCode = <<<EOD
// Función para alternar el cruzamiento cuando se hace click
const toggleCruzamiento = (car: any) => {
  if (!car.viabilidad && car.causa_veto && car.causa_veto.includes("Ambos son Macho")) {
    const confirmEmasculate = confirm("Incompatibilidad de Sexo: Ambos parentales son Macho.\\n\\n¿Deseas Emascular a la Madre (" + car.varA + ") para forzar y permitir este cruce?");
    if (!confirmEmasculate) {
      return; // Abortar
    }
    car.emasculado = true;
  } else if (car.viabilidad) {
    // Si se deselecciona, quitamos el flag por si acaso
    car.emasculado = false;
  }

  // Mutar la viabilidad localmente
  car.viabilidad = !car.viabilidad;
EOD;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
