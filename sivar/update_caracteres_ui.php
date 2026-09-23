<?php
$file = 'src/views/mejoramiento/cruzamientos/CrossingWeightedView.vue';
$content = file_get_contents($file);

$oldSpan = <<<EOD
                <span class="text-sm font-medium text-amber-900">{{ car.nombre }}</span>
EOD;

$newSpan = <<<EOD
                <div class="flex flex-col">
                  <span class="text-sm font-medium text-amber-900 leading-tight">{{ car.nombre }}</span>
                  <span class="text-[10px] text-amber-700 font-semibold mt-0.5" v-if="car.total_flores > 0">
                    {{ car.total_variedades }} vars / {{ car.total_flores }} flores
                  </span>
                  <span class="text-[10px] text-amber-700/50 font-semibold mt-0.5" v-else>
                    Sin flores hoy
                  </span>
                </div>
EOD;

$content = str_replace($oldSpan, $newSpan, $content);
file_put_contents($file, $content);
