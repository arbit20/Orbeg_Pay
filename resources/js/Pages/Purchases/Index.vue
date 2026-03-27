<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    purchases: Array,
    can: Object,
});

const formatNumber = (value, decimals = 2) => {
    if (value === null || value === undefined || value === '') return '';
    const num = parseFloat(value);
    if (isNaN(num)) return '';
    return num.toLocaleString('es-BO', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    });
};

const formatPurity = (value) => {
    if (value === null || value === undefined) return '';
    const pct = parseFloat(value) * 100;
    return pct.toLocaleString('es-BO', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }) + '%';
};
</script>

<template>
    <Head title="Registro de Compras" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Registro de Compra
                </h2>
                <Link v-if="can.create" :href="route('purchases.create')">
                    <PrimaryButton>Nueva Compra</PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-[1400px] space-y-4 sm:px-6 lg:px-8">
                <div v-if="$page.props.flash?.success" class="rounded-md bg-green-50 p-4">
                    <p class="text-sm font-medium text-green-800">{{ $page.props.flash.success }}</p>
                </div>
                <div v-if="$page.props.flash?.error" class="rounded-md bg-red-50 p-4">
                    <p class="text-sm font-medium text-red-800">{{ $page.props.flash.error }}</p>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b-2 border-gray-300 bg-gray-100 px-4 py-3">
                        <h3 class="text-center text-sm font-bold uppercase tracking-wide text-gray-800">
                            REGISTRO DE COMPRA
                        </h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-300 border border-gray-300">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="border border-gray-300 px-3 py-3 text-center text-xs font-bold uppercase text-gray-700">ID</th>
                                    <th class="border border-gray-300 px-3 py-3 text-center text-xs font-bold uppercase text-gray-700">FECHA-HORA</th>
                                    <th class="border border-gray-300 px-3 py-3 text-center text-xs font-bold uppercase text-gray-700">
                                        CANTIDAD<br /><span class="font-normal">(g)</span>
                                    </th>
                                    <th class="border border-gray-300 px-3 py-3 text-center text-xs font-bold uppercase text-gray-700">N&ordm; DE LOTES</th>
                                    <th class="border border-gray-300 px-3 py-3 text-center text-xs font-bold uppercase text-gray-700">LEY</th>
                                    <th class="border border-gray-300 px-3 py-3 text-center text-xs font-bold uppercase text-gray-700">
                                        PRECIO UNITARIO<br /><span class="font-normal">(Bs/g)</span>
                                    </th>
                                    <th class="border border-gray-300 px-3 py-3 text-center text-xs font-bold uppercase text-gray-700">TOTAL (Bs)</th>
                                    <th class="border border-gray-300 px-3 py-3 text-center text-xs font-bold uppercase text-gray-700">
                                        Cotización ref.<br /><span class="font-normal">(USD/oz)</span>
                                    </th>
                                    <th class="border border-gray-300 px-3 py-3 text-center text-xs font-bold uppercase text-gray-700">FUENTE</th>
                                    <th class="border border-gray-300 px-3 py-3 text-center text-xs font-bold uppercase text-gray-700">
                                        TC USADO<br /><span class="font-normal">(Bs/USD)</span>
                                    </th>
                                    <th class="border border-gray-300 px-3 py-3 text-center text-xs font-bold uppercase text-gray-700">EVIDENCIA</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="purchase in purchases" :key="purchase.id" class="hover:bg-gray-50">
                                    <td class="border border-gray-200 px-3 py-4 text-center text-sm text-gray-900">
                                        {{ purchase.id }}
                                    </td>
                                    <td class="whitespace-nowrap border border-gray-200 px-3 py-4 text-center text-sm text-gray-900">
                                        {{ purchase.purchase_datetime }}
                                    </td>
                                    <td class="border border-gray-200 px-3 py-4 text-center text-sm text-gray-900">
                                        {{ formatNumber(purchase.weight_grams, 1) }}
                                    </td>
                                    <td class="border border-gray-200 px-3 py-4 text-center text-sm text-gray-900">
                                        {{ purchase.lot_count }}
                                    </td>
                                    <td class="border border-gray-200 px-3 py-4 text-center text-sm text-gray-900">
                                        {{ formatPurity(purchase.purity) }}
                                    </td>
                                    <td class="border border-gray-200 px-3 py-4 text-center text-sm text-gray-900">
                                        {{ formatNumber(purchase.unit_price_bs) }}
                                    </td>
                                    <td class="border border-gray-200 px-3 py-4 text-center text-sm font-medium text-gray-900">
                                        {{ formatNumber(purchase.total_bs) }}
                                    </td>
                                    <td class="border border-gray-200 px-3 py-4 text-center text-sm text-gray-900">
                                        {{ formatNumber(purchase.reference_quote_usd_oz) }}
                                    </td>
                                    <td class="border border-gray-200 px-3 py-4 text-center text-sm text-gray-900">
                                        {{ purchase.quote_source || '' }}
                                    </td>
                                    <td class="border border-gray-200 px-3 py-4 text-center text-sm text-gray-900">
                                        {{ formatNumber(purchase.exchange_rate_bs_usd) }}
                                    </td>
                                    <td class="border border-gray-200 px-3 py-4 text-center">
                                        <a
                                            v-if="purchase.evidence_url"
                                            :href="purchase.evidence_url"
                                            target="_blank"
                                            class="inline-block"
                                        >
                                            <img
                                                :src="purchase.evidence_url"
                                                alt="Evidencia"
                                                class="h-16 w-16 rounded object-cover shadow-sm"
                                            />
                                        </a>
                                        <span v-else class="text-sm text-gray-400">—</span>
                                    </td>
                                </tr>
                                <tr v-if="purchases.length === 0">
                                    <td colspan="11" class="border border-gray-200 px-6 py-12 text-center text-sm text-gray-500">
                                        No hay compras registradas.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
