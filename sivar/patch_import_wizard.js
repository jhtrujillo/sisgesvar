const fs = require('fs');
const file = 'src/components/floracion/FloracionImportWizard.vue';
let content = fs.readFileSync(file, 'utf8');

const oldMapping = `  floracion: { title: "Tipo Floración (Nat/Ind)", required: false, auto: ['floracion', 'tipo'] },
  fecha: { title: "Fecha de Evaluación", required: true, auto: ['fecha'] }`;

const newMapping = `  flores: { title: "Cantidad de Flores", required: false, auto: ['flores', 'cantidad'] },
  floracion: { title: "Tipo Floración (Nat/Ind)", required: false, auto: ['floracion', 'tipo', 'tipo floracion'] },
  fecha: { title: "Fecha de Evaluación", required: true, auto: ['fecha'] }`;

content = content.replace(oldMapping, newMapping);
fs.writeFileSync(file, content);
