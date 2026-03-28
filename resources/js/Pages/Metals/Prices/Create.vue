<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    metal: Object,
});

const form = useForm({
    price_per_gram: '',
    currency: 'USD',
    effective_date: new Date().toISOString().slice(0, 10),
    source: '',
});

const TROY_OUNCE_GRAMS = 31.1035;

const pricePerTroyOunce = computed(() => {
    const ppg = parseFloat(form.price_per_gram);
    if (isNaN(ppg) || ppg <= 0) return '-';
    return (ppg * TROY_OUNCE_GRAMS).toFixed(2);
});

const submit = () => {
    form.post(route('metals.prices.store', props.metal.id));
};
</script>

<template>
    <Head :title="`Nuevo Precio - ${metal.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Registrar Precio: {{ metal.name }} ({{ metal.symbol }})
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="price_per_gram" value="Precio por Gramo (USD)" />
                                <TextInput
                                    id="price_per_gram"
                                    v-model="form.price_per_gram"
                                    type="number"
                                    step="0.0001"
                                    min="0.0001"
                                    class="mt-1 block w-full"
                                    required
                                    autofocus
                                    placeholder="0.0000"
                                />
                                <InputError :message="form.errors.price_per_gram" class="mt-2" />
                            </div>

                            <div v-if="pricePerTroyOunce !== '-'" class="rounded-md bg-amber-50 p-4">
                                <p class="text-sm text-amber-800">
                                    Precio por Onza Troy: <strong>$ {{ pricePerTroyOunce }}</strong>
                                    <span class="ml-1 text-xs text-amber-600">(1 oz troy = 31.1035 g)</span>
                                </p>
                            </div>

                            <div>
                                <InputLabel for="currency" value="Moneda" />
                                <select
                                    id="currency"
                                    v-model="form.currency"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="USD">USD - Dolar Estadounidense</option>
                                    <option value="PEN">PEN - Sol Peruano</option>
                                </select>
                                <InputError :message="form.errors.currency" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="effective_date" value="Fecha de Vigencia" />
                                <TextInput
                                    id="effective_date"
                                    v-model="form.effective_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.effective_date" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="source" value="Fuente / Referencia (opcional)" />
                                <TextInput
                                    id="source"
                                    v-model="form.source"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="Ej: Bolsa de Londres, Kitco"
                                />
                                <InputError :message="form.errors.source" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end gap-4">
                                <Link :href="route('metals.show', metal.id)" class="text-sm text-gray-600 underline hover:text-gray-900">
                                    Cancelar
                                </Link>
                                <PrimaryButton :disabled="form.processing">
                                    Registrar Precio
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
