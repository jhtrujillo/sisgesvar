const fs = require('fs');

function patchFile() {
  const file = 'src/views/mejoramiento/cruzamientos/CrossingMatrixView.vue';
  let content = fs.readFileSync(file, 'utf8');

  // 1. Add Desemascular buttons to the headers
  const colSpanTarget = `<span v-else-if="isEmasculatedLocal(flor.vrdad)" class="mt-1 text-rose-600 text-[8px] font-black uppercase block">
                      [EMASCULADA]
                    </span>`;
  const newColSpanTarget = `<span v-else-if="isEmasculatedLocal(flor.vrdad)" class="mt-1 flex flex-col items-center">
                      <span class="text-rose-600 text-[8px] font-black uppercase block">[EMASCULADA]</span>
                      <button @click.stop="revertEmasculate(flor.vrdad)" class="mt-0.5 text-[8px] underline text-slate-500 hover:text-slate-700">Deshacer</button>
                    </span>`;
  content = content.replace(colSpanTarget, newColSpanTarget);

  const rowSpanTarget = `<span v-else-if="isEmasculatedLocal(viabilidadRow[0].varA)" class="mt-1 text-rose-600 text-[8px] font-black uppercase block">
                      [EMASCULADA]
                    </span>`;
  const newRowSpanTarget = `<span v-else-if="isEmasculatedLocal(viabilidadRow[0].varA)" class="mt-1 flex flex-col items-center">
                      <span class="text-rose-600 text-[8px] font-black uppercase block">[EMASCULADA]</span>
                      <button @click.stop="revertEmasculate(viabilidadRow[0].varA)" class="mt-0.5 text-[8px] underline text-slate-500 hover:text-slate-700">Deshacer</button>
                    </span>`;
  content = content.replace(rowSpanTarget, newRowSpanTarget);

  // 2. Add originalData tracking and update confirmGlobalEmasculate
  // First, find the declaration of emasculadasLocales
  const injectOriginals = `const emasculadasLocales = ref(new Set<string>());\nconst emasculadasOriginalData = ref(new Map<string, any>());`;
  content = content.replace('const emasculadasLocales = ref(new Set<string>());', injectOriginals);

  // Rewrite confirmGlobalEmasculate and add revertEmasculate
  const oldFuncRegex = /const confirmGlobalEmasculate = \(\) => \{[\s\S]*?showEmasculateModal\.value = false;\n\};/;
  
  const newFuncs = `
const confirmGlobalEmasculate = () => {
  const varName = emasculateTargetVar.value;
  emasculadasLocales.value.add(varName);

  // Guardar datos originales
  const flores = MatrixCrossingStore.matrixCrossingsFilter.flores || [];
  const flor = flores.find((f: any) => f.vrdad === varName);
  if (flor) {
    if (!emasculadasOriginalData.value.has(varName)) {
      emasculadasOriginalData.value.set(varName, { polen: flor.polen, sxo: flor.sxo });
    }
    flor.polen = 0;
    flor.sxo = 'Hembra'; // <- Ahora dice Hembra
  }

  recalculateMatrixViability();
  
  toast.success('Variedad ' + varName + ' emasculada. Matriz recalculada.');
  showEmasculateModal.value = false;
};

const revertEmasculate = (varName: string) => {
  emasculadasLocales.value.delete(varName);
  
  const flores = MatrixCrossingStore.matrixCrossingsFilter.flores || [];
  const flor = flores.find((f: any) => f.vrdad === varName);
  if (flor) {
    const orig = emasculadasOriginalData.value.get(varName);
    if (orig) {
      flor.polen = orig.polen;
      flor.sxo = orig.sxo;
    }
  }

  recalculateMatrixViability();
  toast.info('Variedad ' + varName + ' restaurada a su estado original.');
};

const recalculateMatrixViability = () => {
  const viabilidades = MatrixCrossingStore.matrixCrossingsFilter.viabilidad || [];
  viabilidades.forEach((row: any) => {
    row.forEach((cell: any) => {
      if (!cell || !cell.varA || !cell.varB) return;
      
      const motherEmasc = emasculadasLocales.value.has(cell.varA);
      const fatherEmasc = emasculadasLocales.value.has(cell.varB);
      
      if (motherEmasc) {
          cell.polen = 0;
          cell.sxo = 'Hembra';
      } else {
          const origA = emasculadasOriginalData.value.get(cell.varA);
          if (origA) {
              cell.polen = origA.polen;
              cell.sxo = origA.sxo;
          }
      }
      
      if (fatherEmasc) {
          cell.polen2 = 0;
          cell.sxo2 = 'Hembra';
      } else {
          const origB = emasculadasOriginalData.value.get(cell.varB);
          if (origB) {
              cell.polen2 = origB.polen;
              cell.sxo2 = origB.sxo;
          }
      }

      const motherSex = cell.sxo;
      const fatherSex = cell.sxo2;

      // Restablecer el veto primero, borrando vetos previos inyectados por emasculacion o desemasculacion
      if (cell.causa_veto) {
        cell.causa_veto = cell.causa_veto.replace(/\\s*\\|?\\s*Incompatibilidad de sexo \\(Ambos son Macho\\)/, '');
        cell.causa_veto = cell.causa_veto.replace(/\\s*\\|?\\s*Incompatibilidad de sexo \\(Ambas son Hembra\\)/, '');
        if (cell.causa_veto === 'Incompatibilidad de sexo (Ambos son Macho)' || cell.causa_veto === 'Incompatibilidad de sexo (Ambas son Hembra)' || cell.causa_veto === 'Cruce viable') {
           cell.causa_veto = '';
        }
      }

      // Re-aplicar vetos
      if (motherSex === 'Macho' && fatherSex === 'Macho') {
         cell.viabilidad = false;
         cell.emasculado = false;
         cell.causa_veto = (cell.causa_veto && cell.causa_veto !== '' ? cell.causa_veto + ' | ' : '') + 'Incompatibilidad de sexo (Ambos son Macho)';
      } else if (motherSex === 'Hembra' && fatherSex === 'Hembra') {
         cell.viabilidad = false;
         cell.emasculado = false;
         cell.causa_veto = (cell.causa_veto && cell.causa_veto !== '' ? cell.causa_veto + ' | ' : '') + 'Incompatibilidad de sexo (Ambas son Hembra)';
      } else {
         if (!cell.causa_veto || cell.causa_veto.trim() === '') {
            cell.viabilidad = true;
            if (motherEmasc) cell.emasculado = true;
            else cell.emasculado = false;
            cell.causa_veto = 'Cruce viable';
         } else {
            cell.viabilidad = false;
            cell.emasculado = false;
         }
      }
    });
  });
};
`;

  content = content.replace(oldFuncRegex, newFuncs);
  
  fs.writeFileSync(file, content);
}

patchFile();
