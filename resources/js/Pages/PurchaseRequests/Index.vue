<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/Dashboard/StatusBadge.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

defineProps({
    purchaseRequests: { type: Object, required: true },
});

const page = usePage();
const permissions = page.props.auth.permissions || [];
const can = (perm) => permissions.includes(perm);

const formatCurrency = (amount, currency = 'USD') => {
    return new Intl.NumberFormat('es-PE', {
        style: 'currency',
        currency,
        minimumFractionDigits: 2,
    }).format(amount || 0);
};

const formatNumber = (num) => {
    return new Intl.NumberFormat('es-PE', {
        minimumFractionDigits: 4,
        maximumFractionDigits: 4,
    }).format(num || 0);
};
</script>

<template>
    <Head title="Mis Solicitudes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Solicitudes de Compra
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Historial de tus solicitudes de venta de oro a Orbeg Capital
                    </p>
                </div>
                <Link
                    v-if="can('purchase_requests.create')"
                    :href="route('purchase-requests.create')"
                    class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors"
                >
                    Nueva Solicitud
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table v-if="purchaseRequests.data.length > 0" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nro.</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Metal</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Peso (g)</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Precio/g</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Total Est.</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Estado</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Fecha</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Notas Operador</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr
                                    v-for="req in purchaseRequests.data"
                                    :key="req.id"
                                    class="hover:bg-gray-50 transition-colors"
                                >
                                    <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">
                                        {{ req.request_number }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                        {{ req.metal_name }}
                                        <span class="text-xs text-gray-400">({{ req.metal_symbol }})</span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                        {{ formatNumber(req.estimated_weight_grams) }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                        {{ formatCurrency(req.quoted_price_per_gram, req.currency) }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm font-semibold text-gray-900">
                                        {{ formatCurrency(req.estimated_total, req.currency) }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm">
                                        <StatusBadge :status="req.status" />
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">
                                        {{ req.created_at }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500 max-w-[200px] truncate">
                                        {{ req.operator_notes || '—' }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm">
                                        <Link
                                            :href="route('purchase-requests.show', req.id)"
                                            class="text-indigo-600 hover:text-indigo-800 font-medium"
                                        >
                                            Ver
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-else class="px-5 py-12 text-center text-sm text-gray-400">
                            No tienes solicitudes registradas. Crea tu primera solicitud desde el Dashboard.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
