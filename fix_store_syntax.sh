sed -i '' 's/Math.ceil(totalRecords.value \/ perPage.value, currentSearch.value)/Math.ceil(totalRecords.value \/ perPage.value)/g' sivar/src/stores/germoplasm.ts
sed -i '' 's/perPage.value, currentSearch.value = numberPage/perPage.value = numberPage/g' sivar/src/stores/germoplasm.ts
