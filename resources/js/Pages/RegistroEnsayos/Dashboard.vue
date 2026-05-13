<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Chart, registerables } from 'chart.js';
import { onMounted, ref } from 'vue';

Chart.register(...registerables);

const props = defineProps({
    stats: Object
});

const canvasAmbiente = ref(null);
const canvasSiembra = ref(null);

onMounted(() => {
    if (canvasAmbiente.value && props.stats.por_ambiente?.length > 0) {
        const items = [...props.stats.por_ambiente];
        const labels = items.map(i => i.amb_seleccion || 'S/N');
        const data = items.map(i => i.total);

        new Chart(canvasAmbiente.value, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: ['#059669', '#3b82f6', '#8b5cf6', '#f59e0b', '#ec4899', '#64748b'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 12,
                            padding: 15,
                            font: { family: "'Inter', sans-serif", weight: 'bold', size: 11 },
                            color: '#475569'
                        }
                    },
                    tooltip: { backgroundColor: '#1e293b', titleFont: { weight: 'black' }, padding: 10, cornerRadius: 8 }
                },
                cutout: '65%'
            }
        });
    }

    if (canvasSiembra.value && props.stats.por_ano?.length > 0) {
        const anos = props.stats.por_ano.map(i => i.ano_siembra);
        const totales = props.stats.por_ano.map(i => i.total);

        const ctx = canvasSiembra.value.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 250);
        gradient.addColorStop(0, 'rgba(5, 150, 105, 0.85)');
        gradient.addColorStop(1, 'rgba(16, 185, 129, 0.15)');

        new Chart(canvasSiembra.value, {
            type: 'bar',
            data: {
                labels: anos,
                datasets: [{
                    label: 'Ensayos Registrados',
                    data: totales,
                    backgroundColor: gradient,
                    borderColor: '#059669',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    barThickness: 24,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { backgroundColor: '#1e293b', padding: 10, cornerRadius: 8 }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { weight: 'bold', size: 10 }, color: '#64748b' } },
                    y: { beginAtZero: true, grid: { color: '#f1f5f9', drawBorder: false }, ticks: { precision: 0, font: { weight: 'bold', size: 10 }, color: '#64748b' } }
                }
            }
        });
    }
});

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
};

const getPercent = (total, current) => {
    if (!total) return 0;
    return Math.min(100, Math.max(0, (current / total) * 100));
};
</script>

