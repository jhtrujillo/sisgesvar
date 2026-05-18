<template>
  <div v-if="haveButtonExcel || allowHideColumns || filteredRecords.length >= 10 || haveSearch" class="flex">
    <div v-if="haveSearch" class="flex w-1/3">
      <input
        type="text"
        name="buscar"
        id="buscar"
        placeholder="Buscar"
        class="block w-full rounded-md border-0 px-2 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
        v-model="filterInputValue"
        @keyup="filterRecords"
      />
    </div>
    <div class="flex grow justify-end items-center space-x-2">
      <div v-if="haveButtonExcel" class="sm:col-span-1">
        <button
          type="button"
          class="inline-flex items-center rounded-md bg-indigo-600 px-3.5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          @click="downloadExcel"
        >
          <i class="bi bi-file-earmark-excel-fill"></i> Excel
        </button>
      </div>
      <div v-if="haveButtonAddNew" class="sm:col-span-1">
        <button
          type="button"
          class="inline-flex items-center justify-center rounded-md border border-transparent bg-sky-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 sm:w-auto"
          @click="$emit('addNew')"
        >
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
            <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
          </svg>

          Nuevo
        </button>
      </div>
      <Combobox v-if="allowHideColumns" as="div" v-model="columnsToShow" multiple>
        <div class="relative">
          <ComboboxButton
            class="flex px-3 bg-white rounded-md border-0 py-2 pl-3 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6"
          >
            <div class="text-sm leading-5 text-gray-900 pr-3">Columnas</div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"
              />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </ComboboxButton>
        </div>
        <TransitionRoot leave="transition ease-in duration-100" leaveFrom="opacity-100" leaveTo="opacity-0" @after-leave="filterInputValue = ''">
          <ComboboxOptions
            class="absolute px-2 mt-1 w-auto max-h-60 overflow-y-auto scrollbar-thin scrollbar-thumb-teal-300 scrollbar-track-teal-100 cursor-default rounded-lg bg-white text-left shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75 focus-visible:ring-offset-2 focus-visible:ring-offset-teal-300 sm:text-sm"
          >
            <ComboboxOption v-for="(col, index) in columns" :key="index" :value="col.text" v-slot="{ selected, active }">
              <li
                class="relative cursor-default select-none py-2 pl-10"
                :class="{
                  ' hover:bg-sky-600 text-white': active,
                  'text-gray-900': !active
                }"
              >
                <span
                  class=""
                  :class="{
                    'font-medium': selected,
                    'font-normal': !selected
                  }"
                >
                  {{ col.text }}
                </span>
                <span
                  v-if="selected"
                  class="absolute inset-y-0 left-0 flex items-center pl-3"
                  :class="{
                    'text-white': active,
                    'text-teal-600': !active
                  }"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </span>
              </li>
            </ComboboxOption>
          </ComboboxOptions>
        </TransitionRoot>
      </Combobox>
      <template v-if="filteredRecords.length >= 10">
        <div class="sm:col-span-1">
          <select
            id="pagination"
            name="pagination"
            class="block w-full rounded-md border-0 py-2 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6"
            v-model="perPageRecordNumber"
          >
            <template v-for="option in perPageRecordOptions" :key="option">
              <option :value="option.number">{{ option.field }}</option>
            </template>
          </select>
        </div>
      </template>
    </div>
  </div>
  <div class="mt-8 flow-root">
    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
      <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
          <table class="table-auto overflow-x-scroll w-full divide-y divide-gray-300">
            <thead class="bg-gray-50">
              <tr>
                <template v-for="(col, index) in columns" :key="index">
                  <th
                    v-if="columnsToShow.includes(col.text)"
                    scope="col"
                    :class="[isOrdened ? 'cursor-pointer' : '']"
                    class="cursor-pointer px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500"
                    @click="sortRows(col.keyName)"
                  >
                    {{ col.text }}
                  </th>
                </template>
                <th v-if="isDelete || isEditable" scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                  <span class="sr-only">Edit</span>
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
              <template v-for="(row, index) in showedRecords" :key="index">
                <tr>
                  <template v-for="column in columns" :key="column">
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-800 sm:pl-6" v-if="columnsToShow.includes(column.text)">
                      <template v-if="Array.isArray(row[column.keyName])">
                        <p v-for="(value, index) in row[column.keyName]" :key="'rowValue_' + index">
                          {{ value }}
                        </p>
                      </template>
                      <template v-else>{{ row[column.keyName] }}</template>
                    </td>
                  </template>
                  <td v-if="isDelete || isEditable || otherButtonText" class="whitespace-nowrap py-2 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                    <button
                      v-if="isEditable"
                      type="button"
                      class="ml-3 inline-flex justify-center rounded-md border border-transparent bg-sky-500 py-1 px-1 text-sm font-medium text-white shadow-sm hover:bg-sky-600 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2"
                      @click="emit('editElement', hasBeenFormat ? row.org_row : row)"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"
                        />
                      </svg>
                    </button>
                    <button
                      v-if="isDelete"
                      type="button"
                      class="ml-3 inline-flex items-center rounded-md border border-transparent bg-rose-600 px-1 py-1 text-sm font-medium text-white shadow-sm hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-900 focus:ring-offset-2"
                      @click="emit('deleteElement', hasBeenFormat ? row.org_row : row)"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"
                        />
                      </svg>
                    </button>
                    <button
                      v-if="otherButtonText"
                      type="button"
                      class="inline-flex items-center gap-x-1.5 rounded-md bg-slate-600 px-3 py-2 mx-1 text-sm font-semibold text-white shadow-sm hover:bg-slate-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-600"
                      @click="emit('otherButton', hasBeenFormat ? row.org_row : row)"
                    >
                      {{ otherButtonText }}
                    </button>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <template v-if="filteredRecords.length >= 10">
      <nav class="flex items-center justify-between m-2 px-4 py-3 sm:px-6" aria-label="Pagination">
        <div class="hidden sm:block">
          <p class="text-sm text-gray-700">
            Mostrando
            {{ " " }}
            <span class="font-medium">{{ currentPage * perPageRecordNumber }}</span>
            {{ " " }}
            a
            {{ " " }}
            <span class="font-medium">{{ (currentPage + 1) * perPageRecordNumber }}</span>
            {{ " " }}
            de
            {{ " " }}
            <span class="font-medium">{{ filteredRecords.length }}</span>
            {{ " " }}
            resultados
          </p>
        </div>
        <!-- pagination -->
        <div class="flex flex-1 justify-between sm:justify-end mt-5 mr-6">
          <button
            class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            @click="previousPage"
          >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5" />
            </svg>

            Anterior
          </button>
          <button class="mx-4">
            <span class="font-medium">{{ currentPage + 1 }}</span> /
            <span class="font-medium">{{ pageTotalNumber }}</span>
          </button>
          <button
            class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            @click="nextPage"
          >
            Siguiente
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 4.5l7.5 7.5-7.5 7.5m-6-15l7.5 7.5-7.5 7.5" />
            </svg>
          </button>
        </div>
      </nav>
    </template>
  </div>
