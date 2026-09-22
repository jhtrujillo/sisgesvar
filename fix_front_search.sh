# 1. Update service
sed -i '' 's/export async function getGermoplasmBankList(currentPage: number, perPage: number)/export async function getGermoplasmBankList(currentPage: number, perPage: number, search: string = "")/g' sivar/src/services/germoplasm.services.ts

sed -i '' 's/return await api.get(url, { params: { perPage, currentPage } }, true);/return await api.get(url, { params: { perPage, page: currentPage, search } }, true);/g' sivar/src/services/germoplasm.services.ts

# 2. Update store
sed -i '' 's/const perPage = ref(5000);/const perPage = ref(50);/g' sivar/src/stores/germoplasm.ts

sed -i '' 's/const getGermoplasmBank = async ()/const getGermoplasmBank = async (search: string = "")/g' sivar/src/stores/germoplasm.ts

sed -i '' 's/perPage.value/perPage.value, search/g' sivar/src/stores/germoplasm.ts

# 3. Update component to use server-side search instead of local computed
# First, remove the filteredGermoplasm computed property
sed -i '' '/const filteredGermoplasm = computed/,/});/d' sivar/src/views/mejoramiento/variedades/GermoplasmBankListView.vue

# Bind table back to store
sed -i '' 's/v-for="germoplasma in filteredGermoplasm"/v-for="germoplasma in GermoplasmBankStore.germplasm"/g' sivar/src/views/mejoramiento/variedades/GermoplasmBankListView.vue

sed -i '' 's/v-if="filteredGermoplasm.length === 0"/v-if="GermoplasmBankStore.germplasm.length === 0"/g' sivar/src/views/mejoramiento/variedades/GermoplasmBankListView.vue

# Add watch to searchText to call the store
sed -i '' '/const searchText = ref("");/a\
import { watch } from "vue";\
let searchTimeout: any;\
watch(searchText, (newVal) => {\
  clearTimeout(searchTimeout);\
  searchTimeout = setTimeout(() => {\
    GermoplasmBankStore.currentPage = 1;\
    GermoplasmBankStore.getGermoplasmBank(newVal);\
  }, 300);\
});\
' sivar/src/views/mejoramiento/variedades/GermoplasmBankListView.vue

# In onMounted, make sure to call with searchText
sed -i '' 's/await GermoplasmBankStore.getGermoplasmBank();/await GermoplasmBankStore.getGermoplasmBank(searchText.value);/g' sivar/src/views/mejoramiento/variedades/GermoplasmBankListView.vue

