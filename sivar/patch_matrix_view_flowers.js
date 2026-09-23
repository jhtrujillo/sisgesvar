const fs = require('fs');
const file = 'src/views/mejoramiento/cruzamientos/CrossingMatrixView.vue';
let content = fs.readFileSync(file, 'utf8');

const oldHeaderCol = '<span class="block text-[9px] text-slate-400 font-semibold mt-0.5 mb-0.5">Polen: {{ flor.polen }}</span>';
const newHeaderCol = '<span class="block text-[9px] text-slate-400 font-semibold mt-0.5 mb-0.5">Polen: {{ flor.polen }} | Flores: {{ flor.cantidad_flores || 0 }}</span>';
content = content.replace(oldHeaderCol, newHeaderCol);

const oldHeaderRow = '<span class="block text-[9px] text-slate-400 mt-0.5 font-semibold">Polen: {{ viabilidadRow[0].polen }}</span>';
const newHeaderRow = '<span class="block text-[9px] text-slate-400 mt-0.5 font-semibold">Polen: {{ viabilidadRow[0].polen }} | Flores: {{ viabilidadRow[0].cantidad_flores || 0 }}</span>';
content = content.replace(oldHeaderRow, newHeaderRow);

fs.writeFileSync(file, content);
