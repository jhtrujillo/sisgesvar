<template>
  <div class="vivero-tree-node font-sans">
    <!-- Vivero Card -->
    <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl p-3 mb-2 shadow-sm">
      <div class="p-1.5 bg-green-50 text-green-700 rounded-md">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
      </div>
      <div>
        <div class="text-xs font-bold text-slate-800 flex items-center gap-2">
          <span>Vivero: {{ node.identificador_unico }}</span>
          <span class="text-[10px] px-1.5 py-0.5 bg-slate-200 text-slate-700 rounded-full font-mono font-bold">{{ node.condicion || 'N/A' }}</span>
        </div>
        <div class="text-[10px] text-slate-500 font-medium">
          {{ node.proyecto?.nm_prycto }}
        </div>
      </div>
    </div>

    <!-- Tree Branches (Parcelas and Cuts) -->
    <div class="pl-6 border-l border-dashed border-slate-300 ml-5 space-y-3">
      <div v-for="p in node.parcelas" :key="'tree_p_' + p.id" class="relative">
        <!-- Horizontal line connecting to branch -->
        <div class="absolute top-5 -left-6 w-6 border-t border-dashed border-slate-300"></div>
        
        <!-- Parcela Detail Box -->
        <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm inline-block min-w-[280px]">
          <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Plot {{ p.numero_parcela }}</div>
          <div class="text-xs font-bold text-cenicana mt-0.5">{{ p.variedad?.nm_vrdad || 'N/A' }}</div>
          <div class="text-[10px] text-slate-500 font-mono mt-0.5">{{ p.variedad?.pdgree || 'N/A' }}</div>
          <div class="text-[10px] text-slate-500 mt-0.5 font-medium italic" v-if="p.caracter?.nombre || node.caracter?.nombre">
            Carácter: {{ p.caracter?.nombre || node.caracter?.nombre }}
          </div>
        </div>

        <!-- Recursive Cuts -->
        <div v-if="p.cortes && p.cortes.length > 0" class="mt-3 pl-6 border-l border-dashed border-blue-200 ml-4 space-y-3 relative">
          <div v-for="c in p.cortes" :key="'tree_c_' + c.id" class="relative">
            <div class="absolute top-5 -left-6 w-6 border-t border-dashed border-blue-200"></div>
            <!-- Label to indicate this is a cut -->
            <div class="absolute -top-2 left-0 text-[8px] font-bold uppercase text-blue-600 bg-blue-50 px-1 rounded">
              Corte {{ c.consecutivo_corte }}
            </div>
            <div class="pt-2">
              <ViveroTreeComponent :node="c" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineProps } from 'vue';

defineProps<{
  node: any;
}>();
</script>
