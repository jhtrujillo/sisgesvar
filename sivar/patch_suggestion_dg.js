const fs = require('fs');
const file = 'src/views/mejoramiento/cruzamientos/CrossingSuggestionPerProjectView.vue';
let content = fs.readFileSync(file, 'utf8');

const oldCode = `function getDistancia(varA: string, varB: string) {
  if (!varA || !varB) return "NA";`;

const newCode = `function getDistancia(varA: string, varB: string) {
  if (!varA || !varB) return "NA";
  if (varA === varB) return "NA";`;

content = content.replace(oldCode, newCode);
fs.writeFileSync(file, content);
