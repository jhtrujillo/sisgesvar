<?php
$file = 'src/views/mejoramiento/cruzamientos/CrossingWeightedView.vue';
$content = file_get_contents($file);

$oldHandle = <<<EOD
const handleSiguiente = async () => {
  if (estrategia.value === 'individual') {
    if (!caracterIndividual.value) {
      toast.error("Debe seleccionar un carácter objetivo");
      return;
    }
    
    // Set 100% to selected character, 0% to others
    const proyectoParam = getActiveProjectCode();
    const ambienteParam = getActiveAmbiente();
    isFetchingProfile.value = true;
    try {
      for (const car of ponderadosFiltrados.value) {
        const peso = car.id_caracteristica === caracterIndividual.value ? 100 : 0;
        await modifyFeaturesStore.getModifyFeaturesCrossingList(
          car.id_caracteristica,
          proyectoParam,
          car.nivel?.toString() || "0",
          peso.toString(),
          ambienteParam,
          car.ponderado ? 0 : 1 // if it didn't have ponderado before, it's considered 'nuevo' = 1
        );
      }
    } catch (e) {
      console.error(e);
      toast.error("Error al configurar los pesos individuales.");
      isFetchingProfile.value = false;
      return;
    }
    isFetchingProfile.value = false;
  }
  
  // Continuar a la siguiente vista
  router.push({ name: 'crossing_matrix.show' });
};
EOD;

$newHandle = <<<EOD
const handleSiguiente = async () => {
  if (estrategia.value === 'individual') {
    if (!caracterIndividual.value) {
      toast.error("Debe seleccionar un carácter objetivo");
      return;
    }
    localStorage.setItem('filtroCaracterIndividual', caracterIndividual.value);
  } else {
    localStorage.removeItem('filtroCaracterIndividual');
  }
  
  // Continuar a la siguiente vista
  router.push({ name: 'crossing_matrix.show' });
};
EOD;

$content = str_replace($oldHandle, $newHandle, $content);
file_put_contents($file, $content);
