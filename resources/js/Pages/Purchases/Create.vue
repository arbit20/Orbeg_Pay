<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

const props = defineProps({
    suppliers: Array,
});

const now = new Date();
const localIso = new Date(now.getTime() - now.getTimezoneOffset() * 60000)
    .toISOString()
    .slice(0, 16);

const form = useForm({
    purchase_datetime: localIso,
    weight_grams: '',
    lot_count: 1,
    purity: 0.97,
    unit_price_bs: '',
    total_bs: '',
    reference_quote_usd_oz: '',
    quote_source: 'Kitco',
    exchange_rate_bs_usd: '',
    supplier_id: '',
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
    if (val) form.total_bs = val;
});

const onFileChange = (e) => {
    form.evidence = e.target.files[0] || null;
};

const submit = () => {
    form.post(route('purchases.store'), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Nueva Compra" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Registrar Nueva Compra
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <InputLabel for="purchase_datetime" value="Fecha y Hora" />
                                <TextInput
                                    id="purchase_datetime"
                                    v-model="form.purchase_datetime"
                                    type="datetime-local"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.purchase_datetime" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="supplier_id" value="Proveedor (opcional)" />
                                <select
                                    id="supplier_id"
                                    v-model="form.supplier_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">-- Sin proveedor --</option>
                                    <option v-for="s in suppliers" :key="s.id" :value="s.id">
                                        {{ s.business_name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.supplier_id" class="mt-2" />
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
                                    class="mt-1 block w-full bg-gray-50"
                                    required
                                />
                                <InputError :message="form.errors.total_bs" class="mt-2" />
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

                            <div class="flex items-center justify-end gap-4 md:col-span-2">
                                <Link :href="route('purchases.index')" class="text-sm text-gray-600 underline hover:text-gray-900">
                                    Cancelar
                                </Link>
                                <PrimaryButton :disabled="form.processing">Registrar Compra</PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
