import { ref, computed, watch } from 'vue';
import type { Ref } from 'vue';

export function useDataTable<T>(
    initialData: Ref<T[]>,
    searchFields: (keyof T)[]
) {
    const searchQuery = ref('');
    const sortKey = ref<keyof T | ''>('');
    const sortAsc = ref(true);
    const currentPage = ref(1);
    const itemsPerPage = ref(10);

    const filteredData = computed(() => {
        let result = initialData.value || [];

        // Búsqueda
        if (searchQuery.value) {
            const query = searchQuery.value.toLowerCase();
            result = result.filter(item => {
                return searchFields.some(field => {
                    const value = item[field];
                    return value != null && String(value).toLowerCase().includes(query);
                });
            });
        }

        // Ordenamiento
        if (sortKey.value) {
            result = [...result].sort((a, b) => {
                let valA = a[sortKey.value as keyof T];
                let valB = b[sortKey.value as keyof T];

                if (typeof valA === 'string' && typeof valB === 'string') {
                    return sortAsc.value
                        ? valA.localeCompare(valB)
                        : valB.localeCompare(valA);
                }

                if (valA < valB) return sortAsc.value ? -1 : 1;
                if (valA > valB) return sortAsc.value ? 1 : -1;
                return 0;
            });
        }

        return result;
    });

    const totalItems = computed(() => filteredData.value.length);
    const totalPages = computed(() => Math.ceil(totalItems.value / itemsPerPage.value) || 1);

    const paginatedData = computed(() => {
        const start = (currentPage.value - 1) * itemsPerPage.value;
        const end = start + itemsPerPage.value;
        return filteredData.value.slice(start, end);
    });

    const toggleSort = (key: keyof T) => {
        if (sortKey.value === key) {
            sortAsc.value = !sortAsc.value;
        } else {
            sortKey.value = key;
            sortAsc.value = true;
        }
    };

    const nextPage = () => {
        if (currentPage.value < totalPages.value) currentPage.value++;
    };

    const prevPage = () => {
        if (currentPage.value > 1) currentPage.value--;
    };

    const setPage = (page: number) => {
        if (page >= 1 && page <= totalPages.value) {
            currentPage.value = page;
        }
    };

    // Resetea a la primera página si cambia la búsqueda
    watch(searchQuery, () => {
        currentPage.value = 1;
    });
    
    // Resetea a la primera página si cambian los ítems por página
    watch(itemsPerPage, () => {
        currentPage.value = 1;
    });

    return {
        searchQuery,
        sortKey,
        sortAsc,
        currentPage,
        itemsPerPage,
        filteredData,
        paginatedData,
        totalItems,
        totalPages,
        toggleSort,
        nextPage,
        prevPage,
        setPage
    };
}
