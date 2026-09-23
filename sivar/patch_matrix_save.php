<?php
$file = 'src/views/mejoramiento/cruzamientos/CrossingMatrixView.vue';
$content = file_get_contents($file);

$oldCode = <<<EOD
      if (c && c.varA && c.varB) {
        savedState.push({ varA: c.varA.trim(), varB: c.varB.trim(), viabilidad: !!c.viabilidad });
      }
EOD;

$newCode = <<<EOD
      if (c && c.varA && c.varB) {
        savedState.push({ varA: c.varA.trim(), varB: c.varB.trim(), viabilidad: !!c.viabilidad, emasculado: !!c.emasculado });
      }
EOD;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
