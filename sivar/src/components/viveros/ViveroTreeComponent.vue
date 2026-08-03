<template>
  <div class="vivero-tree-node font-sans">
    <!-- Vivero Card -->
    <div 
      class="flex items-center justify-between gap-2 border rounded-xl p-3 mb-2 shadow-sm transition-all duration-200"
      :class="[
        node.origen_parcela && node.origen_parcela.split('-').length > 3
          ? 'bg-blue-50/70 border-blue-200 hover:border-blue-300'
          : 'bg-green-50/70 border-green-200 hover:border-green-300',
        searchQuery && matchesNursery ? 'ring-2 ring-yellow-400 ring-offset-2' : ''
      ]"
    >
      <div class="flex items-center gap-2.5">
        <!-- Collapse/Expand Toggle -->
        <button 
          @click="isExpanded = !isExpanded" 
          class="text-slate-400 hover:text-slate-600 transition-colors p-0.5 rounded hover:bg-slate-200"
          :title="isExpanded ? 'Colapsar Vivero' : 'Expandir Vivero'"
        >
          <svg 
            xmlns="http://www.w3.org/2000/svg" 
            class="h-3.5 w-3.5 transform transition-transform duration-200"
            :class="{ 'rotate-90': isExpanded }"
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
          </svg>
        </button>

        <div 
          class="p-1.5 rounded-lg"
          :class="node.origen_parcela && node.origen_parcela.split('-').length > 3 ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700'"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
        </div>

        <div>
          <div class="text-xs font-bold text-slate-800 flex items-center gap-2 flex-wrap">
            <span :class="{ 'bg-yellow-200 px-0.5 rounded': searchQuery && node.identificador_unico.toLowerCase().includes(searchQuery.toLowerCase()) }">
              Vivero: {{ node.identificador_unico }}
            </span>
            <span class="text-[9px] px-1.5 py-0.5 bg-slate-200/80 text-slate-700 rounded-full font-mono font-bold">{{ node.condicion || 'N/A' }}</span>
          </div>
          <div class="text-[10px] text-slate-500 font-medium max-w-md truncate" :title="node.proyecto?.nm_prycto">
            {{ node.proyecto?.nm_prycto }}
          </div>
        </div>
      </div>

      <!-- Action: Edit Direct Link -->
      <router-link 
        :to="{ name: 'vivero_editar.show', params: { id: node.id } }"
        class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
        title="Editar este Vivero"
        @click.native="emitClose"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
        </svg>
      </router-link>
    </div>

    <!-- Tree Branches (Parcelas and Cuts) -->
    <div 
      v-if="isExpanded"
      class="pl-6 border-l border-dashed border-slate-300 ml-5 space-y-3 transition-all duration-200"
    >
      <div v-for="p in node.parcelas" :key="'tree_p_' + p.id" class="relative">
        <!-- Horizontal line connecting to branch -->
        <div class="absolute top-6 -left-6 w-6 border-t border-dashed border-slate-300"></div>
        
        <!-- Parcela Card -->
        <div 
          class="bg-white border rounded-xl p-3 shadow-sm inline-block min-w-[290px] transition-all"
          :class="[
            searchQuery && matchesParcela(p) ? 'ring-2 ring-yellow-400 border-yellow-300' : 'border-slate-200 hover:border-slate-300',
          ]"
        >
          <div class="flex items-center justify-between">
            <span 
              class="text-[10px] font-bold text-slate-400 uppercase tracking-wide"
              :class="{ 'bg-yellow-200 px-0.5 rounded text-slate-800': searchQuery && p.numero_parcela.toString().includes(searchQuery) }"
            >
              Plot {{ p.numero_parcela }}
            </span>
            <!-- Toggle for cuts -->
            <button 
              v-if="p.cortes && p.cortes.length > 0"
              @click="toggleParcela(p.id)"
              class="text-[9px] font-bold px-1.5 py-0.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-100 rounded-md transition-colors flex items-center gap-1"
            >
              Cortes ({{ p.cortes.length }})
              <svg 
                xmlns="http://www.w3.org/2000/svg" 
                class="h-2.5 w-2.5 transform transition-transform"
                :class="{ 'rotate-180': expandedParcelas[p.id] !== false }"
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
          </div>

          <div 
            class="text-xs font-bold text-cenicana mt-0.5"
            :class="{ 'bg-yellow-200 px-0.5 rounded text-slate-900 inline-block': searchQuery && p.variedad?.nm_vrdad?.toLowerCase().includes(searchQuery.toLowerCase()) }"
          >
            {{ p.variedad?.nm_vrdad || 'N/A' }}
          </div>
          <div 
            class="text-[10px] text-slate-500 font-mono mt-0.5"
            :class="{ 'bg-yellow-200 px-0.5 rounded text-slate-800 inline-block': searchQuery && p.variedad?.pdgree?.toLowerCase().includes(searchQuery.toLowerCase()) }"
          >
            {{ p.variedad?.pdgree || 'N/A' }}
          </div>
          <div class="text-[10px] text-slate-500 mt-1 font-medium italic border-t border-slate-50 pt-1 flex items-center justify-between" v-if="p.caracter?.nombre || node.caracter?.nombre">
            <span>Carácter: {{ p.caracter?.nombre || node.caracter?.nombre }}</span>
          </div>
        </div>

        <!-- Recursive Cuts (Cortes) -->
        <div 
          v-if="p.cortes && p.cortes.length > 0 && expandedParcelas[p.id] !== false" 
          class="mt-3 pl-6 border-l border-dashed border-blue-200 ml-4 space-y-3 relative transition-all duration-200"
        >
          <div v-for="c in p.cortes" :key="'tree_c_' + c.id" class="relative">
            <div class="absolute top-5 -left-6 w-6 border-t border-dashed border-blue-200"></div>
            <!-- Label to indicate this is a cut -->
            <div class="absolute -top-2.5 left-0 text-[8px] font-bold uppercase text-blue-600 bg-blue-50 px-1 rounded border border-blue-100">
              Corte {{ c.consecutivo_corte }}
            </div>
            <div class="pt-2">
              <ViveroTreeComponent 
                :node="c" 
                :search-query="searchQuery" 
                @close-modal="emitClose"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits, ref, computed, watch } from 'vue';

