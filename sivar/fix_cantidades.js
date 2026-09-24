const fs = require('fs');
const file = 'src/views/mejoramiento/cruzamientos/CrossingSuggestionPerProjectView.vue';
let content = fs.readFileSync(file, 'utf8');

const oldCantidades = `const cantidadesMap = computed(() => {
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

const newCantidades = `const cantidadesMap = computed(() => {
  const filterData = SuggestionCrossingPerProjectStore.suggestionCrossingsPerProjectFilter || {};
  const rawFlores = filterData.flores || [];
  
  const map: Record<string, number> = {};
  
  // En Paso 4, 'flores' contiene el inventario real (f.numero). 
  // flores_bg, flores_pr, etc., son clones con joins agronómicos, sumarlos duplicaría el inventario.
  rawFlores.forEach((f: any) => {
    map[f.vrdad] = f.numero || 0;
  });
  
  return map;
});`;

if (content.includes('addFlores(floresBG);')) {
  content = content.replace(oldCantidades, newCantidades);
  fs.writeFileSync(file, content);
}
