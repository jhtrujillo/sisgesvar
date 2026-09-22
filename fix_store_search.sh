sed -i '' '/const currentPage = ref(1);/a\
  const currentSearch = ref("");\
' sivar/src/stores/germoplasm.ts

sed -i '' 's/const getGermoplasmBank = async (search: string = "") {/const getGermoplasmBank = async (search: string = "") => {/g' sivar/src/stores/germoplasm.ts

sed -i '' '/const getGermoplasmBank = async (search/a\
    currentSearch.value = search;\
' sivar/src/stores/germoplasm.ts

sed -i '' 's/perPage.value, search/perPage.value, currentSearch.value/g' sivar/src/stores/germoplasm.ts

sed -i '' 's/await getGermoplasmBank();/await getGermoplasmBank(currentSearch.value);/g' sivar/src/stores/germoplasm.ts
