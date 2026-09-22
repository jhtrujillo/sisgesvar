import re

with open('sivar/src/views/mejoramiento/variedades/GermoplasmBankListView.vue', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace the template section
new_template = """<template>
  <div class="space-y-4 pb-20">
    <div class="flex justify-start mb-4">
      <BaseButton variant="secondary" size="sm" :to="{ name: 'variedades.show' }">
        <template #icon-left>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
        </template>
        Volver a Variedades
      </BaseButton>
    </div>

    <!-- Header Card -->
    <div class="bg-white rounded-lg shadow-xl border border-gray-100 p-6 sm:px-10 mb-8">
      <div class="sm:flex sm:items-center sm:justify-between">
        <div class="sm:flex-auto">
          <h1 class="text-3xl font-black text-emerald-600">Banco de Germoplasma</h1>
          <p class="mt-2 text-sm text-gray-500">
            Explora la base de datos histórica de ensayos, fenotipos y métricas agroindustriales de todas las variedades.
          </p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none flex space-x-3 items-center">
          
          <!-- Column Selector Dropdown -->
          <div class="relative group">
            <button class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              Columnas ({{ columnsToShow.length }})
            </button>
            <div class="absolute right-0 z-50 mt-2 w-64 origin-top-right rounded-md bg-white shadow-2xl ring-1 ring-black ring-opacity-5 hidden group-hover:block max-h-96 overflow-y-auto">
              <div class="p-4 space-y-2">
                <label v-for="col in tableColumns" :key="'toggle-'+col.key" class="flex items-center space-x-3 cursor-pointer">
                  <input type="checkbox" :value="col.key" v-model="columnsToShow" class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                  <span class="text-sm text-gray-700">{{ col.text }}</span>
                </label>
              </div>
            </div>
          </div>

          <button @click="downloadExcel" class="inline-flex items-center justify-center rounded-md border border-transparent bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Exportar Excel
          </button>
        </div>
      </div>

      <div class="mt-6 relative max-w-md">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
          <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
          </svg>
        </div>
        <input
          type="text"
          placeholder="Buscar variedad, ensayo o cruce..."
          class="block w-full rounded-md border-0 py-2.5 pl-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6"
          v-model="searchText"
          @input="updateFilteredGermoplasmaBank"
        />
      </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-lg shadow-xl border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <template v-for="(column, idx) in tableColumns" :key="column.key">
                <th v-if="columnsToShow.includes(column.key)" 
                    :class="['px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap', 
                             column.key === 'variedad' ? 'sticky left-0 bg-gray-50 z-10 shadow-[1px_0_0_0_#e5e7eb]' : '']">
                  {{ column.text }}
                </th>
              </template>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            <tr v-for="(germoplasma, index) in GermoplasmBankStore.germplasm" :key="getGermoplasmaKey(germoplasma)" class="hover:bg-emerald-50/50 transition-colors">
              <template v-for="column in tableColumns" :key="column.key">
                <td v-if="columnsToShow.includes(column.key)" 
                    :class="['px-4 py-3 text-sm whitespace-nowrap', 
                             column.key === 'variedad' ? 'sticky left-0 bg-white font-bold text-violet-700 z-10 shadow-[1px_0_0_0_#e5e7eb] group-hover:bg-emerald-50/50' : 'text-gray-700']">
                  <!-- Decoradores condicionales para ciertas columnas clave -->
                  <span v-if="column.key === 'roya_cafe_r' || column.key === 'roya_naranja_r' || column.key === 'mosaico_r'" 
                        :class="{'px-2 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800': germoplasma[column.key], 'text-gray-700': !germoplasma[column.key]}">
                    {{ germoplasma[column.key] || '-' }}
                  </span>
                  <span v-else-if="column.key === 'roya_cafe_s' || column.key === 'roya_naranja_s' || column.key === 'mosaico_s'" 
                        :class="{'px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800': germoplasma[column.key], 'text-gray-700': !germoplasma[column.key]}">
                    {{ germoplasma[column.key] || '-' }}
                  </span>
                  <span v-else-if="column.key === 'madre' || column.key === 'padre'" class="text-emerald-700 font-medium">
                    {{ germoplasma[column.key] }}
                  </span>
                  <span v-else>
                    {{ germoplasma[column.key] }}
                  </span>
                </td>
              </template>
            </tr>
            <tr v-if="GermoplasmBankStore.germplasm.length === 0">
              <td :colspan="columnsToShow.length" class="px-4 py-10 text-center text-gray-500">
                No se encontraron registros en el banco de germoplasma.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="GermoplasmBankStore.totalPages > 1" class="flex items-center justify-between border-t border-gray-200 bg-gray-50 px-4 py-3 sm:px-6">
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
          <div>
            <p class="text-sm text-gray-700">
              Página <span class="font-semibold">{{ GermoplasmBankStore.currentPage }}</span> de <span class="font-semibold">{{ GermoplasmBankStore.totalPages }}</span>
            </p>
          </div>
          <div>
            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
              <button @click="firstPage()" :disabled="GermoplasmBankStore.currentPage === 1" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-100 focus:z-20 focus:outline-offset-0 disabled:opacity-50">
                <span class="sr-only">Primera</span>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M15.79 14.77a.75.75 0 01-1.06.02l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 111.04 1.08L11.832 10l3.938 3.71a.75.75 0 01.02 1.06zm-6 0a.75.75 0 01-1.06.02l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 111.04 1.08L5.832 10l3.938 3.71a.75.75 0 01.02 1.06z" clip-rule="evenodd" />
                </svg>
              </button>
              <button @click="previousPage()" :disabled="GermoplasmBankStore.currentPage === 1" class="relative inline-flex items-center px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-100 focus:z-20 focus:outline-offset-0 disabled:opacity-50">
                <span class="sr-only">Anterior</span>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                </svg>
              </button>
              <button @click="nextPage()" :disabled="GermoplasmBankStore.currentPage === GermoplasmBankStore.totalPages" class="relative inline-flex items-center px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-100 focus:z-20 focus:outline-offset-0 disabled:opacity-50">
                <span class="sr-only">Siguiente</span>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                </svg>
              </button>
              <button @click="lastPage()" :disabled="GermoplasmBankStore.currentPage === GermoplasmBankStore.totalPages" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-100 focus:z-20 focus:outline-offset-0 disabled:opacity-50">
                <span class="sr-only">Última</span>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M10.21 14.77a.75.75 0 01.02-1.06L14.168 10 10.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02zM4.21 14.77a.75.75 0 01.02-1.06L8.168 10 4.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                </svg>
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
"""

content = re.sub(r'<template>.*?</template>', new_template, content, flags=re.DOTALL)

# Reorder tableColumns to move "variedad" to top
table_cols_match = re.search(r'const tableColumns = \[(.*?)\];', content, flags=re.DOTALL)
if table_cols_match:
    cols_str = table_cols_match.group(1)
    
    # Simple strategy: find the '{ key: "variedad", text: "Variedad" }' block and move it to the top
    var_block = r'\{\s*key:\s*"variedad",\s*text:\s*"Variedad"\s*\},?'
    var_match = re.search(var_block, cols_str)
    
    if var_match:
        extracted = var_match.group(0)
        cols_str = re.sub(var_block, '', cols_str)
        if extracted.endswith(','): extracted = extracted[:-1]
        
        new_cols_str = f'\n  {extracted},\n' + cols_str.strip()
        content = content[:table_cols_match.start(1)] + new_cols_str + content[table_cols_match.end(1):]


# Reorder columnsToShow to move "variedad" to top
cols_to_show_match = re.search(r'const columnsToShow = ref\(\[(.*?)\]\);', content, flags=re.DOTALL)
if cols_to_show_match:
    cols_str = cols_to_show_match.group(1)
    
    var_str = r'"variedad",?'
    var_match = re.search(var_str, cols_str)
    
    if var_match:
        extracted = var_match.group(0)
        cols_str = re.sub(var_str, '', cols_str)
        if extracted.endswith(','): extracted = extracted[:-1]
        
        new_cols_str = f'\n  {extracted},\n' + cols_str.strip()
        content = content[:cols_to_show_match.start(1)] + new_cols_str + content[cols_to_show_match.end(1):]

with open('sivar/src/views/mejoramiento/variedades/GermoplasmBankListView.vue', 'w', encoding='utf-8') as f:
    f.write(content)

