<?php
$file = 'src/views/mejoramiento/cruzamientos/CrossingSuggestionPerProjectView.vue';
$content = file_get_contents($file);

$oldCode = <<<EOD
                              <div
                                class="text-[9px] font-extrabold leading-tight"
                                :class="[tipoMapaCalor !== 'none' && isDarkBackground(car.varA, car.varB, car.vm2) ? 'text-white' : 'text-slate-900']"
                              >
                                {{ car.varA }} x {{ car.varB }}
                              </div>
EOD;

$newCode = <<<EOD
                              <div
                                class="text-[9px] font-extrabold leading-tight"
                                :class="[tipoMapaCalor !== 'none' && isDarkBackground(car.varA, car.varB, car.vm2) ? 'text-white' : 'text-slate-900']"
                              >
                                {{ car.varA }} x {{ car.varB }}
                                <span v-if="car.emasculado" class="block text-[8.5px] mt-0.5" :class="[tipoMapaCalor !== 'none' && isDarkBackground(car.varA, car.varB, car.vm2) ? 'text-rose-200' : 'text-rose-600']">[EMASCULADA]</span>
                              </div>
EOD;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
