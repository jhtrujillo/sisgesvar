const fs = require('fs');
const file = 'src/views/mejoramiento/cruzamientos/CrossingSuggestionPerProjectView.vue';
let content = fs.readFileSync(file, 'utf8');

// 1. Add `permitirPolicruzamientos` reactive var
const constDef = `const isOptimizing = ref(false);`;
if (!content.includes('const permitirPolicruzamientos = ref(false);')) {
  content = content.replace(constDef, constDef + '\nconst permitirPolicruzamientos = ref(false);');
}

// 2. Add checkbox in UI next to Optimizar button
const uiTarget = `<button
              type="button"
              @click="autoOptimizarFlores"`;
const uiReplacement = `<label class="flex items-center space-x-1.5 cursor-pointer text-[11px] text-slate-600 font-bold mr-3" title="Ignorar restricción biológica de sexo para sugerir cruces inversos">
              <input type="checkbox" v-model="permitirPolicruzamientos" class="rounded h-3.5 w-3.5 border-slate-300 text-indigo-600 focus:ring-indigo-500" />
              <span>Policruzamientos</span>
            </label>
            <button
              type="button"
              @click="autoOptimizarFlores"`;
if (!content.includes('v-model="permitirPolicruzamientos"')) {
  content = content.replace(uiTarget, uiReplacement);
}

// 3. Modify autoOptimizarFlores to respect toggle
const strictFilter = `      let isBiologicallyValid = car.original_viabilidad === true;
      if (causa.includes("Incompatibilidad de sexo")) isBiologicallyValid = false;
      if (causa.includes("Restricción de Autogamia")) isBiologicallyValid = false;
      if (causa.includes("excede límite")) isBiologicallyValid = false;

      // Regla de polen de la interfaz: El padre DEBE tener polen > 20
      if (Number(car?.polen2) <= 20) isBiologicallyValid = false;`;

const newFilter = `      let isBiologicallyValid = car.original_viabilidad === true;
      
      if (!permitirPolicruzamientos.value) {
        if (causa.includes("Incompatibilidad de sexo")) isBiologicallyValid = false;
        // Regla de polen de la interfaz: El padre DEBE tener polen > 20
        if (Number(car?.polen2) <= 20) isBiologicallyValid = false;
      } else {
        // En policruzamientos, ignorar vetos de sexo, forzando a true si el único problema era el sexo
        if (causa.includes("Incompatibilidad de sexo") && !causa.includes("excede límite") && !causa.includes("Restricción de Autogamia")) {
           isBiologicallyValid = true;
        }
      }
      
      if (causa.includes("Restricción de Autogamia")) isBiologicallyValid = false;
      if (causa.includes("excede límite")) isBiologicallyValid = false;`;

if (!content.includes('if (!permitirPolicruzamientos.value) {')) {
  content = content.replace(strictFilter, newFilter);
}

fs.writeFileSync(file, content);