</template>

<script setup lang="ts">
import { toRefs, ref, watchEffect, watch, onMounted } from "vue";
import exportExcel from "./exportExcel";
import { Combobox, ComboboxButton, ComboboxOptions, ComboboxOption, TransitionRoot } from "@headlessui/vue";
import type { Column } from "./models";
import { filterObjectList } from "../../utils";
import { orderBy, chunk } from "lodash";

const emit = defineEmits<{
  (e: "editElement", value: { [key: string]: any }): void;
  (e: "deleteElement", value: { [key: string]: any }): void;
  (e: "otherButton", value: { [key: string]: any }): void;
  (e: "addNew"): void;
}>();
const props = withDefaults(
  defineProps<{
    rows: Array<{ [key: string]: any }>;
    columns: Column[];
    isOrdened?: boolean;
    isDelete?: boolean;
    isEditable?: boolean;
    otherButtonText?: string;
    haveButtonAddNew?: boolean;
    haveButtonExcel?: boolean;
    nameExcel?: string;
    allowHideColumns?: boolean;
    haveSearch?: boolean;
  }>(),
  {
    isOrdened: false,
    isDelete: false,
    isEditable: false,
    haveButtonAddNew: false,
    haveButtonExcel: false,
    nameExcel: "data",
    allowHideColumns: false,
    haveSearch: false
  }
);
const { rows, columns, isOrdened, isDelete, isEditable, otherButtonText, haveButtonAddNew, haveButtonExcel, nameExcel, haveSearch } = toRefs(props);

const filteredRecords = ref([...rows.value]);
const perPageRecordOptions = ref([
  { field: "5", number: 5 },
  { field: "10", number: 10 },
  { field: "15", number: 15 },
  { field: "20", number: 20 },
  { field: "30", number: 30 },
  { field: "50", number: 50 },
  { field: "Todos", number: filteredRecords.value.length }
]);
const perPageRecordNumber = ref(perPageRecordOptions.value[0].number);
const columnsToShow = ref(columns.value.map((column: Column) => column.text));
const isAscSorting = ref(false);
const filterInputValue = ref("");

