<script setup>
import StatCard from '@/Components/Dashboard/StatCard.vue';
import StatusBadge from '@/Components/Dashboard/StatusBadge.vue';
import DataTable from '@/Components/Dashboard/DataTable.vue';
import SectionTitle from '@/Components/Dashboard/SectionTitle.vue';

const props = defineProps({
    stats: { type: Object, required: true },
    myRecentPurchases: { type: Array, default: () => [] },
    myRecentSales: { type: Array, default: () => [] },
    pendingShipments: { type: Array, default: () => [] },
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

const shipmentColumns = [
    { key: 'tracking_number', label: 'Tracking' },
    { key: 'carrier', label: 'Transportista' },
    { key: 'destination_address', label: 'Destino' },
    { key: 'status', label: 'Estado' },
    { key: 'estimated_delivery', label: 'Entrega Est.' },
];
</script>

<template>
    <div class="space-y-8">
        <!-- Mis tareas pendientes -->
        <section>
            <SectionTitle title="Mi Panel de Trabajo" subtitle="Resumen de tus operaciones pendientes" />
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <StatCard
                    title="Mis compras pendientes"
                    :value="stats.purchases?.my_pending ?? 0"
                    subtitle="Borradores por confirmar"
                    icon="&#128221;"
                    color="amber"
                />
                <StatCard
                    title="Mis ventas pendientes"
                    :value="stats.sales?.my_pending ?? 0"
                    subtitle="Borradores por confirmar"
                    icon="&#128221;"
                    color="amber"
                />
                <StatCard
                    title="Liquidaciones por cobrar"
                    :value="(stats.settlements?.pending ?? 0) + (stats.settlements?.partial ?? 0)"
                    subtitle="Pendientes y parciales"
                    icon="&#128176;"
                    color="red"
                />
                <StatCard
                    title="Envíos activos"
                    :value="(stats.shipments?.preparing ?? 0) + (stats.shipments?.in_transit ?? 0)"
                    :subtitle="`${stats.shipments?.in_transit ?? 0} en tránsito`"
                    icon="&#128230;"
                    color="blue"
                />
            </div>
        </section>

        <!-- Estado general de operaciones -->
        <section>
            <SectionTitle title="Estado de Operaciones" subtitle="Vista general de compras y ventas del sistema" />
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <StatCard
                    title="Total compras"
                    :value="stats.purchases?.total ?? 0"
                    :subtitle="`${stats.purchases?.confirmed ?? 0} confirmadas`"
                    icon="&#128230;"
                    color="green"
                />
                <StatCard
                    title="Compras en borrador"
                    :value="stats.purchases?.draft ?? 0"
                    subtitle="Pendientes de confirmar"
                    icon="&#128196;"
                    color="gray"
                />
                <StatCard
                    title="Total ventas"
                    :value="stats.sales?.total ?? 0"
                    :subtitle="`${stats.sales?.confirmed ?? 0} confirmadas`"
                    icon="&#128200;"
                    color="purple"
                />
                <StatCard
                    title="Ventas en borrador"
                    :value="stats.sales?.draft ?? 0"
                    subtitle="Pendientes de confirmar"
                    icon="&#128196;"
                    color="gray"
                />
            </div>
        </section>

        <!-- Directorio rápido -->
        <section>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <StatCard
                    title="Clientes activos"
                    :value="stats.clients?.active ?? 0"
                    :subtitle="`${stats.clients?.total ?? 0} en total`"
                    icon="&#128188;"
                    color="blue"
                />
                <StatCard
                    title="Proveedores activos"
                    :value="stats.suppliers?.active ?? 0"
                    :subtitle="`${stats.suppliers?.total ?? 0} en total`"
                    icon="&#128666;"
                    color="cyan"
                />
            </div>
        </section>

        <!-- Tablas -->
        <section class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <DataTable
                title="Mis Últimas Compras"
                :columns="purchaseColumns"
                :rows="myRecentPurchases"
                empty-message="No tienes compras registradas"
            >
                <template #total="{ row }">
                    {{ formatCurrency(row.total, row.currency) }}
                </template>
                <template #status="{ row }">
                    <StatusBadge :status="row.status" />
                </template>
            </DataTable>

            <DataTable
                title="Mis Últimas Ventas"
                :columns="saleColumns"
                :rows="myRecentSales"
                empty-message="No tienes ventas registradas"
            >
                <template #total="{ row }">
                    {{ formatCurrency(row.total, row.currency) }}
                </template>
                <template #status="{ row }">
                    <StatusBadge :status="row.status" />
                </template>
            </DataTable>
        </section>

        <!-- Envíos pendientes -->
        <section>
            <DataTable
                title="Envíos Pendientes"
                :columns="shipmentColumns"
                :rows="pendingShipments"
                empty-message="No hay envíos pendientes"
            >
                <template #tracking_number="{ row }">
                    <span class="font-mono text-xs">{{ row.tracking_number || '—' }}</span>
                </template>
                <template #destination_address="{ row }">
                    <span class="max-w-[200px] truncate block">{{ row.destination_address }}</span>
                </template>
                <template #status="{ row }">
                    <StatusBadge :status="row.status" />
                </template>
            </DataTable>
        </section>
    </div>
</template>
