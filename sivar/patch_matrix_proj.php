<?php
$file = 'src/views/mejoramiento/cruzamientos/CrossingMatrixView.vue';
$content = file_get_contents($file);

$old1 = <<<EOD
const loadMatrix = async () => {
  const activeProj = selectedCdCntble.value || localStorage.getItem("lastSelectedCdCntble") || localStorage.getItem("selectedCdCntble") || "010105";
  const activeAmb = selectedMegaAmbiente.value || localStorage.getItem("selectedMegaAmbiente") || "Semiseco";
  const activeVar = selectedVariety.value || localStorage.getItem("selectedVariety");

  if (activeProj && activeVar) {
    isLoading.value = true;
    try {
      const caracterFiltro = localStorage.getItem("filtroCaracterIndividual");
      await MatrixCrossingStore.getMatrixCrossingList(activeProj, activeProj, activeVar, activeAmb, caracterFiltro);
EOD;

$new1 = <<<EOD
const loadMatrix = async () => {
  const flowerProj = localStorage.getItem("lastSelectedCdCntble") || "010105";
  const pondProj = selectedCdCntble.value || localStorage.getItem("selectedCdCntble") || "General";
  const activeAmb = selectedMegaAmbiente.value || localStorage.getItem("selectedMegaAmbiente") || "Semiseco";
  const activeVar = selectedVariety.value || localStorage.getItem("selectedVariety");

  if (flowerProj && activeVar) {
    isLoading.value = true;
    try {
      const caracterFiltro = localStorage.getItem("filtroCaracterIndividual");
      await MatrixCrossingStore.getMatrixCrossingList(flowerProj, pondProj, activeVar, activeAmb, caracterFiltro);
EOD;
$content = str_replace($old1, $new1, $content);

$old2 = <<<EOD
watch([selectedMegaAmbiente, selectedCdCntble, selectedVariety], async ([newMegaAmbiente, newCdCntble, newVariety]) => {
  const activeProj = newCdCntble || localStorage.getItem("lastSelectedCdCntble") || localStorage.getItem("selectedCdCntble") || "010105";
  const activeAmb = newMegaAmbiente || localStorage.getItem("selectedMegaAmbiente") || "Semiseco";

  if ((newMegaAmbiente || newCdCntble) && newVariety) {
    isLoading.value = true;
    try {
      const caracterFiltro = localStorage.getItem("filtroCaracterIndividual");
      await MatrixCrossingStore.getMatrixCrossingList(activeProj, activeProj, newVariety, activeAmb, caracterFiltro);

      // Restaurar el borrador para sincronizar Step 2 y Step 3 en ambas direcciones
      const storedDraft = localStorage.getItem(`sivarcc_draft_crossings_\${activeProj}_\${activeAmb}`);
EOD;

$new2 = <<<EOD
watch([selectedMegaAmbiente, selectedCdCntble, selectedVariety], async ([newMegaAmbiente, newCdCntble, newVariety]) => {
  const flowerProj = localStorage.getItem("lastSelectedCdCntble") || "010105";
  const pondProj = newCdCntble || localStorage.getItem("selectedCdCntble") || "General";
  const activeAmb = newMegaAmbiente || localStorage.getItem("selectedMegaAmbiente") || "Semiseco";

  if ((newMegaAmbiente || newCdCntble) && newVariety) {
    isLoading.value = true;
    try {
      const caracterFiltro = localStorage.getItem("filtroCaracterIndividual");
      await MatrixCrossingStore.getMatrixCrossingList(flowerProj, pondProj, newVariety, activeAmb, caracterFiltro);

      // Restaurar el borrador para sincronizar Step 2 y Step 3 en ambas direcciones
      const storedDraft = localStorage.getItem(`sivarcc_draft_crossings_\${flowerProj}_\${activeAmb}`);
EOD;
$content = str_replace($old2, $new2, $content);

file_put_contents($file, $content);
