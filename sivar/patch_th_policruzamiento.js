const fs = require('fs');
const file = 'src/views/mejoramiento/cruzamientos/CrossingSuggestionPerProjectView.vue';
let content = fs.readFileSync(file, 'utf8');

const target = `                        <th
                          v-if="(!ocultarInviables || isColumnViable(indexCol)) && (Number(flor.polen) > 20 || isColumnViable(indexCol))"`;
                        
const replacement = `                        <th
                          v-if="(!ocultarInviables || isColumnViable(indexCol)) && (Number(flor.polen) > 20 || isColumnViable(indexCol) || permitirPolicruzamientos)"`;

if (content.includes(target)) {
  content = content.replace(target, replacement);
  fs.writeFileSync(file, content);
}
