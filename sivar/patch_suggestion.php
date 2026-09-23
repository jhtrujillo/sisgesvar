<?php
$file = 'src/views/mejoramiento/cruzamientos/CrossingSuggestionPerProjectView.vue';
$content = file_get_contents($file);

$oldCall = <<<EOD
      await SuggestionCrossingPerProjectStore.getSuggestionCrossingPerProjectList(
        activeIdProj,
        activeProj,
        activeVariety,
        activeAmb
      );
EOD;

$newCall = <<<EOD
      const caracterFiltro = localStorage.getItem("filtroCaracterIndividual");
      await SuggestionCrossingPerProjectStore.getSuggestionCrossingPerProjectList(
        activeIdProj,
        activeProj,
        activeVariety,
        activeAmb,
        caracterFiltro
      );
EOD;

$content = str_replace($oldCall, $newCall, $content);
file_put_contents($file, $content);
