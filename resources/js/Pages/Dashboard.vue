<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AdminDashboard from '@/Pages/Dashboard/AdminDashboard.vue';
import OperadorDashboard from '@/Pages/Dashboard/OperadorDashboard.vue';
import AuditorDashboard from '@/Pages/Dashboard/AuditorDashboard.vue';
import ClienteDashboard from '@/Pages/Dashboard/ClienteDashboard.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    role: { type: String, required: true },
    stats: { type: Object, default: () => ({}) },
    recentActivity: { type: Array, default: () => [] },
    recentPurchases: { type: Array, default: () => [] },
    recentSales: { type: Array, default: () => [] },
    myRecentPurchases: { type: Array, default: () => [] },
    myRecentSales: { type: Array, default: () => [] },
    pendingShipments: { type: Array, default: () => [] },
    recentAuditLogs: { type: Array, default: () => [] },
    recentSettlements: { type: Array, default: () => [] },
    metals: { type: Array, default: () => [] },
    myRequests: { type: Array, default: () => [] },
    myPurchases: { type: Array, default: () => [] },
});

const page = usePage();
const userName = computed(() => page.props.auth.user?.name ?? 'Usuario');

const roleTitles = {
    administrador: 'Panel de Administración',
    operador: 'Panel de Operaciones',
    auditor: 'Panel de Auditoría',
    cliente: 'Mi Portal',
    default: 'Dashboard',
};

const roleDescriptions = {
    administrador: 'Vista completa del sistema con métricas, operaciones y actividad reciente.',
    operador: 'Tus operaciones pendientes, compras, ventas y envíos.',
    auditor: 'Resumen financiero, liquidaciones y registros de auditoría.',
    cliente: 'Cotiza precios, crea solicitudes de venta y sigue el estado de tus transacciones.',
    default: 'Bienvenido al sistema.',
};

const dashboardTitle = computed(() => roleTitles[props.role] || roleTitles.default);
</script>

<template>
    <Head :title="dashboardTitle" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        {{ dashboardTitle }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ roleDescriptions[role] || roleDescriptions.default }}
                    </p>
                </div>
                <div class="hidden sm:flex items-center gap-2">
                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10">
                        {{ role.charAt(0).toUpperCase() + role.slice(1) }}
                    </span>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <AdminDashboard
                    v-if="role === 'administrador'"
                    :stats="stats"
                    :recent-activity="recentActivity"
                    :recent-purchases="recentPurchases"
                    :recent-sales="recentSales"
                />

                <OperadorDashboard
                    v-else-if="role === 'operador'"
                    :stats="stats"
                    :my-recent-purchases="myRecentPurchases"
                    :my-recent-sales="myRecentSales"
                    :pending-shipments="pendingShipments"
                />

                <AuditorDashboard
                    v-else-if="role === 'auditor'"
                    :stats="stats"
                    :recent-audit-logs="recentAuditLogs"
                    :recent-settlements="recentSettlements"
                />

                <ClienteDashboard
                    v-else-if="role === 'cliente'"
                    :stats="stats"
                    :metals="metals"
                    :my-requests="myRequests"
                    :my-purchases="myPurchases"
                />

                <div v-else class="overflow-hidden rounded-xl bg-white shadow-sm">
                    <div class="p-8 text-center">
                        <div class="mx-auto h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                            <span class="text-2xl">&#128075;</span>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Bienvenido, {{ userName }}</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Tu cuenta no tiene un rol asignado. Contacta al administrador del sistema.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
