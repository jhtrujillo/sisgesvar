<?php
$file = 'src/views/mejoramiento/cruzamientos/CrossingWeightedView.vue';
$content = file_get_contents($file);

// Remove the fetchCaracteresProyecto from the watch of selectedCdCntble
$oldWatch = <<<EOD
        localStorage.setItem("selectedIdProject", proj.id_prycto.toString());
        fetchCaracteresProyecto(proj.id_prycto.toString());
      }
    } else {
      caracteresProyecto.value = [];
      caracteresSeleccionados.value = [];
    }
  }
);
EOD;

$newWatch = <<<EOD
        // NO sobreescribir el proyecto del Paso 1, este dropdown es solo para Ponderados
      }
    } else {
      // No limpiar los caracteres si borran el proyecto de ponderados
    }
  }
);
EOD;
$content = str_replace($oldWatch, $newWatch, $content);

// And in onMounted, fetch characters based on selectedIdProject from Paso 1
$oldOnMounted = <<<EOD
  if (storedCdCntble.value) {
    selectedCdCntble.value = storedCdCntble.value;
    const proj = crossingInitialDataStore.crossingInitialDataList.find((p) => p.cd_cntble === storedCdCntble.value);
    if (proj) fetchCaracteresProyecto(proj.id_prycto.toString());
  }

  if (selectedMegaAmbiente.value || selectedCdCntble.value) {
EOD;

$newOnMounted = <<<EOD
  if (storedCdCntble.value) {
    selectedCdCntble.value = storedCdCntble.value;
  }
  
  // Fetch caracteres siempre del proyecto seleccionado en el Paso 1
  const idProyectoPaso1 = localStorage.getItem("selectedIdProject");
  if (idProyectoPaso1) {
    fetchCaracteresProyecto(idProyectoPaso1);
  }

  if (selectedMegaAmbiente.value || selectedCdCntble.value) {
EOD;
$content = str_replace($oldOnMounted, $newOnMounted, $content);

file_put_contents($file, $content);
