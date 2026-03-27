<script setup>
import StatCard from '@/Components/Dashboard/StatCard.vue';
import StatusBadge from '@/Components/Dashboard/StatusBadge.vue';
import DataTable from '@/Components/Dashboard/DataTable.vue';
import SectionTitle from '@/Components/Dashboard/SectionTitle.vue';

const props = defineProps({
    stats: { type: Object, required: true },
    recentActivity: { type: Array, default: () => [] },
    recentPurchases: { type: Array, default: () => [] },
    recentSales: { type: Array, default: () => [] },
});

const formatCurrency = (amount, currency = 'USD') => {
    return new Intl.NumberFormat('es-PE', {
        style: 'currency',
        currency,
        minimumFractionDigits: 2,
    }).format(amount || 0);
};

const purchaseColumns = [
    { key: 'purchase_number', label: 'Nro.' },
    { key: 'supplier_name', label: 'Proveedor' },
    { key: 'total', label: 'Total' },
    { key: 'status', label: 'Estado' },
    { key: 'purchase_date', label: 'Fecha' },
];

const saleColumns = [
    { key: 'sale_number', label: 'Nro.' },
    { key: 'client_name', label: 'Cliente' },
    { key: 'total', label: 'Total' },
    { key: 'status', label: 'Estado' },
    { key: 'sale_date', label: 'Fecha' },
];

const activityColumns = [
    { key: 'user_name', label: 'Usuario' },
    { key: 'action', label: 'Acción' },
    { key: 'auditable_type', label: 'Entidad' },
    { key: 'auditable_id', label: 'ID' },
    { key: 'created_at', label: 'Fecha' },
];
</script>

<template>
    <div class="space-y-8">
        <!-- Resumen general -->
        <section>
            <SectionTitle title="Resumen General" subtitle="Vista global del sistema" />
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <StatCard
                    title="Usuarios activos"
                    :value="stats.users?.active ?? 0"
                    :subtitle="`${stats.users?.total ?? 0} registrados en total`"
                    icon="&#128101;"
                    color="indigo"
                />
                <StatCard
                    title="Clientes"
                    :value="stats.clients?.active ?? 0"
                    :subtitle="`${stats.clients?.total ?? 0} en total`"
                    icon="&#128188;"
                    color="blue"
                />
                <StatCard
                    title="Proveedores"
                    :value="stats.suppliers?.active ?? 0"
                    :subtitle="`${stats.suppliers?.total ?? 0} en total`"
                    icon="&#128666;"
                    color="cyan"
                />
                <StatCard
                    title="Metales"
                    :value="stats.metals?.active ?? 0"
                    :subtitle="`${stats.metals?.total ?? 0} registrados`"
                    icon="&#129351;"
                    color="amber"
                />
            </div>
        </section>

        <!-- Operaciones -->
        <section>
            <SectionTitle title="Operaciones" subtitle="Estado actual de compras, ventas y liquidaciones" />
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <StatCard
                    title="Compras totales"
                    :value="stats.purchases?.total ?? 0"
                    :subtitle="`${stats.purchases?.draft ?? 0} en borrador`"
                    icon="&#128230;"
                    color="green"
                />
                <StatCard
                    title="Monto compras"
                    :value="formatCurrency(stats.purchases?.total_amount)"
                    subtitle="Confirmadas y liquidadas"
                    icon="&#128176;"
                    color="green"
                />
                <StatCard
                    title="Ventas totales"
                    :value="stats.sales?.total ?? 0"
                    :subtitle="`${stats.sales?.draft ?? 0} en borrador`"
                    icon="&#128200;"
                    color="purple"
                />
                <StatCard
                    title="Monto ventas"
                    :value="formatCurrency(stats.sales?.total_amount)"
                    subtitle="Confirmadas y liquidadas"
                    icon="&#128176;"
                    color="purple"
                />
            </div>
        </section>

        <!-- Liquidaciones y Envíos -->
        <section>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <StatCard
                    title="Liquidaciones pendientes"
                    :value="(stats.settlements?.pending ?? 0) + (stats.settlements?.partial ?? 0)"
                    :subtitle="`${stats.settlements?.completed ?? 0} completadas`"
                    icon="&#128196;"
                    color="amber"
                />
                <StatCard
                    title="Liquidaciones anuladas"
                    :value="stats.settlements?.voided ?? 0"
                    :subtitle="`${stats.settlements?.total ?? 0} en total`"
                    icon="&#128683;"
                    color="red"
                />
                <StatCard
                    title="Envíos en tránsito"
                    :value="stats.shipments?.in_transit ?? 0"
                    :subtitle="`${stats.shipments?.preparing ?? 0} preparando`"
                    icon="&#9992;&#65039;"
                    color="blue"
                />
                <StatCard
                    title="Envíos entregados"
                    :value="stats.shipments?.delivered ?? 0"
                    :subtitle="`${stats.shipments?.total ?? 0} en total`"
                    icon="&#9989;"
                    color="green"
                />
            </div>
        </section>

        <!-- Tablas de datos recientes -->
        <section class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <DataTable
                title="Últimas Compras"
                :columns="purchaseColumns"
                :rows="recentPurchases"
                empty-message="No hay compras registradas"
            >
                <template #total="{ row }">
                    {{ formatCurrency(row.total, row.currency) }}
                </template>
                <template #status="{ row }">
                    <StatusBadge :status="row.status" />
                </template>
            </DataTable>

            <DataTable
                title="Últimas Ventas"
                :columns="saleColumns"
                :rows="recentSales"
                empty-message="No hay ventas registradas"
            >
                <template #total="{ row }">
                    {{ formatCurrency(row.total, row.currency) }}
                </template>
                <template #status="{ row }">
                    <StatusBadge :status="row.status" />
                </template>
            </DataTable>
        </section>

        <!-- Actividad reciente -->
        <section>
            <DataTable
                title="Actividad Reciente (Auditoría)"
                :columns="activityColumns"
                :rows="recentActivity"
                empty-message="No hay actividad registrada"
            >
                <template #action="{ row }">
                    <StatusBadge :status="row.action" />
                </template>
            </DataTable>
        </section>
    </div>
</template>
