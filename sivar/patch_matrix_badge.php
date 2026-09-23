<?php
$file = 'src/views/mejoramiento/cruzamientos/CrossingMatrixView.vue';
$content = file_get_contents($file);

$oldCode = <<<EOD
                          <span class="text-[9px] font-extrabold tracking-tight leading-none text-slate-700 text-center">
                            DG: {{ getDistancia(car?.varA, car?.varB) || "NA" }}
                          </span>
EOD;

$newCode = <<<EOD
                          <span class="text-[9px] font-extrabold tracking-tight leading-none text-slate-700 text-center">
                            DG: {{ getDistancia(car?.varA, car?.varB) || "NA" }}
                          </span>
                          <span v-if="car?.emasculado" class="text-[7.5px] font-black text-rose-600 block text-center uppercase mt-0.5 leading-none">
                            [EMASCULADA]
                          </span>
EOD;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
