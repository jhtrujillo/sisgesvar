const fs = require('fs');
const content = fs.readFileSync('/Users/estuvar4/Documents/2. software/13. SIVAR/sivar/src/views/mejoramiento/cruzamientos/CrossingSuggestionPerProjectView.vue', 'utf8');
const lines = content.split('\n');

let depth = 0;
for (let i = 0; i < lines.length; i++) {
  const line = lines[i];
  if (i > 825) break; // skip script
  
  let tempLine = line;
  while (true) {
    const openIdx = tempLine.indexOf('<div');
    const closeIdx = tempLine.indexOf('</div');
    
    if (openIdx === -1 && closeIdx === -1) break;
    
    if (openIdx !== -1 && (closeIdx === -1 || openIdx < closeIdx)) {
      depth++;
      console.log(`Line ${i+1}: OPEN div (depth ${depth}) ${tempLine.slice(openIdx, openIdx+40)}`);
      tempLine = tempLine.slice(openIdx + 4);
    } else if (closeIdx !== -1) {
      console.log(`Line ${i+1}: CLOSE div (depth ${depth})`);
      depth--;
      tempLine = tempLine.slice(closeIdx + 6);
    }
  }
}
