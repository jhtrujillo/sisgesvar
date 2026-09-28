const fs = require('fs');
const file = 'src/views/mejoramiento/cruzamientos/CrossingSuggestionPerProjectView.vue';
let content = fs.readFileSync(file, 'utf8');

const target = `<!-- Selector numérico cuando es viable -->
                              <div v-if="car?.viabilidad" class="mt-0.5 mb-1">
                                <button`;
                                
const replacement = `<!-- Indicador de Policruzamiento (Cruce Invertido) -->
                              <div v-if="car?.viabilidad && (Number(car?.polen2) <= 20 || Number(car?.polen) > 20) && car?.varA !== car?.varB" class="mb-1">
                                <span class="bg-indigo-100 text-indigo-700 text-[8px] font-bold px-1.5 py-0.5 rounded shadow-sm" title="Cruce Recíproco (Sexos Invertidos)">🔄 Recíproco</span>
                              </div>
                              <!-- Selector numérico cuando es viable -->
                              <div v-if="car?.viabilidad" class="mt-0.5 mb-1">
                                <button`;

if (content.includes(target) && !content.includes('🔄 Recíproco')) {
  content = content.replace(target, replacement);
  fs.writeFileSync(file, content);
}
