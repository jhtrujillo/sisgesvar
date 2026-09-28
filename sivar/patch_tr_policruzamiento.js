const fs = require('fs');
const file = 'src/views/mejoramiento/cruzamientos/CrossingSuggestionPerProjectView.vue';
let content = fs.readFileSync(file, 'utf8');

const target = `                      <tr
                        v-if="(!ocultarInviables || isRowViable(viabilidadRow)) && Number(viabilidadRow[0]?.polen) <= 20"`;
                        
const replacement = `                      <tr
                        v-if="(!ocultarInviables || isRowViable(viabilidadRow)) && (Number(viabilidadRow[0]?.polen) <= 20 || isRowViable(viabilidadRow) || permitirPolicruzamientos)"`;

if (content.includes(target)) {
  content = content.replace(target, replacement);
  fs.writeFileSync(file, content);
}
