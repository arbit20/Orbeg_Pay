<script setup>
import StatCard from '@/Components/Dashboard/StatCard.vue';
import StatusBadge from '@/Components/Dashboard/StatusBadge.vue';
import DataTable from '@/Components/Dashboard/DataTable.vue';
import SectionTitle from '@/Components/Dashboard/SectionTitle.vue';

const props = defineProps({
    stats: { type: Object, required: true },
    recentAuditLogs: { type: Array, default: () => [] },
    recentSettlements: { type: Array, default: () => [] },
});

const formatCurrency = (amount, currency = 'USD') => {
    return new Intl.NumberFormat('es-PE', {
        style: 'currency',
        currency,
        minimumFractionDigits: 2,
    }).format(amount || 0);
};

const auditColumns = [
    { key: 'user_name', label: 'Usuario' },
    { key: 'action', label: 'Acción' },
    { key: 'auditable_type', label: 'Entidad' },
    { key: 'auditable_id', label: 'ID' },
    { key: 'ip_address', label: 'IP' },
    { key: 'created_at', label: 'Fecha' },
];

const settlementColumns = [
    { key: 'settlement_number', label: 'Nro.' },
    { key: 'user_name', label: 'Responsable' },
    { key: 'total_amount', label: 'Monto' },
    { key: 'status', label: 'Estado' },
    { key: 'settlement_date', label: 'Fecha' },
];
</script>

<template>
    <div class="space-y-8">
        <!-- Resumen financiero -->
        <section>
            <SectionTitle title="Resumen Financiero" subtitle="Vista consolidada de transacciones y liquidaciones" />
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <StatCard
                    title="Total compras"
                    :value="stats.purchases?.total ?? 0"
                    :subtitle="`${stats.purchases?.settled ?? 0} liquidadas`"
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
                    title="Total ventas"
                    :value="stats.sales?.total ?? 0"
                    :subtitle="`${stats.sales?.settled ?? 0} liquidadas`"
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

        <!-- Liquidaciones -->
        <section>
            <SectionTitle title="Estado de Liquidaciones" subtitle="Seguimiento de documentos de liquidación" />
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                <StatCard
                    title="Total liquidaciones"
                    :value="stats.settlements?.total ?? 0"
                    icon="&#128196;"
                    color="indigo"
                />
                <StatCard
                    title="Pendientes"
                    :value="stats.settlements?.pending ?? 0"
                    icon="&#9203;"
                    color="amber"
                />
                <StatCard
                    title="Parciales"
                    :value="stats.settlements?.partial ?? 0"
                    icon="&#128260;"
                    color="amber"
                />
                <StatCard
                    title="Completadas"
                    :value="stats.settlements?.completed ?? 0"
                    :subtitle="formatCurrency(stats.settlements?.total_amount)"
                    icon="&#9989;"
                    color="green"
                />
                <StatCard
                    title="Anuladas"
                    :value="stats.settlements?.voided ?? 0"
                    icon="&#128683;"
                    color="red"
                />
            </div>
        </section>

        <!-- Tablas -->
        <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <DataTable
                title="Últimas Liquidaciones"
                :columns="settlementColumns"
                :rows="recentSettlements"
                empty-message="No hay liquidaciones registradas"
            >
                <template #total_amount="{ row }">
                    {{ formatCurrency(row.total_amount, row.currency) }}
                </template>
                <template #status="{ row }">
                    <StatusBadge :status="row.status" />
                </template>
            </DataTable>

            <DataTable
                title="Registro de Auditoría Reciente"
                :columns="auditColumns"
                :rows="recentAuditLogs"
                empty-message="No hay registros de auditoría"
            >
                <template #action="{ row }">
                    <StatusBadge :status="row.action" />
                </template>
                <template #ip_address="{ row }">
                    <span class="font-mono text-xs text-gray-400">{{ row.ip_address || '—' }}</span>
                </template>
            </DataTable>
        </section>
    </div>
</template>
