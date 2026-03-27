<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    metal: Object,
    can: Object,
});

const deleteMetal = () => {
    if (confirm('Esta seguro de que desea eliminar este metal?')) {
        router.delete(route('metals.destroy', props.metal.id));
    }
};
</script>

<template>
    <Head :title="metal.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        {{ metal.name }}
                        <span class="ml-2 inline-flex rounded-full bg-amber-100 px-2 text-sm font-semibold text-amber-800">
                            {{ metal.symbol }}
                        </span>
                    </h2>
                    <p v-if="metal.description" class="mt-1 text-sm text-gray-500">{{ metal.description }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <Link v-if="can.edit" :href="route('metals.edit', metal.id)">
                        <SecondaryButton>Editar</SecondaryButton>
                    </Link>
                    <DangerButton v-if="can.delete" @click="deleteMetal">
                        Eliminar
                    </DangerButton>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div v-if="$page.props.flash?.success" class="mb-4 rounded-md bg-green-50 p-4">
                    <p class="text-sm font-medium text-green-800">{{ $page.props.flash.success }}</p>
                </div>
                <div v-if="$page.props.flash?.error" class="mb-4 rounded-md bg-red-50 p-4">
                    <p class="text-sm font-medium text-red-800">{{ $page.props.flash.error }}</p>
                </div>

                <!-- Informacion general -->
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900">Informacion General</h3>
                        <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nombre</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ metal.name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Simbolo</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ metal.symbol }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Estado</dt>
                                <dd class="mt-1">
                                    <span :class="metal.is_active
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-red-100 text-red-800'"
                                        class="inline-flex rounded-full px-2 text-xs font-semibold leading-5">
                                        {{ metal.is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Historial de precios -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Historial de Precios</h3>
                            <Link v-if="can.create_price" :href="route('metals.prices.create', metal.id)">
                                <PrimaryButton>Registrar Precio</PrimaryButton>
                            </Link>
                        </div>

                        <table class="mt-4 min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Precio/Gramo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Precio/Oz Troy</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Moneda</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Fuente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Registrado por</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="price in metal.prices" :key="price.id" class="hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ price.effective_date }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">$ {{ Number(price.price_per_gram).toFixed(4) }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">$ {{ Number(price.price_per_troy_ounce).toFixed(2) }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ price.currency }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ price.source ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ price.creator ?? '-' }}</td>
                                </tr>
                                <tr v-if="metal.prices.length === 0">
                                    <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                        No hay precios registrados para este metal.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
