<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/Dashboard/StatusBadge.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    purchaseRequest: { type: Object, required: true },
});

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

const TROY_OUNCE_GRAMS = 31.1035;
</script>

<template>
    <Head :title="`Solicitud ${purchaseRequest.request_number}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link
                    :href="route('purchase-requests.index')"
                    class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <div class="flex-1">
                    <div class="flex items-center gap-3">
                        <h2 class="text-xl font-semibold leading-tight text-gray-800">
                            {{ purchaseRequest.request_number }}
                        </h2>
                        <StatusBadge :status="purchaseRequest.status" />
                    </div>
                    <p class="mt-1 text-sm text-gray-500">
                        Creada el {{ purchaseRequest.created_at }}
                    </p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Datos del material -->
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-sm font-semibold text-gray-900">Datos del Material</h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Metal</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ purchaseRequest.metal_name }}
                                    <span class="text-gray-400">({{ purchaseRequest.metal_symbol }})</span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Peso estimado</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ formatNumber(purchaseRequest.estimated_weight_grams) }} g
                                    <span class="text-gray-400">
                                        (≈ {{ formatNumber(purchaseRequest.estimated_weight_grams / TROY_OUNCE_GRAMS) }} oz troy)
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Pureza estimada</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ purchaseRequest.estimated_purity ? (purchaseRequest.estimated_purity * 100).toFixed(2) + '%' : 'No especificada' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Precio cotizado por gramo</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ formatCurrency(purchaseRequest.quoted_price_per_gram, purchaseRequest.currency) }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Resumen financiero -->
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-sm font-semibold text-gray-900">Resumen Financiero</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Total estimado</span>
                            <span class="text-2xl font-bold text-emerald-600">
                                {{ formatCurrency(purchaseRequest.estimated_total, purchaseRequest.currency) }}
                            </span>
                        </div>
                        <p class="mt-2 text-xs text-gray-400">
                            El monto final se determinará tras el análisis del material con equipo XRF y confirmación del operador.
                        </p>
                    </div>
                </div>

                <!-- Estado y revisión -->
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-sm font-semibold text-gray-900">Estado de la Solicitud</h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Estado actual</dt>
                                <dd class="mt-1">
                                    <StatusBadge :status="purchaseRequest.status" />
                                    <span class="ml-2 text-sm text-gray-600">{{ purchaseRequest.status_label }}</span>
                                </dd>
                            </div>
                            <div v-if="purchaseRequest.reviewer_name">
                                <dt class="text-sm font-medium text-gray-500">Revisado por</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ purchaseRequest.reviewer_name }}
                                    <span v-if="purchaseRequest.reviewed_at" class="text-gray-400">
                                        — {{ purchaseRequest.reviewed_at }}
                                    </span>
                                </dd>
                            </div>
                            <div v-if="purchaseRequest.purchase_number">
                                <dt class="text-sm font-medium text-gray-500">Compra asociada</dt>
                                <dd class="mt-1 text-sm font-medium text-indigo-600">
                                    {{ purchaseRequest.purchase_number }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Notas -->
                <div v-if="purchaseRequest.client_notes || purchaseRequest.operator_notes" class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-sm font-semibold text-gray-900">Notas</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div v-if="purchaseRequest.client_notes">
                            <p class="text-xs font-medium text-gray-500 mb-1">Tus notas</p>
                            <p class="text-sm text-gray-700 bg-gray-50 rounded-lg p-3">{{ purchaseRequest.client_notes }}</p>
                        </div>
                        <div v-if="purchaseRequest.operator_notes">
                            <p class="text-xs font-medium text-gray-500 mb-1">Notas del operador</p>
                            <p class="text-sm text-gray-700 bg-blue-50 rounded-lg p-3">{{ purchaseRequest.operator_notes }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
