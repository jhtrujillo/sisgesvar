const fs = require('fs');

const file = 'src/views/mejoramiento/cruzamientos/CrossingSuggestionPerProjectView.vue';
let content = fs.readFileSync(file, 'utf8');

// 1. Quitar el rechazo por val !== -9999
const oldFilter = `      if (m !== p && val !== -9999 && isBiologicallyValid) {`;
const newFilter = `      if (m !== p && isBiologicallyValid) {`;
content = content.replace(oldFilter, newFilter);

// 2. Parchear cantidadesMap para que incluya Bg, PR, EIII
const oldCantidades = `const cantidadesMap = computed(() => {
  const rawFlores = SuggestionCrossingPerProjectStore.suggestionCrossingsPerProjectFilter?.flores || [];
  const map: Record<string, number> = {};
  rawFlores.forEach((f: any) => {
    map[f.vrdad] = f.numero;
  });
  return map;
});`;

const newCantidades = `const cantidadesMap = computed(() => {
  const filterData = SuggestionCrossingPerProjectStore.suggestionCrossingsPerProjectFilter || {};
  const rawFlores = filterData.flores || [];
  const floresBG = filterData.flores_bg || [];
  const floresPR = filterData.flores_pr || [];
  const floresEIII = filterData.flores_eiii || [];
  
  const map: Record<string, number> = {};
  
  const addFlores = (arr: any[]) => {
    arr.forEach((f: any) => {
      if (!map[f.vrdad]) map[f.vrdad] = 0;
      map[f.vrdad] += (f.numero || f.cantidad_flores || 0);
    });
  };
  
  addFlores(rawFlores);
  addFlores(floresBG);
  addFlores(floresPR);
  addFlores(floresEIII);
  
  return map;
});`;

content = content.replace(oldCantidades, newCantidades);

fs.writeFileSync(file, content);
