const fs = require('fs');
const content = fs.readFileSync('/Users/estuvar4/Documents/2. software/13. SIVAR/sivar/src/views/mejoramiento/cruzamientos/CrossingSuggestionPerProjectView.vue', 'utf8');
const templateMatch = content.match(/<template>([\s\S]*?)<\/template>/);
if (!templateMatch) process.exit(1);

const template = templateMatch[1];
const lines = template.split('\n');

let stack = [];

for (let i = 0; i < lines.length; i++) {
  const line = lines[i];
  
  // A simple regex to find opening tags <tagName ...>
  // and closing tags </tagName>
  // We'll just look for <div and </div for simplicity, 
  // since the error is about a div with v-else
  
  let tempLine = line;
  while (true) {
    const openIdx = tempLine.indexOf('<div');
    const closeIdx = tempLine.indexOf('</div');
    
    if (openIdx === -1 && closeIdx === -1) break;
    
    if (openIdx !== -1 && (closeIdx === -1 || openIdx < closeIdx)) {
      const match = tempLine.slice(openIdx).match(/<div[^>]*>/);
      const tagStr = match ? match[0] : '<div...>';
      stack.push({ line: i + 2, tag: tagStr });
      tempLine = tempLine.slice(openIdx + 4);
    } else if (closeIdx !== -1) {
      const last = stack.pop();
      if (last && (last.tag.includes('v-if') || last.tag.includes('v-else'))) {
         console.log(`Line ${last.line} opened ${last.tag.substring(0,40)}... and CLOSED at line ${i + 2}`);
      }
      tempLine = tempLine.slice(closeIdx + 5);
    }
  }
}
