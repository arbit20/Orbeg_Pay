<script setup>
defineProps({
    status: { type: String, required: true },
});

const statusStyles = {
    borrador: 'bg-gray-100 text-gray-700',
    confirmada: 'bg-blue-100 text-blue-700',
    liquidada: 'bg-emerald-100 text-emerald-700',
    cancelada: 'bg-red-100 text-red-700',
    pendiente: 'bg-amber-100 text-amber-700',
    parcial: 'bg-orange-100 text-orange-700',
    completada: 'bg-emerald-100 text-emerald-700',
    anulada: 'bg-red-100 text-red-700',
    preparando: 'bg-yellow-100 text-yellow-700',
    en_transito: 'bg-blue-100 text-blue-700',
    entregado: 'bg-emerald-100 text-emerald-700',
    cancelado: 'bg-red-100 text-red-700',
    en_revision: 'bg-blue-100 text-blue-700',
    aprobada: 'bg-emerald-100 text-emerald-700',
    rechazada: 'bg-red-100 text-red-700',
    convertida: 'bg-indigo-100 text-indigo-700',
    created: 'bg-green-100 text-green-700',
    updated: 'bg-blue-100 text-blue-700',
    deleted: 'bg-red-100 text-red-700',
    restored: 'bg-purple-100 text-purple-700',
};

const statusLabels = {
    borrador: 'Borrador',
    confirmada: 'Confirmada',
    liquidada: 'Liquidada',
    cancelada: 'Cancelada',
    pendiente: 'Pendiente',
    parcial: 'Parcial',
    completada: 'Completada',
    anulada: 'Anulada',
    preparando: 'Preparando',
    en_transito: 'En tránsito',
    entregado: 'Entregado',
    cancelado: 'Cancelado',
    en_revision: 'En Revisión',
    aprobada: 'Aprobada',
    rechazada: 'Rechazada',
    convertida: 'Convertida',
    created: 'Creado',
    updated: 'Actualizado',
    deleted: 'Eliminado',
    restored: 'Restaurado',
};
</script>

<template>
    <span
        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold"
        :class="statusStyles[status] || 'bg-gray-100 text-gray-700'"
    >
        {{ statusLabels[status] || status }}
    </span>
</template>
