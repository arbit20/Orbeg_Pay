<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    supplier: Object,
    can: Object,
});

const deleteSupplier = () => {
    if (confirm('Esta seguro de que desea eliminar este proveedor?')) {
        router.delete(route('suppliers.destroy', props.supplier.id));
    }
};
</script>

<template>
    <Head :title="supplier.business_name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        {{ supplier.business_name }}
                    </h2>
                    <p v-if="supplier.trade_name" class="mt-1 text-sm text-gray-500">
                        {{ supplier.trade_name }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Link v-if="can.edit" :href="route('suppliers.edit', supplier.id)">
                        <SecondaryButton>Editar</SecondaryButton>
                    </Link>
                    <DangerButton v-if="can.delete" @click="deleteSupplier">Eliminar</DangerButton>
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
                                <dt class="text-sm font-medium text-gray-500">Tipo de proveedor</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ supplier.supplier_type_label }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tipo de documento</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ supplier.document_type_label }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Numero de documento</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ supplier.document_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Estado</dt>
                                <dd class="mt-1">
                                    <span
                                        :class="supplier.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                        class="inline-flex rounded-full px-2 text-xs font-semibold leading-5"
                                    >
                                        {{ supplier.is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Correo electronico</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ supplier.email || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Telefono</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ supplier.phone || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Compras registradas</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ supplier.purchases_count }}</dd>
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
                                <dd class="mt-1 text-sm text-gray-900">{{ supplier.address || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Ciudad</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ supplier.city || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Estado / Region</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ supplier.state || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Pais</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ supplier.country || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Persona de contacto</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ supplier.contact_person || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Telefono de contacto</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ supplier.contact_phone || '-' }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Notas / Observaciones</dt>
                                <dd class="mt-1 whitespace-pre-wrap text-sm text-gray-900">{{ supplier.notes || '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900">Datos bancarios</h3>
                        <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Banco</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ supplier.bank_name || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Numero de cuenta</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ supplier.bank_account_number || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">CCI</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ supplier.bank_cci || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Metodo de pago</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ supplier.payment_method_label || '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
