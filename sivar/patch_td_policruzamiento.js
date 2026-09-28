const fs = require('fs');
const file = 'src/views/mejoramiento/cruzamientos/CrossingSuggestionPerProjectView.vue';
let content = fs.readFileSync(file, 'utf8');

const target = `                          <td
                            v-if="(!ocultarInviables || isColumnViable(indexCol)) && (Number(car?.polen2) > 20 || isColumnViable(indexCol))"`;
                        
const replacement = `                          <td
                            v-if="(!ocultarInviables || isColumnViable(indexCol)) && (Number(car?.polen2) > 20 || isColumnViable(indexCol) || permitirPolicruzamientos)"`;

if (content.includes(target)) {
  content = content.replace(target, replacement);
  fs.writeFileSync(file, content);
}
