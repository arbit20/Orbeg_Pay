<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    client: Object,
    can: Object,
});

const deleteClient = () => {
    if (confirm('Esta seguro de que desea eliminar este cliente?')) {
        router.delete(route('clients.destroy', props.client.id));
    }
};
</script>

<template>
    <Head :title="client.business_name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        {{ client.business_name }}
                    </h2>
                    <p v-if="client.trade_name" class="mt-1 text-sm text-gray-500">
                        {{ client.trade_name }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Link v-if="can.edit" :href="route('clients.edit', client.id)">
                        <SecondaryButton>Editar</SecondaryButton>
                    </Link>
                    <DangerButton v-if="can.delete" @click="deleteClient">Eliminar</DangerButton>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-4 sm:px-6 lg:px-8">
                <div v-if="$page.props.flash?.success" class="rounded-md bg-green-50 p-4">
                    <p class="text-sm font-medium text-green-800">{{ $page.props.flash.success }}</p>
                </div>
                <div v-if="$page.props.flash?.error" class="rounded-md bg-red-50 p-4">
                    <p class="text-sm font-medium text-red-800">{{ $page.props.flash.error }}</p>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900">Datos generales</h3>
                        <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tipo de documento</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ client.document_type_label }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Numero de documento</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ client.document_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Estado</dt>
                                <dd class="mt-1">
                                    <span
                                        :class="client.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                        class="inline-flex rounded-full px-2 text-xs font-semibold leading-5"
                                    >
                                        {{ client.is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Correo electronico</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ client.email || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Telefono</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ client.phone || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Ventas registradas</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ client.sales_count }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900">Direccion y contacto</h3>
                        <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Direccion</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ client.address || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Ciudad</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ client.city || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Estado / Region</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ client.state || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Pais</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ client.country || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Persona de contacto</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ client.contact_person || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Telefono de contacto</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ client.contact_phone || '-' }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Notas</dt>
                                <dd class="mt-1 whitespace-pre-wrap text-sm text-gray-900">{{ client.notes || '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
