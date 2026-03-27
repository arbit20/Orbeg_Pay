<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    user: Object,
    can: Object,
});

const confirmingDeletion = ref(false);

const roleBadgeClass = (role) => {
    const classes = {
        administrador: 'bg-indigo-100 text-indigo-800',
        operador: 'bg-emerald-100 text-emerald-800',
        auditor: 'bg-amber-100 text-amber-800',
        cliente: 'bg-cyan-100 text-cyan-800',
    };
    return classes[role] || 'bg-gray-100 text-gray-800';
};

const roleLabel = (role) => {
    if (!role) return '-';
    return role.charAt(0).toUpperCase() + role.slice(1);
};

const deleteUser = () => {
    router.delete(route('users.destroy', props.user.id), {
        onSuccess: () => {
            confirmingDeletion.value = false;
        },
    });
};
</script>

<template>
    <Head :title="`Usuario: ${user.username || user.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Detalle del Usuario
                </h2>
                <div class="flex gap-2">
                    <Link v-if="can.edit" :href="route('users.edit', user.id)">
                        <PrimaryButton>Editar</PrimaryButton>
                    </Link>
                    <DangerButton v-if="can.delete" @click="confirmingDeletion = true">
                        Eliminar
                    </DangerButton>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl space-y-4 sm:px-6 lg:px-8">
                <div v-if="$page.props.flash?.success" class="rounded-md bg-green-50 p-4">
                    <p class="text-sm font-medium text-green-800">{{ $page.props.flash.success }}</p>
                </div>
                <div v-if="$page.props.flash?.error" class="rounded-md bg-red-50 p-4">
                    <p class="text-sm font-medium text-red-800">{{ $page.props.flash.error }}</p>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <dl class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nombre de usuario</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <span v-if="user.username" class="font-mono">{{ user.username }}</span>
                                    <span v-else class="text-amber-600">Sin asignar</span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nombre completo</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ user.name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Fecha de nacimiento</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ user.birth_date || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Correo electrónico</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ user.email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Teléfono</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ user.phone || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Rol</dt>
                                <dd class="mt-1">
                                    <span
                                        :class="roleBadgeClass(user.role)"
                                        class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                    >
                                        {{ roleLabel(user.role) }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Estado</dt>
                                <dd class="mt-1">
                                    <span
                                        :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                        class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                    >
                                        {{ user.is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Proveedor asociado</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <template v-if="user.supplier">
                                        <Link :href="route('suppliers.show', user.supplier.id)" class="text-indigo-600 hover:text-indigo-900">
                                            {{ user.supplier.business_name }}
                                        </Link>
                                    </template>
                                    <template v-else>-</template>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Fecha de registro</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ user.created_at }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Última actualización</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ user.updated_at }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="flex justify-start">
                    <Link :href="route('users.index')">
                        <SecondaryButton>Volver al listado</SecondaryButton>
                    </Link>
                </div>
            </div>
        </div>

        <Modal :show="confirmingDeletion" @close="confirmingDeletion = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    ¿Estás seguro de eliminar este usuario?
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Esta acción eliminará al usuario <strong>{{ user.username || user.name }}</strong>. Esta operación no se puede deshacer fácilmente.
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="confirmingDeletion = false">Cancelar</SecondaryButton>
                    <DangerButton @click="deleteUser">Eliminar Usuario</DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
