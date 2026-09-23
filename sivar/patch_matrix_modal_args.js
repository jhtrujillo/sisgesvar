const fs = require('fs');
const file = 'src/views/mejoramiento/cruzamientos/CrossingMatrixView.vue';
let content = fs.readFileSync(file, 'utf8');

// 1. Template
content = content.replace(
  /@click\.stop="openParentComparator\(car\?\.varA, car\?\.varB, car\?\.viabilidad\)"/g,
  '@click.stop="openParentComparator(car?.varA, car?.varB, car?.viabilidad, car?.causa_veto)"'
);

// 2. State
const stateToAdd = `const comparatorInitiallyViable = ref(true);
const comparatorCausa = ref("");`;
content = content.replace('const comparatorInitiallyViable = ref(true);', stateToAdd);

// 3. Method
const oldMethod = `const openParentComparator = (mother: string, father: string, viable: boolean) => {
  if (mother && father) {
    comparatorMother.value = mother;
    comparatorFather.value = father;
    comparatorInitiallyViable.value = viable;
    isComparatorOpen.value = true;
  }
};`;

const newMethod = `const openParentComparator = (mother: string, father: string, viable: boolean, causa: string = "") => {
  if (mother && father) {
    comparatorMother.value = mother;
    comparatorFather.value = father;
    comparatorInitiallyViable.value = viable;
    comparatorCausa.value = causa;
    isComparatorOpen.value = true;
  }
};`;
content = content.replace(oldMethod, newMethod);

// 4. Component usage
content = content.replace(
  /<ParentComparatorModal\s*:isOpen="isComparatorOpen"/,
  '<ParentComparatorModal :isOpen="isComparatorOpen" :causaVeto="comparatorCausa"'
);

fs.writeFileSync(file, content);
