const fs = require('fs');

function patchMatrixView() {
  const file = 'src/views/mejoramiento/cruzamientos/CrossingMatrixView.vue';
  let content = fs.readFileSync(file, 'utf8');

  // 1. Añadir el botón en la columna (Padre)
  const colSpanTarget = `Polen: {{ flor.polen }} ({{ flor.sxo }}) | Flores: {{ flor.cantidad_flores || 0 }}</span>`;
  const colButton = `
                    <button 
                      v-if="flor.sxo === 'Macho' && !isEmasculatedLocal(flor.vrdad)"
                      @click="promptEmasculate(flor.vrdad)"
                      class="mt-1 bg-rose-100 hover:bg-rose-200 text-rose-700 text-[8px] font-bold py-0.5 px-1.5 rounded uppercase mx-auto block"
                    >
                      Emascular
                    </button>
                    <span v-else-if="isEmasculatedLocal(flor.vrdad)" class="mt-1 text-rose-600 text-[8px] font-black uppercase block">
                      [EMASCULADA]
                    </span>`;
  content = content.replace(colSpanTarget, colSpanTarget + colButton);

  // 2. Añadir el botón en la fila (Madre)
  const rowSpanTarget = `Polen: {{ viabilidadRow[0].polen }} ({{ viabilidadRow[0].sxo }}) | Flores: {{ viabilidadRow[0].cantidad_flores || 0 }}</span>`;
  const rowButton = `
                    <button 
                      v-if="viabilidadRow[0].sxo === 'Macho' && !isEmasculatedLocal(viabilidadRow[0].varA)"
                      @click="promptEmasculate(viabilidadRow[0].varA)"
                      class="mt-1 bg-rose-100 hover:bg-rose-200 text-rose-700 text-[8px] font-bold py-0.5 px-1.5 rounded uppercase mx-auto block"
                    >
                      Emascular
                    </button>
                    <span v-else-if="isEmasculatedLocal(viabilidadRow[0].varA)" class="mt-1 text-rose-600 text-[8px] font-black uppercase block">
                      [EMASCULADA]
                    </span>`;
  content = content.replace(rowSpanTarget, rowSpanTarget + rowButton);

  // 3. Añadir la lógica de JavaScript en <script setup>
  const jsLogic = `
const emasculadasLocales = ref(new Set<string>());

const isEmasculatedLocal = (varName: string) => {
  return emasculadasLocales.value.has(varName);
};

const promptEmasculate = (varName: string) => {
  emasculateTargetVar.value = varName;
  // Usamos el mismo modal pero sin "emasculateTargetCar", indicando que es global para la variedad
  emasculateTargetCar.value = null; 
  showEmasculateModal.value = true;
};

const confirmGlobalEmasculate = () => {
  const varName = emasculateTargetVar.value;
  emasculadasLocales.value.add(varName);

  // Mutar la matriz entera
  const viabilidades = MatrixCrossingStore.matrixCrossingsFilter.viabilidad || [];
  viabilidades.forEach((row: any) => {
    row.forEach((cell: any) => {
      if (!cell || !cell.varA || !cell.varB) return;

      const isMother = cell.varA === varName;
      const isFather = cell.varB === varName;
      
      const motherEmasc = emasculadasLocales.value.has(cell.varA);
      const fatherEmasc = emasculadasLocales.value.has(cell.varB);
      
      const motherSex = motherEmasc ? 'Hembra' : cell.sxo;
      const fatherSex = fatherEmasc ? 'Hembra' : cell.sxo2;

      // Evaluar la nueva viabilidad basada solo en sexo, asumiendo que los limites agronomicos ya estan en causa_veto
      if (motherSex === 'Macho' && fatherSex === 'Macho') {
         cell.viabilidad = false;
         if (!cell.causa_veto?.includes('Incompatibilidad de sexo (Ambos son Macho)')) {
            cell.causa_veto = (cell.causa_veto && cell.causa_veto !== 'Cruce viable' ? cell.causa_veto + ' | ' : '') + 'Incompatibilidad de sexo (Ambos son Macho)';
         }
      } else if (motherSex === 'Hembra' && fatherSex === 'Hembra') {
         cell.viabilidad = false;
         // Remplazar el veto de Macho por Hembra si existia
         if (cell.causa_veto?.includes('Incompatibilidad de sexo (Ambos son Macho)')) {
            cell.causa_veto = cell.causa_veto.replace('Incompatibilidad de sexo (Ambos son Macho)', 'Incompatibilidad de sexo (Ambas son Hembra)');
         } else if (!cell.causa_veto?.includes('Incompatibilidad de sexo (Ambas son Hembra)')) {
            cell.causa_veto = (cell.causa_veto && cell.causa_veto !== 'Cruce viable' ? cell.causa_veto + ' | ' : '') + 'Incompatibilidad de sexo (Ambas son Hembra)';
         }
      } else {
         // Sexos compatibles (Hembra x Macho)
         // Eliminar los vetos de sexo
         if (cell.causa_veto) {
           cell.causa_veto = cell.causa_veto.replace(/\\s*\\|?\\s*Incompatibilidad de sexo \\(Ambos son Macho\\)/, '');
           cell.causa_veto = cell.causa_veto.replace(/\\s*\\|?\\s*Incompatibilidad de sexo \\(Ambas son Hembra\\)/, '');
           if (cell.causa_veto === 'Incompatibilidad de sexo (Ambos son Macho)' || cell.causa_veto === 'Incompatibilidad de sexo (Ambas son Hembra)') {
             cell.causa_veto = '';
           }
         }
         
         // Si despues de quitar el veto de sexo no quedan vetos agronomicos, es viable!
         if (!cell.causa_veto || cell.causa_veto.trim() === '') {
            cell.viabilidad = true;
            if (motherEmasc) cell.emasculado = true;
            cell.causa_veto = 'Cruce viable';
         } else {
            cell.viabilidad = false;
         }
      }
    });
  });
  
  toast.success('Variedad ' + varName + ' emasculada. Matriz recalculada.');
  showEmasculateModal.value = false;
};
`;
  
  const injectTarget = 'const emasculateTargetCar = ref<any>(null);';
  content = content.replace(injectTarget, injectTarget + '\n' + jsLogic);

  // Re-rutear el boton del modal a confirmGlobalEmasculate si emasculateTargetCar es nulo
  const btnTarget = 'const confirmEmasculateAction = () => {';
  const newBtnAction = `
const confirmEmasculateAction = () => {
  if (!emasculateTargetCar.value) {
    return confirmGlobalEmasculate();
  }
`;
  content = content.replace(btnTarget, newBtnAction);

  // Tambien, quitar la etiqueta de EMASCULADA en la celda porque el header ya lo tiene, O dejarla, es opcional. El usuario pidio el boton en el header.
  
  fs.writeFileSync(file, content);
}

patchMatrixView();
