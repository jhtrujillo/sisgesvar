<?php
$file = 'src/views/mejoramiento/cruzamientos/CrossingWeightedView.vue';
$content = file_get_contents($file);

$oldFunc = <<<EOD
const fetchCaracteresProyecto = async (proyectoId: string) => {
  try {
    const res = await api.get(`\${urls.API_PROYECTOS}/\${proyectoId}/caracteres`, {}, true);
    caracteresProyecto.value = res.data;
  } catch (error) {
    console.error("Error al obtener los caracteres del proyecto:", error);
    caracteresProyecto.value = [];
  }
};
EOD;

$newFunc = <<<EOD
const fetchCaracteresProyecto = async (proyectoId: string) => {
  try {
    console.log("Fetching caracteres from:", `\${urls.API_PROYECTOS}/\${proyectoId}/caracteres`);
    const res = await api.get(`\${urls.API_PROYECTOS}/\${proyectoId}/caracteres`, {}, true);
    console.log("Caracteres response:", res.data);
    caracteresProyecto.value = res.data;
  } catch (error) {
    console.error("Error al obtener los caracteres del proyecto:", error);
    caracteresProyecto.value = [];
  }
};
EOD;

$content = str_replace($oldFunc, $newFunc, $content);
file_put_contents($file, $content);
