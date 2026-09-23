const fs = require('fs');

function patchPaso3() {
  const file = 'src/views/mejoramiento/cruzamientos/CrossingMatrixView.vue';
  let content = fs.readFileSync(file, 'utf8');

  const computedKey = 'const draftKey = computed(() => `sivarcc_draft_crossings_${selectedCdCntble.value}_${selectedMegaAmbiente.value}`);';
  const computedEmascKey = 'const draftKey = computed(() => `sivarcc_draft_crossings_${selectedCdCntble.value}_${selectedMegaAmbiente.value}`);\nconst emasculadasKey = computed(() => `sivarcc_draft_emasculadas_${selectedCdCntble.value}_${selectedMegaAmbiente.value}`);';
  
  content = content.replace(computedKey, computedEmascKey);
  
  // Re-run the confirm save replacements in case they didn't run
  const confirmSave = `  recalculateMatrixViability();
  
  toast.success('Variedad ' + varName + ' emasculada. Matriz recalculada.');`;
  const confirmSaveNew = `  recalculateMatrixViability();
  
  if (typeof emasculadasKey !== 'undefined' && emasculadasKey.value) {
    localStorage.setItem(emasculadasKey.value, JSON.stringify(Array.from(emasculadasLocales.value)));
  }
  
  toast.success('Variedad ' + varName + ' emasculada. Matriz recalculada.');`;
  if (!content.includes('localStorage.setItem(emasculadasKey.value')) {
      content = content.replace(confirmSave, confirmSaveNew);
  }

  const revertSave = `  recalculateMatrixViability();
  toast.info('Variedad ' + varName + ' restaurada a su estado original.');`;
  const revertSaveNew = `  recalculateMatrixViability();
  
  if (typeof emasculadasKey !== 'undefined' && emasculadasKey.value) {
    localStorage.setItem(emasculadasKey.value, JSON.stringify(Array.from(emasculadasLocales.value)));
  }
  
  toast.info('Variedad ' + varName + ' restaurada a su estado original.');`;
  if (!content.includes("restaurada a su estado original.')") || content.includes(revertSave)) {
      content = content.replace(revertSave, revertSaveNew);
  }

  fs.writeFileSync(file, content);
}

function patchPaso4() {
  const file = 'src/views/mejoramiento/cruzamientos/CrossingSuggestionPerProjectView.vue';
  let content = fs.readFileSync(file, 'utf8');

  const computedKey = 'const draftKey = computed(() => `sivarcc_draft_crossings_${selectedCdCntble.value}_${selectedMegaAmbiente.value}`);';
  const computedEmascKey = 'const draftKey = computed(() => `sivarcc_draft_crossings_${selectedCdCntble.value}_${selectedMegaAmbiente.value}`);\nconst emasculadasKey = computed(() => `sivarcc_draft_emasculadas_${selectedCdCntble.value}_${selectedMegaAmbiente.value}`);';
  
  if (!content.includes('const emasculadasKey = computed')) {
      content = content.replace(computedKey, computedEmascKey);
  }

  // En loadSuggestionCrossings, despues de respaldar viabilidad
  const injectTarget = `      // Restaurar borrador de cruzamientos si existe
      const storedDraft = localStorage.getItem(draftKey.value);`;
      
  const injectLogic = `      // 1. Restaurar EMASCULADAS primero para moverlas de columna a fila
      const storedEmasc = localStorage.getItem(emasculadasKey.value);
      if (storedEmasc) {
        const emascSet = new Set(JSON.parse(storedEmasc));
        const rawFlores = SuggestionCrossingPerProjectStore.suggestionCrossingsPerProjectFilter?.flores || [];
        rawFlores.forEach((flor: any) => {
          if (emascSet.has(flor.vrdad)) {
            flor.polen = 0;
            flor.sxo = 'Hembra';
          }
        });
        
        const rows = viabilidadesMatriz.value || [];
        rows.forEach((row: any) => {
          row.forEach((car: any) => {
            if (car && car.varA && car.varB) {
              if (emascSet.has(car.varA)) {
                car.polen = 0;
                car.sxo = 'Hembra';
              }
              if (emascSet.has(car.varB)) {
                car.polen2 = 0;
                car.sxo2 = 'Hembra';
              }
            }
          });
        });
      }

      // Restaurar borrador de cruzamientos si existe
      const storedDraft = localStorage.getItem(draftKey.value);`;

  if (!content.includes('storedEmasc')) {
      content = content.replace(injectTarget, injectLogic);
  }

  fs.writeFileSync(file, content);
}

patchPaso3();
patchPaso4();
