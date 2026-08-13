<template>
  <div class="flex justify-start">
    <BaseButton variant="secondary" size="sm" rounded="full" :to="to" class="group hover:bg-emerald-50 hover:text-cenicana-800 hover:border-emerald-200">
      <template #icon-left>
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-4 w-4 transform transition-transform group-hover:-translate-x-1"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="2.5"
        >
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
      </template>
      {{ label }}
    </BaseButton>
  </div>
</template>

<script setup lang="ts">
import type { RouteLocationRaw } from "vue-router";

withDefaults(
  defineProps<{
    to: RouteLocationRaw;
    label?: string;
  }>(),
  {
    label: "Volver"
  }
);
</script>
