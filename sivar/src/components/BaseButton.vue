<template>
  <component
    :is="componentTag"
    v-bind="buttonAttrs"
    :class="computedClasses"
    :aria-disabled="disabled || loading ? 'true' : undefined"
    :aria-busy="loading ? 'true' : undefined"
  >
    <slot name="loading" v-if="loading">
      <svg class="animate-spin h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path
          class="opacity-75"
          fill="currentColor"
          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
        ></path>
      </svg>
    </slot>

    <!-- Icon Left Slot (hidden while loading if loading slot is default) -->
    <span v-if="$slots['icon-left'] && !loading" class="inline-flex items-center shrink-0">
      <slot name="icon-left"></slot>
    </span>

    <!-- Main Content / Default Slot -->
    <span v-if="$slots.default" class="inline-flex items-center gap-1.5">
      <slot></slot>
    </span>

    <!-- Icon Right Slot -->
    <span v-if="$slots['icon-right']" class="inline-flex items-center shrink-0">
      <slot name="icon-right"></slot>
    </span>
  </component>
</template>

<script setup lang="ts">
import { computed } from "vue";
import type { RouteLocationRaw } from "vue-router";

export type ButtonVariant = "primary" | "secondary" | "success" | "danger" | "warning" | "info" | "violet" | "ghost" | "outline" | "link";

export type ButtonSize = "xs" | "sm" | "md" | "lg" | "xl";
export type ButtonRounded = "none" | "sm" | "md" | "lg" | "xl" | "full";

const props = withDefaults(
  defineProps<{
    variant?: ButtonVariant;
    size?: ButtonSize;
    rounded?: ButtonRounded;
    type?: "button" | "submit" | "reset";
    to?: RouteLocationRaw;
    href?: string;
    tag?: string;
    disabled?: boolean;
    loading?: boolean;
    block?: boolean;
    iconOnly?: boolean;
    active?: boolean;
  }>(),
  {
    variant: "primary",
    size: "md",
    rounded: "xl",
    type: "button",
    disabled: false,
    loading: false,
    block: false,
    iconOnly: false,
    active: false
  }
);

const componentTag = computed(() => {
  if (props.tag) return props.tag;
  if (props.to) return "router-link";
  if (props.href) return "a";
  return "button";
});

const buttonAttrs = computed(() => {
  if (componentTag.value === "button") {
    return {
      type: props.type,
      disabled: props.disabled || props.loading
    };
  }
  if (componentTag.value === "router-link") {
    return {
      to: props.to
    };
  }
  if (componentTag.value === "a") {
    return {
      href: props.href
    };
  }
  return {};
});

const variantClasses: Record<ButtonVariant, string> = {
  primary: "bg-cenicana text-white hover:bg-cenicana-800 focus-visible:ring-emerald-500 shadow-sm border border-transparent",
  secondary: "bg-white text-slate-700 hover:bg-slate-50 hover:text-slate-900 border border-slate-200 focus-visible:ring-slate-400 shadow-sm",
  success: "bg-emerald-600 text-white hover:bg-emerald-700 focus-visible:ring-emerald-500 shadow-sm border border-transparent",
  danger: "bg-red-600 text-white hover:bg-red-700 focus-visible:ring-red-500 shadow-sm border border-transparent",
  warning: "bg-amber-500 text-white hover:bg-amber-600 focus-visible:ring-amber-400 shadow-sm border border-transparent",
  info: "bg-blue-600 text-white hover:bg-blue-700 focus-visible:ring-blue-500 shadow-sm border border-transparent",
  violet: "bg-violet-800 text-white hover:bg-violet-900 focus-visible:ring-violet-500 shadow-sm border border-transparent",
  ghost: "bg-transparent text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus-visible:ring-slate-400 border border-transparent",
  outline: "bg-transparent text-cenicana hover:bg-cenicana-50 border border-cenicana focus-visible:ring-cenicana",
  link: "bg-transparent text-cenicana hover:underline underline-offset-4 p-0 focus-visible:ring-cenicana border border-transparent"
};

const sizeClasses: Record<ButtonSize, string> = {
  xs: "text-xs px-2.5 py-1 gap-1 font-medium",
  sm: "text-xs px-3 py-1.5 gap-1.5 font-semibold",
  md: "text-sm px-4 py-2 gap-2 font-semibold",
  lg: "text-base px-5 py-2.5 gap-2.5 font-bold",
  xl: "text-lg px-6 py-3 gap-3 font-bold"
};

const iconOnlySizeClasses: Record<ButtonSize, string> = {
  xs: "p-1 text-xs",
  sm: "p-1.5 text-xs",
  md: "p-2 text-sm",
  lg: "p-2.5 text-base",
  xl: "p-3 text-lg"
};

const roundedClasses: Record<ButtonRounded, string> = {
  none: "rounded-none",
  sm: "rounded-sm",
  md: "rounded-md",
  lg: "rounded-lg",
  xl: "rounded-xl",
  full: "rounded-full"
};

const computedClasses = computed(() => {
  const classes = [
    "inline-flex items-center justify-center font-sans select-none transition-all duration-200 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 active:scale-[0.98]",
    variantClasses[props.variant],
    props.iconOnly ? iconOnlySizeClasses[props.size] : sizeClasses[props.size],
    props.variant !== "link" ? roundedClasses[props.rounded] : "",
    props.block ? "w-full flex" : "",
    props.disabled || props.loading ? "opacity-60 cursor-not-allowed pointer-events-none active:scale-100 shadow-none" : "cursor-pointer"
  ];

  return classes.filter(Boolean).join(" ");
});
</script>
