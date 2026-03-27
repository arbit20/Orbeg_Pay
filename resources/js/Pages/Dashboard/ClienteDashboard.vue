<script setup>
import { ref, computed, watch } from 'vue';
import StatCard from '@/Components/Dashboard/StatCard.vue';
import StatusBadge from '@/Components/Dashboard/StatusBadge.vue';
import DataTable from '@/Components/Dashboard/DataTable.vue';
import SectionTitle from '@/Components/Dashboard/SectionTitle.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    stats: { type: Object, required: true },
    metals: { type: Array, default: () => [] },
    myRequests: { type: Array, default: () => [] },
    myPurchases: { type: Array, default: () => [] },
});

const selectedMetalId = ref(props.metals[0]?.id ?? null);
const weightGrams = ref('');
const estimatedPurity = ref('0.9999');

const selectedMetal = computed(() =>
    props.metals.find((m) => m.id === selectedMetalId.value)
);

const TROY_OUNCE_GRAMS = 31.1035;

const weightTroyOunces = computed(() => {
    const w = parseFloat(weightGrams.value);
    if (!w || w <= 0) return 0;
    return w / TROY_OUNCE_GRAMS;
});

const estimatedTotal = computed(() => {
    const w = parseFloat(weightGrams.value);
    const p = parseFloat(estimatedPurity.value);
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

const requestColumns = [
    { key: 'request_number', label: 'Nro.' },
    { key: 'metal_name', label: 'Metal' },
    { key: 'estimated_weight_grams', label: 'Peso (g)' },
    { key: 'estimated_total', label: 'Total Est.' },
    { key: 'status', label: 'Estado' },
    { key: 'created_at', label: 'Fecha' },
];

const purchaseColumns = [
    { key: 'purchase_number', label: 'Nro. Compra' },
    { key: 'total', label: 'Total' },
    { key: 'status', label: 'Estado' },
    { key: 'purchase_date', label: 'Fecha' },
];
</script>

<template>
    <div class="space-y-8">
        <!-- Resumen -->
        <section>
            <SectionTitle title="Mis Solicitudes" subtitle="Estado de tus solicitudes de venta de oro" />
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <StatCard
                    title="Total solicitudes"
                    :value="stats.requests?.total ?? 0"
                    icon="&#128203;"
                    color="indigo"
                />
                <StatCard
                    title="En proceso"
                    :value="stats.requests?.pending ?? 0"
                    subtitle="Pendientes y en revisión"
                    icon="&#9203;"
                    color="amber"
                />
                <StatCard
                    title="Aprobadas"
                    :value="stats.requests?.approved ?? 0"
                    subtitle="Listas para compra"
                    icon="&#9989;"
                    color="green"
                />
                <StatCard
                    title="Convertidas a compra"
                    :value="stats.requests?.converted ?? 0"
                    subtitle="Transacciones formalizadas"
                    icon="&#128176;"
                    color="purple"
                />
            </div>
        </section>

        <!-- Cotizador de precios -->
        <section>
            <SectionTitle
                title="Cotizador de Precios"
                subtitle="Ingresa el peso estimado de tu oro para calcular el valor aproximado"
            />
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="p-6">
                    <!-- Precios actuales -->
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-500 mb-3">Precios actuales por metal</h4>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            <button
                                v-for="metal in metals"
                                :key="metal.id"
                                @click="selectedMetalId = metal.id"
                                class="flex items-center justify-between rounded-lg border-2 p-3 text-left transition-all"
                                :class="selectedMetalId === metal.id
                                    ? 'border-indigo-500 bg-indigo-50'
                                    : 'border-gray-200 hover:border-gray-300 bg-white'"
                            >
                                <div>
                                    <p class="font-semibold text-gray-900">{{ metal.name }}
                                        <span class="text-xs text-gray-400">({{ metal.symbol }})</span>
                                    </p>
                                    <p v-if="metal.price_per_gram" class="text-sm text-gray-500">
                                        {{ formatCurrency(metal.price_per_gram, metal.currency) }}/g
                                    </p>
                                    <p v-else class="text-sm text-red-400">Sin precio disponible</p>
                                </div>
                                <div v-if="metal.effective_date" class="text-right">
                                    <p class="text-xs text-gray-400">{{ metal.effective_date }}</p>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Formulario de cotización -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-700 mb-1">
                                Peso estimado (gramos)
                            </label>
                            <input
                                id="weight"
                                v-model="weightGrams"
                                type="number"
                                step="0.0001"
                                min="0"
                                placeholder="Ej: 100.0000"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            <p v-if="weightTroyOunces > 0" class="mt-1 text-xs text-gray-400">
                                ≈ {{ formatNumber(weightTroyOunces) }} oz troy
                            </p>
                        </div>
                        <div>
                            <label for="purity" class="block text-sm font-medium text-gray-700 mb-1">
                                Pureza estimada
                            </label>
                            <select
                                id="purity"
                                v-model="estimatedPurity"
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
                                La pureza final se determina con el análisis XRF
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Valor estimado
                            </label>
                            <div class="flex items-center h-[42px] rounded-lg border border-gray-200 bg-gray-50 px-4">
                                <span class="text-lg font-bold" :class="estimatedTotal > 0 ? 'text-emerald-600' : 'text-gray-400'">
                                    {{ estimatedTotal > 0
                                        ? formatCurrency(estimatedTotal, selectedMetal?.currency || 'USD')
                                        : '—' }}
                                </span>
                            </div>
                            <p v-if="estimatedTotal > 0" class="mt-1 text-xs text-gray-400">
                                Precio referencial. El monto final depende del análisis con equipo XRF.
                            </p>
                        </div>
                    </div>

                    <!-- Botón de crear solicitud -->
                    <div class="mt-6 flex items-center justify-between border-t border-gray-100 pt-4">
                        <p class="text-xs text-gray-400">
                            Al crear una solicitud, un operador de Orbeg Capital será notificado para coordinar la transacción.
                        </p>
                        <Link
                            v-if="estimatedTotal > 0"
                            :href="route('purchase-requests.create')"
                            class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors"
                        >
                            Crear Solicitud de Venta
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mis solicitudes recientes -->
        <section>
            <DataTable
                title="Mis Solicitudes Recientes"
                :columns="requestColumns"
                :rows="myRequests"
                empty-message="Aún no has creado solicitudes de venta"
            >
                <template #estimated_weight_grams="{ row }">
                    {{ formatNumber(row.estimated_weight_grams) }} g
                </template>
                <template #estimated_total="{ row }">
                    {{ formatCurrency(row.estimated_total, row.currency) }}
                </template>
                <template #status="{ row }">
                    <StatusBadge :status="row.status" />
                </template>
            </DataTable>
        </section>

        <!-- Mis compras formalizadas -->
        <section v-if="myPurchases.length > 0">
            <DataTable
                title="Mis Transacciones Formalizadas"
                :columns="purchaseColumns"
                :rows="myPurchases"
            >
                <template #total="{ row }">
                    {{ formatCurrency(row.total, row.currency) }}
                </template>
                <template #status="{ row }">
                    <StatusBadge :status="row.status" />
                </template>
            </DataTable>
        </section>
    </div>
</template>
