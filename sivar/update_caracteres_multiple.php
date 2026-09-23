<?php
$file = 'src/views/mejoramiento/cruzamientos/CrossingWeightedView.vue';
$content = file_get_contents($file);

// 1. Change reactive variable from string to array
$content = str_replace(
    "const caracterIndividual = ref<string>('');",
    "const caracteresSeleccionados = ref<any[]>([]);",
    $content
);

// 2. Change the template UI from <select> to checkboxes
$oldUI = <<<EOD
          <!-- Select the specific character if individual -->
          <div v-if="estrategia === 'individual'" class="mt-4 p-4 bg-amber-50 rounded-xl border border-amber-100">
            <label class="block uppercase tracking-wider text-amber-800 text-[10px] font-bold mb-2">Seleccione el carácter objetivo</label>
            <select v-model="caracterIndividual" class="w-full md:w-1/2 rounded-lg border-amber-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm p-2 outline-none">
              <option value="" disabled>Seleccione...</option>
              <option v-for="car in caracteresProyecto" :key="car.id" :value="car.id">
                {{ car.nombre }}
              </option>
            </select>
            <p class="text-[10px] text-amber-700 mt-2 font-medium flex items-center gap-1.5">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              Al generar la matriz, se asignará 100% de peso a este carácter y 0% al resto.
            </p>
          </div>
EOD;

$newUI = <<<EOD
          <!-- Checkboxes for specific characters -->
          <div v-if="estrategia === 'individual'" class="mt-4 p-4 bg-amber-50 rounded-xl border border-amber-100">
            <label class="block uppercase tracking-wider text-amber-800 text-[10px] font-bold mb-3">Seleccione los caracteres objetivo</label>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
              <label v-for="car in caracteresProyecto" :key="car.id" class="flex items-center gap-2 cursor-pointer p-2 rounded hover:bg-amber-100/50 transition">
                <input type="checkbox" v-model="caracteresSeleccionados" :value="car.id" class="w-4 h-4 text-amber-600 focus:ring-amber-500 rounded border-amber-300" />
                <span class="text-sm font-medium text-amber-900">{{ car.nombre }}</span>
              </label>
            </div>
            <p class="text-[10px] text-amber-700 mt-4 font-medium flex items-center gap-1.5">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              Solo las flores asociadas a los caracteres seleccionados serán incluidas en la matriz.
            </p>
          </div>
EOD;
$content = str_replace($oldUI, $newUI, $content);

// 3. Update the handleSiguiente logic
$oldLogic = <<<EOD
const handleSiguiente = async () => {
  if (estrategia.value === 'individual') {
    if (!caracterIndividual.value) {
      toast.error("Debe seleccionar un carácter objetivo");
      return;
    }
    localStorage.setItem('filtroCaracterIndividual', caracterIndividual.value);
  } else {
    localStorage.removeItem('filtroCaracterIndividual');
  }
  
  // Continuar a la siguiente vista
  router.push({ name: 'crossing_matrix.show' });
};
EOD;

$newLogic = <<<EOD
const handleSiguiente = async () => {
  if (estrategia.value === 'individual') {
    if (caracteresSeleccionados.value.length === 0) {
      toast.error("Debe seleccionar al menos un carácter objetivo");
      return;
    }
    // Convert array to comma-separated string for local storage
    localStorage.setItem('filtroCaracterIndividual', caracteresSeleccionados.value.join(','));
  } else {
    localStorage.removeItem('filtroCaracterIndividual');
  }
  
  // Continuar a la siguiente vista
  router.push({ name: 'crossing_matrix.show' });
};
EOD;
$content = str_replace($oldLogic, $newLogic, $content);

// 4. Update the "Generar Matriz" button disabled state
$oldDisabled = "caracterIndividual)";
$newDisabled = "caracteresSeleccionados.length === 0)";
$content = str_replace($oldDisabled, $newDisabled, $content);

// 5. Update reset on project change
$oldReset = "caracterIndividual.value = '';";
$newReset = "caracteresSeleccionados.value = [];";
$content = str_replace($oldReset, $newReset, $content);

file_put_contents($file, $content);
