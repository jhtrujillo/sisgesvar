const fs = require('fs');
const files = [
  'src/views/mejoramiento/cruzamientos/CrossingMatrixView.vue',
  'src/views/mejoramiento/cruzamientos/CrossingSuggestionPerProjectView.vue'
];

for (const file of files) {
  if (!fs.existsSync(file)) continue;
  let content = fs.readFileSync(file, 'utf8');
  
  const oldText = 'confirm("Incompatibilidad de Sexo: Ambos parentales son Macho.\\n\\n¿Deseas Emascular a la Madre (" + car.varA + ") para forzar y permitir este cruce?")';
  const newText = 'confirm("⚠️ Atención: Incompatibilidad de sexo.\\n\\nAmbos parentales son masculinos. Para que la polinización sea posible, la variedad receptora debe ser emasculada.\\n\\n¿Autoriza registrar a la variedad (" + car.varA + ") como emasculada para programar este cruzamiento?")';

  content = content.replace(oldText, newText);
  fs.writeFileSync(file, content);
}
