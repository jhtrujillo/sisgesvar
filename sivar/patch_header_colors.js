const fs = require('fs');
const file = 'src/views/mejoramiento/cruzamientos/CrossingMatrixView.vue';
let content = fs.readFileSync(file, 'utf8');

// Modificar columna (Padre)
const colOld = `                    class="px-2 py-2 text-center text-[11px] font-bold uppercase tracking-wider text-slate-650 bg-slate-50 border-r border-slate-100 sticky top-0 z-10 min-w-[75px]"`;
const colNew = `                    :class="['px-2 py-2 text-center text-[11px] font-bold uppercase tracking-wider border-r border-slate-100 sticky top-0 z-10 min-w-[75px]', flor.sxo === 'Hembra' || isEmasculatedLocal(flor.vrdad) ? 'bg-rose-50/80 text-rose-900' : 'bg-sky-50/80 text-sky-900']"`;
content = content.replace(colOld, colNew);

// Modificar fila (Madre)
const rowOld = `                    class="whitespace-nowrap px-2 py-2 text-center text-[11px] font-bold text-slate-700 bg-white border-r border-slate-100 sticky left-0 z-10 shadow-[2px_0_5px_rgba(0,0,0,0.02)] min-w-[110px]"`;
const rowNew = `                    :class="['whitespace-nowrap px-2 py-2 text-center text-[11px] font-bold border-r border-slate-100 sticky left-0 z-10 shadow-[2px_0_5px_rgba(0,0,0,0.02)] min-w-[110px]', viabilidadRow[0].sxo === 'Hembra' || isEmasculatedLocal(viabilidadRow[0].varA) ? 'bg-rose-50/60 text-rose-900' : 'bg-sky-50/60 text-sky-900']"`;
content = content.replace(rowOld, rowNew);

fs.writeFileSync(file, content);
