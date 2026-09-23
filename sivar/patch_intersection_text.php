<?php
$file = 'src/views/mejoramiento/cruzamientos/CrossingSuggestionPerProjectView.vue';
$content = file_get_contents($file);

$oldCell = <<<EOD
                              <div
                                class="text-[9px] font-extrabold leading-tight"
                                :class="[tipoMapaCalor !== 'none' && isDarkBackground(car.varA, car.varB, car.vm2) ? 'text-white' : 'text-slate-900']"
                              >
                                {{ car.varB }}
                              </div>
EOD;

$newCell = <<<EOD
                              <div
                                class="text-[9px] font-extrabold leading-tight"
                                :class="[tipoMapaCalor !== 'none' && isDarkBackground(car.varA, car.varB, car.vm2) ? 'text-white' : 'text-slate-900']"
                              >
                                {{ car.varA }} x {{ car.varB }}
                              </div>
EOD;

$content = str_replace($oldCell, $newCell, $content);
file_put_contents($file, $content);
