<template>
  <div>
    <Combobox v-model:model-value="selectedDataBuffer">
      <div class="relative w-full">
        <div
          class="w-full border-2 border-cenicana-principal rounded-md shadow-sm ring-cenicana-principal placeholder:text-gray-400 focus:ring-2 focus:ring-outset focus:ring-cenicana-principal sm:text-sm sm:leading-6"
        >
          <ComboboxInput
            class="w-full border-none py-2 pl-3 pr-10 text-sm leading-5 focus:ring-1 focus:rounded-md ring-red-900"
            :displayValue="(record: any) => displayValue(record)"
            @change="inputToFilterList = $event.target.value"
          />
          <ComboboxButton class="absolute inset-y-0 right-0 flex items-center pr-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
            </svg>
          </ComboboxButton>
        </div>
        <TransitionRoot>
          <ComboboxOptions
            class="z-50 absolute mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
          >
            <div v-if="dataList.length === 0 && inputToFilterList !== ''" class="relative cursor-default select-none py-2 px-4 text-gray-700">
              No hay resultados.
            </div>

            <ComboboxOption v-for="(record, index) in filteredData" as="template" :key="index" :value="record" v-slot="{ selected, active }">
              <li
                class="relative cursor-default select-none py-2 pl-10 pr-4"
                :class="{
                  'bg-violet-600 text-white': active,
                  'text-violet-900': !active
                }"
              >
                <span
                  class="block truncate"
                  :class="{
                    'font-medium': selected,
                    'font-normal': !selected
                  }"
                >
                  {{ displayValue(record) }}
                </span>
                <span
                  v-if="selected"
                  class="absolute inset-y-0 left-0 flex items-center pl-3"
                  :class="{
                    'text-white': active,
                    'text-violet-600': !active
                  }"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                  </svg>
                </span>
              </li>
            </ComboboxOption>
          </ComboboxOptions>
        </TransitionRoot>
      </div>
    </Combobox>
  </div>
</template>
<script setup lang="ts">
import type { AnyObject, BasicType } from "../services/types";
import { filterObjectList } from "../utils";
import { Combobox, ComboboxInput, ComboboxButton, ComboboxOptions, ComboboxOption, TransitionRoot } from "@headlessui/vue";
import { isEqual } from "lodash";
import { computed, onMounted, ref, toRefs, watch } from "vue";
const ALL_OPTION_TEXT = "Todos";
const selectedDataBuffer = ref<AnyObject | BasicType>({});
const selectedData = defineModel<any>("selectedData", {});
const props = defineProps<{
  dataList: Array<BasicType | AnyObject>;
  columnValue?: string;
  columnToShow?: string;
  allowSelectALL?: boolean;
}>();
const { dataList, columnToShow, columnValue } = toRefs(props);
const inputToFilterList = ref("");
function getAllOption() {
  if (columnToShow?.value) {
    return { [columnToShow.value]: ALL_OPTION_TEXT };
  }
  if (columnValue?.value) {
    return { [columnValue.value]: ALL_OPTION_TEXT };
  }
  return ALL_OPTION_TEXT;
}
const filteredData = computed(() => {
  const MAX_RECORDS_TO_SHOW = 10;
  const buffer = [];
  if (props.allowSelectALL) {
    buffer.push(getAllOption());
  }
  var data = buffer.concat(filterObjectList(dataList.value, inputToFilterList.value));
  if (data.length > MAX_RECORDS_TO_SHOW) {
    data = data.slice(0, MAX_RECORDS_TO_SHOW);
  }
  return data;
});
const displayValue = (row: AnyObject | BasicType) => {
  let value = row;
  if (columnToShow?.value) {
    value = value as AnyObject;
    return value[columnToShow.value];
  }
  return value;
};
watch(dataList, () => {
  inputToFilterList.value = "";
  selectedDataBuffer.value = [];
});
watch(selectedDataBuffer, () => {
  if (columnValue?.value) {
    selectedData.value = (selectedDataBuffer.value as AnyObject)[columnValue.value];
  } else {
    selectedData.value = selectedDataBuffer.value;
  }
});
const initSelectedDataBuffer = (selectedData: BasicType | AnyObject) => {
  if (isEqual(selectedData, selectedDataBuffer.value)) return;
  if (!columnValue.value) {
    selectedDataBuffer.value = selectedData;
    return;
  }
  selectedDataBuffer.value = dataList.value.filter((record: any) => selectedData == record[columnValue.value!]);
};

onMounted(() => {
  initSelectedDataBuffer(selectedData.value);
});
defineExpose({ initSelectedDataBuffer });
</script>
