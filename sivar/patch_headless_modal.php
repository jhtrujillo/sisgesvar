<?php

function patchFile($file) {
    $content = file_get_contents($file);

    $modalHTML = <<<HTML

    <!-- Modal HTML de Emasculación -->
    <div v-if="showEmasculateModal" class="relative z-[9999]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="fixed inset-0 bg-slate-900/60 transition-opacity"></div>
      <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
          <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-rose-100 sm:mx-0 sm:h-10 sm:w-10">
                  <svg class="h-6 w-6 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                </div>
                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                  <h3 class="text-lg font-black leading-6 text-slate-900" id="modal-title">Atención: Incompatibilidad de Sexo</h3>
                  <div class="mt-2">
                    <p class="text-sm text-slate-500 font-medium">
                      Ambos parentales seleccionados son masculinos. Para que la polinización sea biológicamente viable, la variedad receptora debe ser emasculada.
                    </p>
                    <div class="mt-4 bg-slate-50 border border-slate-100 rounded-lg p-3">
                      <p class="text-sm text-slate-700 font-bold">
                        ¿Autoriza registrar a la variedad <span class="text-rose-600 font-black">{{ emasculateTargetVar }}</span> como <span class="uppercase font-black">emasculada</span> para programar este cruzamiento?
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
              <button @click="confirmEmasculateAction" type="button" class="inline-flex w-full justify-center rounded-lg bg-rose-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-rose-500 sm:ml-3 sm:w-auto">Autorizar Emasculación</button>
              <button @click="cancelEmasculateAction" type="button" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-4 py-2.5 text-sm font-bold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto">Cancelar</button>
            </div>
          </div>
        </div>
      </div>
    </div>
HTML;
    
    // Insert before the LAST </template>
    $pos = strrpos($content, '</template>');
    if ($pos !== false) {
        $content = substr_replace($content, $modalHTML . "\n</template>", $pos, strlen('</template>'));
    }

    $oldScript = <<<JS
    const confirmEmasculate = confirm("⚠️ Atención: Incompatibilidad de sexo.\\n\\nAmbos parentales son masculinos. Para que la polinización sea posible, la variedad receptora debe ser emasculada.\\n\\n¿Autoriza registrar a la variedad (" + car.varA + ") como emasculada para programar este cruzamiento?");
    if (!confirmEmasculate) {
      return; // Abortar
    }
    car.emasculado = true;
JS;

    $newScript = <<<JS
    emasculateTargetVar.value = car.varA;
    emasculateTargetCar.value = car;
    showEmasculateModal.value = true;
    return; // Esperamos la accion del modal
JS;

    $content = str_replace($oldScript, $newScript, $content);

    $scriptAdd = <<<JS
const showEmasculateModal = ref(false);
const emasculateTargetVar = ref("");
const emasculateTargetCar = ref<any>(null);

const confirmEmasculateAction = () => {
  if (emasculateTargetCar.value) {
    const car = emasculateTargetCar.value;
    car.emasculado = true;
    
    // Continuar con la logica original
    car.viabilidad = true;
    if (car.flores_madre !== undefined) {
      car.flores_madre = 1;
      car.flores_padre = 1;
    }
    
    // Save draft
    if (typeof draftKey !== 'undefined' && typeof viabilidadesMatriz !== 'undefined') {
       const rows = viabilidadesMatriz.value || [];
       const savedState = [];
       rows.forEach((row) => {
         if (row && Array.isArray(row)) {
           row.forEach((c) => {
             if (c && c.varA && c.varB) {
               savedState.push({ varA: c.varA.trim(), varB: c.varB.trim(), viabilidad: !!c.viabilidad, emasculado: !!c.emasculado });
             }
           });
         }
       });
       localStorage.setItem(draftKey.value, JSON.stringify(savedState));
    } else if (typeof draftKey !== 'undefined' && typeof MatrixCrossingStore !== 'undefined') {
       const savedState = [];
       const viabilidades = MatrixCrossingStore.matrixCrossingsFilter.viabilidad || [];
       viabilidades.forEach((row) => {
         if (row && Array.isArray(row)) {
           row.forEach((c) => {
             if (c && c.varA && c.varB) {
               savedState.push({ varA: c.varA.trim(), varB: c.varB.trim(), viabilidad: !!c.viabilidad, emasculado: !!c.emasculado });
             }
           });
         }
       });
       localStorage.setItem(draftKey.value, JSON.stringify(savedState));
    }
  }
  showEmasculateModal.value = false;
};

const cancelEmasculateAction = () => {
  emasculateTargetCar.value = null;
  showEmasculateModal.value = false;
};
JS;

    $posScript = strpos($content, '<script setup lang="ts">');
    if ($posScript !== false) {
        $posImportsEnd = strpos($content, "\n\n", $posScript);
        if ($posImportsEnd === false) $posImportsEnd = $posScript + 24;
        
        $content = substr_replace($content, "\n" . $scriptAdd . "\n", $posImportsEnd, 0);
    }
    
    file_put_contents($file, $content);
}

patchFile('src/views/mejoramiento/cruzamientos/CrossingMatrixView.vue');
patchFile('src/views/mejoramiento/cruzamientos/CrossingSuggestionPerProjectView.vue');

