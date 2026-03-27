<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    metals: { type: Array, required: true },
});

const selectedMetalId = ref(props.metals[0]?.id ?? null);
const weightGrams = ref('');
const purity = ref('0.9999');

const TROY_OUNCE_GRAMS = 31.1035;

const selectedMetal = computed(() =>
    props.metals.find((m) => m.id === selectedMetalId.value)
);

const weightTroyOunces = computed(() => {
    const w = parseFloat(weightGrams.value);
    if (!w || w <= 0) return 0;
    return w / TROY_OUNCE_GRAMS;
});

const estimatedTotal = computed(() => {
    const w = parseFloat(weightGrams.value);
    const p = parseFloat(purity.value);
    const price = parseFloat(selectedMetal.value?.price_per_gram);
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
</script>

<template>
    <Head title="Cotizador de Precios" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Cotizador de Precios
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Consulta precios actuales y calcula el valor estimado de tu metal precioso
            </p>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Precios actuales -->
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-sm font-semibold text-gray-900">Precios Vigentes</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <button
                                v-for="metal in metals"
                                :key="metal.id"
                                @click="selectedMetalId = metal.id"
                                class="flex flex-col rounded-lg border-2 p-4 text-left transition-all"
                                :class="selectedMetalId === metal.id
                                    ? 'border-indigo-500 bg-indigo-50 shadow-sm'
                                    : 'border-gray-200 hover:border-gray-300'"
                            >
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-bold text-gray-900">{{ metal.name }}</span>
                                    <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded">{{ metal.symbol }}</span>
                                </div>
                                <div v-if="metal.price_per_gram">
                                    <p class="text-lg font-semibold text-emerald-600">
                                        {{ formatCurrency(metal.price_per_gram, metal.currency) }}
                                        <span class="text-xs text-gray-400 font-normal">/gramo</span>
                                    </p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ formatCurrency(metal.price_per_troy_ounce, metal.currency) }}
                                        <span class="text-xs text-gray-400">/oz troy</span>
                                    </p>
                                    <p class="text-xs text-gray-400 mt-2">
                                        Vigente desde: {{ metal.effective_date }}
                                        <span v-if="metal.source"> &middot; {{ metal.source }}</span>
                                    </p>
                                </div>
                                <p v-else class="text-sm text-red-400">Sin precio disponible</p>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Calculadora -->
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-sm font-semibold text-gray-900">Calcular Valor</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="calc-weight" class="block text-sm font-medium text-gray-700 mb-1">
                                    Peso (gramos)
                                </label>
                                <input
                                    id="calc-weight"
                                    v-model="weightGrams"
                                    type="number"
                                    step="0.0001"
                                    min="0"
                                    placeholder="Ingresa el peso en gramos"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                <p v-if="weightTroyOunces > 0" class="mt-1 text-xs text-gray-400">
                                    ≈ {{ formatNumber(weightTroyOunces) }} onzas troy
                                </p>
                            </div>
                            <div>
                                <label for="calc-purity" class="block text-sm font-medium text-gray-700 mb-1">
                                    Pureza estimada
                                </label>
                                <select
                                    id="calc-purity"
                                    v-model="purity"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="1.0000">100% (puro)</option>
                                    <option value="0.9999">99.99%</option>
                                    <option value="0.9990">99.90%</option>
                                    <option value="0.9950">99.50%</option>
                                    <option value="0.9580">95.80% (24K)</option>
                                    <option value="0.9160">91.60% (22K)</option>
                                    <option value="0.7500">75.00% (18K)</option>
                                    <option value="0.5850">58.50% (14K)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Resultado -->
                        <div v-if="estimatedTotal > 0" class="mt-6 rounded-lg bg-emerald-50 border border-emerald-200 p-6 text-center">
                            <p class="text-sm text-emerald-700 mb-1">Valor estimado de tu {{ selectedMetal?.name }}</p>
                            <p class="text-3xl font-bold text-emerald-700">
                                {{ formatCurrency(estimatedTotal, selectedMetal?.currency || 'USD') }}
                            </p>
                            <p class="text-xs text-emerald-600/70 mt-2">
                                {{ formatNumber(parseFloat(weightGrams)) }} g &times; {{ purity * 100 }}% pureza &times;
                                {{ formatCurrency(selectedMetal?.price_per_gram, selectedMetal?.currency || 'USD') }}/g
                            </p>
                            <p class="text-xs text-gray-400 mt-3">
                                Precio referencial. El monto final se calcula tras el análisis con equipo XRF (Thermo Scientific Niton XL2).
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
