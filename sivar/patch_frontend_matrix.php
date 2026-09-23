<?php
// 1. crossings.services.ts
$file = 'src/services/crossings.services.ts';
$content = file_get_contents($file);
$old = <<<EOD
async function getMatrix(proyectos: string, proyecto: string, testigo: string, ambiente: string): Promise<any> {
  const url = `\${urls.API_GENERATE_MATRIX}/\${proyectos}/\${proyecto}/\${testigo}/\${ambiente}`;
  return await api.get(url, {}, true);
}
EOD;
$new = <<<EOD
async function getMatrix(proyectos: string, proyecto: string, testigo: string, ambiente: string, caracter?: string | null): Promise<any> {
  let url = `\${urls.API_GENERATE_MATRIX}/\${proyectos}/\${proyecto}/\${testigo}/\${ambiente}`;
  if (caracter) url += `/\${caracter}`;
  return await api.get(url, {}, true);
}
EOD;
$content = str_replace($old, $new, $content);
file_put_contents($file, $content);

// 2. crossingmatrix.ts store
$file = 'src/stores/crossingmatrix.ts';
$content = file_get_contents($file);
$old = <<<EOD
    const getMatrixCrossingList = async (proyectos: string, proyecto: string, testigo: string, ambiente: string): Promise<void> => {
      try {
        const result = await CrossingsService.getMatrix(proyectos, proyecto, testigo, ambiente);
EOD;
$new = <<<EOD
    const getMatrixCrossingList = async (proyectos: string, proyecto: string, testigo: string, ambiente: string, caracter?: string | null): Promise<void> => {
      try {
        const result = await CrossingsService.getMatrix(proyectos, proyecto, testigo, ambiente, caracter);
EOD;
$content = str_replace($old, $new, $content);
file_put_contents($file, $content);

// 3. CrossingMatrixView.vue
$file = 'src/views/mejoramiento/cruzamientos/CrossingMatrixView.vue';
$content = file_get_contents($file);

$oldCall1 = "await MatrixCrossingStore.getMatrixCrossingList(activeProj, activeProj, activeVar, activeAmb);";
$newCall1 = <<<EOD
      const caracterFiltro = localStorage.getItem("filtroCaracterIndividual");
      await MatrixCrossingStore.getMatrixCrossingList(activeProj, activeProj, activeVar, activeAmb, caracterFiltro);
EOD;

$oldCall2 = "await MatrixCrossingStore.getMatrixCrossingList(activeProj, activeProj, newVariety, activeAmb);";
$newCall2 = <<<EOD
      const caracterFiltro = localStorage.getItem("filtroCaracterIndividual");
      await MatrixCrossingStore.getMatrixCrossingList(activeProj, activeProj, newVariety, activeAmb, caracterFiltro);
EOD;

$content = str_replace($oldCall1, $newCall1, $content);
$content = str_replace($oldCall2, $newCall2, $content);
file_put_contents($file, $content);

