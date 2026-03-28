<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

const props = defineProps({
    clients: Array,
});

const now = new Date();
const localIso = new Date(now.getTime() - now.getTimezoneOffset() * 60000)
    .toISOString()
    .slice(0, 16);

const form = useForm({
    sale_datetime: localIso,
    weight_grams: '',
    lot_count: 1,
    purity: 0.97,
    unit_price_bs: '',
    total_bs: '',
    discount_percentage: 0,
    tax_percentage: 0,
    reference_quote_usd_oz: '',
    quote_source: 'Kitco',
    exchange_rate_bs_usd: '',
    client_id: '',
    evidence: null,
    notes: '',
});

const calculatedTotal = computed(() => {
    const w = parseFloat(form.weight_grams);
    const p = parseFloat(form.unit_price_bs);
    if (!isNaN(w) && !isNaN(p) && w > 0 && p > 0) {
        return (w * p).toFixed(2);
    }
    return '';
});

watch(calculatedTotal, (val) => {
    form.total_bs = val;
});

const subtotalValue = computed(() => {
    const v = parseFloat(form.total_bs);
    return isNaN(v) ? null : v;
});

const discountAmountPreview = computed(() => {
    if (subtotalValue.value === null) return '';
    const dp = parseFloat(form.discount_percentage);
    if (isNaN(dp) || dp <= 0) return '0.00';
    const discount = (subtotalValue.value * dp) / 100;
    return discount.toFixed(2);
});

const taxAmountPreview = computed(() => {
    if (subtotalValue.value === null) return '';
    const dp = parseFloat(form.discount_percentage);
    const tp = parseFloat(form.tax_percentage);
    const discount = isNaN(dp) || dp <= 0 ? 0 : (subtotalValue.value * dp) / 100;
    const net = subtotalValue.value - discount;
    const tax = isNaN(tp) || tp <= 0 ? 0 : (net * tp) / 100;
    return tax.toFixed(2);
});

const totalPreview = computed(() => {
    if (subtotalValue.value === null) return '';
    const dp = parseFloat(form.discount_percentage);
    const tp = parseFloat(form.tax_percentage);
    const discount = isNaN(dp) || dp <= 0 ? 0 : (subtotalValue.value * dp) / 100;
    const net = subtotalValue.value - discount;
    const tax = isNaN(tp) || tp <= 0 ? 0 : (net * tp) / 100;
    return (net + tax).toFixed(2);
});

const onFileChange = (e) => {
    form.evidence = e.target.files[0] || null;
};

const submit = () => {
    form.post(route('sales.store'), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Nueva Venta" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Registrar Nueva Venta
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <InputLabel for="sale_datetime" value="Fecha y Hora" />
                                <TextInput
                                    id="sale_datetime"
                                    v-model="form.sale_datetime"
                                    type="datetime-local"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.sale_datetime" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="client_id" value="Cliente (opcional)" />
                                <select
                                    id="client_id"
                                    v-model="form.client_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">-- Sin cliente --</option>
                                    <option v-for="c in clients" :key="c.id" :value="c.id">
                                        {{ c.business_name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.client_id" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="weight_grams" value="Cantidad (g)" />
                                <TextInput
                                    id="weight_grams"
                                    v-model="form.weight_grams"
                                    type="number"
                                    step="0.1"
                                    min="0"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.weight_grams" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="lot_count" value="N° de Lotes" />
                                <TextInput
                                    id="lot_count"
                                    v-model="form.lot_count"
                                    type="number"
                                    min="1"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.lot_count" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="purity" value="Ley (ej: 0.97 = 97%)" />
                                <TextInput
                                    id="purity"
                                    v-model="form.purity"
                                    type="number"
                                    step="0.0001"
                                    min="0"
                                    max="1"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.purity" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="unit_price_bs" value="Precio Unitario (Bs/g)" />
                                <TextInput
                                    id="unit_price_bs"
                                    v-model="form.unit_price_bs"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.unit_price_bs" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="total_bs" value="Total (Bs)" />
                                <TextInput
                                    id="total_bs"
                                    v-model="form.total_bs"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    :readonly="true"
                                    class="mt-1 block w-full bg-gray-50"
                                    required
                                />
                                <InputError :message="form.errors.total_bs" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="discount_percentage" value="Descuento (%)" />
                                <TextInput
                                    id="discount_percentage"
                                    v-model="form.discount_percentage"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    max="100"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.discount_percentage" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="tax_percentage" value="Impuesto (%)" />
                                <TextInput
                                    id="tax_percentage"
                                    v-model="form.tax_percentage"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    max="100"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.tax_percentage" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="reference_quote_usd_oz" value="Cotización ref. (USD/oz)" />
                                <TextInput
                                    id="reference_quote_usd_oz"
                                    v-model="form.reference_quote_usd_oz"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.reference_quote_usd_oz" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="quote_source" value="Fuente" />
                                <TextInput
                                    id="quote_source"
                                    v-model="form.quote_source"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="Ej: Kitco"
                                />
                                <InputError :message="form.errors.quote_source" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="exchange_rate_bs_usd" value="TC Usado (Bs/USD)" />
                                <TextInput
                                    id="exchange_rate_bs_usd"
                                    v-model="form.exchange_rate_bs_usd"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.exchange_rate_bs_usd" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="evidence" value="Evidencia (foto)" />
                                <input
                                    id="evidence"
                                    type="file"
                                    accept="image/*"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100"
                                    @change="onFileChange"
                                />
                                <InputError :message="form.errors.evidence" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <InputLabel for="notes" value="Notas / Observaciones" />
                                <textarea
                                    id="notes"
                                    v-model="form.notes"
                                    rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                <InputError :message="form.errors.notes" class="mt-2" />
                            </div>

                            <div class="md:col-span-2 rounded-md border border-gray-200 bg-gray-50 p-4">
                                <div class="text-sm font-semibold text-gray-800">Resumen de impuestos y descuentos</div>
                                <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-3">
                                    <div class="text-sm">
                                        <div class="text-gray-500">Descuento</div>
                                        <div class="font-semibold">{{ discountAmountPreview }}</div>
                                    </div>
                                    <div class="text-sm">
                                        <div class="text-gray-500">Impuesto</div>
                                        <div class="font-semibold">{{ taxAmountPreview }}</div>
                                    </div>
                                    <div class="text-sm">
                                        <div class="text-gray-500">Total final</div>
                                        <div class="font-semibold">{{ totalPreview }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-4 md:col-span-2">
                                <Link :href="route('sales.index')" class="text-sm text-gray-600 underline hover:text-gray-900">
                                    Cancelar
                                </Link>
                                <PrimaryButton :disabled="form.processing">Registrar Venta</PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
