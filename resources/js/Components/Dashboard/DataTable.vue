<script setup>
defineProps({
    title: { type: String, required: true },
    columns: { type: Array, required: true },
    rows: { type: Array, required: true },
    emptyMessage: { type: String, default: 'No hay datos disponibles' },
});
</script>

<template>
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 px-5 py-4">
            <h3 class="text-sm font-semibold text-gray-900">{{ title }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table v-if="rows.length > 0" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th
                            v-for="col in columns"
                            :key="col.key"
                            class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                        >
                            {{ col.label }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    <tr v-for="(row, idx) in rows" :key="idx" class="hover:bg-gray-50 transition-colors">
                        <td v-for="col in columns" :key="col.key" class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                            <slot :name="col.key" :row="row" :value="row[col.key]">
                                {{ row[col.key] }}
                            </slot>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-else class="px-5 py-8 text-center text-sm text-gray-400">
                {{ emptyMessage }}
            </div>
        </div>
    </div>
</template>
