<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    client: Object,
    documentTypes: Array,
});

const form = useForm({
    document_type: props.client.document_type,
    document_number: props.client.document_number,
    business_name: props.client.business_name,
    trade_name: props.client.trade_name ?? '',
    email: props.client.email ?? '',
    phone: props.client.phone ?? '',
    address: props.client.address ?? '',
    city: props.client.city ?? '',
    state: props.client.state ?? '',
    country: props.client.country ?? 'Peru',
    contact_person: props.client.contact_person ?? '',
    contact_phone: props.client.contact_phone ?? '',
    notes: props.client.notes ?? '',
    is_active: props.client.is_active,
});

const submit = () => {
    form.put(route('clients.update', props.client.id));
};
</script>

<template>
    <Head :title="`Editar ${client.business_name}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Editar Cliente
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="grid grid-cols-1 gap-6 md:grid-cols-2">
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

                            <div class="md:col-span-2">
                                <InputLabel for="business_name" value="Razon social" />
                                <TextInput id="business_name" v-model="form.business_name" type="text" class="mt-1 block w-full" required />
                                <InputError :message="form.errors.business_name" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
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

                            <div class="md:col-span-2">
                                <InputLabel for="address" value="Direccion" />
                                <TextInput id="address" v-model="form.address" type="text" class="mt-1 block w-full" />
                                <InputError :message="form.errors.address" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="city" value="Ciudad" />
                                <TextInput id="city" v-model="form.city" type="text" class="mt-1 block w-full" />
                                <InputError :message="form.errors.city" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="state" value="Estado / Region" />
                                <TextInput id="state" v-model="form.state" type="text" class="mt-1 block w-full" />
                                <InputError :message="form.errors.state" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="country" value="Pais" />
                                <TextInput id="country" v-model="form.country" type="text" class="mt-1 block w-full" />
                                <InputError :message="form.errors.country" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="contact_person" value="Persona de contacto" />
                                <TextInput id="contact_person" v-model="form.contact_person" type="text" class="mt-1 block w-full" />
                                <InputError :message="form.errors.contact_person" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="contact_phone" value="Telefono de contacto" />
                                <TextInput id="contact_phone" v-model="form.contact_phone" type="text" class="mt-1 block w-full" />
                                <InputError :message="form.errors.contact_phone" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <InputLabel for="notes" value="Notas" />
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
                                <InputLabel for="is_active" value="Cliente activo" />
                            </div>

                            <div class="md:col-span-2 flex items-center justify-end gap-4">
                                <Link :href="route('clients.show', client.id)" class="text-sm text-gray-600 underline hover:text-gray-900">
                                    Cancelar
                                </Link>
                                <PrimaryButton :disabled="form.processing">Actualizar Cliente</PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
