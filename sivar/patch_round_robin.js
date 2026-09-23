const fs = require('fs');
const file = 'src/views/mejoramiento/cruzamientos/CrossingSuggestionPerProjectView.vue';
let content = fs.readFileSync(file, 'utf8');

const oldLogic = `    // 4. Asignación "1 flor a la vez" para esta madre
    for (let i = 0; i < crossesForMother.length; i++) {
      const item = crossesForMother[i];
      const p = item.varB;

      // Si la madre tiene espacio Y el padre también tiene espacio
      if (disp.madre[m] > usadas.madre[m] && disp.padre[p] > usadas.padre[p]) {
        item.car.viabilidad = true;
        item.car.flores_madre = 1;
        item.car.flores_padre = 1;

        usadas.madre[m]++;
        usadas.padre[p]++;
        adiciones++;
      }
      // Si la madre ya se quedó sin flores (usadas == disp), ya no buscamos más padres para ella
      if (usadas.madre[m] >= disp.madre[m]) break;
    }`;

const newLogic = `    // 4. Asignación iterativa (Round-Robin) para maximizar uso de flores
    let madeAssignment = true;
    while (madeAssignment && usadas.madre[m] < disp.madre[m]) {
      madeAssignment = false;
      for (let i = 0; i < crossesForMother.length; i++) {
        if (usadas.madre[m] >= disp.madre[m]) break;
        
        const item = crossesForMother[i];
        const p = item.varB;

        // Si el padre todavía tiene espacio en esta ronda
        if (disp.padre[p] > usadas.padre[p]) {
          item.car.viabilidad = true;
          item.car.flores_madre = (item.car.flores_madre || 0) + 1;
          item.car.flores_padre = (item.car.flores_padre || 0) + 1;

          usadas.madre[m]++;
          usadas.padre[p]++;
          adiciones++;
          madeAssignment = true;
        }
      }
    }`;

content = content.replace(oldLogic, newLogic);
fs.writeFileSync(file, content);
