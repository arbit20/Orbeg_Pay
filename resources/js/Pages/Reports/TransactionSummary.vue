<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    filters: Object,
    totals: Object,
    byMetal: Array,
    timeSeries: Array,
    metals: Array,
});

const dateFrom = ref(props.filters.date_from);
const dateTo = ref(props.filters.date_to);
const groupBy = ref(props.filters.group_by);

const applyFilters = () => {
    router.get(
        route('reports.transaction-summary'),
        { date_from: dateFrom.value, date_to: dateTo.value, group_by: groupBy.value },
        { preserveState: true, preserveScroll: true },
    );
};

const formatCurrency = (value) =>
    new Intl.NumberFormat('es-PE', { style: 'currency', currency: 'USD', minimumFractionDigits: 2 }).format(value || 0);

const formatWeight = (value) =>
    new Intl.NumberFormat('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 4 }).format(value || 0);

const formatNumber = (value) =>
    new Intl.NumberFormat('es-PE').format(value || 0);

const groupByLabel = computed(() => {
    return { day: 'Diario', week: 'Semanal', month: 'Mensual' }[groupBy.value] || 'Diario';
});

const chartMaxValue = computed(() => {
    if (!props.timeSeries?.length) return 1;
    let max = 0;
    for (const item of props.timeSeries) {
        if (item.purchase_amount > max) max = item.purchase_amount;
        if (item.sale_amount > max) max = item.sale_amount;
    }
    return max || 1;
});

const purchaseBars = computed(() =>
    props.timeSeries?.map((item) => ({
        ...item,
        height: Math.max((item.purchase_amount / chartMaxValue.value) * 100, 2),
    })) ?? [],
);

const saleBars = computed(() =>
    props.timeSeries?.map((item) => ({
        ...item,
        height: Math.max((item.sale_amount / chartMaxValue.value) * 100, 2),
    })) ?? [],
);

const shortPeriodLabel = (period) => {
    if (groupBy.value === 'month') {
        const [y, m] = period.split('-');
        const months = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        return `${months[parseInt(m) - 1]} ${y.slice(2)}`;
    }
    if (groupBy.value === 'week') {
        return period.replace(/^\d{4}-/, '');
    }
    const parts = period.split('-');
    return `${parts[2]}/${parts[1]}`;
};

const netClass = (value) => {
    if (value > 0) return 'text-emerald-600';
    if (value < 0) return 'text-red-600';
    return 'text-gray-500';
};
</script>

<template>
    <Head title="Resumen por Periodo" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Resumen por Periodo
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">

                <!-- Filtros -->
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-wrap items-end gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Desde</label>
                            <input
                                v-model="dateFrom"
                                type="date"
                                class="mt-1 block rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Hasta</label>
                            <input
                                v-model="dateTo"
                                type="date"
                                class="mt-1 block rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Agrupar por</label>
                            <select
                                v-model="groupBy"
                                class="mt-1 block rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                                <option value="day">Diario</option>
                                <option value="week">Semanal</option>
                                <option value="month">Mensual</option>
                            </select>
                        </div>
                        <div>
                            <button
                                @click="applyFilters"
                                class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Aplicar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tarjetas resumen -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-xl border border-blue-200 bg-white p-5 shadow-sm">
                        <p class="text-sm font-medium text-gray-500">Total Compras</p>
                        <p class="mt-1 text-2xl font-bold text-blue-600">{{ formatCurrency(totals.purchase_total) }}</p>
                        <p class="mt-1 text-xs text-gray-400">{{ formatNumber(totals.purchase_count) }} operaciones &middot; {{ formatWeight(totals.purchase_weight_grams) }} g</p>
                    </div>
                    <div class="rounded-xl border border-emerald-200 bg-white p-5 shadow-sm">
                        <p class="text-sm font-medium text-gray-500">Total Ventas</p>
                        <p class="mt-1 text-2xl font-bold text-emerald-600">{{ formatCurrency(totals.sale_total) }}</p>
                        <p class="mt-1 text-xs text-gray-400">{{ formatNumber(totals.sale_count) }} operaciones &middot; {{ formatWeight(totals.sale_weight_grams) }} g</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                        <p class="text-sm font-medium text-gray-500">Margen Neto</p>
                        <p class="mt-1 text-2xl font-bold" :class="netClass(totals.net)">{{ formatCurrency(totals.net) }}</p>
                        <p class="mt-1 text-xs text-gray-400">Ventas &minus; Compras</p>
                    </div>
                    <div class="rounded-xl border border-purple-200 bg-white p-5 shadow-sm">
                        <p class="text-sm font-medium text-gray-500">Operaciones Totales</p>
                        <p class="mt-1 text-2xl font-bold text-purple-600">{{ formatNumber(totals.purchase_count + totals.sale_count) }}</p>
                        <p class="mt-1 text-xs text-gray-400">{{ formatNumber(totals.purchase_count) }} compras + {{ formatNumber(totals.sale_count) }} ventas</p>
                    </div>
                </div>

                <!-- Gráfico de barras comparativo -->
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-sm font-semibold text-gray-900">Compras vs Ventas &mdash; {{ groupByLabel }}</h3>
                    </div>
                    <div class="p-6">
                        <div v-if="timeSeries && timeSeries.length > 0" class="flex items-end gap-1 overflow-x-auto" style="min-height: 220px;">
                            <div
                                v-for="(item, idx) in timeSeries"
                                :key="idx"
                                class="flex flex-col items-center"
                                :style="{ minWidth: timeSeries.length > 20 ? '28px' : '48px', flex: '1 1 0%' }"
                            >
                                <div class="flex w-full items-end justify-center gap-0.5" style="height: 180px;">
                                    <div
                                        class="w-2.5 rounded-t bg-blue-400 transition-all duration-300"
                                        :style="{ height: purchaseBars[idx].height + '%' }"
                                        :title="'Compras: ' + formatCurrency(item.purchase_amount)"
                                    ></div>
                                    <div
                                        class="w-2.5 rounded-t bg-emerald-400 transition-all duration-300"
                                        :style="{ height: saleBars[idx].height + '%' }"
                                        :title="'Ventas: ' + formatCurrency(item.sale_amount)"
                                    ></div>
                                </div>
                                <span class="mt-1.5 text-center text-[10px] leading-tight text-gray-400" :class="{ 'hidden sm:block': timeSeries.length > 15 }">
                                    {{ shortPeriodLabel(item.period) }}
                                </span>
                            </div>
                        </div>
                        <div v-else class="flex items-center justify-center py-12 text-sm text-gray-400">
                            No hay datos para el periodo seleccionado.
                        </div>
                        <!-- Leyenda -->
                        <div v-if="timeSeries && timeSeries.length > 0" class="mt-4 flex items-center justify-center gap-6 text-xs text-gray-500">
                            <span class="flex items-center gap-1.5">
                                <span class="inline-block h-2.5 w-2.5 rounded-sm bg-blue-400"></span> Compras
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="inline-block h-2.5 w-2.5 rounded-sm bg-emerald-400"></span> Ventas
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Tabla desglose por metal -->
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-sm font-semibold text-gray-900">Desglose por Metal</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table v-if="byMetal && byMetal.length > 0" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Metal</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Comprado (g)</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Vendido (g)</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Neto (g)</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Monto Compras</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Monto Ventas</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Margen</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="row in byMetal" :key="row.metal_id" class="transition-colors hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">
                                        {{ row.metal_name }}
                                        <span class="ml-1 text-xs text-gray-400">({{ row.metal_symbol }})</span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">{{ formatWeight(row.purchase_weight) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">{{ formatWeight(row.sale_weight) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-medium" :class="netClass(row.net_weight)">
                                        {{ formatWeight(row.net_weight) }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-blue-600">{{ formatCurrency(row.purchase_amount) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-emerald-600">{{ formatCurrency(row.sale_amount) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold" :class="netClass(row.net_amount)">
                                        {{ formatCurrency(row.net_amount) }}
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr class="font-semibold">
                                    <td class="px-4 py-3 text-sm text-gray-900">Total</td>
                                    <td class="px-4 py-3 text-right text-sm text-gray-900">{{ formatWeight(totals.purchase_weight_grams) }}</td>
                                    <td class="px-4 py-3 text-right text-sm text-gray-900">{{ formatWeight(totals.sale_weight_grams) }}</td>
                                    <td class="px-4 py-3 text-right text-sm" :class="netClass(totals.sale_weight_grams - totals.purchase_weight_grams)">
                                        {{ formatWeight(totals.sale_weight_grams - totals.purchase_weight_grams) }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm text-blue-600">{{ formatCurrency(totals.purchase_total) }}</td>
                                    <td class="px-4 py-3 text-right text-sm text-emerald-600">{{ formatCurrency(totals.sale_total) }}</td>
                                    <td class="px-4 py-3 text-right text-sm" :class="netClass(totals.net)">{{ formatCurrency(totals.net) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                        <div v-else class="px-6 py-12 text-center text-sm text-gray-400">
                            No hay operaciones con metales en el periodo seleccionado.
                        </div>
                    </div>
                </div>

                <!-- Tabla detallada por periodo -->
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-sm font-semibold text-gray-900">Detalle por Periodo ({{ groupByLabel }})</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table v-if="timeSeries && timeSeries.length > 0" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Periodo</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500"># Compras</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Monto Compras</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500"># Ventas</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Monto Ventas</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Neto</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="row in timeSeries" :key="row.period" class="transition-colors hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ row.period }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">{{ row.purchase_count }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-blue-600">{{ formatCurrency(row.purchase_amount) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">{{ row.sale_count }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-emerald-600">{{ formatCurrency(row.sale_amount) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-medium" :class="netClass(row.sale_amount - row.purchase_amount)">
                                        {{ formatCurrency(row.sale_amount - row.purchase_amount) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-else class="px-6 py-12 text-center text-sm text-gray-400">
                            No hay datos para el periodo seleccionado.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
