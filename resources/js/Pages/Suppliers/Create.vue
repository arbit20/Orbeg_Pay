<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    supplierTypes: Array,
    documentTypes: Array,
    paymentMethods: Array,
});

const form = useForm({
    supplier_type: 'empresa',
    document_type: 'NIT',
    document_number: '',
    business_name: '',
    trade_name: '',
    email: '',
    phone: '',
    address: '',
    city: '',
    state: '',
    country: 'Bolivia',
    contact_person: '',
    contact_phone: '',
    bank_name: '',
    bank_account_number: '',
    bank_cci: '',
    payment_method: 'transferencia',
    notes: '',
    is_active: true,
});

const isPersona = computed(() => form.supplier_type === 'persona');
const isEmpresa = computed(() => form.supplier_type === 'empresa');

const normalizeByType = () => {
    if (isPersona.value) {
        form.trade_name = '';
        form.address = '';
        form.contact_person = '';
        form.contact_phone = '';
        form.bank_name = '';
        form.bank_account_number = '';
        form.bank_cci = '';
    }
};

const submit = () => {
    normalizeByType();
    form.post(route('suppliers.store'));
};
</script>

<template>
    <Head title="Nuevo Proveedor" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Registrar Nuevo Proveedor
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <InputLabel for="supplier_type" value="Tipo de proveedor" />
                                <select
                                    id="supplier_type"
                                    v-model="form.supplier_type"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option v-for="type in supplierTypes" :key="type.value" :value="type.value">
                                        {{ type.label }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.supplier_type" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="document_type" value="Tipo de documento" />
                                <select
                                    id="document_type"
                                    v-model="form.document_type"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option v-for="type in documentTypes" :key="type.value" :value="type.value">
                                        {{ type.label }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.document_type" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="document_number" value="Numero de documento" />
                                <TextInput id="document_number" v-model="form.document_number" type="text" class="mt-1 block w-full" required />
                                <InputError :message="form.errors.document_number" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="payment_method" value="Metodo de pago" />
                                <select
                                    id="payment_method"
                                    v-model="form.payment_method"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option v-for="method in paymentMethods" :key="method.value" :value="method.value">
                                        {{ method.label }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.payment_method" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <InputLabel :for="'business_name'" :value="isPersona ? 'Nombre completo' : 'Razon social'" />
                                <TextInput id="business_name" v-model="form.business_name" type="text" class="mt-1 block w-full" required />
                                <InputError :message="form.errors.business_name" class="mt-2" />
                            </div>

                            <div v-if="isEmpresa" class="md:col-span-2">
                                <InputLabel for="trade_name" value="Nombre comercial" />
                                <TextInput id="trade_name" v-model="form.trade_name" type="text" class="mt-1 block w-full" />
                                <InputError :message="form.errors.trade_name" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="email" value="Correo electronico" />
                                <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" />
                                <InputError :message="form.errors.email" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="phone" value="Telefono" />
                                <TextInput id="phone" v-model="form.phone" type="text" class="mt-1 block w-full" />
                                <InputError :message="form.errors.phone" class="mt-2" />
                            </div>

                            <div v-if="isEmpresa" class="md:col-span-2">
                                <InputLabel for="address" value="Direccion" />
                                <TextInput id="address" v-model="form.address" type="text" class="mt-1 block w-full" />
                                <InputError :message="form.errors.address" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="city" value="Ciudad" />
                                <TextInput id="city" v-model="form.city" type="text" class="mt-1 block w-full" required />
                                <InputError :message="form.errors.city" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="state" value="Estado / Region" />
                                <TextInput id="state" v-model="form.state" type="text" class="mt-1 block w-full" />
                                <InputError :message="form.errors.state" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="country" value="Pais" />
                                <TextInput id="country" v-model="form.country" type="text" class="mt-1 block w-full" required />
                                <InputError :message="form.errors.country" class="mt-2" />
                            </div>

                            <div v-if="isEmpresa">
                                <InputLabel for="contact_person" value="Persona de contacto" />
                                <TextInput id="contact_person" v-model="form.contact_person" type="text" class="mt-1 block w-full" />
                                <InputError :message="form.errors.contact_person" class="mt-2" />
                            </div>

                            <div v-if="isEmpresa">
                                <InputLabel for="contact_phone" value="Telefono de contacto" />
                                <TextInput id="contact_phone" v-model="form.contact_phone" type="text" class="mt-1 block w-full" />
                                <InputError :message="form.errors.contact_phone" class="mt-2" />
                            </div>

                            <div v-if="isEmpresa">
                                <InputLabel for="bank_name" value="Banco" />
                                <TextInput id="bank_name" v-model="form.bank_name" type="text" class="mt-1 block w-full" />
                                <InputError :message="form.errors.bank_name" class="mt-2" />
                            </div>

                            <div v-if="isEmpresa">
                                <InputLabel for="bank_account_number" value="Numero de cuenta" />
                                <TextInput id="bank_account_number" v-model="form.bank_account_number" type="text" class="mt-1 block w-full" />
                                <InputError :message="form.errors.bank_account_number" class="mt-2" />
                            </div>

                            <div v-if="isEmpresa">
                                <InputLabel for="bank_cci" value="CCI" />
                                <TextInput id="bank_cci" v-model="form.bank_cci" type="text" class="mt-1 block w-full" />
                                <InputError :message="form.errors.bank_cci" class="mt-2" />
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

                            <div class="md:col-span-2 flex items-center gap-3">
                                <input
                                    id="is_active"
                                    v-model="form.is_active"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    :true-value="true"
                                    :false-value="false"
                                />
                                <InputLabel for="is_active" value="Proveedor activo" />
                            </div>

                            <div class="md:col-span-2 flex items-center justify-end gap-4">
                                <Link :href="route('suppliers.index')" class="text-sm text-gray-600 underline hover:text-gray-900">
                                    Cancelar
                                </Link>
                                <PrimaryButton :disabled="form.processing">Guardar Proveedor</PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
