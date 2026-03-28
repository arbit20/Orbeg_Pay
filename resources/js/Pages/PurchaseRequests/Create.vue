<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    metals: { type: Array, required: true },
});

const form = useForm({
    metal_id: props.metals[0]?.id ?? '',
    estimated_weight_grams: '',
    estimated_purity: '0.9999',
    client_notes: '',
});

const TROY_OUNCE_GRAMS = 31.1035;

const selectedMetal = computed(() =>
    props.metals.find((m) => m.id === form.metal_id)
);

const weightTroyOunces = computed(() => {
    const w = parseFloat(form.estimated_weight_grams);
    if (!w || w <= 0) return 0;
    return w / TROY_OUNCE_GRAMS;
});

const estimatedTotal = computed(() => {
    const w = parseFloat(form.estimated_weight_grams);
    const p = parseFloat(form.estimated_purity);
    const price = parseFloat(selectedMetal.value?.latest_price?.price_per_gram);
    if (!w || !price || w <= 0 || !p) return 0;
    return w * p * price;
});

const formatCurrency = (amount, currency = 'USD') => {
    return new Intl.NumberFormat('es-PE', {
        style: 'currency',
        currency,
        minimumFractionDigits: 2,
    }).format(amount || 0);
};

const formatNumber = (num, decimals = 4) => {
    return new Intl.NumberFormat('es-PE', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    }).format(num || 0);
};

const submit = () => {
    form.post(route('purchase-requests.store'));
};
</script>

<template>
    <Head title="Nueva Solicitud de Compra" />

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
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Nueva Solicitud de Venta
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Ingresa los datos de tu oro para crear una solicitud de compra
                    </p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="p-6 space-y-6">
                            <!-- Metal -->
                            <div>
                                <label for="metal" class="block text-sm font-medium text-gray-700 mb-2">
                                    Metal precioso
                                </label>
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                    <button
                                        v-for="metal in metals"
                                        :key="metal.id"
                                        type="button"
                                        @click="form.metal_id = metal.id"
                                        class="flex items-center justify-between rounded-lg border-2 p-3 text-left transition-all"
                                        :class="form.metal_id === metal.id
                                            ? 'border-indigo-500 bg-indigo-50'
                                            : 'border-gray-200 hover:border-gray-300'"
                                    >
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ metal.name }}</p>
                                            <p class="text-xs text-gray-400">{{ metal.symbol }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p v-if="metal.latest_price" class="text-sm text-emerald-600 font-medium">
                                                {{ formatCurrency(metal.latest_price.price_per_gram, metal.latest_price.currency) }}/g
                                            </p>
                                            <p v-else class="text-xs text-red-400">Sin precio</p>
                                        </div>
                                    </button>
                                </div>
                                <p v-if="form.errors.metal_id" class="mt-1 text-sm text-red-600">{{ form.errors.metal_id }}</p>
                            </div>

                            <!-- Peso -->
                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-700 mb-1">
                                    Peso estimado (gramos)
                                </label>
                                <input
                                    id="weight"
                                    v-model="form.estimated_weight_grams"
                                    type="number"
                                    step="0.0001"
                                    min="0.0001"
                                    placeholder="Ej: 100.0000"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                <div class="mt-1 flex gap-4">
                                    <p v-if="weightTroyOunces > 0" class="text-xs text-gray-400">
                                        ≈ {{ formatNumber(weightTroyOunces) }} onzas troy
                                    </p>
                                </div>
                                <p v-if="form.errors.estimated_weight_grams" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.estimated_weight_grams }}
                                </p>
                            </div>

                            <!-- Pureza -->
                            <div>
                                <label for="purity" class="block text-sm font-medium text-gray-700 mb-1">
                                    Pureza estimada
                                </label>
                                <select
                                    id="purity"
                                    v-model="form.estimated_purity"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="1.0000">100% (puro)</option>
                                    <option value="0.9999">99.99%</option>
                                    <option value="0.9990">99.90%</option>
                                    <option value="0.9950">99.50%</option>
                                    <option value="0.9580">95.80% (24K)</option>
                                    <option value="0.9160">91.60% (22K)</option>
                                    <option value="0.7500">75.00% (18K)</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-400">
                                    La pureza final será determinada por el operador con la pistola de análisis XRF (Thermo Scientific Niton XL2).
                                </p>
                                <p v-if="form.errors.estimated_purity" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.estimated_purity }}
                                </p>
                            </div>

                            <!-- Notas -->
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                                    Notas adicionales (opcional)
                                </label>
                                <textarea
                                    id="notes"
                                    v-model="form.client_notes"
                                    rows="3"
                                    placeholder="Describe el tipo de material, cantidad de lotes, u otra información relevante..."
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                ></textarea>
                                <p v-if="form.errors.client_notes" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.client_notes }}
                                </p>
                            </div>
                        </div>

                        <!-- Resumen -->
                        <div v-if="estimatedTotal > 0" class="border-t border-gray-100 bg-gray-50 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500">Valor estimado</p>
                                    <p class="text-2xl font-bold text-emerald-600">
                                        {{ formatCurrency(estimatedTotal, selectedMetal?.latest_price?.currency || 'USD') }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        Este es un valor referencial. El monto final se calcula tras el análisis del material.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div class="border-t border-gray-200 bg-white px-6 py-4 flex items-center justify-end gap-3">
                            <Link
                                :href="route('purchase-requests.index')"
                                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition-colors"
                            >
                                Cancelar
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing || estimatedTotal <= 0"
                                class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                {{ form.processing ? 'Enviando...' : 'Crear Solicitud' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