<template>
    <Head title="Dashboard Registro Ensayos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-emerald-100 text-emerald-700 rounded-xl shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-slate-800 leading-tight">Dashboard Ensayos</h2>
                        <p class="text-[11px] text-slate-500 font-semibold tracking-wide uppercase mt-0.5">Métricas analíticas del registro histórico</p>
                    </div>
                </div>
                
                <!-- Botón Primario Gigante para Ingresar a la Matriz de Datos -->
                <Link 
                    :href="route('ensayos.index')"
                    class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-black px-6 py-3 rounded-2xl shadow-lg hover:shadow-emerald-700/20 hover:-translate-y-0.5 transition-all duration-200 group text-sm w-full md:w-auto"
                >
                    <svg class="w-5 h-5 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    Ingresar a Ensayos
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </Link>
            </div>
        </template>

        <div class="py-8 bg-slate-50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

                <!-- Row 1: Primary Quick KPIs -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Total Ensayos -->
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6 flex items-center relative overflow-hidden transition-transform duration-300 hover:-translate-y-0.5 hover:shadow-md col-span-1 md:col-span-2">
                        <div class="p-5 bg-emerald-50 text-emerald-600 rounded-2xl mr-6 shadow-inner">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Ensayos Registrados</div>
                            <div class="text-4xl font-black text-slate-800 leading-none">{{ stats.total_ensayos }}</div>
                        </div>
                        <div class="absolute -bottom-6 -right-6 text-slate-50 text-9xl font-black select-none opacity-40 z-0">#</div>
                    </div>

                    <!-- Total Ingenios -->
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6 flex items-center relative overflow-hidden transition-transform duration-300 hover:-translate-y-0.5 hover:shadow-md">
                        <div class="p-5 bg-sky-50 text-sky-600 rounded-2xl mr-6 shadow-inner">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <div>
                            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Ingenios Activos</div>
                            <div class="text-4xl font-black text-slate-800 leading-none">{{ stats.total_ingenios }}</div>
                        </div>
                        <div class="absolute -bottom-6 -right-6 text-slate-50 text-9xl font-black select-none opacity-40 z-0">⚡</div>
                    </div>
                </div>

                <!-- Row 2: Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col group hover:shadow-md transition duration-300">
                        <div class="px-6 py-5 bg-slate-50/50 border-b border-slate-100 flex justify-between items-center">
                            <h4 class="font-black text-sm text-slate-700 flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                Distribución por Ambiente de Selección
                            </h4>
                        </div>
                        <div class="p-6 flex-grow flex items-center justify-center relative" style="min-height: 300px;">
                            <div v-if="stats.por_ambiente?.length > 0" class="w-full h-64">
                                <canvas ref="canvasAmbiente"></canvas>
                            </div>
                            <div v-else class="flex flex-col items-center justify-center text-slate-400 italic text-sm h-full">
                                <div class="text-4xl mb-2">🍕</div> Sin datos para graficar.
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col group hover:shadow-md transition duration-300">
                        <div class="px-6 py-5 bg-slate-50/50 border-b border-slate-100 flex justify-between items-center">
                            <h4 class="font-black text-sm text-slate-700 flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                                Cronológico de Siembra
                            </h4>
                        </div>
                        <div class="p-6 flex-grow relative" style="min-height: 300px;">
                            <div v-if="stats.por_ano?.length > 0" class="w-full h-64">
                                <canvas ref="canvasSiembra"></canvas>
                            </div>
                            <div v-else class="flex flex-col items-center justify-center text-slate-400 italic text-sm h-full">
                                <div class="text-4xl mb-2">📊</div> Sin años registrados.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 3: Table & Ingenios breakdown -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col lg:col-span-1">
                        <div class="px-6 py-5 bg-slate-50/50 border-b border-slate-100 flex justify-between items-center">
                            <h4 class="font-black text-sm text-slate-700">Top 5 Ingenios Activos</h4>
                        </div>
                        <div class="p-6 flex-grow flex flex-col gap-4 justify-center">
                            <div v-for="(ing, idx) in stats.por_ingenio" :key="ing.ingenio || 'n/a'" class="w-full">
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="font-bold text-slate-600 truncate max-w-[70%]">{{ ing.ingenio || 'Sin Registrar' }}</span>
                                    <span class="font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">{{ ing.total }} ens.</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="h-2 rounded-full transition-all duration-1000" :class="idx === 0 ? 'bg-emerald-500' : 'bg-slate-400'" :style="{ width: getPercent(stats.por_ingenio[0].total, ing.total) + '%' }"></div>
                                </div>
                            </div>
                            <div v-if="!stats.por_ingenio?.length" class="text-center text-slate-400 text-xs italic py-4">
                                🚫 Sin registros.
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden lg:col-span-2 flex flex-col">
                        <div class="px-6 py-5 bg-slate-50/50 border-b border-slate-100 flex justify-between items-center">
                            <h4 class="font-black text-sm text-slate-700">Últimos Ensayos Registrados</h4>
                            <Link :href="route('ensayos.index')" class="text-[11px] text-emerald-600 hover:text-emerald-800 font-extrabold tracking-wider uppercase">Ver Todo ➡️</Link>
                        </div>
                        <div class="overflow-x-auto flex-grow">
                            <table class="min-w-full divide-y divide-slate-100">
                                <thead class="bg-slate-50/20 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                    <tr>
                                        <th class="px-6 py-3.5 text-left">Ensayo</th>
                                        <th class="px-6 py-3.5 text-left">Proyecto</th>
                                        <th class="px-6 py-3.5 text-left">Cargado Por</th>
                                        <th class="px-6 py-3.5 text-right">Fecha</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-100 text-xs">
                                    <tr v-for="row in stats.recent_uploads" :key="row.id" class="hover:bg-emerald-50/20 transition duration-150">
                                        <td class="px-6 py-4 font-extrabold text-slate-800 truncate max-w-[160px]">{{ row.nombre_ensayo }}</td>
                                        <td class="px-6 py-4 text-slate-500 font-bold">{{ row.proyecto || 'General' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center font-extrabold px-2.5 py-0.5 rounded bg-emerald-50 border border-emerald-100 text-emerald-700 text-[9px] uppercase">{{ row.user?.name ?? 'Sistema' }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-right font-mono text-slate-400 font-bold">{{ formatDate(row.created_at) }}</td>
                                    </tr>
                                    <tr v-if="!stats.recent_uploads?.length">
                                        <td colspan="4" class="px-6 py-10 text-center text-slate-400 font-medium italic">
                                            🚀 No hay registros cargados.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
