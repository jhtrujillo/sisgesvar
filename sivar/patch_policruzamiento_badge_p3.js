const fs = require('fs');
const file = 'src/views/mejoramiento/cruzamientos/CrossingMatrixView.vue';
let content = fs.readFileSync(file, 'utf8');

const target = `<div class="flex flex-col items-center justify-center w-full border-t border-slate-100/50 pt-1.5 mt-1 space-y-1">`;
                                
const replacement = `<div v-if="car?.viabilidad && (Number(car?.polen2) <= 20 || Number(car?.polen) > 20) && car?.varA !== car?.varB" class="mb-1">
                          <span class="bg-indigo-100 text-indigo-700 text-[8px] font-bold px-1.5 py-0.5 rounded shadow-sm" title="Cruce Recíproco (Sexos Invertidos)">🔄 Recíproco</span>
                        </div>
                        <div class="flex flex-col items-center justify-center w-full border-t border-slate-100/50 pt-1.5 mt-1 space-y-1">`;

if (content.includes(target) && !content.includes('🔄 Recíproco')) {
  content = content.replace(target, replacement);
  fs.writeFileSync(file, content);
}