const props = defineProps<{
  node: any;
  searchQuery?: string;
}>();

const emit = defineEmits<{
  (e: 'close-modal'): void;
}>();

const emitClose = () => {
  emit('close-modal');
};

const isExpanded = ref(true);
const expandedParcelas = ref<Record<string | number, boolean>>({});

const toggleParcela = (id: string | number) => {
  if (expandedParcelas.value[id] === false) {
    expandedParcelas.value[id] = true;
  } else {
    expandedParcelas.value[id] = false;
  }
};

// Check if this Vivero node matches the search query
const matchesNursery = computed(() => {
  if (!props.searchQuery) return false;
  const q = props.searchQuery.toLowerCase();
  return (
    props.node.identificador_unico?.toLowerCase().includes(q) ||
    props.node.nombre?.toLowerCase().includes(q)
  );
});

// Check if a specific Parcela matches the search query
const matchesParcela = (p: any) => {
  if (!props.searchQuery) return false;
  const q = props.searchQuery.toLowerCase();
  return (
    p.numero_parcela.toString().includes(q) ||
    p.variedad?.nm_vrdad?.toLowerCase().includes(q) ||
    p.variedad?.pdgree?.toLowerCase().includes(q) ||
    p.id_plot_origen?.toLowerCase().includes(q)
  );
};

// Helper for recursive match check
const checkRecursiveMatch = (node: any, q: string): boolean => {
  if (node.identificador_unico?.toLowerCase().includes(q)) return true;
  for (const p of node.parcelas || []) {
    if (p.numero_parcela.toString().includes(q)) return true;
    if (p.variedad?.nm_vrdad?.toLowerCase().includes(q)) return true;
    if (p.variedad?.pdgree?.toLowerCase().includes(q)) return true;
    if (p.id_plot_origen?.toLowerCase().includes(q)) return true;
    if (p.cortes && p.cortes.length > 0) {
      if (p.cortes.some((c: any) => checkRecursiveMatch(c, q))) return true;
    }
  }
  return false;
};

// Compute if this node or any of its descendants/parcelas contain a search match
const hasAnyMatch = computed(() => {
  if (!props.searchQuery) return false;
  const q = props.searchQuery.toLowerCase();
  
  if (matchesNursery.value) return true;
  
  for (const p of props.node.parcelas || []) {
    if (matchesParcela(p)) return true;
    if (p.cortes && p.cortes.length > 0) {
      const cutsMatch = p.cortes.some((c: any) => checkRecursiveMatch(c, q));
      if (cutsMatch) return true;
    }
  }
  return false;
});

// Auto-expand this node when there is a search match
watch(() => props.searchQuery, (newVal) => {
  if (newVal && hasAnyMatch.value) {
    isExpanded.value = true;
    // Also auto-expand all parcelas that have matching cuts
    const q = newVal.toLowerCase();
    for (const p of props.node.parcelas || []) {
      if (p.cortes && p.cortes.length > 0) {
        const cutsMatch = p.cortes.some((c: any) => checkRecursiveMatch(c, q));
        if (cutsMatch) {
          expandedParcelas.value[p.id] = true;
        }
      }
    }
  }
}, { immediate: true });
</script>
