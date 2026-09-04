<template>
  <component
    :is="to ? 'router-link' : href ? 'a' : 'div'"
    :to="to"
    :href="href"
    :target="target"
    :rel="rel"
    class="group relative flex flex-col bg-white border border-slate-100 rounded-2xl p-6 shadow-premium hover:shadow-premium-hover hover:-translate-y-1.5 transition-all duration-300 overflow-hidden cursor-pointer"
    :class="[hoverBorderColorClass]"
  >
    <div class="absolute top-0 left-0 w-full h-1 transition-colors duration-300" :class="[accentColorClass, groupHoverAccentColorClass]"></div>

    <div class="flex flex-col h-full items-center text-center">
      <div
        class="p-3.5 rounded-xl transition-all duration-300 mb-4 flex items-center justify-center"
        :class="[iconBgClass, iconTextClass, groupHoverIconBgClass, groupHoverIconTextClass]"
      >
        <slot name="icon">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
        </slot>
      </div>

      <h3 class="text-lg font-bold text-slate-800 mb-2 transition-colors" :class="[groupHoverTitleColorClass]">
        {{ title }}
      </h3>

      <p class="text-slate-500 text-xs leading-relaxed mb-4">
        {{ description }}
      </p>

      <span v-if="actionText" class="inline-flex items-center text-xs font-semibold mt-auto" :class="[actionTextColorClass, groupHoverActionTextColorClass]">
        {{ actionText }}
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-3.5 w-3.5 ml-1 transform transition-transform group-hover:translate-x-1"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="2.5"
        >
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
      </span>
    </div>
  </component>
</template>

<script setup lang="ts">
import { computed } from "vue";
import type { RouteLocationRaw } from "vue-router";

interface Props {
  title: string;
  description: string;
  actionText?: string;
  to?: RouteLocationRaw;
  href?: string;
  target?: string;
  rel?: string;
  variant?: "emerald" | "blue" | "amber" | "violet" | "teal" | "indigo" | "rose" | "slate";
}

const props = withDefaults(defineProps<Props>(), {
  actionText: "Ingresar",
  variant: "emerald"
});

const accentColorClass = computed(() => {
  switch (props.variant) {
    case "blue":
      return "bg-blue-400";
    case "amber":
      return "bg-amber-400";
    case "violet":
      return "bg-violet-400";
    case "teal":
      return "bg-teal-400";
    case "indigo":
      return "bg-indigo-400";
    case "rose":
      return "bg-rose-400";
    case "slate":
      return "bg-slate-400";
    case "emerald":
    default:
      return "bg-emerald-400";
  }
});

const groupHoverAccentColorClass = computed(() => {
  switch (props.variant) {
    case "blue":
      return "group-hover:bg-blue-600";
    case "amber":
      return "group-hover:bg-amber-600";
    case "violet":
      return "group-hover:bg-violet-600";
    case "teal":
      return "group-hover:bg-teal-600";
    case "indigo":
      return "group-hover:bg-indigo-600";
    case "rose":
      return "group-hover:bg-rose-600";
    case "slate":
      return "group-hover:bg-slate-600";
    case "emerald":
    default:
      return "group-hover:bg-cenicana";
  }
});

const hoverBorderColorClass = computed(() => {
  switch (props.variant) {
    case "blue":
      return "hover:border-blue-100";
    case "amber":
      return "hover:border-amber-100";
    case "violet":
      return "hover:border-violet-100";
    case "teal":
      return "hover:border-teal-100";
    case "indigo":
      return "hover:border-indigo-100";
    case "rose":
      return "hover:border-rose-100";
    case "slate":
      return "hover:border-slate-200";
    case "emerald":
    default:
      return "hover:border-emerald-100";
  }
});

const iconBgClass = computed(() => {
  switch (props.variant) {
    case "blue":
      return "bg-blue-50";
    case "amber":
      return "bg-amber-50";
    case "violet":
      return "bg-violet-50";
    case "teal":
      return "bg-teal-50";
    case "indigo":
      return "bg-indigo-50";
    case "rose":
      return "bg-rose-50";
    case "slate":
      return "bg-slate-100";
    case "emerald":
    default:
      return "bg-emerald-50";
  }
});

const iconTextClass = computed(() => {
  switch (props.variant) {
    case "blue":
      return "text-blue-600";
    case "amber":
      return "text-amber-600";
    case "violet":
      return "text-violet-600";
    case "teal":
      return "text-teal-600";
    case "indigo":
      return "text-indigo-600";
    case "rose":
      return "text-rose-600";
    case "slate":
      return "text-slate-600";
    case "emerald":
    default:
      return "text-cenicana";
  }
});

const groupHoverIconBgClass = computed(() => {
  switch (props.variant) {
    case "blue":
      return "group-hover:bg-blue-600";
    case "amber":
      return "group-hover:bg-amber-500";
    case "violet":
      return "group-hover:bg-violet-600";
    case "teal":
      return "group-hover:bg-teal-600";
    case "indigo":
      return "group-hover:bg-indigo-600";
    case "rose":
      return "group-hover:bg-rose-600";
    case "slate":
      return "group-hover:bg-slate-700";
    case "emerald":
    default:
      return "group-hover:bg-cenicana";
  }
});

const groupHoverIconTextClass = computed(() => "group-hover:text-white");

const groupHoverTitleColorClass = computed(() => {
  switch (props.variant) {
    case "blue":
      return "group-hover:text-blue-600";
    case "amber":
      return "group-hover:text-amber-600";
    case "violet":
      return "group-hover:text-violet-600";
    case "teal":
      return "group-hover:text-teal-600";
    case "indigo":
      return "group-hover:text-indigo-600";
    case "rose":
      return "group-hover:text-rose-600";
    case "slate":
      return "group-hover:text-slate-800";
    case "emerald":
    default:
      return "group-hover:text-cenicana";
  }
});

const actionTextColorClass = computed(() => {
  switch (props.variant) {
    case "blue":
      return "text-blue-600";
    case "amber":
      return "text-amber-600";
    case "violet":
      return "text-violet-600";
    case "teal":
      return "text-teal-600";
    case "indigo":
      return "text-indigo-600";
    case "rose":
      return "text-rose-600";
    case "slate":
      return "text-slate-600";
    case "emerald":
    default:
      return "text-emerald-600";
  }
});

const groupHoverActionTextColorClass = computed(() => {
  switch (props.variant) {
    case "blue":
      return "group-hover:text-blue-700";
    case "amber":
      return "group-hover:text-amber-700";
    case "violet":
      return "group-hover:text-violet-700";
    case "teal":
      return "group-hover:text-teal-700";
    case "indigo":
      return "group-hover:text-indigo-700";
    case "rose":
      return "group-hover:text-rose-700";
    case "slate":
      return "group-hover:text-slate-800";
    case "emerald":
    default:
      return "group-hover:text-emerald-700";
  }
});
</script>