const pageTotalNumber = ref<number>(0);
const currentPage = ref<number>(0);
const showedRecords = ref<Array<{ [key: string]: any }>>([]);
const hasBeenFormat = ref(false);

const needFormat = ref(false);
const formatRow = (row: { [key: string]: any }, columns: Column[]) => {
  const formattedRow = columns.map((column) => {
    const formatValue = column.formatValue ? column.formatValue : (value: any) => value;
    let value: any;

    if (column.formatFromRow) {
      return [column.keyName, column.formatFromRow(row)];
    }
    if (column.path) {
      if (column.keyNames) {
        throw "not implemented for keyNameS and path";
      }
      value = extractNestedVar(row, column.path, column.isLastFatherList);
      return [column.keyName, formatValue(value)];
    }
    if (column.keyNames) {
      value = getVarsFromObject(row, column.keyNames);

      return [column.keyName, formatValue(value)];
    }

    return [column.keyName, formatValue(row[column.keyName])];
  });
  formattedRow.push(["org_row", row]);
  return Object.fromEntries(formattedRow);
};

function refreshFilteredRecords() {
  needFormat.value = columns.value.some((column) => (column.keyNames || column.path || column.formatValue || column.formatFromRow ? 1 : 0));
  if (needFormat.value) {
    filteredRecords.value = rows.value.map((row) => formatRow(row, columns.value));
    hasBeenFormat.value = true;
  } else {
    filteredRecords.value = [...rows.value];
    hasBeenFormat.value = false;
  }
  perPageRecordOptions.value[perPageRecordOptions.value.length - 1] = {
    field: "Todos",
    number: filteredRecords.value.length
  };
}
watch(rows, () => {
  // get all records to new array
  refreshFilteredRecords();
});
watchEffect(() => {
  // main: change showedRecords
  //calculate totalPage count
  pageTotalNumber.value = Math.ceil(filteredRecords.value.length / perPageRecordNumber.value);
  // set first page of datatable
  currentPage.value = 0;
  showedRecords.value = chunk(filteredRecords.value, perPageRecordNumber.value)[0]; // warning: use [0] rather current_page to set showedRecords. that will force to run this block at every change of currentPage, which is not a desirable feature
});

const sortRows = (keyName: string | string[]) => {
  isAscSorting.value = !isAscSorting.value;
  if (isAscSorting.value) {
    filteredRecords.value = orderBy(filteredRecords.value, [keyName], "asc");
  } else {
    filteredRecords.value = orderBy(filteredRecords.value, [keyName], "desc");
  }
};

const downloadExcel = () => {
  exportExcel.exportFile(nameExcel.value, columns.value, rows.value);
};
// PreviousPage or next button function
const previousPage = () => {
  if (currentPage.value >= 1) {
    currentPage.value--;
    showedRecords.value = chunk(filteredRecords.value, perPageRecordNumber.value)[currentPage.value];
  }
};
const nextPage = () => {
  if (currentPage.value + 1 < pageTotalNumber.value) {
    currentPage.value = currentPage.value + 1;
    showedRecords.value = chunk(filteredRecords.value, perPageRecordNumber.value)[currentPage.value];
  }
};
function filterRecords() {
  refreshFilteredRecords();
  if (filterInputValue.value) {
    filteredRecords.value = filterObjectList(filteredRecords.value, filterInputValue.value);
  }
}
const getVarsFromObject = (obj: { [key: string]: any }, keyNames: string[]) => {
  return keyNames.map((key) => obj[key]);
};
const extractNestedVar = (father: { [key: string]: any } | Array<{ [key: string]: any }>, path: string[], isLastFatherList = false) => {
  const __getNestedVar = (father: { [key: string]: any } | Array<{ [key: string]: any }>, path: string[]) => {
    return path.reduce((accum, keyName) => {
      if (Array.isArray(accum)) {
        return accum[0][keyName]; // consider only the first element if the object is a list
      } else {
        return accum ? accum[keyName] : "null";
      }
    }, father);
  };
  if (isLastFatherList) {
    const pathBuffer = [...path];
    const varKey = pathBuffer.pop() as string;
    if (pathBuffer.length < 1) {
      throw "path.length must be >=2";
    }
    const lastFather = __getNestedVar(father, pathBuffer);
    return lastFather.map((child: { [key: string]: any }) => child[varKey]);
  }
  if (!isLastFatherList) {
    return __getNestedVar(father, path);
  }
};
onMounted(() => {
  refreshFilteredRecords();
});
</script>
