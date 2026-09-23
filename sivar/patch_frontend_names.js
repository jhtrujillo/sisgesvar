const fs = require('fs');
const file = 'src/views/mejoramiento/cruzamientos/CrossingSuggestionPerProjectView.vue';
let content = fs.readFileSync(file, 'utf8');

const oldPush = 'motivos.push(`${p.equivalente.toUpperCase()} excede límite`);';
const newPush = `
            const nombresLegibles: Record<string, string> = {
              'scrsa': 'Sacarosa',
              'tchm': 'TCHM (Producción)',
              'msco_r': 'Mosaico',
              'rya_cfe_r': 'Roya',
              'roya': 'Roya',
              'roya_naranja': 'Roya Naranja',
              'carbon': 'Carbón',
              'volcamiento': 'Volcamiento',
              'altura_planta': 'Altura de Planta',
              'poblacion': 'Población',
              'dmtro_tllo': 'Diámetro de Tallo'
            };
            const colName = p.equivalente.toLowerCase();
            const nombreLegible = nombresLegibles[colName] || p.equivalente.toUpperCase();
            motivos.push(\`\${nombreLegible} excede límite\`);
`;

content = content.replace(oldPush, newPush);
fs.writeFileSync(file, content);
