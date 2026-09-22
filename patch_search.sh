# Bind table to filteredGermoplasm instead of GermoplasmBankStore.germplasm
sed -i '' 's/v-for="germoplasma in GermoplasmBankStore.germplasm"/v-for="germoplasma in filteredGermoplasm"/' sivar/src/views/mejoramiento/variedades/GermoplasmBankListView.vue

sed -i '' 's/v-if="GermoplasmBankStore.germplasm.length === 0"/v-if="filteredGermoplasm.length === 0"/' sivar/src/views/mejoramiento/variedades/GermoplasmBankListView.vue

# Add computed filteredGermoplasm to script setup
sed -i '' '/const searchText = ref("");/a\
const filteredGermoplasm = computed(() => {\
  const text = searchText.value.trim().toLowerCase();\
  if (!text) return GermoplasmBankStore.germplasm;\
  return GermoplasmBankStore.germplasm.filter(g => \
    Object.values(g).some(val => \
      val !== null && val.toString().toLowerCase().includes(text)\
    )\
  );\
});\
' sivar/src/views/mejoramiento/variedades/GermoplasmBankListView.vue

# Remove manual updateFilteredGermoplasmaBank since we use computed property directly
sed -i '' 's/@input="updateFilteredGermoplasmaBank"//g' sivar/src/views/mejoramiento/variedades/GermoplasmBankListView.vue

# Remove original search function
sed -i '' '/const filteredJornalesList = computed/,/};/d' sivar/src/views/mejoramiento/variedades/GermoplasmBankListView.vue
