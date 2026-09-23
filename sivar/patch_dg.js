const fs = require('fs');
const files = [
  'src/views/mejoramiento/cruzamientos/CrossingMatrixView.vue',
  'src/views/mejoramiento/cruzamientos/CrossingSuggestionPerProjectView.vue'
];

for (const file of files) {
  if (!fs.existsSync(file)) continue;
  let content = fs.readFileSync(file, 'utf8');
  
  const oldCode = `const getDistancia = (varA: string, varB: string) => {
  const distancias = MatrixCrossingStore.matrixCrossingsFilter.distancias || {};
  return distancias[varA]?.[varB] || "NA";
};`;

  const newCode = `const getDistancia = (varA: string, varB: string) => {
  if (varA && varB && varA === varB) return "NA";
  const distancias = MatrixCrossingStore.matrixCrossingsFilter.distancias || {};
  return distancias[varA]?.[varB] || "NA";
};`;

  content = content.replace(oldCode, newCode);
  fs.writeFileSync(file, content);
}
