const fs = require('fs');

function patchFile() {
  const file = 'src/views/mejoramiento/cruzamientos/CrossingMatrixView.vue';
  let content = fs.readFileSync(file, 'utf8');

  const oldCode = `      const motherSex = motherEmasc ? 'Hembra' : cell.sxo;`;
  const newCode = `      
      // Ajustar polen visual a 0 si esta emasculada
      if (motherEmasc) cell.polen = 0;
      if (fatherEmasc) cell.polen2 = 0;
      
      const motherSex = motherEmasc ? 'Hembra' : cell.sxo;`;

  // Actually, we also need to change the header's flor.polen
  const headerCode = `emasculadasLocales.value.add(varName);`;
  const newHeaderCode = `emasculadasLocales.value.add(varName);

  // Cambiar el polen visual en la cabecera
  const flores = MatrixCrossingStore.matrixCrossingsFilter.flores || [];
  const flor = flores.find((f: any) => f.vrdad === varName);
  if (flor) {
    flor.polen = 0;
  }
`;

  content = content.replace(oldCode, newCode);
  content = content.replace(headerCode, newHeaderCode);
  
  fs.writeFileSync(file, content);
}

patchFile();
